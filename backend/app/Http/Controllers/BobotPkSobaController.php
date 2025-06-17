<?php

namespace App\Http\Controllers;

use App\Models\BobotPkSoba;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BobotPkSobaController extends Controller
{
    public function index()
    {
        $bobots = BobotPkSoba::orderBy('id', 'desc')->get();
        $nomorBaris = 1;

        return response()->json([
            'results' => $bobots,
            'count' => $bobots->count(),
            'nomorBaris' => $nomorBaris
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'bobot_pk' => 'required|numeric|min:0|max:100',
            'bobot_soba' => 'required|numeric|min:0|max:100',
        ]);

        if ($request->bobot_pk + $request->bobot_soba != 100) {
            return response()->json([
                'message' => 'Total bobot harus 100%'
            ], 422);
        }

        try {
            DB::beginTransaction();

            if ($request->is_active) {
                BobotPkSoba::where('id', '!=', 0)->update(['is_active' => false]);
            }

            $bobot = BobotPkSoba::create([
                'name' => $request->name,
                'bobot_pk' => $request->bobot_pk,
                'bobot_soba' => $request->bobot_soba,
                'is_active' => $request->is_active ?? false,
                'created_by' => $request->user()->id ?? null,
            ]);

            DB::commit();
            return response()->json($bobot, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error creating bobot: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $bobot = BobotPkSoba::find($id);

        if (!$bobot) {
            return response()->json([
                'message' => 'Bobot not found'
            ], 404);
        }

        return response()->json($bobot);
    }

    public function update(Request $request, $id)
    {
        $bobot = BobotPkSoba::find($id);

        if (!$bobot) {
            return response()->json([
                'message' => 'Bobot not found'
            ], 404);
        }

        try {
            DB::beginTransaction();

            // Jika hanya update status
            if ($request->has('is_active') && !$request->has('name')) {
                if ($request->is_active) {
                    BobotPkSoba::where('id', '!=', $id)->update(['is_active' => false]);
                }

                $bobot->update([
                    'is_active' => $request->is_active,
                    'updated_by' => $request->user()->id ?? null,
                ]);
            } else {
                // Update penuh
                $this->validate($request, [
                    'name' => 'required|string|max:255',
                    'bobot_pk' => 'required|numeric|min:0|max:100',
                    'bobot_soba' => 'required|numeric|min:0|max:100',
                ]);

                if ($request->bobot_pk + $request->bobot_soba != 100) {
                    return response()->json([
                        'message' => 'Total bobot harus 100%'
                    ], 422);
                }

                if ($request->is_active) {
                    BobotPkSoba::where('id', '!=', $id)->update(['is_active' => false]);
                }

                $bobot->update([
                    'name' => $request->name,
                    'bobot_pk' => $request->bobot_pk,
                    'bobot_soba' => $request->bobot_soba,
                    'is_active' => $request->is_active ?? false,
                    'updated_by' => $request->user()->id ?? null,
                ]);
            }

            DB::commit();
            return response()->json([
                'message' => 'Bobot berhasil diupdate',
                'data' => $bobot
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error updating bobot: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $bobot = BobotPkSoba::find($id);

        if (!$bobot) {
            return response()->json([
                'message' => 'Bobot not found'
            ], 404);
        }

        if ($bobot->is_active) {
            return response()->json([
                'message' => 'Tidak dapat menghapus bobot yang sedang aktif'
            ], 422);
        }

        try {
            $bobot->update(['deleted_by' => request()->user()->id ?? null]);
            $bobot->delete();

            return response()->json([
                'message' => 'Bobot berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting bobot: ' . $e->getMessage()
            ], 500);
        }
    }
}