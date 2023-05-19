<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Admin\Permission;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\ApiController;
use App\Services\Permissions\PermissionService;

use Illuminate\Validation\UnauthorizedException;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\Permissions\StorePermissionRequest;
use App\Http\Requests\Permissions\UpdatePermissionRequest;

class PermissionController extends ApiController
{
    /**
     * @var App\Services\Permissions\PermissionService
     */
    public $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
        $this->middleware(['role:Admin']);
    }

    /**
     * Display a listing of the resource
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Permission::class);
        try {
           
            return $this->successResponse($this->permissionService->getAll(), 200);

        } catch (Exception $exception) {
            
            throw $exception;

        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\Permissions\StorePermissionRequest  $request
     * @return Illuminate\Http\JsonResponse
     */
    public function store(StorePermissionRequest $request): JsonResponse
    {
        $this->authorize('create', Permission::class);

        try{
            
            $record = $this->permissionService->createPermission($request);

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
        $this->authorize('view', Permission::class);

        try {
            
            $record = $this->permissionService->getById($id);
            
        } catch (Exception $exception) {
            
            throw $exception;

        }

        return $this->showOne($record, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\Permissions\UpdatePermissionRequest  $request
     * @param  int  $id
     * @return Illuminate\Http\JsonResponse
     */
    public function update(UpdatePermissionRequest $request, int $id): JsonResponse
    {
        $this->authorize('update', Permission::class);

        try{
         
            $record = $this->permissionService->updatePermission($request, $id);

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
        $this->authorize('delete', Permission::class);

        try{
         
            $this->permissionService->deletePermission($id);

        } catch (Exception $exception) {
            
            throw $exception;

        }
    
        return $this->showMessage("Permission deleted", 200);
    }
}
