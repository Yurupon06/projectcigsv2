<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class complement extends Model
{
    use HasFactory;
    protected $table = 'complements';
    protected $fillable = [
        'name',
        'category',
        'description',	
        'price',
        'stok',	
        'image',	
    ];
}