<?php

namespace App\Models\Admin;

use Spatie\Permission\Models\Role;

use App\Builders\RolBuilder;

use Illuminate\Database\Eloquent\Model;

use App\Transformers\RolTransformer;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rol extends Role
{
    use HasFactory;

    protected $table = "roles";
    protected $fillable = ['name','guard_name'];
    public $transformer = RolTransformer::class;

    public function newEloquentBuilder($query): RolBuilder
    {
        return new RolBuilder($query);
    }
}
