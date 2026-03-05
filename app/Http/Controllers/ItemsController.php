<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\SumberDana;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Imports\ItemImport;
use Maatwebsite\Excel\Facades\Excel;

class ItemsController extends Controller
{

    public function index()
    {
        $items = Item::with(['category', 'fundingSource', 'itemUnits'])
        ->paginate(10);

        $categories = Category::all();
        $sumberDanas = SumberDana::all();

        return view('admin.inventaris.barang.index', compact(
            'items',
            'categories',
            'sumberDanas'
        ));
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'funding_source_id' => 'nullable|exists:funding_sources,id',
            'name' => 'required|string|max:200',
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'specification' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Item::create($request->only([
            'category_id',
            'funding_source_id',
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
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'funding_source_id' => 'nullable|exists:funding_sources,id',
            'name' => 'required|string|max:200',
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'specification' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $item->update($request->only([
            'category_id',
            'funding_source_id',
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