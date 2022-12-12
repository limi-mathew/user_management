<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([           
              'first_name' => 'Super', 
              'last_name' => 'Admin', 
              'phonenumber' => '989087654',
              'city' => 'Chennai',
              'country'=>'India',
        	  'email' => 'superadmin@gmail.com',
        	  'password' => bcrypt('123456')

        ],
        /*[       
            'first_name' => 'Limi', 
            'last_name' => 'Mathew', 
            'phonenumber' => '9890876541',
            'city' => 'Chennai',
            'country'=>'India',
        	'email' => 'limi@gmail.com',
        	'password' => bcrypt('123456')
        
        
    ]
        */
    );
  
        $role = Role::create(['name' => 'Admin']
          /*  ['name' => 'Admin'],
            ['name' => 'Super Admin']*/
            
        );
   
        $permissions = Permission::pluck('id','id')->all();
  
        $role->syncPermissions($permissions);
   
        $user->assignRole([$role->id]);
    }
}
