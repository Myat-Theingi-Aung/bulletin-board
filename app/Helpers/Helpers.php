<?php

use App\Models\User;

function getUserByName($username)
{
    return User::where('name', $username)->first();
}

