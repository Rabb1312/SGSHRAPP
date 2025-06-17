<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\carbon;
use Exception;

use App\Models\hr_rek_karyawan;
use App\Models\PublicModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use App\Models\Users;

class HrRekKarController extends Controller
{
    protected $judul_halaman_notif;
    public function __construct()
    {
        $this->judul_halaman_notif = 'MASTER UNIT';
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
        $URL = URL::current();
        if (!isset($request->search)) {
            $count = (new hr_rek_karyawan)->count();
            $arr_pagination = (new PublicModel())->pagination_without_search($URL, $request->limit, $request->offset);
            $todos = (new hr_rek_karyawan())->get_data_($request->search, $arr_pagination);
        } else {
            $arr_pagination = (new PublicModel())->pagination_without_search(
                $URL,
                $request->limit,
                $request->offset,
                $request->search
            );
            $todos = (new hr_rek_karyawan())->get_data_($request->search, $arr_pagination);
            $count = $todos->count();
        }

        return response()->json(
            (new PublicModel())->array_respon_200_table($todos, $count, $arr_pagination),
            200
        );
    }

    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        $user_id = $this->getCurrentUser();
        $data = $this->validate($request, [
            'profiles_id' => 'required',
            'bank_id' => 'required',
            'karyawan_rek' => 'required',
            'karyawan_rek_kcp' => 'required',
            'karyawan_name_rek' => 'required',
        ]);
        try {
            $data['created_by'] = $user_id;
            $data['updated_by'] = $user_id;
            $todos = hr_rek_karyawan::create($data);

            DB::commit();
            return response()->json([
                'code' => 201,
                'status' => true,
                'message' => 'Created Successfully',
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'code' => 409,
                'status' => true,
                'message' => 'Created Failed',
            ], 409);
        }
    }

    public function destroy(int $id)
    {

        DB::beginTransaction();
        $user_id = $this->getCurrentUser();

        try {
            $todo = hr_rek_karyawan::findOrFail($id);

            hr_rek_karyawan::where('id', $id)->update(['deleted_by' => $user_id]);
            $todo->delete();

            DB::commit();
            return response()->json([
                'code' => 201,
                'status' => true,
                'message' => 'deleted succsessfully',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'failed delete'
            ], status: 409);
        }
    }

    public function show($id)
    {
        try {
            $rekening = hr_rek_karyawan::where('id', $id)
                ->whereNull('deleted_at')
                ->first();

            if (!$rekening) {
                // Jika tidak ditemukan dengan id, coba cari dengan profiles_id
                $rekening = hr_rek_karyawan::where('profiles_id', $id)
                    ->whereNull('deleted_at')
                    ->first();

                if (!$rekening) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Data rekening tidak ditemukan'
                    ], 404);
                }
            }

            return response()->json([
                'code' => 200,
                'status' => true,
                'data' => $rekening
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error in show rekening: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengambil data rekening'
            ], 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        DB::beginTransaction();
        $user_id = $this->getCurrentUser();
        $data = $this->validate($request, [
            'profiles_id' => 'required',
            'bank_id' => 'required',
            'karyawan_rek' => 'required',
            'karyawan_rek_kcp' => 'required',
            'karyawan_name_rek' => 'required',
        ]);
        try {
            $todos = hr_rek_karyawan::findOrFail($id);
            $todos->fill($data);
            $todos->save();

            hr_rek_karyawan::where('id', $id)->update(['updated_by' => $user_id, 'updated_at' => date('Y-m-d H:m:s')]);

            DB::commit();
            return response()->json([
                'code' => 201,
                'status' => true,
                'message' => 'updated successfully',
                'data' => $todos
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'code' => 409,
                'status' => false,
                'message' => 'failed updated',
            ], 409);
        }
    }

    public function getAllData()
    {
        try {
            $getAllData = hr_rek_karyawan::all();
            return response()->json($getAllData, 200);
        } catch (Exception $e) {
            return response()->json([$e->getMessage()], 400);
        }
    }

    public function getByProfileId($profiles_id)
    {
        try {
            $rekening = hr_rek_karyawan::where('profiles_id', $profiles_id)->first();
            return response()->json([
                'code' => 200,
                'status' => true,
                'data' => $rekening
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
            $rekening = hr_rek_karyawan::where('profiles_id', $profiles_id)->first();

            $data = [
                'profiles_id' => $profiles_id,
                'bank_id' => $request->bank_id,
                'karyawan_rek' => $request->karyawan_rek,
                'karyawan_rek_kcp' => $request->karyawan_rek_kcp,
                'karyawan_name_rek' => $request->karyawan_name_rek
            ];

            if (!$rekening) {
                $data['created_by'] = 'USER TEST';
                $data['updated_by'] = 'USER TEST';
                hr_rek_karyawan::create($data);
            } else {
                $data['updated_by'] = 'USER TEST';
                $rekening->update($data);
            }

            DB::commit();
            return response()->json([
                'code' => 201,
                'status' => true,
                'message' => 'Data rekening berhasil disimpan'
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'code' => 409,
                'status' => false,
                'message' => 'Gagal menyimpan data rekening'
            ], 409);
        }
    }
}