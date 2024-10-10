<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationSetting extends Model
{
    use HasFactory;
    protected $table = 'application_settings';
    protected $fillable = [
        'app_name',
        'app_logo',
        'app_address',
        'japati_token',
        'japati_gateway',
        'japati_url',
    ];
}
