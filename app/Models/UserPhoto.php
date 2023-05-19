<?php

namespace App\Models;

use App\Models\User;
use App\Models\Photo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserPhoto extends Model
{
    use HasFactory;

    protected $table = "user_photos";
    protected $fillable = [
        'user_id',
        'photo_id'
    ];

    public function photo()
    {
        return $this->belongsTo(Photo::class, 'photo_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
