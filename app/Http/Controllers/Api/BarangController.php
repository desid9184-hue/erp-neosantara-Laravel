<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barang;

class BarangController extends Controller
{
    // GET /api/barangs
    // Menampilkan semua data barang
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'Data barang berhasil diambil',
            'data' => Barang::latest()->get(),
        ]);
    }

    // GET /api/barangs/{id}
    // Menampilkan satu barang berdasarkan ID
    public function show($id)
    {
        $barang = Barang::find($id);

        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => 'Data barang tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail barang berhasil diambil',
            'data' => $barang,
        ]);
    }
}
