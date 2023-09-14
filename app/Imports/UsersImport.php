<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // $createdUser = User::where('name', $row['createdusername'])->first();
        // $updatedUser = User::where('name', $row['updatedusername'])->first();
        $createdUser = getUserByName($row['createdusername']);
        $updatedUser = getUserByName($row['updatedusername']);
        $type = $row['type'] == 'Admin' ? 0 : 1;

        if($row['action'] == 'delete'){
            $user = User::where('id', $row['id']);
            $user->update(['deleted_user_id' => $row['deleteduserid']]);
            $user->delete();
        }else {
            $data = [
                'name' => $row['name'],
                'email' => $row['email'],
                'profile' => $row['profile'],
                'type' => $type,
                'phone' => $row['phone'],
                'address' => $row['address'],
                'dob' => null,
                'created_user_id' => $createdUser->id,
                'updated_user_id' => $updatedUser->id,
            ];
            
            if (($row['action'] == 'create' ||  $row['password'] != null)) {
                $data['password'] = bcrypt($row['password']);
            }
            
            User::updateOrCreate(['id' => $row['id']], $data);
        }
    }
}
