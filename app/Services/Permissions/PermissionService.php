<?php

namespace App\Services\Permissions;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;

use App\Models\Admin\Permission;

use App\Http\Requests\Permissions\StorePermissionRequest;
use App\Http\Requests\Permissions\UpdatePermissionRequest;

use Illuminate\Database\Eloquent\ModelNotFoundException;

use Illuminate\Pagination\LengthAwarePaginator;


use Illuminate\Support\Collection as SupportCollection;

class PermissionService
{
    use ApiResponse;

    
    
    /**
     * @var Permission
     */
    public $permission;

    public function __construct(Permission $permission)
    {
        $this->permission = $permission;
    }

    /**
     * Function to get all data in table 
     * 
     * @return  Illuminate\Support\Collection;
     */
    public function getAll(): SupportCollection
    {
        
        
        
        $data = $this->permission->query();

        //Get request params
        //-------------------------------------------------------------------------------------------------------------------
            $sortBy = (request()->input('sort_by') == null ? 'id':request()->input('sort_by'));
            $sortDirection = (request()->input('sort_direction') == null ? 'asc':request()->input('sort_direction'));
            $search = request()->input('search');
        //-------------------------------------------------------------------------------------------------------------------

        //Apply defined params in data
        //-------------------------------------------------------------------------------------------------------------------
            $data->when((isset($sortBy))&&(isset($sortDirection)), fn ($query) => $query->reorder($sortBy,$sortDirection) );
            $data->when(isset($search), fn ($query) => $query->filterInTable($search) );
        //-------------------------------------------------------------------------------------------------------------------

        //Get splited data for group by permission name
        //-------------------------------------------------------------------------------------------------------------------
            /* $data->select('id', DB::raw("split_part(name, '.', 1) as permission_front_name"), 'name');
            $data->orderBy('id'); */
          

            $allPermission = collect($this->permission->all());

            $permissions = collect($this->permission->get()->pluck('name', 'id'));
            $permissionsNames = $permissions->map(function($name, $key){
                return explode('.', $name)[0];
            });

            $permissionsNames = array_unique($permissionsNames->toArray());
            $permissionsGrouped = $permissions->groupBy(function ($value, $key) {
                return explode('.', $value)[0];
            });
         
            foreach ($permissionsNames as $permission) {
                foreach ($this->permission::GROUPS as $name => $items) {
                    if (in_array($permission, $items)) {
                        $finalArray[$name][$permission] = $permissionsGrouped[$permission];
                    }
                }
            }

            $data = collect($finalArray);

        
        //-------------------------------------------------------------------------------------------------------------------
        
       return $data;
    }

    /**
     * Function to get all data paginated in table 
     * 
     * @return  LengthAwarePaginator
     */
    public function getAllPaginate(): LengthAwarePaginator
    {
        $data = $this->permission->query();

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
     * @return  Permission
     */
    public function getById(int $id): ?Permission
    {
        return $this->permission->where('id', $id)->firstOrFail();
    }

    /** 
     * Function to create a new rol
     * 
     * @param App\Http\Requests\Permissions\StorePermissionRequest  $request
     * 
     * @return  Permission
     */
    public function createPermission(StorePermissionRequest $request): ?Permission
    {
        // Get data request
        //-----------------------------------------------------------------
            $data = $request->safe()->all();
        //-----------------------------------------------------------------

            DB::beginTransaction();
            try {

                //Insert Record
                //---------------------------------------------------
                    $record = $this->permission->create($data);
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
     * @param  App\Http\Requests\Permissions\UpdatePermissionRequest  $request
     * @param int $id
     * 
     * @return  Permission
     */
    public function updatePermission(UpdatePermissionRequest $request, int $id): ?Permission
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
     * Function to delete a especific permission
     * 
     * @param int $permissionId
     * 
     * @return  bool
     */
    public function deletePermission(int $permissionId): bool
    {
        try {
            
            //Delete Record
            //-------------------------------------------------------
                $this->permission->destroy($permissionId);
            //-------------------------------------------------------
        } catch (\Exception $exception) {

            throw new \Exception($exception->getMessage());
        }

        return true;
    }

}
