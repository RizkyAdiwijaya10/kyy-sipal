<?php

namespace App\Imports;

use App\Models\SumberDana;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SumberDanaImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return new SumberDana([
            'name'        => $row['name'],
            'code'        => $row['code'] ?? null,
            'year'        => $row['year'] ?? null,
            'description' => $row['description'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.name' => 'required|string|max:100|unique:funding_sources,name',
            '*.code' => 'nullable|string|max:50|unique:funding_sources,code',
            '*.year' => 'nullable|integer|min:2000|max:' . (date('Y') + 5),
            '*.description' => 'nullable|string|max:255',
        ];
    }
}
