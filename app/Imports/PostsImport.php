<?php

namespace App\Imports;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PostsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // $createdUser = getUserByName($row['created_user_name']);
        // $updatedUser = getUserByName($row['updated_user_name']);
        // $status = $row['status'] == 'Inactive' ? 0 : 1;

        if(isset($row['action']) && $row['action'] == 'delete'){
            $post = Post::where('id', $row['id']);
            $post->update(['deleted_user_id' => $row['deleted_user_id']]);
            $post->delete();
        }else {
            $data = [
                'title' => $row['title'],
                'description' => $row['description'],
                'status' => $row['status'],
                'created_user_id' => Auth::user()->id,
                'updated_user_id' => Auth::user()->id,
            ];
            
            Post::updateOrCreate(['id' => isset($row['id'])], $data);
        }
    }
}
