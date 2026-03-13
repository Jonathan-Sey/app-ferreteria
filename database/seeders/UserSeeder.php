<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $user=User::create([
        //     'nombre1' => 'Benito',
        //     'nombre2' => '',
        //     'nombre3' => '',
        //     'apellido1' => 'Quib',
        //     'apellido2' => '',
        //     'email_verified_at' => '2024-11-23 18:26:13',
        //     'email' => 'quibbeni@gmail.com',
        //     'password' => bcrypt('12345678'),
        // ]);

        // $user=User::create([
        //     'nombre1' => 'Irvin',
        //     'nombre2' => '',
        //     'nombre3' => '',
        //     'apellido1' => 'Paau',
        //     'apellido2' => '',
        //     'email_verified_at' => '2024-11-23 18:26:13',
        //     'email' => 'irvinpaau77@gmail.com',
        //     'password' => bcrypt('12345678'),
        // ]);

        $user=User::create([
            'nombre1' => 'admin',
            'nombre2' => '',
            'nombre3' => '',
            'apellido1' => 'admin',
            'apellido2' => '',
            'email_verified_at' => '2024-11-23 18:26:13',
            'email' => 'admin@admin.com',
            'password' => bcrypt('admin321'),
        ]);
    }
}
