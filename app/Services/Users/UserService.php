<?php

namespace App\Services\Users;

use App\Models\User;
use App\Traits\ApiResponse;
use App\Traits\Crud;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use Illuminate\Pagination\LengthAwarePaginator;


class UserService
{
    use ApiResponse, Crud;

    /**
     * @var User
     */
    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->defineModel($user);
    }

    /**
     * Function to get all data in table
     *
     * @return  Collection
     */
    public function getAll(): Collection
    {
        return $this->getAllRecords();
    }

    /**
     * Function to get all data paginated in table
     *
     * @return  LengthAwarePaginator
     */
    public function getAllPaginate(): LengthAwarePaginator
    {
        return $this->getRecordsPaginate();
    }

    /**
     * Function to get especified data by id
     *
     * @param int $id
     *
     * @return  User
     */
    public function getById(int $id): ?User
    {
        return $this->getRecordById($id);
    }

    /**
     * Function to create a new user
     *
     * @param @param  App\Http\Requests\Users\StoreUserRequest  $request
     *
     * @return  User
     */
    public function createUser($request): ?User
    {
        // Get data request
        //-----------------------------------------------------------------
            $data = $request->safe()->all();
            $data = [
                'email' => $data["email"],
                'password' => Hash::make($data["password"]),
                'first_name' => $data["first_name"]
            ];
        //-----------------------------------------------------------------

        //Save Record
        //-----------------------------------------------------------------
            $record = $this->createRecord($data);
        //-----------------------------------------------------------------

        //Assign Role
        //-----------------------------------------------------------------
            self::assignRole($request, $record->id);
        //-----------------------------------------------------------------

        return $record;
    }

    /**
     * Function to update a especific user
     *
     * @param  App\Http\Requests\Users\UpdateUserRequest  $request
     * @param int $id
     *
     * @return  User
     */
    public function updateUser($request, int $id): ?User
    {
        // Get data request
        //-----------------------------------------------------------------
            $data = $request->safe()->all();
        //-----------------------------------------------------------------
        
            DB::beginTransaction();
            try{
                
                //Update Record
                //---------------------------------------------------
                    
                    $record = self::getById($id);
                    if (isset($data["password"])) {
                        $data["password"] = Hash::make($data["password"]);
                    }
                    $record->update($data);
                    unset($data['password']);
                    if (isset($data["roles"])) {
                        self::assignRole($request, $record->id);
                    }
                    
                    DB::commit();
                //---------------------------------------------------

            }catch(\Exception $exception){

                DB::rollback();
                throw new \Exception($exception->getMessage());
            }
        return $record;
    }

    /**
     * Function to delete a especific user
     *
     * @param array $id
     *
     * @return  bool
     */
    public function deleteUser(array $id): bool
    {

        //Delete Record
        //-------------------------------------------------------
            $record = $this->deleteRecord($id);
        //-------------------------------------------------------

        return true;
    }

    /**
     * Function to assign one or more roles to user by id
     *
     * @param  \Illuminate\Http\Request  $request
     * @param int $id
     * @return  User
     */
    public function assignRole($request, int $id): ?User
    {
        //Get request data and find user by id
        //---------------------------------------
            $data = $request->all();
            $user = self::getById($id);
        //---------------------------------------

        //Assing one or more roles to user
        //---------------------------------------
            $user->syncRoles($data["roles"]);
        //---------------------------------------

        return $user;
    }

    /**
     * Function to assign permission to user by id
     *
     * @param  \Illuminate\Http\Request  $request
     * @param int $id
     * @return  User
     */
    public function assignPermission($request, int $id): ?User
    {
        //Get request data and find user by id
        //---------------------------------------
            $data = $request->all();
            $user = self::getById($id);
        //---------------------------------------

        //Assing permission to user
        //---------------------------------------
            $user->givePermissionTo($data["permission"]);
        //---------------------------------------

        return $user;
    }

    /**
     * Function to revoke permission to user by id
     *
     * @param  \Illuminate\Http\Request  $request
     * @param int $id
     * @return  User
     */
    public function revokePermission($request, int $id): ?User
    {
        //Get request data and find user by id
        //---------------------------------------
            $data = $request->all();
            $user = self::getById($id);
        //---------------------------------------

        //Assing permission to user
        //---------------------------------------
            $user->revokePermissionTo($data["permission"]);
        //---------------------------------------

        return $user;
    }

    /**
     * Function to update a user password
     *
     * @param  Illuminate\Http\Request  $request
     * @param int $id
     *
     * @return  User
     */
    public function updatePassword($request, int $id): ?User
    {
        // Get data request
        //-----------------------------------------------------------------
            $data = $request->all();
        //-----------------------------------------------------------------

            DB::beginTransaction();
            try{

                //Update Record
                //---------------------------------------------------
                    $record = self::getById($id);
                    $record->update([
                        'password' => Hash::make($data["password"]),
                    ]);
                    DB::commit();
                //---------------------------------------------------

            }catch(\Exception $exception){

                DB::rollback();
                throw new \Exception($exception->getMessage());
            }
        return $record;
    }
}
