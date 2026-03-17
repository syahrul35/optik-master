<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Throwable;

class ProductImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    private $errors = [];
    private $successCount = 0;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Find category by name
        $category = Category::where('nama', $row['kategori'])->first();
        if (!$category) {
            throw new \Exception("Kategori '{$row['kategori']}' tidak ditemukan.");
        }

        $product = new Product([
            'category_id' => $category->id,
            'kode_produk' => $row['kode_produk'] ?? Product::generateKode(),
            'nama' => $row['nama'],
            'deskripsi' => $row['deskripsi'],
            'merek' => $row['merek'],
            'harga_beli' => $row['harga_beli'],
            'harga_jual' => $row['harga_jual'],
            'stok' => $row['stok'],
            'stok_minimum' => $row['stok_minimum'],
            'satuan' => $row['satuan'],
            'is_active' => isset($row['status']) ? ($row['status'] === 'Aktif') : true,
        ]);

        $product->save();
        $this->successCount++;

        return $product;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:150',
            'deskripsi' => 'nullable|string',
            'merek' => 'nullable|string|max:100',
            'kategori' => 'required|string|exists:categories,nama',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'stok_minimum' => 'required|integer|min:0',
            'satuan' => 'required|string|max:20',
            'status' => 'nullable|in:Aktif,Nonaktif',
        ];
    }

    /**
     * @param Throwable $e
     */
    public function onError(Throwable $e)
    {
        $this->errors[] = $e->getMessage();
    }

    /**
     * @param Failure[] $failures
     */
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->errors[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
        }
    }

    /**
     * Get the errors
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get the success count
     *
     * @return int
     */
    public function getSuccessCount(): int
    {
        return $this->successCount;
    }
}