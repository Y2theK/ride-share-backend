<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Illuminate\Http\Request;

class TripController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'origin' => 'required',
            'destination' => 'required',
            'destination_name' => 'required'
        ]);

        $trip = $request->user()->trips()->create($request->only([
            'origin',
            'destination',
            'destination_name'
        ]));

        return $trip;
    }
    public function show(Request $request, Trip $trip)
    {
        if($request->user()->id === $trip->user->id) {
            return $trip;
        }

        if($request->user()->driver && $trip->driver) {
            if($request->user()->driver->id === $trip->driver->id) {
                return $trip;
            }
        }

        return response()->json(['message' => 'Trip not found'], 404);
    }
    public function accept(Request $request, Trip $trip)
    {
        $request->validate([
            'driver_location' => 'required'
        ]);

        $trip->update([
            'driver_id' => $request->user()->id,
            'driver_location' => $request->driver_location
        ]);


        $trip->load('driver.user');  //load both associated driver and user

        return $trip;
    }
    public function start(Request $request, Trip $trip)
    {
       
        $trip->update([
            'is_started' => true
        ]);


        $trip->load('driver.user');  //load both associated driver and user

        return $trip;
    }
    public function end(Request $request, Trip $trip)
    {
        $trip->update([
            'is_completed' => true
        ]);


        $trip->load('driver.user');  //load both associated driver and user

        return $trip;
    }
    public function location(Request $request, Trip $trip)
    {
        $request->validate([
            'driver_location' => 'required'
        ]);

        $trip->update([
            'driver_location' => $request->driver_location
        ]);


        $trip->load('driver.user');  //load both associated driver and user

        return $trip;
    }
}
