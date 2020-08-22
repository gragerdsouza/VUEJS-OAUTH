<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    //

    public function respondWithMessage($message, $status) 
    {
        return $this->respond([
            'status' => $status,
            'message' => $message
        ]);
    }

    public function login(Request $request)
    {
        $response_data = array();
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            $response_data['message'] = $validator->messages();
            $response_data['status'] = false;
            return response()->json($response_data);
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
        $response_data = json_decode($response->getContent());
        //print_r( $response_data->token_type);
        return $response->setStatusCode(200);

        return response()->json([
            'message' => 'Successfully Loged In',
            'status' => true,
            'token_type' => $response_data->token_type,
            'expires_in' => $response_data->expires_in,
            'access_token' => $response_data->access_token,
            'refresh_token' => $response_data->refresh_token
        ], 200);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            $response_data['message'] = $validator->messages();
            $response_data['status'] = false;
            return response()->json($response_data);
        }

        return User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
        ]);
    }

    public function logout()
    {
        //$a = \Laravel\Passport\Token::where('user_id', 2)->get();
        //print_r(auth()->user()->tokens);
        /*auth()->user()->tokens->each(function ($token, $key){
            $token->delete();
            //$token->revoke();
        });*/
        $accessToken = Auth::user()->token();
        DB::table('oauth_refresh_tokens')
            ->where('access_token_id', $accessToken->id)
            ->delete();
            /*->update([
                'revoked' => true
            ]);*/

        $accessToken->delete();
        //$accessToken->revoke();
        return response()->json('Logged Out Successfully', 200);
    }
}
