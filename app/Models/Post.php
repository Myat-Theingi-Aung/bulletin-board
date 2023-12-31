<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'status',
        'created_user_id',
        'updated_user_id',
        'deleted_user_id'
    ];

    public function scopeActive($query)
    {
        return $query->orderBy('status', '1');
    }

    public function scopeInActive($query)
    {
        return $query->orderBy('status', '0');
    }

    public function createdUser()
    {
        return $this->belongsTo(User::class, 'created_user_id');
    }

    public function updatedUser()
    {
        return $this->belongsTo(User::class, 'updated_user_id');
    }

    public function deletedUser()
    {
        return $this->belongsTo(User::class, 'deleted_user_id');
    }
}
