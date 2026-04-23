<?php

namespace App\Imports;

use App\Models\category;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CategoryImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $name = trim($row['name'] ?? '');

        if ($name == null) return null;

        return new category([
            'name' => $name,
            'description' => $row['description'] ?? null
        ]);
    }
}
