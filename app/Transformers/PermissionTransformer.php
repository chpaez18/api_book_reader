<?php

namespace App\Transformers;

use Spatie\Permission\Models\Permission;

use League\Fractal\TransformerAbstract;

class PermissionTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected array $defaultIncludes = [
        //
    ];
    
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected array $availableIncludes = [
        //
    ];
    
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Permission $permission): array
    {
        
        return [
            'id'         => $permission->id, 
            'name'       => $permission->name,
            'guard_name' => $permission->guard_name
        ];
    }
}
