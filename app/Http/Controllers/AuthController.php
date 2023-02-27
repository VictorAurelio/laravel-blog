<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    private $isSignedIn;
    public function __construct() {
      $this->middleware('auth:api', ['except' => ['loginAction', 'registerAction', 'unauthorized']]);
      $this->isSignedIn = Auth::user();
    }
  
    public function registerAction(Request $request) {
      // $array = ['error' => ''];

      // $request->validate([
      //   'firstName' => 'required|string|min:2|max:55',
      //   'lastName' => 'required|string|min:2|max:55',
      //   'email' => 'required|email|max:255|unique:users',
      //   'password' => 'required|min:8|confirmed'
      // ]);

      
      // $user = User::create([
      //   'firstName' => $request->firstName,
      //   'lastName' => $request->lastName,
      //   'email' => $request->email,
      //   'password' => Hash::make($request->password)
      // ]);

      // if($user) {
      //   $token = Auth::login($user);
      //   if($token) {
      //     $array['success'] = '200';
      //     $array['token'] = $token;
      //   }else {
      //     $array['error'] = 'Something went wrong';
      //     return $array;
      //   }
      // }else {
      //   $array['error'] = 'Something went wrong';
      //   return $array;
      // }      
      // return $array;
      
      $validator = Validator::make($request->all(), [
        'firstName' => 'required|string|max:255',
        'lastName' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 400);
    }

    $user = User::create([
        'firstName' => $request->input('firstName'),
        'lastName' => $request->input('lastName'),
        'email' => $request->input('email'),
        'password' => bcrypt($request->input('password')),
    ]);

    $token = JWTAuth::fromUser($user);

    return response()->json(compact('user', 'token'), 201);
    
    }

    public function loginAction(Request $request) {
      $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required',
      ]);

      if ($validator->fails()) {
          return response()->json(['error' => $validator->errors()], 400);
      }

      $credentials = $request->only('email', 'password');

      if (!$token = JWTAuth::attempt($credentials)) {
        return response()->json(['error' => 'Invalid credentials'], 401);
      }

      $user = auth()->user();

      return response()->json([
          'firstName' => $user->firstName,
          'lastName' => $user->lastName,
          'email' => $user->email,
          'token' => $token
      ], 200);
    }

    public function logout() {      
      try {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'User logged out successfully']);
      } catch (JWTException $exception) {
          return response()->json(['error' => 'Sorry, the user could not be logged out'], 500);
      }
    }

    public function refreshToken() {
      try {
        $token = JWTAuth::parseToken()->refresh();
        $user = JWTAuth::user();
        return response()->json([
            'token' => $token,
            'firstName' => $user->firstName,
            'email' => $user->email,
            'avatar' => url('media/avatars/'.$user['avatar'])
        ]);
      } catch (JWTException $e) {
          return response()->json(['error' => 'Token could not be refreshed'], 401);
      }
    }
    
    public function unauthorized() {
      return response()->json([
        'error' => 'Unauthorized Access'
      ], 401);
    }
}
