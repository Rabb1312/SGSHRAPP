<?php

namespace App\Http\Controllers;

use App\Models\MstKriteriaRapor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class MstKriteriaRaporController extends Controller
{
    /**
     * Menampilkan daftar kriteria rapor dengan paginasi dan pencarian
     */
    public function index(Request $request)
    {
        try {
            $query = MstKriteriaRapor::query();

            // Pencarian
            if ($request->has('search')) {
                $searchTerm = $request->search;
                $query->where('kriteria', 'like', "%{$searchTerm}%");
            }

            // Paginasi
            $limit = $request->input('limit', 10);
            $offset = $request->input('offset', 0);

            // Total data (untuk paginasi)
            $total = $query->count();

            // Ambil data
            $result = $query->orderBy('created_at', 'desc')
                           ->skip($offset)
                           ->take($limit)
                           ->get();

            return response()->json([
                'status' => 'success',
                'count' => $total,
                'results' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menyimpan kriteria rapor baru
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'kriteria' => 'required|string|max:255',
                'is_active' => 'required|in:0,1',
                'created_by' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            // Simpan data
            $kriteria = MstKriteriaRapor::create([
                'kriteria' => $request->kriteria,
                'is_active' => $request->is_active,
                'created_by' => $request->created_by
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Kriteria berhasil ditambahkan',
                'data' => $kriteria
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan detail kriteria rapor
     */
    public function show($id)
    {
        try {
            $kriteria = MstKriteriaRapor::findOrFail($id);

            return response()->json([
                'status' => 'success',
                'data' => $kriteria
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Mengupdate kriteria rapor
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            // Cek keberadaan data
            $kriteria = MstKriteriaRapor::findOrFail($id);

            // Validasi input
            $validator = Validator::make($request->all(), [
                'kriteria' => 'required|string|max:255',
                'is_active' => 'required|in:0,1',
                'updated_by' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            // Update data
            $kriteria->update([
                'kriteria' => $request->kriteria,
                'is_active' => $request->is_active,
                'updated_by' => $request->updated_by
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Kriteria berhasil diupdate',
                'data' => $kriteria
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghapus kriteria rapor (soft delete)
     */
    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'deleted_by' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            // Cek keberadaan data
            $kriteria = MstKriteriaRapor::findOrFail($id);

            // Update deleted_by sebelum soft delete
            $kriteria->update([
                'deleted_by' => $request->deleted_by
            ]);

            // Soft delete
            $kriteria->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Kriteria berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle status aktif/nonaktif
     */
    public function toggleStatus(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'is_active' => 'required|in:0,1',
                'updated_by' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            // Cek keberadaan data
            $kriteria = MstKriteriaRapor::findOrFail($id);

            // Update status
            $kriteria->update([
                'is_active' => $request->is_active,
                'updated_by' => $request->updated_by
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Status berhasil diupdate',
                'data' => $kriteria
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}