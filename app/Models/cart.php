<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cart extends Model
{
    use HasFactory;
    protected $table = 'carts';
    protected $fillable = [
        'user_id',
        'complement_id',
        'quantity',
        'total',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function complement(){
        return $this->belongsTo(Complement::class, 'complement_id');
    }
}
