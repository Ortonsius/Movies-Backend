<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fav_movies extends Model
{
    use HasFactory;
    protected $table = "fav_movies";
    protected $guarded = [];
}
