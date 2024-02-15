<?php

namespace App\Transformers;

use App\Models\User;

use App\Transformers\RolTransformer;

use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected array $defaultIncludes = [
        //'photos'
    ];

    /* public function includePhotos(User $user)
    {
        $photos = $user->photos;
        
        if ($photos)
            return $this->collection($photos, new RolTransformer());
    } */

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(User $user): array
    {
        return [
            'id'         => $user->id, 
            'name' => $user->name,
            'email'      => $user->email,
            'rol'      => $user->getRoleNames()[0],
            'code_validated'      => ($user->code != null ? $user->code->is_validated : 0),
            'status'     => $user->status,
        ];
    }
}