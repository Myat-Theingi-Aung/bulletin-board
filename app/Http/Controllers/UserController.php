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
    public function index()
    {
        $name = request('name');
        $email = request('email');
        $from = request('from');
        $to = request('to');

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
            ->orderBy('id', 'desc')
            ->paginate(10);

        return response()->json([
            'users' => UserResource::collection($users),
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

    public function show(User $user)
    {
        return response()->json(['user' => new UserResource($user)]);
    }

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

    public function destroy(User $user)
    {
        $user->update(['deleted_user_id' => Auth::user()->id]);
        $user->delete();

        return response()->json(['success' => 'User delete successfully!']);
    }

    public function count()
    {
        return response()->json(['count' => User::withTrashed()->count() + 1]);
    }

    public function export()
    {
        return Excel::download(new UsersExport(), 'users'.uniqid(time()).'.csv');
    }

    public function import(ImportRequest $request) 
    {
        Excel::import(new UsersImport, $request->file);
        
        return response()->json(['success' => 'Users Import Successfully!']);
    }

    public function image($filename)
    {
        $path = storage_path('app/public/img/' . $filename);

        if (!file_exists($path)) {
            return response()->json(['message' => 'Image not found.'], 404);
        }

        return response()->file($path);
    }
}
