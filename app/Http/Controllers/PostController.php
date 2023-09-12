<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Http\Resources\PostResource;
use App\Http\Requests\PostCreateRequest;
use App\Http\Requests\PostUpdateRequest;

class PostController extends Controller
{
    public function index()
    {
        return response()->json(['posts' => PostResource::collection(Post::all())]);
    }

    public function store(PostCreateRequest $request)
    {
        $post = Post::create($request->all());

        return response()->json(['success' => 'Post create successfully', 'post' => new PostResource($post)]);
    }

    public function show(Post $post)
    {
        return response()->json(['post' => new PostResource($post)]);
    }

    public function update(PostUpdateRequest $request, Post $post)
    {
        $post->update($request->all());

        return response()->json(['success' => 'Post update successfully', 'post' => new PostResource($post)]);
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return response()->json(['success' => 'Post delete successfully!']);
    }
}
