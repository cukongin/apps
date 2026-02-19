<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    use HasFactory;

    protected $table = 'semester';
    protected $guarded = ['id'];

    public $timestamps = false; // Usually semester table doesn't have timestamps, check migration if needed
}
