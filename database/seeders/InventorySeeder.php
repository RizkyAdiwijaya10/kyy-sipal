<?php

namespace Database\Seeders;

use App\Models\categories;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\FundingSource;
use App\Models\Item;
use App\Models\ItemUnit;
use App\Models\SumberDana;
use Illuminate\Support\Facades\DB;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            // ================================
            // CATEGORY
            // ================================
            $alatLab = categories::firstOrCreate(['name' => 'ALAT LAB']);
            $elektronik = categories::firstOrCreate(['name' => 'ELEKTRONIK']);
            $meubeler = categories::firstOrCreate(['name' => 'MEUBELER']);

            $funding = SumberDana::firstOrCreate([
                'name' => 'APBN/APBD'
            ]);

            /*
            =====================================================
            A. ALAT LAB
            =====================================================
            */

            $trainer = Item::create([
                'name' => 'Basic Electronic Trainer',
                'category_id' => $alatLab->id,
                'funding_source_id' => $funding->id,
                'brand' => null,
                'model' => null,
                'specification' => null,
                
            ]);

            ItemUnit::create([
                'item_id' => $trainer->id,
                'serial_number' => null,
                'inventory_code' => null,
                'condition' => 'baik',
                'status' => 'tersedia',
            ]);

            /*
            =====================================================
            B. ELEKTRONIK
            =====================================================
            */

            // PC Desktop i9
            $pcI9 = Item::create([
                'name' => 'PC Desktop i9',
                'category_id' => $elektronik->id,
                'funding_source_id' => $funding->id,
                'brand' => 'Axioo',
                'model' => 'MyPC Pro H61MSC i9-13900F',
                
            ]);

            ItemUnit::create([
                'item_id' => $pcI9->id,
                'serial_number' => 'CPU0224480110754007504',
                'inventory_code' => null,
                'condition' => 'baik',
                'status' => 'tersedia',
            ]);

            // PC Desktop i7 (30 unit)
            $pcI7 = Item::create([
                'name' => 'PC Desktop i7',
                'category_id' => $elektronik->id,
                'funding_source_id' => $funding->id,
                'brand' => 'Acer',
                'model' => 'Veriton M R01-A3 i7-12700',
                
            ]);

            for ($i = 1; $i <= 30; $i++) {
                ItemUnit::create([
                    'item_id' => $pcI7->id,
                    'serial_number' => null,
                    'inventory_code' => "NUP-PC-I7-$i",
                    'condition' => 'baik',
                    'status' => 'tersedia',
                ]);
            }

            // Laptop i9 (4 unit)
            $laptop = Item::create([
                'name' => 'Laptop i9',
                'category_id' => $elektronik->id,
                'funding_source_id' => $funding->id,
                'brand' => 'Acer',
                'model' => 'Nitro ANX Intel Ci9',
                
            ]);

            for ($i = 1; $i <= 4; $i++) {
                ItemUnit::create([
                    'item_id' => $laptop->id,
                    'serial_number' => null,
                    'inventory_code' => "NUP-LAPTOP-$i",
                    'condition' => 'baik',
                    'status' => 'tersedia',
                ]);
            }

            // UPS CP700 (20 unit)
            $ups700 = Item::create([
                'name' => 'UPS CP700 (700VA/350W)',
                'category_id' => $elektronik->id,
                'funding_source_id' => $funding->id,
                'brand' => 'CP700',
                'model' => '700VA/350W',
                
            ]);

            for ($i = 1; $i <= 20; $i++) {
                ItemUnit::create([
                    'item_id' => $ups700->id,
                    'serial_number' => null,
                    'inventory_code' => "NUP-UPS700-$i",
                    'condition' => 'baik',
                    'status' => 'tersedia',
                ]);
            }

            // Headset Sony MDR-7506 (30 unit)
            $headset = Item::create([
                'name' => 'Headset Tali',
                'category_id' => $elektronik->id,
                'funding_source_id' => $funding->id,
                'brand' => 'Sony',
                'model' => 'MDR-7506',
                
            ]);

            for ($i = 1; $i <= 30; $i++) {
                ItemUnit::create([
                    'item_id' => $headset->id,
                    'serial_number' => null,
                    'inventory_code' => "NUP-HEADSET-$i",
                    'condition' => 'baik',
                    'status' => 'tersedia',
                ]);
            }

            /*
            =====================================================
            C. MEUBELER
            =====================================================
            */

            // Meja Kerja (2 unit)
            $mejaKerja = Item::create([
                'name' => 'Meja Kerja',
                'category_id' => $meubeler->id,
                'funding_source_id' => $funding->id,
                'brand' => 'Ferro',
                'model' => 'Elegant Office System WDB-04PD',
                
            ]);

            for ($i = 1; $i <= 2; $i++) {
                ItemUnit::create([
                    'item_id' => $mejaKerja->id,
                    'serial_number' => null,
                    'inventory_code' => "NUP-MEJA-KERJA-$i",
                    'condition' => 'baik',
                    'status' => 'tersedia',
                ]);
            }

            // Meja Komputer (40 unit)
            $mejaKomputer = Item::create([
                'name' => 'Meja Komputer',
                'category_id' => $meubeler->id,
                'funding_source_id' => $funding->id,
                'brand' => 'Ferro',
                'model' => 'Elegant Office System CD-08A',
                
            ]);

            for ($i = 1; $i <= 40; $i++) {
                ItemUnit::create([
                    'item_id' => $mejaKomputer->id,
                    'serial_number' => null,
                    'inventory_code' => "NUP-MEJA-KOMPUTER-$i",
                    'condition' => 'baik',
                    'status' => 'tersedia',
                ]);
            }

        });
    }
}
