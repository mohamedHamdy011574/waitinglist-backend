<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class CustomAsignRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$role = Role::where('name','Admin')->first();
        $user = User::find(2);
		$user->assignRole([$role->id]);
    }
}
