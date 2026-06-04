<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Imports\ItemImport;
use Maatwebsite\Excel\Facades\Excel;

class ItemsController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::with(['category', 'itemUnits'])
                    ->withCount('itemUnits as item_unit_count')
                    ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

       

        $items      = $query->paginate(10)->withQueryString();
        $categories = Category::all();

        return view('admin.inventaris.barang.index', compact('items', 'categories'));
    }
    

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:200',
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'specification' => 'nullable|string',
        ]);

        Item::create($request->only([
            'category_id',
            'name',
            'brand',
            'model',
            'specification',
        ]));

        return redirect()->route('items.index')
            ->with('success', 'Barang berhasil ditambahkan');
    }


    public function update(Request $request, Item $item)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:200',
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'specification' => 'nullable|string',
        ]);


        $item->update($request->only([
            'category_id',
            'name',
            'brand',
            'model',
            'specification',
        ]));

        return redirect()->route('items.index')
            ->with('success', 'Barang berhasil diperbarui');
    }


    public function destroy(Item $item)
    {
        if ($item->itemUnits()->count() > 0) {

            return redirect()->route('items.index')
                ->with('error', 'Tidak dapat menghapus barang karena masih memiliki unit');
        }

        $item->delete();

        return redirect()->route('items.index')
            ->with('success', 'Barang berhasil dihapus');
    }


    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        Excel::import(new ItemImport, $request->file('file'));

        return redirect()->route('items.index')
            ->with('success', 'Data berhasil diimport');
    }
}
