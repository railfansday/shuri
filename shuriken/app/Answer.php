<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $guarded = [];
    protected $casts = [
        "answers" => "array",
    ];

    use HasFactory;
}
