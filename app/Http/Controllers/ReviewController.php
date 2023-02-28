<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReviewResource;
use App\Models\Post;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    private $isSignedIn;

    public function __construct() {
      $this->middleware('auth:api', ['except' => ['showReviews']]);
      $this->isSignedIn = Auth::user();
    }

    public function createReview(Request $request, Post $post) {
      // Get user data
      $user = $this->isSignedIn;

      $validator = Validator::make($request->all(), [
          'rate' => 'required|numeric|max:5.0',
          'commentary' => 'required|max:450',
      ]);
      // Return a message in case validator fails
      if ($validator->fails()) {
          return response()->json([
              'error' => $validator->messages(),
          ], 400);
      }

      if (!$user) {
          return response()->json([
              'error' => 'You need to log in in order to review a post.',
          ], 401);
      }

      // Check if the user is signed in
      if($user && !$validator->fails()) {
        // Create a new review
        $review = $user->reviews()->create([
          'rate' => $request->input('rate'),
          'commentary' => $request->input('commentary'),
          'post_id' => $post->id,
          'user_id' => $user->id
        ]);

        // Save the review to the post
        $post->reviews()->save($review);

        // Save the review to the user
        $user->reviews()->save($review);

        return response()->json([
          'message' => 'Review created successfully',
          'review' => $review
        ]);
      }else {
        return response()->json([
          'error' => 'You need to log in in order to review a post.'
        ], 401);
      }
    }

    public function showReviews(Post $post) {
      $reviews = $post->reviews;
      $reviewResource = new ReviewResource($reviews, $this->isSignedIn);
      return response()->json($reviewResource);
    }

    public function deleteReview(Post $post, Review $review) {
      // Get the authenticated user, if any
      $user = Auth::user();
    
      // Make sure the user is authenticated
      if (!$user) {
        return response()->json([
          'error' => 'You need to log in to delete a review.',
        ], 401);
      }
    
      // Check if the user created the review
      if ($review->user_id !== $user->id) {
        return response()->json([
            'error' => 'You are not authorized to delete this review.',
        ], 401);
      }
      
      if($review->post_id !== $post->id) {
        return response()->json([
          'error' => 'This review does not belong to this post.',
        ], 400);
      }

      // Delete the review
      $review->delete();
    
      // Return a success response
      return response()->json([
        'message' => 'Review deleted successfully.'
      ], 204);
    }

}
