<?php

namespace App\Http\Controllers;

use App\Models\profiles;
use App\Models\hr_kontrak;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\carbon;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Models\ProfilesDetail;
use App\Models\PublicModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class ProfilesDetailController extends Controller
{
    protected $judul_halaman_notif;
    public function __construct()
    {
        $this->judul_halaman_notif = 'MASTER PROFILE DETAIL';
    }

    // public function paging(Request $request): JsonResponse
    // {
    //     $URL = URL::current();
    //     if (!isset($request->search)) {
    //         $count = (new ProfilesDetail)->count();
    //         $arr_pagination = (new PublicModel())->pagination_without_search($URL, $request->limit, $request->offset);
    //         $todos = (new ProfilesDetail())->get_data_($request->search, $arr_pagination);
    //     } else {
    //         $arr_pagination = (new PublicModel())->pagination_without_search(
    //             $URL,
    //             $request->limit,
    //             $request->offset,
    //             $request->search
    //         );
    //         $todos = (new ProfilesDetail())->get_data_($request->search, $arr_pagination);
    //         $count = $todos->count();
    //     }

    //     return response()->json(
    //         (new PublicModel())->array_respon_200_table($todos, $count, $arr_pagination),
    //         200
    //     );
    // }

    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        $user_id = ' USER TEST';
        $data = $this->validate($request, [
            'profiles_id' => 'required',
            'masa_kerja_ke' => 'nullable',
            'tahun' => 'required',
            'bulan' => 'required',
            'hari' => 'required',
            'sts_take_out' => 'nullable',
            'ket' => 'nullable',
        ]);
        try {
            $data['created_by'] = $user_id;
            $data['updated_by'] = $user_id;
            $todos = ProfilesDetail::create($data);

            DB::commit();
            return response()->json([
                'code' => 201,
                'status' => true,
                'message' => 'Created Successfully',
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            // Tambahkan ini untuk melihat error yang terjadi
            return response()->json([
                'code' => 409,
                'status' => false,
                'message' => 'Created Failed',
                'error' => $e->getMessage() // Pesan error yang sebenarnya
            ], 409);
        }
    }

    public function destroy(int $id)
    {

        DB::beginTransaction();
        $user_id = 'USER TEST';

        try {
            $todo = ProfilesDetail::findOrFail($id);

            ProfilesDetail::where('id', $id)->update(['deleted_by' => $user_id]);
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

    public function show(int $id)
    {
        try {
            $todos = ProfilesDetail::findOrFail($id);

            return response()->json([
                'code' => 200,
                'status' => true,
                'data' => $todos
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'failed get data'
            ], status: 404);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        DB::beginTransaction();
        $user_id = ' USER TEST';
        $data = $this->validate($request, [
            'profiles_id' => 'required',
            'masa_kerja_ke' => 'nullable',
            'tahun' => 'required',
            'bulan' => 'required',
            'hari' => 'required',
            'sts_take_out' => 'nullable',
            'ket' => 'nullable',
        ]);
        try {
            $todos = ProfilesDetail::findOrFail($id);
            $todos->fill($data);
            $todos->save();

            ProfilesDetail::where('id', $id)->update(['updated_by' => $user_id, 'updated_at' => date('Y-m-d H:m:s')]);

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
            $getAllData = ProfilesDetail::all();
            return response()->json($getAllData, 200);
        } catch (Exception $e) {
            return response()->json([$e->getMessage()], 400);
        }
    }

    public function getProfileDetailData(Request $request)
    {
        try {
            $limit = $request->input('limit', 10);
            $offset = $request->input('offset', 0);
            $search = $request->input('search');

            // Gunakan method dari model untuk mendapatkan data
            $profileDetail = new ProfilesDetail();
            $result = $profileDetail->getDetailedData($search, $limit, $offset);

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Error in getProfileDetailData: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to get profile detail data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function checkFiveYear()
{
    try {
        $employee = DB::table('profiles_detail as pd')
            ->select(
                'pd.id',
                'pd.profiles_id',
                'pd.masa_kerja_ke',
                'pd.tahun',
                'pd.bulan',
                'pd.hari',
                'pd.sts_take_out',
                'pd.ket',
                'p.profile_name',
                'p.cabang_id',
                'p.jabatan_id', 
                'p.unit_id',
                'p.identity_number',
                'p.join_date'
            )
            ->join('profiles as p', 'p.profile_name', '=', 'pd.profiles_id')
            ->whereRaw('(pd.tahun > 5 OR (pd.tahun = 5 AND pd.bulan >= 0))')
            ->where('pd.sts_take_out', '!=', 'Tidak Aktif')
            ->where('p.is_active', '=', 'Y')
            ->orderBy('pd.tahun', 'DESC')
            ->orderBy('pd.bulan', 'DESC')
            ->first();

        if ($employee) {
            return response()->json([
                'status' => 'success',
                'data' => $employee,
                'message' => 'Data karyawan 5 tahun ditemukan'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => null,
            'message' => 'Tidak ada karyawan yang mencapai masa kerja 5 tahun'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Gagal mengambil data karyawan 5 tahun: ' . $e->getMessage()
        ], 500);
    }
}
    public function takeoutFiveYear(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validasi input
            $this->validate($request, [
                'profiles_id' => 'required',
                'tgl_take_out' => 'required|date',
                'userid' => 'required'
            ]);

            $currentTime = Carbon::now();

            // Update status di profiles_detail
            DB::table('profiles_detail')
                ->where('profiles_id', $request->input('profiles_id'))
                ->update([
                    'sts_take_out' => 'Tidak Aktif',
                    'tgl_take_out' => $request->input('tgl_take_out'),
                    'ket' => 'Habis Kontrak',
                    'updated_at' => $currentTime,
                    'updated_by' => $request->input('userid')
                ]);

            // Update status di profiles
            DB::table('profiles')
                ->where('id', $request->input('profiles_id'))
                ->update([
                    'is_active' => 'N',
                    'employment_status' => 'Non-Active',
                    'updated_at' => $currentTime,
                    'updated_by' => $request->input('userid')
                ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Take out berhasil diproses'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memproses take out: ' . $e->getMessage()
            ], 500);
        }
    }

    // public function setTakeOut(Request $request)
    // {
    //     $validatedData = $this->validate($request, [
    //         'profiles_id' => 'required|exists:profiles,id',
    //         'tgl_take_out' => 'required|date',
    //         'ket' => 'nullable|string',
    //     ]);

    //     DB::beginTransaction();

    //     try {
    //         // Cari data kontrak terbaru berdasarkan profiles_id
    //         $latestContract = hr_kontrak::where('profiles_id', $validatedData['profiles_id'])
    //             ->orderBy('created_at', 'desc')
    //             ->first();

    //         if ($latestContract) {
    //             // Update tgl_take_out di tabel hr_kontrak
    //             $latestContract->tgl_take_out = $validatedData['tgl_take_out'];
    //             $latestContract->ket = $validatedData['ket'];
    //             $latestContract->save();
    //         }

    //         // Update status di tabel profiles
    //         $profile = profiles::findOrFail($validatedData['profiles_id']);
    //         $profile->status_aktif = 'tidak aktif';  // Menonaktifkan status aktif
    //         $profile->save();

    //         DB::commit();

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Status dan tanggal take out berhasil disimpan di hr_kontrak.'
    //         ], 200);
    //     } catch (\Exception $e) {
    //         DB::rollback();

    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Gagal menyimpan status dan tanggal take out.',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }
}