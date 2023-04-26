<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function show(Request $request)
    {

        $user = $request->user();

        $user->load('driver');    // load driver object if there is any relationship with user and driver

        return $user;
        
    }
    public function update(Request $request)
    {
        $request->validate([
            'year' => 'required|numeric|between:2010,2024',
            'make' => 'required',
            'color' => 'required|alpha',
            'license_plate' => 'required',
            'model' => 'required',
            'name' => 'required'
        ]);

        $user = $request->user();
        //update user name
        $user->update($request->only('name'));

        
        // create or update a driver associated with this user
        $user->driver()->updateOrCreate(['user_id' => $user->id], $request->only([
            'year',
            'make',
            'color',
            'model',
            'license_plate'
        ]));

        $user->load('driver');

        return $user;


    }
}
