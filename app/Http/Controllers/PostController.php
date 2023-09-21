<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Exports\PostsExport;
use App\Imports\PostsImport;
use App\Http\Requests\ImportRequest;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\PostCreateRequest;
use App\Http\Requests\PostUpdateRequest;

class PostController extends Controller
{
    public function index()
    {
        $user = request('user') != '' ? User::find(request('user')) : '';

        if($user instanceof User) {
            $posts = Post::when(request('search'), function($query){
                $search = '%' . request('search') . '%';
                $query->where('title','like', $search)
                      ->orWhere('description', 'like', $search);
                });

            if ($user->type == '0') {
                $posts = $posts->orderBy('id', 'desc')->paginate(10);
            } else {
                $posts = $posts->where('created_user_id', $user->id)
                    ->orderBy('id', 'desc')->paginate(10);
            }
        }else {
            $posts = Post::where('status', 1)->when(request('search'), function($query){
                $search = '%' . request('search') . '%';
                $query->where('title','like', $search)
                      ->orWhere('description', 'like', $search);
                })->orderBy('id','desc')->paginate(10);
        }

        return response()->json([
            'posts' => PostResource::collection($posts),
            'pagination' => [
                'total' => $posts->total(),
                'per_page' => $posts->perPage(),
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'from' => $posts->firstItem(),
                'to' => $posts->lastItem(),
            ],
        ]);
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
        $post->update(['deleted_user_id' => Auth::user()->id]);
        $post->delete();

        return response()->json(['success' => 'Post delete successfully!']);
    }

    public function export()
    {
        return Excel::download(new PostsExport, 'posts'.uniqid(time()).'.csv');
    }

    public function import(ImportRequest $request)
    {
        $csv = array_map('str_getcsv', file($request->file));

        if (count($csv[0]) != 3) {
            return response()->json(['error' => 'CSV file must have exactly 3 columns.'],422);
        }

        Excel::import(new PostsImport, $request->file);
        
        return response()->json(['success' => 'Posts Import Successfully!']);
    }
}
