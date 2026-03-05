<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\ItemUnit;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ItemUnitImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Cari item berdasarkan nama
        $item = Item::where('name', $row['item_name'])->first();

        // Kalau tidak ditemukan, skip
        if (!$item) {
            return null;
        }

        return new ItemUnit([
            'item_id'       => $item->id,
            'serial_number' => $row['serial_number'],
            'inventory_code'=> $row['inventory_code'],
            'condition'     => $row['condition'],
            'status'        => $row['status'],
        ]);
    }
}
