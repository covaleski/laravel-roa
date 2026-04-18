<?php

namespace App\Models;

use App\Attributes\Ruleset;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable('isbn', 'title', 'author')]
#[Ruleset('isbn', 'required|unique:books')]
#[Ruleset('title', 'required')]
#[Ruleset('author', 'required')]
class Book extends Model
{
    //
}
