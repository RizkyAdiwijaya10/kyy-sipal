<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\Categories;
use App\Models\SumberDana;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ItemImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // Cari kategori berdasarkan nama
        $category = Categories::where('name', $row['category'])->first();

        // Cari sumber dana berdasarkan nama (opsional)
        $fundingSource = null;
        if (!empty($row['funding_source'])) {
            $fundingSource = SumberDana::where('name', $row['funding_source'])->first();
        }

        if (!$category) {
            return null; // skip kalau kategori tidak ditemukan
        }

        return new Item([
            'category_id' => $category->id,
            'funding_source_id' => $fundingSource?->id,
            'name' => $row['name'],
            'brand' => $row['brand'] ?? null,
            'model' => $row['model'] ?? null,
            'specification' => $row['specification'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.name' => 'required|string|max:200',
            '*.category' => 'required|string',
            '*.funding_source' => 'nullable|string',
            '*.brand' => 'nullable|string|max:100',
            '*.model' => 'nullable|string|max:100',
            '*.specification' => 'nullable|string',
        ];
    }
}
