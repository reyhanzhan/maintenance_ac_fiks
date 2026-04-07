<?php

namespace Database\Seeders;

use App\Models\RumahSakit;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@maintenance.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'M. Choirudin',
            'username' => 'choirudin',
            'email' => 'choirudin@maintenance.com',
            'password' => bcrypt('123'),
            'role' => 'teknisi',
        ]);

        User::create([
            'name' => 'Inas Purnomo',
            'username' => 'inas',
            'email' => 'inas@maintenance.com',
            'password' => bcrypt('123'),
            'role' => 'teknisi',
        ]);

        $rsUbaya = RumahSakit::create(['nama' => 'RS Ubaya', 'alamat' => 'Surabaya']);
        $rsHaji = RumahSakit::create(['nama' => 'RS Haji', 'alamat' => 'Surabaya']);

        $ruanganUbaya = [
            'PICU I', 'R. Tunggu', 'R. Racik 1', 'R. Racik 2', 'R. Racik 3', 'R. Racik 4',
            'R. Gudang Obat', 'R. Pengambilan Obat', 'Musholla', 'Depo Farmasi',
        ];
        foreach ($ruanganUbaya as $nama) {
            Ruangan::create(['rumah_sakit_id' => $rsUbaya->id, 'nama' => $nama]);
        }

        $ruanganHaji = [
            'R. Pengambilan Hasil', 'R. Tunggu', 'R. Rawat Inap 1', 'R. Rawat Inap 2',
            'R. ICU', 'R. UGD', 'R. Administrasi',
        ];
        foreach ($ruanganHaji as $nama) {
            Ruangan::create(['rumah_sakit_id' => $rsHaji->id, 'nama' => $nama]);
        }

        $this->call(RsMataSeeder::class);
    }
}
