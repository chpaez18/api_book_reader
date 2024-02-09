<?php


namespace App\Models;

use App\Models\Admin\Rol;

use App\Models\{UserPhoto, UserQuote};
use App\Builders\UserBuilder;
use Laravel\Passport\HasApiTokens;
use App\Transformers\UserTransformer;


use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable
{
    use Notifiable, HasApiTokens, HasRoles, HasFactory, CanResetPassword;

    const STATUS = [
        'Active' => 1,
        'Inactive' => 2,
        'Deleted' => 0
    ];


    public $table = "users";

    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'access_token',
        'reset_password_token',
        'reset_password_token_created_at',
        'status'
    ];
    protected $hidden = [
        'password', 'created_at', 'updated_at'
    ];

    public $transformer = UserTransformer::class;

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('active', function ($query) {
            return $query->where('status', self::STATUS["Active"])->orWhere('status', self::STATUS["Inactive"]);
        });
    }

    public function newEloquentBuilder($query): UserBuilder
    {
        return new UserBuilder($query);
    }

    public function photos()
    {
        return $this->HasMany(UserPhoto::class,'user_id');
    } 

    public function quotes()
    {
        return $this->HasMany(UserQuote::class,'user_id');
    }


}
