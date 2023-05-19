<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\UserPhoto;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\Users\UserService;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Http\Requests\Users\UpdatePasswordRequest;



class UserController extends ApiController
{

    /**
     * @var App\Services\Users\UserService
     */
    public $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', User::class);

        try {

            return $this->showPaginate($this->userService->getAllPaginate(), 200);

        } catch (Exception $exception) {
            
            throw $exception;

        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\Users\StoreUserRequest  $request
     * @return Illuminate\Http\JsonResponse
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $this->authorize('create', User::class);

        try{
            
            $record = $this->userService->createUser($request);

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
        $this->authorize('view', User::class);
        try {
            $user = $this->userService->getById($id);
            
        } catch (Exception $exception) {
            
            throw $exception;

        }

        return $this->showOne($user, 200);
    }

    /**
     * Display user info.
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function getInfo(): JsonResponse
    {

        try {
            $user = auth('api')->user();

        } catch (Exception $exception) {

            throw $exception;

        }

        return $this->showOne($user, 200);
    }

    /**
     * Display user photos.
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function getPhotos(): JsonResponse
    {

        try {
            $userPhotos = UserPhoto::select('id', 'user_id', 'photo_id')
            ->where('user_id', auth('api')->user()->id)
            ->with('photo:id,google_drive_id')
            ->get();

        } catch (Exception $exception) {

            throw $exception;

        }

        return $this->successResponse($userPhotos->toArray(), 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\Users\UpdateUserRequest  $request
     * @param  int  $id
     * @return Illuminate\Http\JsonResponse
     */
    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        $this->authorize('update', User::class);

        try{
           
            $record = $this->userService->updateUser($request, $id);

        } catch (Exception $exception) {
            
            throw $exception;

        }
    
        return $this->showOne($record, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request): JsonResponse
    {
        $this->authorize('delete', User::class);

        try{
            $id = $request->all()['id'];
            $this->userService->deleteUser($id);

        } catch (Exception $exception) {
            
            throw $exception;

        }
    
        return $this->showMessage("User deleted", 200);
    }

    /**
     * Assign role to user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return Illuminate\Http\JsonResponse
     */
    public function assignRole(Request $request, int $id): JsonResponse
    {
        $this->authorize('assignRole', User::class);

        try {
            
            $this->userService->assignRole($request, $id);

        } catch (Exception $exception) {
            
            throw $exception;

        }

        return $this->showMessage("Rol(es) assigned", 200);
    }

    /**
     * Assign permission to user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return Illuminate\Http\JsonResponse
     */
    public function assignPermission(Request $request, int $id): JsonResponse
    {
        $this->authorize('assignPermission', User::class);

        try {
            
            $this->userService->assignPermission($request, $id);

        } catch (Exception $exception) {
            
            throw $exception;

        }

        return $this->showMessage("Permission assigned", 200);
    }

    /**
     * Revoke permission to user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return Illuminate\Http\JsonResponse
     */
    public function revokePermission(Request $request, int $id): JsonResponse
    {
        $this->authorize('revokePermission', User::class);

        try {
            
            $this->userService->revokePermission($request, $id);

        } catch (Exception $exception) {
            
            throw $exception;

        }

        return $this->showMessage("Permission revoked", 200);
    }

    /**
     * Update the user password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return Illuminate\Http\JsonResponse
     */
    public function updatePassword(UpdatePasswordRequest $request, int $id): JsonResponse
    {
        $this->authorize('updatePassword', User::class);

        try{
         
            $record = $this->userService->updatePassword($request, $id);

        } catch (Exception $exception) {
            
            throw $exception;

        }
    
        return $this->showOne($record, 200);
    }
}
