<?php

namespace App\Http\Controllers;

use App\Models\SumberDana;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Imports\SumberDanaImport;
use Maatwebsite\Excel\Facades\Excel;

class SumberDanaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sumberDanas = SumberDana::withCount('items')
            ->latest()
            ->paginate(10);
        return view('admin.inventaris.sumber_dana.index', compact('sumberDanas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get years for dropdown
        $currentYear = date('Y');
        $years = range($currentYear - 5, $currentYear + 5);
        
        return view('admin.inventaris.sumber_dana.create', compact('years'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:funding_sources',
            'code' => 'nullable|string|max:50|unique:funding_sources',
            'year' => 'nullable|integer|min:2000|max:' . (date('Y') + 5),
            'description' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Nama sumber dana wajib diisi',
            'name.unique' => 'Nama sumber dana sudah digunakan',
            'name.max' => 'Nama sumber dana maksimal 100 karakter',
            'code.unique' => 'Kode sumber dana sudah digunakan',
            'code.max' => 'Kode sumber dana maksimal 50 karakter',
            'year.integer' => 'Tahun harus berupa angka',
            'year.min' => 'Tahun minimal 2000',
            'year.max' => 'Tahun tidak boleh lebih dari ' . (date('Y') + 5),
            'description.max' => 'Deskripsi maksimal 255 karakter',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        SumberDana::create($request->only(['name', 'code', 'year', 'description']));

        return redirect()->route('sumber-dana.index')
            ->with('success', 'Sumber dana berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(SumberDana $sumberDana)
    {
        $sumberDana->load(['items' => function($query) {
            $query->latest()->limit(10);
        }, 'items.itemUnits']);
        
        return view('admin.inventaris.sumber_dana.show', compact('sumberDana'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SumberDana $sumberDana)
    {
        $currentYear = date('Y');
        $years = range($currentYear - 5, $currentYear + 5);
        
        return view('admin.inventaris.sumber_dana.edit', compact('sumberDana', 'years'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SumberDana $sumberDana)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:funding_sources,name,' . $sumberDana->id,
            'code' => 'nullable|string|max:50|unique:funding_sources,code,' . $sumberDana->id,
            'year' => 'nullable|integer|min:2000|max:' . (date('Y') + 5),
            'description' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Nama sumber dana wajib diisi',
            'name.unique' => 'Nama sumber dana sudah digunakan',
            'name.max' => 'Nama sumber dana maksimal 100 karakter',
            'code.unique' => 'Kode sumber dana sudah digunakan',
            'code.max' => 'Kode sumber dana maksimal 50 karakter',
            'year.integer' => 'Tahun harus berupa angka',
            'year.min' => 'Tahun minimal 2000',
            'year.max' => 'Tahun tidak boleh lebih dari ' . (date('Y') + 5),
            'description.max' => 'Deskripsi maksimal 255 karakter',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $sumberDana->update($request->only(['name', 'code', 'year', 'description']));

        return redirect()->route('sumber-dana.index')
            ->with('success', 'Sumber dana berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SumberDana $sumberDana)
    {
        // Check if funding source has items
        if ($sumberDana->items()->count() > 0) {
            return redirect()->route('sumber-dana.index')
                ->with('error', 'Tidak dapat menghapus sumber dana karena masih memiliki barang');
        }

        $sumberDanaName = $sumberDana->name;
        $sumberDana->delete();

        return redirect()->route('sumber-dana.index')
            ->with('success', "Sumber dana '{$sumberDanaName}' berhasil dihapus");
    }

    /**
     * Get funding sources for API/Select2
     */
    public function getFundingSources(Request $request)
    {
        $search = $request->get('search');
        
        $sumberDanas = SumberDana::when($search, function($query) use ($search) {
            return $query->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
        })
        ->orderBy('name')
        ->limit(10)
        ->get();

        return response()->json($sumberDanas);
    }

    /**
     * Get funding sources by year
     */
    public function getByYear(Request $request, $year)
    {
        $sumberDanas = SumberDana::where('year', $year)
            ->orderBy('name')
            ->get();

        return response()->json($sumberDanas);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ], [
            'file.required' => 'File wajib dipilih',
            'file.mimes' => 'File harus berupa xlsx, xls, atau csv',
            'file.max' => 'Ukuran file maksimal 2MB'
        ]);

        try {
            Excel::import(new SumberDanaImport, $request->file('file'));

            return redirect()->route('sumber-dana.index')
                ->with('success', 'Data sumber dana berhasil diimport');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

}