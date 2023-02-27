<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use Carbon\Carbon;

class PostController extends Controller
{
  private $isSignedIn;
  public function __construct() {
    $this->middleware('auth:api', ['except' => ['showAllPosts', 'showOnePost']]);
    $this->isSignedIn = Auth::user();
  }
  public function createPostAction(Request $request) {
    // Check if the user is authenticated
    if (!$this->isSignedIn) {
        return response()->json([
            'message' => 'User is not authenticated.',
        ], 401);
    }

    // Validate the request data
    $validator = Validator::make($request->all(), [
        'title' => ['required', 'string', 'max:255', function ($attribute, $value, $fail) {
          $existingPost = Post::where('title', $value)->first();
          if ($existingPost) {
              $fail('A post with the same title already exists.');
              }
          }],
        'body' => 'required|string',
        'picture' => 'required|image|mimes:jpeg,png,jpg,svg|max:2048',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation error.',
            'errors' => $validator->errors(),
        ], 422);
    }

    // Save the post
    $post = new Post([
        'title' => $request->input('title'),
        'body' => $request->input('body'),
        'picture' => $request->file('picture')->store('public/posts'),
    ]);

    $this->isSignedIn->posts()->save($post);
    //dd($this->isSignedIn->posts());

    // Return a success response
    return response()->json([
        'message' => 'Post created successfully.',
        'post' => $post,
    ]);
  }

  public function editPostAction(Request $request, $id) {
    // Get the authenticated user
    $user = Auth::user();

    // Find the post by ID
    $post = Post::findOrFail($id);

    // Check if the user created the post
    if ($post->user_id !== $user->id) {
        return response()->json([
            'message' => 'You are not authorized to edit this post.',
        ], 403);
    }

    // Validate the request data
    $validatedData = $request->validate([
        'title' => 'sometimes|string|max:255|unique:posts,title,'.$id,
        'body' => 'sometimes|string'
    ]);

    // Check if any fields were included in the request
    if (!$request->filled('title') && !$request->filled('body')) {
      return response()->json([
          'message' => 'No fields were included in the request.',
      ], 400);
    }

    // Update the post data
    $post->title = $validatedData['title'] ?? $post->title;
    $post->body = $validatedData['body'] ?? $post->body;

    // Update the updated_at column
    $post->updated_at = now();

    // Save the post changes to the database
    $post->save();

    // Return a success response
    return response()->json([
        'message' => 'Post updated successfully.',
        'post' => $post,
    ]);
  }

  public function updatePostPictureAction(Request $request, $postId) {
    // Get the authenticated user
    $user = Auth::user();

    // Find the post by id
    $post = Post::findOrFail($postId);

    // Check if the user is the owner of the post
    if ($user->id !== $post->user_id) {
        return response()->json([
            'message' => 'You are not authorized to update this post.',
        ], 401);
    }

    // Validate the request data
    $validator = Validator::make($request->all(), [
        'picture' => 'required|image|max:2048|mimes:jpg,png,svg,jpeg',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Invalid picture file.',
            'errors' => $validator->errors(),
        ], 400);
    }

    // Upload the picture
    $picture = $request->file('picture');
    $path = $picture->store('public/posts');
    $url = Storage::url($path);

    // Update the post's picture
    $post->picture = $url;
    $post->updated_at = Carbon::now();
    $post->save();

    return response()->json([
        'message' => 'Post picture updated successfully.',
        'post' => $post,
    ]);
  }

  public function showAllPosts() {
    // Find all posts
    $posts = Post::all();

    if($posts) {
      return response()->json([
        'posts'   => $posts
      ]);
    }else {
      return response()->json([
        'message' => 'There are no posts yet to be shown.',
      ]);
    }
  }

  public function showOnePost($id) {
    // Find the post by ID
    $post = Post::findOrFail($id);

    // Find the post author
    $author = User::select('firstName', 'lastName', 'avatar')->where('id', $post->user_id)->get();

    if($post) {
      $data = [
        'title' => $post->title,
        'body' => $post->title,
        'picture' => $post->picture,
        'updated_at' => $post->updated_at,
        'author' => $author
      ];

      return response()->json([
        'data' => $data
      ]);
    }else {
      return response()->json(['error' => 'Post not found.'], 404);
    }
  }

  public function delete($id) {
     // Get the authenticated user
     $user = Auth::user();

     // Find the post by ID
     $post = Post::findOrFail($id);
 
    //Check if the post exist
    if($post) {
     // Check if the user created the post
     if ($post->user_id !== $user->id) {
        return response()->json([
            'message' => 'You are not authorized to delete this post.',
        ], 403);
      }else {
        $post->delete();
        // Return a success response
        return response()->json([
          'message' => 'Post deleted successfully.'
        ]);
      }
    }else {
      return response()->json(['message' => 'Post not found.']);
    }
  }
  
}
