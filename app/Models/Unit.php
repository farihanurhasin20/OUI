<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;
    public $timestamps = true;
    protected $table = 'units';

    protected $fillable = [
        'name', 'nameMn','timestamps',
        // Add more fields here if needed
    ];
}
