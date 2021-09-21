<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoopAvailability extends Model
{
    use HasFactory;

    protected $table = 'coopavailability';
    public $timestamps = false;
}
