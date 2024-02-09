<?php

namespace App\Models;

use App\Models\Photo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserQuote extends Model
{
    use HasFactory;

    public $fillable = [
        'user_id',
        'quote_id',
        'is_completed',
        'mood',
        'quote_description',
        'words',
        'date',
        'photo_id'
    ];

    public function photo()
    {
        return $this->hasOne(Photo::class, 'id','photo_id');
    }
}
