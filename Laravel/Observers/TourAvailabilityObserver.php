<?php

namespace App\Observers;

use App\Models\Tour;
use App\Services\TourService;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class TourAvailabilityObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * Handle the Tours "created" event.
     */
    public function created(Tour $tour): void
    {
        $this->saveTourDetails($tour);
    }

    /**
     * Handle the Tours "updated" event.
     */
    public function updated(Tour $tour): void
    {
        $this->saveTourDetails($tour);

    }

    private function saveTourDetails($tour) {
        /**
         * Fetch and bake availability for a tour when it updates.
         */
        $tourService = new TourService();

        $tourDetails = $tourService->deriveTourDetailsFromComponents($tour);

        /**
         * If the tour we are responding to is set to Active then availability will be constructed and saved for it. Otherwise we NULL the 
         * availability column because we don't want to chance an inactive tour coming back in a query for available tours. 
         */
        if($tour->active) {
            $dates = $tourService->getAllAvailableDates($tour, $tourService->getSeasonStartorToday(), $tourService->getSeasonEnd());
            $tourDetails['availability'] = $dates;

            /**
             * While we have total availability at hand, we are going to persist a small additional set of availability information.
             * Storing a small array that describes the broad availbility for a tour (in the form of YEAR-MONTH) will allow results to be 
             * filtered easily withough returning the entire availability array for each Tour. At the beginning of a season each availability column on a tour 
             * can include over 150 items. With full inventory that could be an additional 4,500 items that we don't need unless a user requests it. 
             */
            $tourDetails['available_months'] = $tourService->getAvailbleMonthsFromAvailabilityArray($dates);
        } else {
            $tour->availability = NULL;
        }

        $tour->fill($tourDetails);

        //shhh... SAVE QUIETLY! or loop infinitely 
        $tour->saveQuietly();
    }
}
