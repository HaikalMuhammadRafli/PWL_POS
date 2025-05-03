<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangModel extends Model
{
    use HasFactory;
    protected $table = 'm_barang';
    protected $primaryKey = 'barang_id';

    protected $fillable = [
        'barang_kode',
        'barang_nama',
        'barang_image',
        'harga_beli',
        'harga_jual',
        'kategori_id'
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriModel::class, 'kategori_id', 'kategori_id');
    }

    public function stok()
    {
        return $this->hasMany(StokModel::class, 'barang_id', 'barang_id');
    }

    public function detail()
    {
        return $this->hasMany(PenjualanDetailModel::class, 'barang_id', 'barang_id');
    }

    public function getStok()
    {
        return $this->stok()->sum('stok_jumlah') - $this->detail()->sum('jumlah');
    }

    protected function barangImage(): Attribute
    {
        return Attribute::make(
            get: fn($barang_image) => url('/storage/barangs/' . $barang_image),
        );
    }
}
