<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Photo extends Model
{
    use HasFactory;
    public $transformer = PhotoTransformer::class;

    protected $table = "photos";
    protected $fillable = [
        'google_drive_id'
    ];

    public function photo()
    {
        return $this->HasMany(Photo::class,'photo_id');
    }

    public function userPhotos()
    {
        return $this->hasMany(UserPhoto::class, 'photo_id');
    }
}
