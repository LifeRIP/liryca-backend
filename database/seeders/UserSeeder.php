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
        transactional(function () {
            // Crear un nuevo usuario
            $user = User::create([
                'id' => '8f6eea1e-9011-40ec-8ae4-19916127672b',
                'username' => 'John Doe',
                'birthday' => '1995-01-01',
                'country' => 'United States',
                'email' => 'john.doe@example.com',
                'password' => Hash::make('12345678'),
                'description' => 'Hello, I am John Doe',
                'profile_picture' => 'https://firebasestorage.googleapis.com/v0/b/liryca-c9f2e.appspot.com/o/profileIcons%2Fdefault-avatar.png?alt=media&token=09e84995-6605-4b4f-9131-5b99ace4395d',
                'profile_banner' => 'https://firebasestorage.googleapis.com/v0/b/liryca-c9f2e.appspot.com/o/profileBanner%2FbannerDefault.png?alt=media&token=aa685868-63c2-46fe-b73c-aa91c26d6c19',
            ]);

            $user->assignRole(RoleEnum::USER->value);

            // Crear un nuevo artista
            $user = User::create([
                'id' => '264e1f6c-52ec-48ea-bfb1-13100f8b5cf3',
                'username' => 'Blessd',
                'birthday' => '2000-01-27',
                'country' => 'Colombia',
                'email' => 'blessd@example.com',
                'password' => Hash::make('12345678'),
                'role' => RoleEnum::ARTIST->value,
                'description' => 'Hecho en Medellín',
                'profile_picture' => 'https://i.scdn.co/image/ab676161000051741b5902b400971bc07d3473dd',
                'profile_banner' => 'https://www.infobae.com/resizer/v2/ARBKXYU6NRDZZCFBAUUGBAK4QY.jpg?auth=7dbd6d124f15e1558eb1bc52d6094b6a07fc831848047c1bf1c29d09ea7f550a&smart=true&width=1200&height=675&quality=85',
            ]);

            $user->assignRole(RoleEnum::ARTIST->value);

            // Crear un nuevo usuario
            $user = User::create([
                'id' => '741944d5-4f2e-4533-af0d-1155ededce3a',
                'username' => 'Valentina',
                'birthday' => '2001-01-01',
                'country' => 'Colombia',
                'email' => 'valentina@example.com',
                'password' => Hash::make('12345678'),
                'description' => 'Hello, I am Valentina',
                'profile_picture' => 'https://firebasestorage.googleapis.com/v0/b/liryca-c9f2e.appspot.com/o/profileIcons%2Fdefault-avatar.png?alt=media&token=09e84995-6605-4b4f-9131-5b99ace4395d',
                'profile_banner' => 'https://firebasestorage.googleapis.com/v0/b/liryca-c9f2e.appspot.com/o/profileBanner%2FbannerDefault.png?alt=media&token=aa685868-63c2-46fe-b73c-aa91c26d6c19',
            ]);

            $user->assignRole(RoleEnum::USER->value);

            // Crear un nuevo administrador
            $user = User::create([
                'id' => '9c87a67f-e553-4e5f-8c7b-dcbd8c39c27a',
                'username' => 'Admin',
                'birthday' => '1990-01-01',
                'country' => 'United States',
                'email' => 'admin@example.com',
                'password' => Hash::make('12345678'),
                'role' => RoleEnum::ADMIN->value,
                'description' => 'Hello, I am Admin',
                'profile_picture' => 'https://firebasestorage.googleapis.com/v0/b/liryca-c9f2e.appspot.com/o/profileIcons%2Fdefault-avatar.png?alt=media&token=09e84995-6605-4b4f-9131-5b99ace4395d',
                'profile_banner' => 'https://firebasestorage.googleapis.com/v0/b/liryca-c9f2e.appspot.com/o/profileBanner%2FbannerDefault.png?alt=media&token=aa685868-63c2-46fe-b73c-aa91c26d6c19',
            ]);

            $user->assignRole(RoleEnum::ADMIN->value);

            // Crear un nuevo artista
            $user = User::create([
                'id' => '5d5e1b6c-71ec-49ea-bfb1-19100f7d5cf7',
                'username' => 'Peso Pluma',
                'birthday' => '1999-06-15', // Fecha de nacimiento
                'country' => 'México', // País de origen
                'email' => 'pesopluma@example.com',
                'password' => Hash::make('12345678'), // Hashear la contraseña
                'role' => RoleEnum::ARTIST->value, // Rol de artista
                'description' => 'El doble P', // Descripción o biografía
                'profile_picture' => 'https://firebasestorage.googleapis.com/v0/b/liryca-c9f2e.appspot.com/o/profileIcons%2Fdefault-avatar.png?alt=media&token=09e84995-6605-4b4f-9131-5b99ace4395d',
                'profile_banner' => 'https://firebasestorage.googleapis.com/v0/b/liryca-c9f2e.appspot.com/o/profileBanner%2FbannerDefault.png?alt=media&token=aa685868-63c2-46fe-b73c-aa91c26d6c19',
            ]);

            $user->assignRole(RoleEnum::ARTIST->value);
        });
    }
}
