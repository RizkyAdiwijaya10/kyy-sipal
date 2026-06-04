<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CategoryImport;

class CategoryController extends Controller
{

    public function index(Request $request)
    {
        $query = Category::withCount('items');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('has_items') && $request->has_items !== '') {
            if ($request->has_items == '1') {
                $query->having('items_count', '>', 0);
            } else {
                $query->having('items_count', '=', 0);
            }
        }

        $categories = $query->paginate(10)->withQueryString();

        return view('admin.inventaris.kategori.index', compact('categories'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
            'description' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Nama kategori wajib diisi',
            'name.unique' => 'Nama kategori sudah digunakan',
        ]);

        Category::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil ditambahkan');
    }


    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:255',
        ]);

        $category->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil diperbarui');
    }


    public function destroy(Category $category)
    {
        if ($category->items()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih memiliki barang');
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil dihapus');
    }


    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xlsx,xls|max:2048'
        ]);

        Excel::import(new CategoryImport, $request->file('file'));

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil diimport');
    }
}