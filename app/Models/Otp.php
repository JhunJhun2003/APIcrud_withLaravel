<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    protected $fillable = [
        'email',
        'otp',
    ];
    protected $table = 'otps';
    protected $primaryKey = 'email';
    public $incrementing = false;
    public $timestamps = false;
}
