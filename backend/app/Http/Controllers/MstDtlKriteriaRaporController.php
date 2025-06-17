<?php

namespace App\Http\Controllers;

use App\Models\MstDtlKriteriaRapor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class MstDtlKriteriaRaporController extends Controller
{
    /**
     * Menampilkan daftar detail kriteria rapor dengan paginasi dan pencarian
     */
    public function index(Request $request)
    {
        try {
            $query = MstDtlKriteriaRapor::with(['jabatan', 'kriteria'])
                ->where('is_active', 1); // Tambahkan filter is_active = 1

            // Pencarian berdasarkan nama jabatan jika ada
            if ($request->has('search')) {
                $searchTerm = $request->search;
                $query->whereHas('jabatan', function ($q) use ($searchTerm) {
                    $q->where('jabatan', 'like', "%{$searchTerm}%");
                });
            }

            // Paginasi
            $limit = $request->input('limit', 10);
            $offset = $request->input('offset', 0);

            // Total data
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
     * Menyimpan detail kriteria rapor baru
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'jabatan_id' => 'required|exists:hr_jabatan,id',
                'kriteria_ids' => 'required|array|min:1',
                'kriteria_ids.*' => 'exists:mst_kriteria_rapor,id',
                'created_by' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            // Cek apakah kombinasi jabatan dan kriteria sudah ada
            $existing = MstDtlKriteriaRapor::where('jabatan_id', $request->jabatan_id)
                ->whereIn('kriteria_id', $request->kriteria_ids)
                ->exists();

            if ($existing) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kombinasi jabatan dan kriteria sudah ada'
                ], 422);
            }

            // Simpan data
            foreach ($request->kriteria_ids as $kriteria_id) {
                MstDtlKriteriaRapor::create([
                    'jabatan_id' => $request->jabatan_id,
                    'kriteria_id' => $kriteria_id,
                    'is_active' => '1',
                    'created_by' => $request->created_by
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Detail kriteria berhasil ditambahkan'
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
     * Menampilkan detail kriteria rapor spesifik
     */
    public function show($id)
    {
        try {
            // Dapatkan jabatan_id dari detail yang dipilih
            $detail = MstDtlKriteriaRapor::where('id', $id)
                ->with(['jabatan', 'kriteria'])
                ->first();

            if (!$detail) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            // Ambil semua kriteria untuk jabatan tersebut
            $allKriteria = MstDtlKriteriaRapor::where('jabatan_id', $detail->jabatan_id)
                ->with(['kriteria'])
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $detail->id,
                    'jabatan_id' => $detail->jabatan_id,
                    'kriteria_ids' => $allKriteria->pluck('kriteria_id')->toArray(),
                    'is_active' => $detail->is_active
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengupdate detail kriteria rapor
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'jabatan_id' => 'required|exists:hr_jabatan,id',
                'kriteria_ids' => 'required|array|min:1',
                'kriteria_ids.*' => 'exists:mst_kriteria_rapor,id',
                'updated_by' => 'required|string',
                'is_active' => 'required|in:0,1',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            // Dapatkan detail yang akan diupdate
            $detail = MstDtlKriteriaRapor::where('jabatan_id', $request->jabatan_id)
                ->whereNull('deleted_at')
                ->get();

            // Hapus kriteria yang lama
            foreach ($detail as $item) {
                $item->update(['deleted_by' => $request->updated_by]);
                $item->delete();
            }

            // Simpan kriteria yang baru
            foreach ($request->kriteria_ids as $kriteria_id) {
                MstDtlKriteriaRapor::create([
                    'jabatan_id' => $request->jabatan_id,
                    'kriteria_id' => $kriteria_id,
                    'created_by' => $request->updated_by,
                    'is_active' => '1'
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Detail kriteria berhasil diupdate'
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
     * Menghapus detail kriteria rapor
     */
    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'deleted_by' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            // Ambil detail untuk mendapatkan jabatan_id
            $detail = MstDtlKriteriaRapor::findOrFail($id);
            $jabatan_id = $detail->jabatan_id;

            // Update deleted_by dan soft delete semua kriteria untuk jabatan tersebut
            MstDtlKriteriaRapor::where('jabatan_id', $jabatan_id)
                ->update(['deleted_by' => $request->deleted_by]);

            MstDtlKriteriaRapor::where('jabatan_id', $jabatan_id)->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Detail kriteria berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'is_active' => 'required|in:0,1',
                'updated_by' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            // Ambil detail untuk mendapatkan jabatan_id
            $detail = MstDtlKriteriaRapor::findOrFail($id);
            $jabatan_id = $detail->jabatan_id;

            // Update semua kriteria dengan jabatan_id yang sama
            $updated = MstDtlKriteriaRapor::where('jabatan_id', $jabatan_id)
                ->whereNull('deleted_at')
                ->update([
                    'is_active' => (string) $request->is_active, // Pastikan sebagai string
                    'updated_by' => $request->updated_by
                ]);

            if (!$updated) {
                throw new \Exception('Gagal mengubah status');
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Status berhasil diupdate'
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