<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use App\Models\Component;
use App\Services\TourService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\DB;

class TourController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('Tours/Index', [
            'tours' => Tour::get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //Pull in all components to show them in the edit form. We only need the ID and the internal name for this
        $components = Component::all(['id', 'internal_name']);

        return Inertia::render('Tours/Create', [
            'components' => $components
        ]);
    }
    /*
    Shared validation rules for the Tour Controller.
    **/
    private $tourValidationRules = [
        'type' => 'required|string|max:255',
        'internal_code' => 'required|string|max:255',
        'tour_code' => 'required|string|max:255',
        'display_name' => 'required|string|max:255',
        'description' => 'string|max:1500',
        'inclusions' => 'string|max:1500',
        'grouping_code' => 'string|max:255',
        'special_text' => 'string|nullable|max:255',
        'direction' => 'required|string|max:255',
        'active' => 'required|boolean',
        'online_booking' => 'required|boolean',
        'is_denali' => 'required|boolean',
    ];

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, TourService $tourService): RedirectResponse
    {
        $validated = $request->validate($this->tourValidationRules);

        $tour = DB::transaction(function () use ($validated, $request, $tourService) {
            /*
             * Create Tour
             */

            $callbackTour = Tour::create($validated);

            /*
            Dig into the components array in the request. If the array is not empty begin preparing the associative array
            that we will hand off to the attach method on the tour model. 

            The interpolated string with a $key is used because we get back an array of arrays if we access componenets
            directly. The input() method on request needs to be leveraged to reliably extract specific field values from 
            the nested objects sent over from the create form. 
            */

            $callbackTour->itineraries()->delete();
            $tourService->createOrUpdateComponent($request, $callbackTour);

            /*
            Next we save rate information for tours.
            */
            $this->createOrUpdateRates($request, $callbackTour);
            return $callbackTour;
        });


        return redirect('/tours');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tour $tour)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tour $tour)
    {
        //Pull in all components to show them in the edit form. We only need the ID and the internal name for this
        $components = Component::all(['id', 'internal_name']);

        //lazy-eager load rates and itineraries related to this tour
        $tour->load([
            'rates',
            'itineraries' => function (Builder $query) {
                $query
                    ->orderBy(
                        'tour_day'
                    );
            }
        ]);

        return Inertia::render('Tours/Edit', [
            'tour' => $tour,
            'components' => $components
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tour $tour, TourService $tourService): RedirectResponse
    {
        /*
            @todo need to find a way to dry this out. While we do need to make some changes on 
            how things are managed with the component pivot here
        */
        $validated = $request->validate($this->tourValidationRules);

        DB::transaction(function () use ($tour, $validated, $request, $tourService) {
            $tour->update($validated);

            /*
            Dig into the components array in the request. If the array is not empty begin preparing the associative array
            that we will hand off to the attach method on the tour model. 

            The interpolated string with a $key is used because we get back an array of arrays if we access componenets
            directly. The input() method on request needs to be leveraged to reliably extract specific field values from 
            the nested objects sent over from the create form. 
            */

            $tour->itineraries()->delete();
            $tourService->createOrUpdateComponent($request, $tour);

            /*
            Next we save rate information for tours.
            */
            $this->createOrUpdateRates($request, $tour);
        });

        return redirect('/tours');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tour $tour): RedirectResponse
    {
        $tour->delete();

        return redirect(route('tours.index'));
    }

    //@todo MOVE THESE INTO THE TOUR SERVICE. CONTROLLERS SHOULD ONLY RESPOND TO REQUESTS
    private function createOrUpdateRates(Request $request, Tour $tour)
    {
        if (!empty($request->input('rates'))) {
            foreach ($request->input('rates') as $key => $rate) {
                //Check if the rate has an ID. If not we will create it
                $rate_array = $this->ratesArrayFromRequest($request, $key);

                if (!$request->input("rates.{$key}.id")) {
                    $tour->rates()->create($rate_array);
                } else {
                    $tour->rates()->where('id', $request->input("rates.{$key}.id"))->update($rate_array);
                }
            }
        }
    }

    private function ratesArrayFromRequest(Request $request, $key)
    {
        $rate_array = [
            'single' => $request->input("rates.{$key}.single"),
            'double' => $request->input("rates.{$key}.double"),
            'triple' => $request->input("rates.{$key}.triple"),
            'quad' => $request->input("rates.{$key}.quad"),
            'child' => $request->input("rates.{$key}.child"),
            'addon' => $request->input("rates.{$key}.addon"),
            'tax' => $request->input("rates.{$key}.tax"),
            'valid_from' => $request->input("rates.{$key}.valid_from"),
            'valid_to' => $request->input("rates.{$key}.valid_to"),
            'start_date' => $request->input("rates.{$key}.start_date"),
            'end_date' => $request->input("rates.{$key}.end_date"),
            'rate_type' => $request->input("rates.{$key}.rate_type"),
            'display_label' => $request->input("rates.{$key}.display_label"),
        ];

        return $rate_array;
    }
}
