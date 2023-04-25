<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\LoginNeedsVerification;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function submit(Request $request)
    {
        //validate phone
        $request->validate([
            'phone' => 'required|numeric|max:10'
        ]);


        //create or find user
        $user = User::firstOrCreate([
            'phone' => $request->phone
        ]);

        if(!$user) {
            return response()->json(['message' => 'Cannot use this phone number!'], 401);

        }

        //send sms via twilio
        $user->notify(new LoginNeedsVerification());


        //return back response message
        return response()->json(['message' => 'Text message notification sent!'], 200);
    }
}
