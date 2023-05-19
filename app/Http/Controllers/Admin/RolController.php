<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Admin\Rol;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Services\Roles\RolService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ApiController;

use Spatie\Permission\Models\Permission;

use App\Http\Requests\Roles\StoreRolRequest;
use App\Http\Requests\Roles\UpdateRolRequest;
use Illuminate\Validation\UnauthorizedException;
use App\Http\Requests\Roles\AssignPermissionsRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class RolController extends ApiController
{
    /**
     * @var App\Services\Roles\RolService
     */
    public $rolService;

    public function __construct(RolService $rolService)
    {
        $this->rolService = $rolService;
        $this->middleware(['role:Admin']);
    }

    /**
     * Display a listing of the resource
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Rol::class);

        try {
            
            return $this->showPaginate($this->rolService->getAllPaginate(), 200);

        } catch (Exception $exception) {
            
            throw $exception;

        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\Roles\StoreRolRequest  $request
     * @return Illuminate\Http\JsonResponse
     */
    public function store(StoreRolRequest $request): JsonResponse
    {
        $this->authorize('create', Rol::class);

        try{
            
            $record = $this->rolService->createRol($request);

        } catch (Exception $exception) {
            
            throw $exception;

        }
        
        return $this->showOne($record, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Illuminate\Http\JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $this->authorize('view', Rol::class);
        
        try {
            
            $record = $this->rolService->getById($id);

        } catch (Exception $exception) {
            
            throw new $exception;
            
        }

        return $this->showOne($record, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\Roles\UpdateRolRequest  $request
     * @param  int  $id
     * @return Illuminate\Http\JsonResponse
     */
    public function update(UpdateRolRequest $request, int $id): JsonResponse
    {
        $this->authorize('update', Rol::class);

        try{
         
            $record = $this->rolService->updateRol($request, $id);

        } catch (Exception $exception) {
            
            throw $exception;

        }
    
        return $this->showOne($record, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Illuminate\Http\JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $this->authorize('delete', Rol::class);

        try{
         
            $this->rolService->deleteRol($id);

        } catch (Exception $exception) {
            
            throw $exception;

        }
    
        return $this->showMessage("Rol deleted", 200);
    }


    /**
     * Assign permissions to role.
     *
     * @param  App\Http\Requests\Roles\AssignPermissionsRequest  $request
     * @param  int $id
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function assignPermissions(User $user, AssignPermissionsRequest $request, $id): JsonResponse
    {
        $this->authorize('assignPermissions', Rol::class);

        try{
         
            $record = $this->rolService->assignPermissions($request, $id);

        } catch (Exception $exception) {
            
            throw $exception;

        }
    
        return $this->showMessage("Permission(s) assigned", 200);
    }
}
