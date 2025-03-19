<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\tm_category;
use App\Models\tm_roleuser;
use App\Models\tr_property;
use App\Models\tr_unit;
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


        tm_roleuser::create([
            'role_name' => 'admin'
        ]);

        tm_roleuser::create([
            'role_name' => 'user'
        ]);
        tm_roleuser::create([
            'role_name' => 'owner'
        ]);

        User::create([
            'name' => 'sekar',
            'username' => 'sekar',
            'email' => 'sekar@gmail.com',
            'password' => bcrypt('12345'),
            'id_role_user' => 2
        ]);

        User::create([
            'name' => 'daus',
            'username' => 'daus',
            'email' => 'daus@gmail.com',
            'password' => bcrypt('12345'),
            'id_role_user' => 3
        ]);

        // akun unit
        User::create([
            'name' => 'Ibnu Mahjub',
            'username' => 'mrxnunu',
            'email' => 'mrxnunu@gmail.com',
            'password' => bcrypt('12345'),
            'id_role_user' => 1
        ]);

        tm_category::create([
            'name_category' => 'Villa',
            'slug' => 'villa'
        ]);
        tm_category::create([
            'name_category' => 'Hotel',
            'slug' => 'hotel'
        ]);


        tr_property::create([
            'name_property' => 'Villa MRXNUNU',
            'slug' => 'villa-mrxnunu',
            'name_category' => 'Pendidikan',
            'category_id' => 1,
            'user_id' => 2,
            'alamat' => 'Jl. Raya Cendana, Cendana, Kec. Cendana, Kabupaten Tangerang, Provinsi Banten, 15132',
        ]);

        tr_property::create([
            'name_property' => 'Villa Pelangi',
            'slug' => 'villa-pelangi',
            'name_category' => 'Olahraga',
            'category_id' => 2,
            'user_id' => 2,
            'alamat' => 'Jl. Raya Dukuh Puntang, Dukuh Puntang, Kec. Dukuh Puntang, Kabupaten Cirebon, Provinsi Jawabarat, 12321',
        ]);

        tr_unit::create([
            'property_id' => 1,
            'name_property' => 'Villa MRXNUNU',
            'tipe' => 'Deluxe',
            'harga_unit' => 'Rp. 500.000',
            'jumlah_kamar' => 10,
            'deskripsi' => 'Unit Deluxe dengan fasilitas lengkap'
        ]);

        tr_unit::create([
            'property_id' => 2,
            'name_property' => 'Villa Pelangi',
            'tipe' => 'Standard',
            'harga_unit' => 'Rp. 400.000',
            'jumlah_kamar' => 8,
            'deskripsi' => 'Unit Standard dengan fasilitas dasar'
        ]);
    }
}
