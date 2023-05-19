<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogError extends Model
{
    protected $table = "log_errors";
    
    protected $fillable = [
        'env',
        'user_id',
        'error',
        'ip',
        'browser',
        'trace'
    ];


}
