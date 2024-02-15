<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Code extends Model
{
    use HasFactory;

    const Active = 1;
    const Inactive = 0;

    protected $table = "codes";
    protected $fillable = [
        'name',
        'email',
        'code',
        'status'
    ];

}
