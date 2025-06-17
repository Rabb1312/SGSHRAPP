<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\carbon;
use Exception;

use App\Models\hr_npwp;
use App\Models\PublicModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use App\Models\Users;

class HrNpwpController extends Controller
{
    protected $judul_halaman_notif;
    public function __construct()
    {
        $this->judul_halaman_notif = 'MASTER NPWP';
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
            $count = (new hr_npwp)->count();
            $arr_pagination = (new PublicModel())->pagination_without_search($URL, $request->limit, $request->offset);
            $todos = (new hr_npwp())->get_data_($request->search, $arr_pagination);
        } else {
            $arr_pagination = (new PublicModel())->pagination_without_search(
                $URL,
                $request->limit,
                $request->offset,
                $request->search
            );
            $todos = (new hr_npwp())->get_data_($request->search, $arr_pagination);
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
            'npwp_no' => 'required|unique:hr_npwp,npwp_no',
            'npwp_address' => 'required',
        ]);
        try {
            $data['created_by'] = $user_id;
            $data['updated_by'] = $user_id;
            $todos = hr_npwp::create($data);

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
            $todo = hr_npwp::findOrFail($id);

            hr_npwp::where('id', $id)->update(['deleted_by' => $user_id]);
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
            $npwp = hr_npwp::where('id', $id)
                ->whereNull('deleted_at')
                ->first();

            if (!$npwp) {
                // Jika tidak ditemukan dengan id, coba cari dengan profiles_id
                $npwp = hr_npwp::where('profiles_id', $id)
                    ->whereNull('deleted_at')
                    ->first();

                if (!$npwp) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Data npwp tidak ditemukan'
                    ], 404);
                }
            }

            return response()->json([
                'code' => 200,
                'status' => true,
                'data' => $npwp
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error in show npwp: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengambil data npwp'
            ], 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        DB::beginTransaction();
        $user_id = $this->getCurrentUser();
        $data = $this->validate($request, [
            'profiles_id' => 'required',
            'npwp_no' => 'required:hr_npwp,npwp_no',
            'npwp_address' => 'nullable',
        ]);
        try {
            $todos = hr_npwp::findOrFail($id);
            $todos->fill($data);
            $todos->save();

            hr_npwp::where('id', $id)->update(['updated_by' => $user_id, 'updated_at' => date('Y-m-d H:m:s')]);

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
            $getAllData = hr_npwp::all();
            return response()->json($getAllData, 200);
        } catch (Exception $e) {
            return response()->json([$e->getMessage()], 400);
        }
    }

    public function getByProfileId($profiles_id)
    {
        try {
            $npwp = hr_npwp::where('profiles_id', $profiles_id)
                ->whereNull('deleted_at')
                ->first();
            return response()->json([
                'code' => 200,
                'status' => true,
                'data' => $npwp
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
            $npwp = hr_npwp::where('profiles_id', $profiles_id)
                ->whereNull('deleted_at')
                ->first();

            if (!$npwp) {
                hr_npwp::create([
                    'profiles_id' => $profiles_id,
                    'npwp_no' => $request->npwp_no,
                    'npwp_address' => $request->npwp_address,
                    'created_by' => 'USER TEST',
                    'updated_by' => 'USER TEST'
                ]);
            } else {
                $npwp->update([
                    'npwp_no' => $request->npwp_no,
                    'npwp_address' => $request->npwp_address,
                    'updated_by' => 'USER TEST'
                ]);
            }

            DB::commit();
            return response()->json([
                'code' => 201,
                'status' => true,
                'message' => 'Data NPWP berhasil disimpan'
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'code' => 409,
                'status' => false,
                'message' => 'Gagal menyimpan data NPWP'
            ], 409);
        }
    }
}