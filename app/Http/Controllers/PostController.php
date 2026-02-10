<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Post::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         $validatedData = $request->validate([
            'title' => 'required|max:255',
            'body' => 'required',
        ]);

        $post = Post::create($validatedData);

        return [ 
            'post' => $post,
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return $post; 
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $validatedData = $request->validate([
            'title' => 'sometimes|required|max:255',
            'body' => 'sometimes|required',
        ]);

        $post->update($validatedData);

        return [
            'post' => $post,
        ];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $post->delete();

        return response()->json([
            'message' => 'Post deleted successfully',
        ]);
    }
}
