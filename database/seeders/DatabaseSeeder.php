<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\tm_category;
use App\Models\tm_roleuser;
use App\Models\tr_invoice;
use App\Models\tr_order;
use App\Models\tr_property;
use App\Models\tr_unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Str;


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


        // $faker = Faker::create();

        // for ($i = 1; $i <= 500; $i++) {
        //     tr_property::create([
        //         'name_property' => $faker->company . ' Property',
        //         'slug' => 'property-' . $i,
        //         'name_category' => $faker->randomElement(['Pendidikan', 'Olahraga', 'Kesehatan', 'Liburan']),
        //         'category_id' => $faker->numberBetween(1, 2),
        //         'user_id' => $faker->numberBetween(2, 3),
        //         'alamat' => $faker->address
        //     ]);
        // }

        tr_unit::create([
            'property_id' => 1,
            'name_property' => 'Villa MRXNUNU',
            'tipe' => 'Deluxe',
            'harga_unit' => '500.000',
            'jumlah_kamar' => 10,
            'deskripsi' => 'Unit Deluxe dengan fasilitas lengkap'
        ]);

        tr_unit::create([
            'property_id' => 2,
            'name_property' => 'Villa Pelangi',
            'tipe' => 'Standard',
            'harga_unit' => '400.000',
            'jumlah_kamar' => 8,
            'deskripsi' => 'Unit Standard dengan fasilitas dasar'
        ]);

        // tr_order::create([
        //     'property_id' => 1,
        //     'kode_pemesanan' => 'ORD-12345',
        //     'unit_id' => 1,
        //     'status' => 'unpaid',    
        // ]);

        // $faker = Faker::create();


        // for ($i = 0; $i < 50; $i++) {
        //     $kodePemesanan = 'ORD-' . strtoupper(Str::random(6));
        //     $propertyId = $faker->numberBetween(1, 2);
        //     $unitId = $propertyId;
        //     $userId = $faker->numberBetween(1, 3);
        //     $user = \App\Models\User::find($userId);
        //     $username = $user->username ?? 'guest';

        //     $hargaUnit = $propertyId == 1 ? 500000 : 400000;
        //     $jumlahHari = $faker->numberBetween(1, 5);
        //     $totalHarga = $hargaUnit * $jumlahHari;

        //     $tanggalCheckIn = $faker->dateTimeBetween('now', '+30 days');
        //     $tanggalCheckOut = (clone $tanggalCheckIn)->modify("+$jumlahHari days");

        //     // ORDER
        //     tr_order::create([
        //         'kode_pemesanan' => $kodePemesanan,
        //         'property_id' => $propertyId,
        //         'unit_id' => $unitId,
        //         'user_id' => $userId,
        //         'username' => $username,
        //         'name_property' => $propertyId == 1 ? 'Villa MRXNUNU' : 'Villa Pelangi',
        //         'harga_unit' => (string)$hargaUnit,
        //         'jumlah_kamar' => $faker->numberBetween(1, 3),
        //         'tanggal_check_in' => $tanggalCheckIn->format('Y-m-d'),
        //         'tanggal_check_out' => $tanggalCheckOut->format('Y-m-d'),
        //         'jumlah_hari' => $jumlahHari,
        //         'catatan' => $faker->optional()->sentence,
        //         'status' => 'paid',
        //         'total_harga' => $totalHarga,
        //     ]);

        //     // INVOICE dengan status 'paid'
        //     tr_invoice::create([
        //         'kode_pemesanan' => $kodePemesanan,
        //         'no_invoice' => 'INV-' . strtoupper(Str::random(6)),
        //         'total_harga' => $totalHarga,
        //         'status' => 'paid',
        //         'user_id' => $userId,
        //     ]);
        // }
    }
}
