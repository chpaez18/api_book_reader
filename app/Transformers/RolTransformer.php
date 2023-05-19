<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

use Spatie\Permission\Models\Role as Rol;

use App\Transformers\PermissionTransformer;

class RolTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected array $defaultIncludes = [
        'permissions'
    ];
    
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected array $availableIncludes = [
        //
    ];
    
    public function includePermissions(Rol $rol)
    {
        $permissions = $rol->permissions;
        
        if ($permissions)
            return $this->collection($permissions, new PermissionTransformer());
    }

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Rol $role): array
    {
        
        return [
            'id'         => $role->id, 
            'name'       => $role->name,
            'guard_name' => $role->guard_name
        ];
    }
}
