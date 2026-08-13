<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('usuarios')->insert([
            [
                'nombre' => 'Administrador',
                'email' => 'admin@ventas.com',
                'password' => Hash::make('123456'),
                'rol' => 'admin',
                'activo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Vendedor',
                'email' => 'vendedor@ventas.com',
                'password' => Hash::make('123456'),
                'rol' => 'vendedor',
                'activo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}