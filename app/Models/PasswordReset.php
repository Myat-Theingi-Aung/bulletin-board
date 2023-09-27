<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PasswordReset extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'email';
    protected $keyType = 'string';

    protected $fillable = [
        'email',
        'token'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }
}
