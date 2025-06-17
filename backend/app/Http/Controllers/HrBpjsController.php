<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\carbon;
use Exception;
use App\Models\hr_bpjs;
use App\Models\PublicModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use App\Models\Users;

class HrBpjsController extends Controller
{
    protected $judul_halaman_notif;
    public function __construct()
    {
        $this->judul_halaman_notif = 'MASTER BPJS';
    }

    protected function getCurrentUser($userid = null)
    {
        try {
            // Get userid from request data
            $userid = request()->input('userid');
            if (!$userid) {
                $userid = request()->input('data.userid'); // Untuk method DELETE yang mengirim di data
            }

            // Log untuk debugging
            Log::info('Received userid: ' . $userid);

            // Get user by ID
            if ($userid) {
                $user = Users::find($userid);
                Log::info('Found user: ' . ($user ? $user->name : 'not found'));
                return $user ? $user->name : 'USER TEST';
            }

            return 'USER TEST';
        } catch (\Exception $e) {
            Log::error('Error getting user: ' . $e->getMessage());
            return 'USER TEST';
        }
    }

    public function paging(Request $request): JsonResponse
    {
        try {
            $search = $request->search ?? '';
            $limit = $request->limit ?? 10;
            $offset = $request->offset ?? 0;

            $model = new hr_bpjs();

            $data = $model->get_data_($search, [
                'limit' => $limit,
                'offset' => $offset
            ]);

            $count = !empty($search) ?
                $model->get_total_data_($search) :
                $model->count();

            return response()->json([
                'results' => $data,
                'count' => $count,
                'offset' => (int)$offset,
                'nomorBaris' => (int)$offset + 1
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        $user_id = $this->getCurrentUser();

        try {
            $data = $this->validate($request, [
                'profiles_id' => 'required',
                'bpjs_no' => 'required|unique:hr_bpjs,bpjs_no',
                'bpjs_ket' => 'required',
            ]);

            $data['created_by'] = $user_id;
            $data['updated_by'] = $user_id;

            $todo = hr_bpjs::create($data);

            DB::commit();
            return response()->json([
                'code' => 201,
                'status' => true,
                'message' => 'Created Successfully',
                'data' => $todo
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'code' => 409,
                'status' => false,
                'message' => 'Created Failed',
                'error' => $e->getMessage()
            ], 409);
        }
    }

    public function show($id)
    {
        try {
            $bpjs = hr_bpjs::where('id', $id)
                ->whereNull('deleted_at')
                ->first();

            if (!$bpjs) {
                // Jika tidak ditemukan dengan id, coba cari dengan profiles_id
                $bpjs = hr_bpjs::where('profiles_id', $id)
                    ->whereNull('deleted_at')
                    ->first();

                if (!$bpjs) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Data BPJS tidak ditemukan'
                    ], 404);
                }
            }

            return response()->json([
                'code' => 200,
                'status' => true,
                'data' => $bpjs
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error in show BPJS: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengambil data BPJS'
            ], 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        DB::beginTransaction();
        $user_id = $this->getCurrentUser();

        try {
            $data = $this->validate($request, [
                'profiles_id' => 'required',
                'bpjs_no' => 'required:hr_bpjs,bpjs_no',
                'bpjs_ket' => 'nullable',
            ]);

            $todo = hr_bpjs::findOrFail($id);
            $todo->fill($data);
            $todo->updated_by = $user_id;
            $todo->save();

            DB::commit();
            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => 'Updated successfully',
                'data' => $todo
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'code' => 409,
                'status' => false,
                'message' => 'Update Failed',
                'error' => $e->getMessage()
            ], 409);
        }
    }

    public function destroy(int $id)
    {
        DB::beginTransaction();
        try {
            $todo = hr_bpjs::findOrFail($id);
            $todo->deleted_by = $this->getCurrentUser();
            $todo->save();
            $todo->delete();

            DB::commit();
            return response()->json([
                'message' => 'Deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Delete Failed',
                'error' => $e->getMessage()
            ], 409);
        }
    }

    // Method untuk API lainnya tetap sama
    public function getAllData()
    {
        try {
            $data = hr_bpjs::with('profile')->get();
            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function getByProfileId($profiles_id)
    {
        try {
            $bpjs = hr_bpjs::where('profiles_id', $profiles_id)
                ->whereNull('deleted_at')
                ->first();

            return response()->json([
                'code' => 200,
                'status' => true,
                'data' => $bpjs
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'failed get data'
            ], 404);
        }
    }

    public function updateByProfileId(Request $request, $profiles_id)
    {
        DB::beginTransaction();
        try {
            $bpjs = hr_bpjs::where('profiles_id', $profiles_id)
                ->whereNull('deleted_at')
                ->first();

            if (!$bpjs) {
                hr_bpjs::create([
                    'profiles_id' => $profiles_id,
                    'bpjs_no' => $request->bpjs_no,
                    'bpjs_ket' => $request->bpjs_ket,
                    'created_by' => 'USER TEST',
                    'updated_by' => 'USER TEST'
                ]);
            } else {
                $bpjs->update([
                    'bpjs_no' => $request->bpjs_no,
                    'bpjs_ket' => $request->bpjs_ket,
                    'updated_by' => 'USER TEST'
                ]);
            }

            DB::commit();
            return response()->json([
                'code' => 201,
                'status' => true,
                'message' => 'Data BPJS berhasil disimpan'
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'code' => 409,
                'status' => false,
                'message' => 'Gagal menyimpan data BPJS'
            ], 409);
        }
    }
}