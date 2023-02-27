<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Response;
use Illuminate\View\View;


class UserController extends Controller
{
    private $isSignedIn;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['register', 'userProfile']]);
        $this->isSignedIn = Auth::user();
    }

    public function login() {
      return view('user.login');
    }

    public function register() {
      return view('auth.register');
    }

    public function myProfile() {
      $user = $this->isSignedIn;

      $posts = $user->posts;

      $userData = [
          'firstName' => $user->firstName,
          'lastName' => $user->lastName,
          'title' => $user->firstName . ' ' . $user->lastName,
          'email' => $user->email,
          'avatar' => url('media/avatars/' . $user->avatar),
          'posts' => $posts
      ];

      if (request()->expectsJson()) {
          return response()->json($userData);
      } else {
          return view('user.profile', ['userData' => $userData]);
      }
    }

    public function userProfile($id) {
      $user = User::find($id);

      if($user) {
        $avatar = url('media/avatars/'.$user['avatar']);
        $name = $user->firstName . ' ' . $user->lastName;
        /*
        . Add his posts, along with his 'description'
        .
        .
        */
        // Store the data in an array
        $data = [
          'name' => $name,
          'avatar' => $avatar,
      ];
       // If the user doesn't exist, redirect to the register page with an error message
      }else {
        return response()->json(['error' => 'User not found.'], 404);
      }
      // Return a view with the user's name and a JSON response
      return response()->view('user.profile_guest', $data)->header('Content-Type', 'application/json');
    }

    public function editProfileAction(Request $request) {
      // Get the user ID from the JWT token
      $userId = auth()->user()->id;

      // Get the user from the database
      $user = User::find($userId);

      // Validate the request data
      $validator = Validator::make($request->all(), [
      'firstName' => 'sometimes|string|max:255',
      'lastName' => 'sometimes|string|max:255',
      'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
      'password' => 'sometimes|string|min:8|confirmed',
      ]);

      // Check if the validation fails
      if ($validator->fails()) {
        return response()->json([
            'message' => 'The given data was invalid.',
            'errors' => $validator->errors(),
        ], 422);
      }

        // Check if any fields were included in the request
        if (!$request->filled('firstName') && !$request->filled('lastName') && !$request->filled('email') && !$request->filled('password')) {
          return response()->json([
              'message' => 'No fields were included in the request.',
          ], 400);
        }

      // Check which fields were included in the request and update the user accordingly
      $firstName = $request->filled('firstName') ? $request->firstName : $user->firstName;
      $lastName = $request->filled('lastName') ? $request->lastName : $user->lastName;
      $email = $request->filled('email') ? $request->email : $user->email;
      $password = $request->filled('password') ? bcrypt($request->password) : $user->password;

      // Update the user record
      $result = $user->update([
        'firstName' => $firstName,
        'lastName' => $lastName,
        'email' => $email,
        'password' => $password,
      ]);
      // Save the changes to the database
      $user->save();

        // Check if the update was successful
      if (!$result) {
        return response()->json([
            'message' => 'Error updating user profile.',
        ], 500);
      }

      // Return a success response
      return response()->json([
          'message' => 'User profile updated successfully.',
          'user' => $user,
      ]);
    }

    public function edit() {
      $user = [
        'firstName' => $this->isSignedIn->firstName,
        'lastName' => $this->isSignedIn->lastName,
        'email' => $this->isSignedIn->email,
      ];

      return view('user.edit', ['user' => $user]);
    }
}
