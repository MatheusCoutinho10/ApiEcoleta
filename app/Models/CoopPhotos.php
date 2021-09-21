<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoopPhotos extends Model
{
    use HasFactory;

    protected $table = 'coopphotos';
    public $timestamps = false;
}
