<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use Carbon\Carbon;
use ValidateRequests;
use Auth;

class AuthController extends Controller
{

	public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();

            return $next($request);
        });
    }

	public function signup(Request $request){
		/*$request->validate([
			'name' => 'required',
			'email' => 'required|string|unique:users',
			'password' => 'required|string|confirmed'
		]);
		return $request or die();*/

		/*$validator = \Validator::make(['data' => $request],
            ['name' => 'required'],
            ['email' => 'required|string|unique:users'],
            ['password' => 'required|string|confirmed']
        );

        if ($validator->fails()) {
            return $validator->errors();// your code
            // return response()->json(['message' => 'User validation failed. Enter required fields with correct formate']);// your code
        }*/
        
		$user = new User([
			'name' => $request->name,
			'email' => $request->email,
			'password' => bcrypt($request->password)
		]);

		$success = $user->save();
		// $user->save();

		if($success)
			return response()->json(['message' => 'User Successfully created']);

	}

	public function login(Request $request){

		/*$request->validate([
			'name' => 'required',
			'email' => 'required|string',
			'remember' => 'boolean'
		]);
		do or die()*/

		/*$validator = \Validator::make(['data' => $request],
            // ['name' => 'required'],
            ['email' => 'required'],
            ['password' => 'required'],
            ['remember' => 'boolean']
        );

        if ($validator->fails()) {
            // return response()->json(['message' => 'Validation Failed. Fill correct data']);// your code
            return $validator->errors();// your code
        }*/

		$credentials = request('email','password');

		if(!Auth::attempt($credentials))
			return response()->json(['message' => 'Unauthorised'],401);

		$user = $request->user();
		$tokenRes = $user->createToken('Personal Access Token');
		$token = $tokenRes->token;

		if($request->remember_me)
			$token->expires_at = Carbon::now()->addWeeks(1);
		$token->save();
		return response()->json([
			'access_token' => $tokenResult->accessToken,
			'token_type' => 'Bearer',
			'expires_at' => Carbon::parse(
				$tokenResult->token->expires_at
			)->toDateTimeString()
		]);
	}


	public function logout(Request $request){

		$request->user()->token()->revoke();
		return response()->json([
			'message' => 'Successfully logged out'
		]);
	}

	public function user(Request $request){
		return response()->json($request->user());
	}
    //
}
