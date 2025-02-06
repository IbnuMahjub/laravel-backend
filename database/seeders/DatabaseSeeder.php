<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        User::create([
            'name' => 'sekar',
            'username' => 'sekar',
            'email' => 'sekar@gmail.com',
            'password' => bcrypt('12345')
        ]);

        // akun unit
        User::create([
            'name' => 'Ibnu Mahjub',
            'username' => 'mrxnunu',
            'is_admin' => 1,
            'email' => 'mrxnunu@gmail.com',
            'password' => bcrypt('12345')
        ]);

        Category::create([
            'name' => 'Pendidikan',
            'slug' => 'pendidikan'
        ]);

        Category::create([
            'name' => 'Olahraga',
            'slug' => 'olahraga'
        ]);
    }
}
