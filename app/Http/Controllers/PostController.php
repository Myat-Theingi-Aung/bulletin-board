<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Exports\PostsExport;
use App\Imports\PostsImport;
use App\Http\Requests\ImportRequest;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\QueryException;
use App\Http\Requests\PostCreateRequest;
use App\Http\Requests\PostUpdateRequest;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  PostCreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostCreateRequest $request)
    {
        if (!$request->flag) { return response()->json(['success' => 'Success']); }

        $post = Post::create($request->all());

        return response()->json(['success' => 'Post create successfully', 'post' => new PostResource($post)]);
    }

    /**
     * Display the specified resource.
     *
     * @param  Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        Gate::authorize('check-user', $post);

        return response()->json(['post' => new PostResource($post)]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  PostUpdateRequest  $request
     * @param  Post $post
     * @return \Illuminate\Http\Response
     */
    public function update(PostUpdateRequest $request, Post $post)
    {
        Gate::authorize('check-user', $post);
        
        if (!$request->flag) { return response()->json(['success' => 'Success']); }

        $post->update($request->all());

        return response()->json(['success' => 'Post update successfully', 'post' => new PostResource($post)]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Post $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $post->update(['deleted_user_id' => Auth::user()->id]);
        $post->delete();

        return response()->json(['success' => 'Post delete successfully!']);
    }

    /**
     * To download post information
     * 
     * @return \Illuminate\Http\Response
     */
    public function export()
    {
        return Excel::download(new PostsExport, 'posts'.uniqid(time()).'.csv');
    }

    /**
     * To import post information
     * 
     * @param ImportRequest $request request with inputs 
     * @return \Illuminate\Http\Response
     */
    public function import(ImportRequest $request)
    {
        try {
            $csv = array_map('str_getcsv', file($request->file));

            if (count($csv[0]) != 3) {
                return response()->json(['error' => 'CSV file must have exactly 3 columns.'],422);
            }
            Excel::import(new PostsImport, $request->file);

            return response()->json(['success' => 'Posts Import Successfully!']);

        }catch(QueryException $e) 
        {
            if ($e->errorInfo[1] === 1062) { return response()->json(['error' => 'Post title must be unique'], 400);}
        } 
    }
}
