<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Hash;
use App\User;
use Auth;

class PatientController extends Controller
{
    public function login(Request $request)
    {

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
             $user = $request->user();
             $data['token'] = $user->createToken('MyApp')->accessToken;
             $data['name']  = $user->name;
             return response()->json($data, 200);
         }

       return response()->json(['error'=>'Unauthorized'], 401);
    }

    public function register(Request $request)
    {

      $validator = Validator::make($request->all(), [
        'fname' => 'required',
        'mname' => 'required',
        'lname' => 'required',
        'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|unique:users'
        'email' => 'required|email|unique:users',
        'password' => 'required|min:8|confirmed',
      ]);

      if ($validator->fails()) {
          return response()->json(['error'=>$validator->errors()], 401);
      }

      $user = $request->all();
      $user['password'] = Hash::make($user['password']);
      $user = User::create($user);
      $success['token'] =  $user->createToken('MyApp')->accessToken; 
      $success['name'] =  $user->name;

      return response()->json(['success'=>$success, 'message' =>'User created successfully']); 
    }

     public function userDetail()
    {
        $user = Auth::user();

        return response()->json(['user' => $user], 200);
    }
}
