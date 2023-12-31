<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Arr;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use App\Http\Requests\ImportRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $name = request('name');
        $email = request('email');
        $from = request('from');
        $to = request('to');
        $user = request('user') != '' ? User::find(request('user')) : '';

        $users = User::query()
            ->when($name, function ($query) use ($name) {
                $query->where('name', 'like', '%' . $name . '%');
            })
            ->when($email, function ($query) use ($email) {
                $query->where('email', 'like', '%' . $email . '%');
            })
            ->when($from, function ($query) use ($from) {
                $query->whereDate('created_at', '>=', $from);
            })
            ->when($to, function ($query) use ($to) {
                $query->whereDate('created_at', '<=', $to);
            })
            ->orderBy('id', 'desc');
        
        $users = ($user instanceof User && $user->type == '0') ? $users->paginate(10) : $users->where('created_user_id', $user?->id)->paginate(10);

        return response()->json([
            'users' => UserResource::collection($users),
            'all_users' => UserResource::collection(User::all()),
            'pagination' => [
                'total' => $users->total(),
                'per_page' => $users->perPage(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'from' => $users->firstItem(),
                'to' => $users->lastItem(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  UserCreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserCreateRequest $request)
    {
        if(filter_var($request->flag, FILTER_VALIDATE_BOOLEAN)) {
            $user = User::create($request->all());

            $path = storage_path('app/public/img/');
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }

            $file = $request->file('profile');
            $fileName = uniqid() . '-' . $file->getClientOriginalName();
            $file->storeAs('public/img', $fileName);
            $user->update(['profile' => $fileName]);

            return response()->json(['success' => 'User create successfully!', 'user' => new UserResource($user)]);
        }
        
        return response()->json(['success' => 'Success']);
    }

    /**
     * Display the specified resource.
     *
     * @param  User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return response()->json(['user' => new UserResource($user)]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UserUpdateRequest  $request
     * @param  User $user
     * @return \Illuminate\Http\Response
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        if ($request->hasFile('profile')) {
            $user->profile != 'img/default.jpg' ? Storage::delete('public/img/' . $user->profile) : '';
            $file = $request->file('profile');
            $fileName = uniqid() . '-' . $file->getClientOriginalName();
            $file->storeAs('public/img', $fileName);
            $user->update(['profile' => $fileName]);
        }
        $user->update(Arr::except($request->except('profile'), ['profile']));
        // $user->update(['updated_user_id' => Auth()->user()->id()]);

        return response()->json(['success' => 'User update successfully!', 'user' => new UserResource($user)]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->update(['deleted_user_id' => Auth::user()->id]);
        $user->delete();

        return response()->json(['success' => 'User delete successfully!']);
    }

    /**
     * To download user information
     * 
     * @return \Illuminate\Http\Response
     */
    public function export()
    {
        return Excel::download(new UsersExport(), 'users'.uniqid(time()).'.csv');
    }

    /**
     * To import user information
     * 
     * @param ImportRequest $request request with inputs 
     * @return \Illuminate\Http\Response
     */
    public function import(ImportRequest $request) 
    {
        Excel::import(new UsersImport, $request->file);
        
        return response()->json(['success' => 'Users Import Successfully!']);
    }

    /**
     * To get specific user image
     *
     * @param File $filename
     * @return \Illuminate\Http\Response
     */
    public function image($filename)
    {
        $path = storage_path('app/public/img/' . $filename);

        if (!file_exists($path)) {
            return response()->json(['message' => 'Image not found.'], 404);
        }

        return response()->file($path);
    }
}
