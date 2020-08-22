<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    //
    public function login(Request $request)
    {
        $data = array();
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            $data['message'] = $validator->messages();
            $data['status'] = false;
            return response()->json($data);
        }

        $data = [
            'grant_type' => 'password',
            'client_id' => config()->get('services.oauth.client_id'),
            'client_secret' => config()->get('services.oauth.client_secret'),
            'username' => request('username'),
            'password' => request('password'),
        ];
        $request = Request::create('/oauth/token', 'POST', $data);
        $response = app()->handle($request);

        // Check if the request was successful
        if ($response->getStatusCode() != 200) {
            return response()->json([
                'message' => 'Wrong email or password',
                'status' => false
            ], 422);
        }
        
        // Get the data from the response
        //$data = json_decode($response->getContent());
        return $response;
    }
}
