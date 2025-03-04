<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\tm_category;
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

        tm_category::create([
            'name_category' => 'Pendidikan',
            'slug' => 'pendidikan'
        ]);

        tm_category::create([
            'name_category' => 'Olahraga',
            'slug' => 'olahraga'
        ]);

        tr_property::create([
            'name_property' => 'Villa MRXNUNU',
            'slug' => 'villa-mrxnunu',
            'name_category' => 'Pendidikan',
            'category_id' => 1,
            'alamat' => 'Jl. Raya Cendana, Cendana, Kec. Cendana, Kabupaten Tangerang, Provinsi Banten, 15132',
        ]);

        tr_property::create([
            'name_property' => 'Villa Pelangi',
            'slug' => 'villa-pelangi',
            'name_category' => 'Olahraga',
            'category_id' => 2,
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
