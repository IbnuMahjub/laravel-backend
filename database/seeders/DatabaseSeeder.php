<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Category;
use App\Models\Property;
use App\Models\Unit;
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
        ]);

        Category::create([
            'name' => 'Olahraga',
        ]);

        Property::create([
            'name' => 'Villa MRXNUNU',
            'category_id' => 1,
            'alamat' => 'Jl. Raya Cendana, Cendana, Kec. Cendana, Kabupaten Tangerang, Provinsi Banten, 15132',
        ]);

        Property::create([
            'name' => 'Villa Pelangi',
            'category_id' => 2,
            'alamat' => 'Jl. Raya Dukuh Puntang, Dukuh Puntang, Kec. Dukuh Puntang, Kabupaten Cirebon, Provinsi Jawabarat, 12321',
        ]);

        Unit::create([
            'property_id' => 1,
            'tipe' => 'Deluxe',
            'harga_unit' => 'Rp. 500.000',
            'jumlah_kamar' => 10,
            'deskripsi' => 'Unit Deluxe dengan fasilitas lengkap'
        ]);

        Unit::create([
            'property_id' => 2,
            'tipe' => 'Standard',
            'harga_unit' => 'Rp. 400.000',
            'jumlah_kamar' => 8,
            'deskripsi' => 'Unit Standard dengan fasilitas dasar'
        ]);
    }
}
