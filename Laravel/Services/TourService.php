<?php 

namespace App\Services;
use App\Models\Tour;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class TourService {

    /**
     * Collection overnight desitnations from 'overnight' itinerary items.
     * @param \Illuminate\Support\Collection $itineraries
     * @return array|null
     */
    public function getTourOvernightDestinations(Collection $itineraries): ?Array {
        $overnight_destinations = [];

        foreach($itineraries as $itinerary) {
            if(strtolower($itinerary->components->type) == 'overnight') {
                $overnight_destinations[] = $itinerary->components->city;
            }
        }

        return !empty($overnight_destinations) ? array_unique($overnight_destinations) : null;
    }

    /**
     * Collect the number of days a tour will last based on its saved itinerary.
     * @param \Illuminate\Support\Collection $intineraries
     * @return mixed
     */
    public function getTourDays(Collection $intineraries) {
        $tourDays = [];

        foreach($intineraries as $itinerary) {
            $tourDays[] = $itinerary->tour_day;
        }

        return !empty($tourDays) ? max($tourDays) : 0;
    }

    /**
     *  Extract a start or end point and return it 
     * @param \Illuminate\Support\Collection $itinerary
     * @param string $direction
     * @return mixed
     */
    public function getTourDepartOrEnd(Collection $itinerary, string $direction = 'depart') {
        $city = null;

        if($itinerary->isNotEmpty()) {
            $component = $direction != 'depart' ? $itinerary->last()->components : $itinerary->first()->components;
            $city = strtolower($component->type) == 'rail' ? $component->origin : $component->city;
        }
        
        return $city;
    }

    /**
     * Method to derive tour details from components. This will persist this information at the tour level and 
     * reduce the number of operations that are performed when hitting the API endpoints.
     * @param \App\Models\Tour $tour
     * @return array
     */
    public function deriveTourDetailsFromComponents(Tour $tour): array {
        $tour->load('itineraries.components');
        $days = $this->getTourDays($tour->itineraries);

        return [
            'days' => $days,
            'overnight_destinations' => $this->getTourOvernightDestinations($tour->itineraries),
            'depart_location' => $this->getTourDepartOrEnd($tour->itineraries, 'depart'),
            'end_location' => $this->getTourDepartOrEnd($tour->itineraries, 'end' )
        ];
    }

    /**
     * Method to pull and return an array of available months from an array of available dates.
     * This method is used to collect months that a tour is available as a broad indicator of availability and 
     * will be used to query specific sets of tours or filter results. 
     * @param array $dates
     * @return void
     */
    public function getAvailbleMonthsFromAvailabilityArray(Array $dates): array {
        $months = [];

        foreach( $dates as $date ) {
            /*
            * We expect to have an array of ISO date strings and we only want the year and month to do filtering
            * slice off the last three characters -DD and then place the new string in the array if it
            * isnt in there already
            */
            $dateString = substr($date, 0,7);
            if(!in_array($dateString, $months, true)) {
                $months[] = $dateString;
            }
        }

        return $months;
    }

    /**
     * Method that determines availability for a single tour based on attached components 
     * and itinerary items.
     * 
     * @param \App\Models\Tour $tour
     * @param string $departure_date
     * @return bool
     */
    public function isTourAvailable(Tour $tour, string $departure_date): bool {
        $is_available = false;
        
        if($departure_date) {
            foreach ($tour->itineraries as $itinerary) {
                $availability_array = $itinerary->components->availability ?? [];

                /**
                 * Itinerary items fall on different days of a tour. If an item happens further ahead of the 
                 * departure date (day 1) then we need to increase the date we check accordingly. 
                 * Otherwise we assume that all components happen on the same day which is 
                 * not true. 
                 * 
                 * @todo move into an offsetDate function
                 */
                if($itinerary->tour_day > 1){
                    //Explode the departure date because we need to increase the day
                    $explode_date = explode('-', $departure_date);

                    //Update the day but the saved tour day minus one, otherwise we will add one more day than needed. 
                    $explode_date[2] += $itinerary->tour_day - 1;

                    //Smash it back together
                    $departure_date = implode("-", $explode_date);
                }

                /**
                 * Availability dates are stored as key/value pairs. The key is the date string 
                 * saved in YYYY-MM-DD format. The value should be 1. Only valid pairs are stored for componenet. 
                 * Either way the value is not important. We are using a JSON column to make checking easier and 
                 * leave room should the value need to be different. 
                 */
                if(!in_array($departure_date, $availability_array)) {
                    $is_available = false;
                    break;
                }

                $is_available = true;
            }
        }

        return $is_available;
    }

    /**
     * Method to determine if a tour has ANY availability within a given date range, ideally SEASON Start 
     * and SEASON End. The function withh break its loop and return true if/when a single available date is 
     * found. 
     * @param \App\Models\Tour $tour
     * @return bool
     */
    public function tourHasAvailability(Tour $tour) {
        $has_availability = false;
        $dates = array_reverse($this->get_dates_between($this->getSeasonStartorToday(), $this->getSeasonEnd()));
        
        foreach( $dates as $date ) {
            if($this->isTourAvailable($tour, $date)) {
                $has_availability = true;
                break;
            }
        }

        return $has_availability;
    }

    /**
     * Method for collecting all available dates within a date range for a Tour. Returns an 
     * array of available dates in ISO format.
     * @param \App\Models\Tour $tour
     * @param mixed $range_start
     * @param mixed $range_end
     * @return array
     */
    public function getAllAvailableDates(Tour $tour, $range_start, $range_end): array
    {
        //Get everyday between the start and end range.
        $dates = $this->get_dates_between($range_start, $range_end);

        $available_dates = [];

        foreach ($dates as $date) {
            $available = $this->isTourAvailable($tour, $date);

            if ($available) {
                $available_dates[] = $date;
            }
        }

        return $available_dates;
    }

    /**
     * Check if a component needs to be added to the itinerary table of if it needs to be updated
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Tour $tour
     * @return void
     */
    public function createOrUpdateComponent(Request $request, Tour $tour) {
        if(!empty($request->input('components')) ) {
            foreach($request->input('components') as $key=>$component) {
                $itinerary_array = $this->itineraryArrayFromRequest($request, $key);
                $tour->itineraries()->create( $itinerary_array);
            }

            //Need to boop the tour since this happens outside of the model update
            $tour->touch();
        }
    }

    /**
     * Build an array of itinerary information from a request to assist in validation.
     * @param \Illuminate\Http\Request $request
     * @param mixed $key
     * @return array
     */
    public function itineraryArrayFromRequest(Request $request, $key): Array {
        $current_key = "components.{$key}";
        $itinerary_array = [
            'component_id' => $request->input("{$current_key}.component_id"),
            'tour_day' => $request->input("{$current_key}.tour_day"),
            'departure_time' => $request->input("{$current_key}.departure_time"),
            'arrival_time' => $request->input("{$current_key}.arrival_time"),
            'itinerary_label' => $request->input("{$current_key}.itinerary_label"),
            'itinerary_description' => $request->input("{$current_key}.itinerary_description")
        ];

        return $itinerary_array;
    }

    /**
     * Helper function to determine the current seasonal year
     * If we are between the season start and end month the current year is returned
     * Otherwise we are in the new season and need to roll over to the next year. 
     * 
     * @return int
     */
    public function getSeasonalYear() {
        $season_year = 0000;
        $season_start_month = config('app.season_start_month');
        $season_end_month = config('app.season_end_month');
        $current_month = Carbon::now()->month;

        if($current_month >= $season_start_month && $current_month <= $season_end_month) {
            $season_year = Carbon::now()->year;
        } else {
            $season_year = Carbon::now()->year + 1;
        }

        return $season_year;
    }

    /**
     * Method to return the season start date. If the current date is greater than the season start date
     * return the current date, otherwise return the season start date.
     * 
     * @return string
     */
    public function getSeasonStartorToday(): string {
        $season_start_month = config('app.season_start_month');
        $season_year = $this->getSeasonalYear();

        if(Carbon::now()->month >= $season_start_month) {
            return Carbon::now()->format('Y-m-d');
        }

        return "{$season_year}-{$season_start_month}-01";
    }

    /**
     * Method to return the season end date.
     */
    public function getSeasonEnd(): string {
        $season_end_month = config('app.season_end_month');
        $season_year = $this->getSeasonalYear();

        //get the last day of $season_end_month
        $last_day_of_season_end_month = Carbon::parse("{$season_year}-{$season_end_month}-01")->daysInMonth;

        return "{$season_year}-{$season_end_month}-{$last_day_of_season_end_month}";
    }

    /**
     * Helper function to return all dates within a range. Can accept multiple types 
     * of date strings and will return them in ISO format, which is what most functions and 
     * models expect.
     * 
     * @param mixed $start
     * @param mixed $end
     * @return array
     */
    private function get_dates_between($start, $end)
    {
        $start = Carbon::parse($start);
        $end = Carbon::parse($end);
        $dateRange = CarbonPeriod::create($start, $end);

        $dates = [];
        foreach ($dateRange as $date) {
            $dates[] = $date->format('Y-m-d');
        }

        return $dates;

    }
}
