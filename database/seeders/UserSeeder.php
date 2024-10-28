<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear un nuevo usuario
        $user = User::create([
            'id' => '8f6eea1e-9011-40ec-8ae4-19916127672b',
            'username' => 'John Doe',
            'birthday' => '1995-01-01',
            'country' => 'United States',
            'email' => 'john.doe@gmail.com',
            'password' => Hash::make('12345678'),
            'description' => 'Hello, I am John Doe',
            'profile_picture' => 'https://i.pinimg.com/280x280_RS/ec/16/b8/ec16b8e8e09a99a2b8fff167dbf53c11.jpg',
            'profile_banner' => 'https://tinkercademy.com/wp-content/uploads/2017/04/Generic-Banner-07-Web-App-Developer.png',
        ]);

        $user->assignRole(RoleEnum::USER->value);

        // Crear un nuevo artista
        $user = User::create([
            'id' => '264e1f6c-52ec-48ea-bfb1-13100f8b5cf3',
            'username' => 'Blessd',
            'birthday' => '2000-01-27',
            'country' => 'Colombia',
            'email' => 'blessd@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => RoleEnum::ARTIST->value,
            'description' => 'Hecho en Medellín',
            'profile_picture' => 'https://i.scdn.co/image/ab676161000051741b5902b400971bc07d3473dd',
            'profile_banner' => 'https://www.infobae.com/resizer/v2/ARBKXYU6NRDZZCFBAUUGBAK4QY.jpg?auth=7dbd6d124f15e1558eb1bc52d6094b6a07fc831848047c1bf1c29d09ea7f550a&smart=true&width=1200&height=675&quality=85',
        ]);

        $user->assignRole(RoleEnum::USER->value);

        // Crear un nuevo usuario
        $user = User::create([
            'id' => '741944d5-4f2e-4533-af0d-1155ededce3a',
            'username' => 'Valentina',
            'birthday' => '2001-01-01',
            'country' => 'Colombia',
            'email' => 'valentina@gmail.com',
            'password' => Hash::make('12345678'),
            'description' => 'Hello, I am Valentina',
            'profile_picture' => 'https://i.ibb.co/HCR6qqF/a.png',
            'profile_banner' => 'https://tinkercademy.com/wp-content/uploads/2017/04/Generic-Banner-07-Web-App-Developer.png',
        ]);

        $user->assignRole(RoleEnum::USER->value);

        // Crear un nuevo administrador
        $user = User::create([
            'id' => '9c87a67f-e553-4e5f-8c7b-dcbd8c39c27a',
            'username' => 'Admin',
            'birthday' => '1990-01-01',
            'country' => 'United States',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => RoleEnum::ADMIN->value,
            'description' => 'Hello, I am Admin',
        ]);

        $user->assignRole(RoleEnum::ADMIN->value);

        $user = User::create([
            'id' => '5d5e1b6c-71ec-49ea-bfb1-19100f7d5cf7',
            'username' => 'Peso Pluma',
            'birthday' => '1999-06-15', // Fecha de nacimiento
            'country' => 'México', // País de origen
            'email' => 'pesopluma@gmail.com',
            'password' => Hash::make('12345678'), // Hashear la contraseña
            'role' => RoleEnum::ARTIST->value, // Rol de artista
            'description' => 'El doble P', // Descripción o biografía
            'profile_picture' => 'https://i.scdn.co/image/ab67616100005174a597b11d236292d34533fcd2', // URL de la foto de perfil
            'profile_banner' => 'https://cdn-3.expansion.mx/dims4/default/24b5a44/2147483647/strip/true/crop/1366x768+0+0/resize/1200x675!/quality/90/?url=https%3A%2F%2Fcdn-3.expansion.mx%2Fbe%2F77%2F30f81942433d99e52da8b8d4c28a%2Fpeso-pluma.jpg', // URL del banner de perfil
        ]);

        $user->assignRole(RoleEnum::ARTIST->value);
    }
}
