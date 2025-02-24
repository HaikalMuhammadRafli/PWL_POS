<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StokSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'stok_id' => 1,
                'barang_id' => 1,
                'user_id' => 3,
                'stok_tanggal' => '2025-02-10 09:30:00',
                'stok_jumlah' => 50,
            ],
            [
                'stok_id' => 2,
                'barang_id' => 2,
                'user_id' => 3,
                'stok_tanggal' => '2025-02-10 10:15:00',
                'stok_jumlah' => 25,
            ],
            [
                'stok_id' => 3,
                'barang_id' => 3,
                'user_id' => 1,
                'stok_tanggal' => '2025-02-11 08:45:00',
                'stok_jumlah' => 100,
            ],
            [
                'stok_id' => 4,
                'barang_id' => 4,
                'user_id' => 1,
                'stok_tanggal' => '2025-02-11 09:20:00',
                'stok_jumlah' => 75,
            ],
            [
                'stok_id' => 5,
                'barang_id' => 5,
                'user_id' => 3,
                'stok_tanggal' => '2025-02-12 11:30:00',
                'stok_jumlah' => 200,
            ],
            [
                'stok_id' => 6,
                'barang_id' => 6,
                'user_id' => 3,
                'stok_tanggal' => '2025-02-12 13:40:00',
                'stok_jumlah' => 150,
            ],
            [
                'stok_id' => 7,
                'barang_id' => 7,
                'user_id' => 2,
                'stok_tanggal' => '2025-02-13 14:15:00',
                'stok_jumlah' => 500,
            ],
            [
                'stok_id' => 8,
                'barang_id' => 8,
                'user_id' => 2,
                'stok_tanggal' => '2025-02-13 15:30:00',
                'stok_jumlah' => 350,
            ],
            [
                'stok_id' => 9,
                'barang_id' => 9,
                'user_id' => 3,
                'stok_tanggal' => '2025-02-14 09:45:00',
                'stok_jumlah' => 40,
            ],
            [
                'stok_id' => 10,
                'barang_id' => 10,
                'user_id' => 1,
                'stok_tanggal' => '2025-02-14 10:30:00',
                'stok_jumlah' => 30,
            ],
        ];

        DB::table('t_stok')->insert($data);
    }
}
