<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ItemUnit;
use App\Models\Item;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ItemUnitImport;


class ItemsUnitController extends Controller
{
    public function index(Request $request)
    {
        $query = ItemUnit::with('item');
        
        // Filter by item_id jika ada
        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }
        
        
        $itemUnits = $query->latest()->paginate(10)->withQueryString();
        $items = Item::orderBy('name')->get();
        
        $statuses = ['tersedia', 'dipinjam', 'dipesan', 'nonaktif'];
        $conditions = ['baik', 'rusak', 'maintenance', 'hilang'];
        
        return view('admin.inventaris.satuan.index', compact('itemUnits', 'items', 'statuses', 'conditions'));
    }
    // {
    //     $itemUnits = ItemUnit::paginate(10);

    //     return view('inventaris.satuan.index', [
    //         'itemUnits' => $itemUnits,
    //         'items' => [],
    //         'statuses' => [],
    //         'conditions' => [],
    //     ]);
    // }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $items = Item::orderBy('name')->get();
        $selectedItemId = $request->get('item_id');
        
        $statuses = [
            'tersedia' => 'Tersedia',
            'dipinjam' => 'Dipinjam',
            'dipesan' => 'Dipesan',
            'nonaktif' => 'Nonaktif'
        ];
        
        $conditions = [
            'baik' => 'Baik',
            'rusak' => 'Rusak',
            'maintenance' => 'Maintenance',
            'hilang' => 'Hilang'
        ];
        
        // Generate suggested inventory code
        $inventoryCode = '';
        if ($selectedItemId) {
            $item = Item::find($selectedItemId);
            $itemCode = strtoupper(substr($item->name, 0, 3));
            $count = ItemUnit::where('item_id', $selectedItemId)->count() + 1;
            $inventoryCode = 'INV-' . $itemCode . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        }
        
        return view('admin.inventaris.satuan.create', compact('items', 'selectedItemId', 'statuses', 'conditions', 'inventoryCode'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|exists:items,id',
            'serial_number' => 'nullable|string|max:100|unique:item_units',
            'inventory_code' => 'nullable|string|max:50|unique:item_units',
            'condition' => 'required|in:baik,rusak,maintenance,hilang',
            'status' => 'required|in:tersedia,dipinjam,dipesan,nonaktif',
        ], [
            'item_id.required' => 'Barang wajib dipilih',
            'item_id.exists' => 'Barang tidak valid',
            'serial_number.unique' => 'Nomor seri sudah digunakan',
            'serial_number.max' => 'Nomor seri maksimal 100 karakter',
            'inventory_code.unique' => 'Kode inventaris sudah digunakan',
            'inventory_code.max' => 'Kode inventaris maksimal 50 karakter',
            'condition.required' => 'Kondisi wajib dipilih',
            'condition.in' => 'Kondisi tidak valid',
            'status.required' => 'Status wajib dipilih',
            'status.in' => 'Status tidak valid',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        ItemUnit::create($request->only([
            'item_id', 
            'serial_number', 
            'inventory_code', 
            'condition', 
            'status'
        ]));

        return redirect()->route('item-units.index')
            ->with('success', 'Unit barang berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(ItemUnit $itemUnit)
    {
        $itemUnit->load('item.category', 'item.fundingSource');
        
        return view('admin.inventaris.satuan.show', compact('itemUnit'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ItemUnit $itemUnit)
    {
        $items = Item::orderBy('name')->get();
        
        $statuses = [
            'tersedia' => 'Tersedia',
            'dipinjam' => 'Dipinjam',
            'dipesan' => 'Dipesan',
            'nonaktif' => 'Nonaktif'
        ];
        
        $conditions = [
            'baik' => 'Baik',
            'rusak' => 'Rusak',
            'maintenance' => 'Maintenance',
            'hilang' => 'Hilang'
        ];
        
        return view('admin.inventaris.satuan.edit', compact('itemUnit', 'items', 'statuses', 'conditions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ItemUnit $itemUnit)
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|exists:items,id',
            'serial_number' => 'nullable|string|max:100|unique:item_units,serial_number,' . $itemUnit->id,
            'inventory_code' => 'nullable|string|max:50|unique:item_units,inventory_code,' . $itemUnit->id,
            'condition' => 'required|in:baik,rusak,maintenance,hilang',
            'status' => 'required|in:tersedia,dipinjam,dipesan,nonaktif',
        ], [
            'item_id.required' => 'Barang wajib dipilih',
            'item_id.exists' => 'Barang tidak valid',
            'serial_number.unique' => 'Nomor seri sudah digunakan',
            'serial_number.max' => 'Nomor seri maksimal 100 karakter',
            'inventory_code.unique' => 'Kode inventaris sudah digunakan',
            'inventory_code.max' => 'Kode inventaris maksimal 50 karakter',
            'condition.required' => 'Kondisi wajib dipilih',
            'condition.in' => 'Kondisi tidak valid',
            'status.required' => 'Status wajib dipilih',
            'status.in' => 'Status tidak valid',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $itemUnit->update($request->only([
            'item_id', 
            'serial_number', 
            'inventory_code', 
            'condition', 
            'status'
        ]));

        return redirect()->route('item-units.index')
            ->with('success', 'Unit barang berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ItemUnit $itemUnit)
    {
        // Check if unit is borrowed
        if ($itemUnit->status === 'dipinjam') {
            return redirect()->route('item-units.index')
                ->with('error', 'Tidak dapat menghapus unit barang yang sedang dipinjam');
        }

        $itemUnit->delete();

        return redirect()->route('item-units.index')
            ->with('success', 'Unit barang berhasil dihapus');
    }

    /**
     * Bulk create units for an item
     */
    public function bulkCreate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1|max:50',
            'prefix' => 'nullable|string|max:10',
            'start_number' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $item = Item::find($request->item_id);
        $createdCount = 0;

        for ($i = 0; $i < $request->quantity; $i++) {
            $number = ($request->start_number ?? 1) + $i;
            
            // Generate inventory code
            $itemCode = strtoupper(substr($item->name, 0, 3));
            $prefix = $request->prefix ? $request->prefix . '-' : '';
            $inventoryCode = 'INV-' . $prefix . $itemCode . '-' . str_pad($number, 3, '0', STR_PAD_LEFT);
            
            // Check if code already exists
            if (!ItemUnit::where('inventory_code', $inventoryCode)->exists()) {
                ItemUnit::create([
                    'item_id' => $request->item_id,
                    'inventory_code' => $inventoryCode,
                    'condition' => 'baik',
                    'status' => 'tersedia',
                ]);
                $createdCount++;
            }
        }

        return redirect()->route('items.show', $item)
            ->with('success', "{$createdCount} unit barang berhasil ditambahkan");
    }

    /**
     * Update status of unit
     */
    public function updateStatus(Request $request, ItemUnit $itemUnit)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:tersedia,dipinjam,dipesan,nonaktif',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $itemUnit->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diperbarui'
        ]);
    }

    /**
     * Update condition of unit
     */
    public function updateCondition(Request $request, ItemUnit $itemUnit)
    {
        $validator = Validator::make($request->all(), [
            'condition' => 'required|in:baik,rusak,maintenance,hilang',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $itemUnit->update(['condition' => $request->condition]);

        return response()->json([
            'success' => true,
            'message' => 'Kondisi berhasil diperbarui'
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xlsx,xls,txt|max:4096'
        ]);

        Excel::import(new ItemUnitImport, $request->file('file'));

        return redirect()->route('item-units.index')
            ->with('success', 'Data unit barang berhasil diimport');
    }

}
