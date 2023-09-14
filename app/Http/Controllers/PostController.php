<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Exports\PostsExport;
use App\Imports\PostsImport;
use App\Http\Requests\ImportRequest;
use App\Http\Resources\PostResource;
use Maatwebsite\Excel\Facades\Excel;
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

    public function export()
    {
        return Excel::download(new PostsExport, 'posts'.uniqid(time()).'.csv');
    }

    public function import(ImportRequest $request)
    {
        Excel::import(new PostsImport, $request->file);
        
        return response()->json(['success' => 'Posts Import Successfully!']);
    }
}
