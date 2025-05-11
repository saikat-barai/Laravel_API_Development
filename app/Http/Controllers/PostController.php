<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::all();

        if ($posts->isEmpty()) {
            return response()->json(['message' => 'No posts found'], 404);
        }

        return response()->json([
            'data' => PostResource::collection($posts),
            'message' => 'Posts retrieved successfully',
            'status' => 200
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Validation Error',
                'status' => 422
            ], 422);
        }

        $post = Post::create([
            'title' => $request->title,
            'body' => $request->body
        ]);
        return response()->json([
            'data' => new PostResource($post),
            'message' => 'Post created successfully',
            'status' => 201
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }
        return response()->json([
            'data' => $post,
            'message' => 'Post retrieved successfully',
            'status' => 200
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response()->json([
                'message' => 'Post not found',
                'status' => 404
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Validation Error',
                'status' => 422
            ], 422);
        }

        try {
            $post->update([
                'title' => $request->title,
                'body' => $request->body
            ]);
            return response()->json([
                'data' => $post,
                'message' => 'Post updated successfully',
                'status' => 200
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response()->json([
                'message' => 'Post not found',
                'status' => 404
            ], 404);
        }
        try {
            $post->delete();
            return response()->json([
                'message' => 'Post deleted successfully',
                'status' => 200
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
