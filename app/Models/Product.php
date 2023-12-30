<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'barcode',
        'qty',
        'unit_id', // Foreign key for the units table
        'color_id', // Foreign key for the colors table
        'size',
        'type',
        'price',
        'image',
    ];
}
