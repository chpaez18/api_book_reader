<?php

namespace App\Services\Roles;

use App\Models\User;
use App\Models\Admin\Rol;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Storage;

use App\Http\Requests\Roles\StoreRolRequest;
use Illuminate\Database\Eloquent\Collection;
use App\Http\Requests\Roles\UpdateRolRequest;

use Illuminate\Pagination\LengthAwarePaginator;

use App\Http\Requests\Roles\AssignPermissionsRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class RolService
{
    use ApiResponse;
    
    const ADMIN_PERMISSIONS = [
        'permissions.index',
        'permissions.show',
        'permissions.store',
        'permissions.update',
        'permissions.destroy',
        'roles.index',
        'roles.show',
        'roles.store',
        'roles.update',
        'roles.destroy',
        'roles.assign-permissions',
        'users.index',
        'users.show',
        'users.store',
        'users.update',
        'users.destroy',
        'users.assign-role',
        'users.assign-permissions',
        'users.revoke-permissions',
        'users.update-password'
    ];

    /**
     * @var Rol
     */
    public $rol;


    public function __construct(Rol $rol)
    {
        $this->rol = $rol;
    }

    /**
     * Function to get all data in table 
     * 
     * @return  Collection
     */
    public function getAll(): Collection
    {
        $data = $this->rol->query();
        
        //Get request params
        //-------------------------------------------------------------------------------------------------------------------
            $sortBy = (request()->input('sort_by') == null ? 'id' : request()->input('sort_by'));
            $sortDirection = (request()->input('sort_direction') == null ? 'asc':request()->input('sort_direction'));
            $search = request()->input('search');
            $perPage = (request()->input('per_page') == null ? 10:request()->input('per_page'));
        //-------------------------------------------------------------------------------------------------------------------

        //Apply defined params in data
        //-------------------------------------------------------------------------------------------------------------------
            $data->when((isset($sortBy))&&(isset($sortDirection)), fn ($query) => $query->reorder($sortBy,$sortDirection) );
            $data->when(isset($search), fn ($query) => $query->filterInTable($search) );
        //-------------------------------------------------------------------------------------------------------------------
            
        return $data->orderBy('id')->get();
    }

    /**
     * Function to get all data paginated in table 
     * 
     * @return  LengthAwarePaginator
     */
    public function getAllPaginate(): LengthAwarePaginator
    {
        $data = $this->rol->query();

        //Get request params
        //-------------------------------------------------------------------------------------------------------------------
            $sortBy = (request()->input('sort_by') == null ? 'id' : request()->input('sort_by'));
            $sortDirection = (request()->input('sort_direction') == null ? 'asc' : request()->input('sort_direction'));
            $search = request()->input('search');
            $perPage = (request()->input('per_page') == null ? 10 : request()->input('per_page'));
        //-------------------------------------------------------------------------------------------------------------------

        //Apply defined params in data
        //-------------------------------------------------------------------------------------------------------------------
            $data->when((isset($sortBy))&&(isset($sortDirection)), fn ($query) => $query->reorder($sortBy,$sortDirection) );
            $data->when(isset($search), fn ($query) => $query->filterInTable($search) );
        //-------------------------------------------------------------------------------------------------------------------
            
        return $data->paginate($perPage);
    }

    /** 
     * Function to get especified data by id
     * 
     * @param int $id
     * 
     * @return  Rol
     */
    public function getById(int $id): ?Rol
    {
        return $this->rol->where('id', $id)->firstOrFail();
    }

    /** 
     * Function to create a new rol
     * 
     * @param App\Http\Requests\Roles\StoreRolRequest  $request
     * 
     * @return  Rol
     */
    public function createRol(StoreRolRequest $request): ?Rol
    {
        // Get data request
        //-----------------------------------------------------------------
            $data = $request->safe()->all();
        //-----------------------------------------------------------------

            DB::beginTransaction();
            try {

                //Insert Record
                //---------------------------------------------------
                    $record = $this->rol->create($data);
                    DB::commit();
                //---------------------------------------------------

            } catch(\Exception $exception) {

                DB::rollback();
                throw new \Exception($exception->getMessage());
            }
        return $record;
    }

    /** 
     * Function to update a especific adapter
     * 
     * @param  App\Http\Requests\Roles\UpdateRolRequest  $request
     * @param int $id
     * 
     * @return  Rol
     */
    public function updateRol(UpdateRolRequest $request, int $id): ?Rol
    {
        // Get data request
        //-----------------------------------------------------------------
            $data = $request->safe()->all();
        //-----------------------------------------------------------------

            DB::beginTransaction();
            try {
                
                //Update Record
                //---------------------------------------------------
                    $record = self::getById($id);
                    $record->update($data);
                    DB::commit();
                //---------------------------------------------------

            } catch (ModelNotFoundException $exception) {

                DB::rollback();
                throw new ModelNotFoundException();

            } catch (\Exception $exception) {

                DB::rollback();
                throw new \Exception($exception->getMessage());
            }
        return $record;
    }

    /** 
     * Function to delete a especific rol
     * 
     * @param int $rolId
     * 
     * @return  bool
     */
    public function deleteRol(int $rolId): bool
    {
        try {
            
            //Delete Record
            //-------------------------------------------------------
                if ($rolId !== 1) {
                    $this->rol->destroy($rolId);
                } else {
                    throw new \Exception('The super administrator role cannot be deleted');
                }
            //-------------------------------------------------------
        } catch (\Exception $exception) {

            throw new \Exception($exception->getMessage());
        }

        return true;
    }

    /** 
     * Function to assign one or more permissions to role
     * 
     * @param App\Http\Requests\Roles\AssignPermissionsRequest  $request
     * @param int $rolId
     * 
     * @return  Rol
     */
    public function assignPermissions(AssignPermissionsRequest $request, int $rolId): ?Rol
    {
        // Get data request
        //-----------------------------------------------------------------
            $data = $request->safe()->all();
        //-----------------------------------------------------------------
          
        try {
            //Sync permissions to rol
            //---------------------------------------------------
                $user = auth()->user();
                $rol = self::getById($rolId);
                
                if ($user->hasRole('Admin')) {
                    $data["permissions"] = array_merge(self::ADMIN_PERMISSIONS, $data["permissions"]);
                }
                $rol->syncPermissions($data["permissions"]);
            //---------------------------------------------------
        } catch (\Exception $exception) {

            throw new \Exception($exception->getMessage());
        }

        return $rol;
    }

}
