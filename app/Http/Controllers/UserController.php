<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
   public function login(Request $request){
      if(Auth::attempt($request->only('username', 'password'))){
         $user = Auth::user();
         $user= User::where('username', $request->username)->first();
         $token = $user->createToken('my-app-token')->plainTextToken;
         $response = [
             'user' => $user->id,
             'token' => $token,
         ];
         $cookie = \cookie('sanctum', $token, 3600);
          return \response($response, 201)->withCookie($cookie);
      }
      return response ([
            'error' => 'Invalid Credentials',
      ], Response::HTTP_UNAUTHORIZED);


   }

   public function register(Request $request){
      $user = User::create($request->only('first_name','last_name','address','brgy','city','province','region' ,'username') + [
         'password' => Hash::make($request->input('password'))
      ]);
      return response($user, Response::HTTP_CREATED);
   }
   

   public function logout(){
      $cookie = \Illuminate\Support\Facades\Cookie::forget('sanctum');
      return \response([
            'message' => 'success'
      ])->withCookie($cookie);
   }
 
}
