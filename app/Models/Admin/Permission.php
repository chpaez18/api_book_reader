<?php

namespace App\Models\Admin;

use Spatie\Permission\Models\Permission as Permissions;

use App\Builders\PermissionBuilder;

use Illuminate\Database\Eloquent\Model;

use App\Transformers\PermissionTransformer;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permission extends Permissions
{
    use HasFactory;

    const GROUPS = [
        'Administration' => ['roles', 'users'],
        'Customers' => ['customers'],
        'Order' => ['orders'],
        'Medication' => ['medications'],
        'Parts' => ['part-numbers','adapters','med-plates', 'paddles'],
        'New Registers' => ['part-groups', 'part-titles', 'packing-type-manufactures', 'distributors', 'machines', 'manufacturers', 'shapes']
    ];

    protected $table = "permissions";
    protected $fillable = ['name','guard_name'];
    public $transformer = PermissionTransformer::class;

    public function newEloquentBuilder($query): PermissionBuilder
    {
        return new PermissionBuilder($query);
    }

}
