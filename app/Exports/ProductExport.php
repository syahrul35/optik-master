<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Product::with('category')->get()->map(function ($product) {
            return [
                'kode_produk' => $product->kode_produk,
                'nama' => $product->nama,
                'deskripsi' => $product->deskripsi,
                'merek' => $product->merek,
                'kategori' => $product->category->nama,
                'harga_beli' => $product->harga_beli,
                'harga_jual' => $product->harga_jual,
                'stok' => $product->stok,
                'stok_minimum' => $product->stok_minimum,
                'satuan' => $product->satuan,
                'is_active' => $product->is_active ? 'Aktif' : 'Nonaktif',
            ];
        });
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Kode Produk',
            'Nama',
            'Deskripsi',
            'Merek',
            'Kategori',
            'Harga Beli',
            'Harga Jual',
            'Stok',
            'Stok Minimum',
            'Satuan',
            'Status',
        ];
    }
}