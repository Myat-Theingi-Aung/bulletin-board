<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Arr;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(['users' => UserResource::collection(User::all())]);
    }

    public function store(UserCreateRequest $request)
    {
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

    public function show(User $user)
    {
        return response()->json(['user' => new UserResource($user)]);
    }

    public function update(UserUpdateRequest $request, User $user)
    {
        if ($request->hasFile('profile')) {
            Storage::delete('public/img/' . $user->profile);
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
        Storage::delete('public/img/' . $user->profile);
        // $user->deleted_user_id = Auth::user()->id();
        $user->delete();

        return response()->json(['success' => 'User delete successfully!']);
    }
}
