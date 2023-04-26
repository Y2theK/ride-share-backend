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
            'phone' => 'required|min:8'
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
    public function verify(Request $request)
    {

        //validate request
        $request->validate([
            'phone' => 'required|min:8',
            'login_code' => 'required|numeric|between:111111,999999'
        ]);

        //find user
        $user = User::where('phone', $request->phone)
                ->where('login_code', $request->login_code)
                ->first();

        if($user) {
            $user->update([
                'login_code' => null
            ]);
            return $user->createToken($request->login_code)->plainTextToken;
        }

        return response()->json(['message' => 'Invalid Login Code'], 403);


    }
}
