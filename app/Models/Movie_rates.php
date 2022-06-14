<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie_rates extends Model
{
    use HasFactory;
    protected $table = "movie_rates";
    protected $guarded = [];
}
