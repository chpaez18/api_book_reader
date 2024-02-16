<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Create initial role
        //--------------------------------------------------------
            $roleAdmin = Role::create([
                'name'=>'Admin',
                'guard_name'=>'api'
            ]);

            $roleBuyer = Role::create([
                'name'=>'Buyer',
                'guard_name'=>'api'
            ]);
        //--------------------------------------------------------

        //Create initial permissions
        //--------------------------------------------------------

            //Permissions
            //--------------------------------------------------------
                Permission::create(['name' => 'permissions.index', 'guard_name'=>'api'])->assignRole($roleAdmin);
                Permission::create(['name' => 'permissions.show', 'guard_name'=>'api'])->assignRole($roleAdmin);
                Permission::create(['name' => 'permissions.store', 'guard_name'=>'api'])->assignRole($roleAdmin);
                Permission::create(['name' => 'permissions.update', 'guard_name'=>'api'])->assignRole($roleAdmin);
                Permission::create(['name' => 'permissions.destroy', 'guard_name'=>'api'])->assignRole($roleAdmin);
            //--------------------------------------------------------

            //Roles
            //--------------------------------------------------------
                Permission::create(['name' => 'roles.index', 'guard_name'=>'api'])->assignRole($roleAdmin);
                Permission::create(['name' => 'roles.show', 'guard_name'=>'api'])->assignRole($roleAdmin);
                Permission::create(['name' => 'roles.store', 'guard_name'=>'api'])->assignRole($roleAdmin);
                Permission::create(['name' => 'roles.update', 'guard_name'=>'api'])->assignRole($roleAdmin);
                Permission::create(['name' => 'roles.destroy', 'guard_name'=>'api'])->assignRole($roleAdmin);
                Permission::create(['name' => 'roles.assign-permissions', 'guard_name'=>'api'])->assignRole($roleAdmin);
            //--------------------------------------------------------

            //Users
            //--------------------------------------------------------
                Permission::create(['name' => 'users.index', 'guard_name'=>'api'])->assignRole($roleAdmin);
                Permission::create(['name' => 'users.show', 'guard_name'=>'api'])->assignRole($roleAdmin);
                Permission::create(['name' => 'users.store', 'guard_name'=>'api'])->assignRole($roleAdmin);
                Permission::create(['name' => 'users.update', 'guard_name'=>'api'])->assignRole($roleAdmin);
                Permission::create(['name' => 'users.destroy', 'guard_name'=>'api'])->assignRole($roleAdmin);
                Permission::create(['name' => 'users.assign-role', 'guard_name'=>'api'])->assignRole($roleAdmin);
                Permission::create(['name' => 'users.assign-permissions', 'guard_name'=>'api'])->assignRole($roleAdmin);
                Permission::create(['name' => 'users.revoke-permissions', 'guard_name'=>'api'])->assignRole($roleAdmin);
                Permission::create(['name' => 'users.update-password', 'guard_name'=>'api'])->assignRole($roleAdmin);
            //--------------------------------------------------------

            //Code generator
            //--------------------------------------------------------
                Permission::create(['name' => 'code.index', 'guard_name'=>'api'])->assignRole($roleAdmin);
                Permission::create(['name' => 'code.generate', 'guard_name'=>'api'])->assignRole($roleAdmin);
                Permission::create(['name' => 'code.validate', 'guard_name'=>'api'])->assignRole($roleAdmin);
                Permission::create(['name' => 'code.change-status', 'guard_name'=>'api'])->assignRole($roleAdmin);
                Permission::create(['name' => 'code.delete', 'guard_name'=>'api'])->assignRole($roleAdmin);
            //--------------------------------------------------------


        //--------------------------------------------------------

    }
}
