<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accounts extends Model
{
    protected $table = "accounts";
    protected $fillable = ["name","pwd","role"];
    use HasFactory;
}
