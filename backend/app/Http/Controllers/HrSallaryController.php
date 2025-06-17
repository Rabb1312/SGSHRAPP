<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\carbon;
use Exception;

use App\Models\Users;
use App\Models\hr_sallary;
use App\Models\PublicModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;


class HrSallaryController extends Controller
{
    protected $judul_halaman_notif;
    public function __construct()
    {
        $this->judul_halaman_notif = 'MASTER SALLARY';
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
            $count = (new hr_sallary)->count();
            $arr_pagination = (new PublicModel())->pagination_without_search($URL, $request->limit, $request->offset);
            $todos = (new hr_sallary())->get_data_($request->search, $arr_pagination);
        } else {
            $arr_pagination = (new PublicModel())->pagination_without_search(
                $URL,
                $request->limit,
                $request->offset,
                $request->search
            );
            $todos = (new hr_sallary())->get_data_($request->search, $arr_pagination);
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
            'cabang_id' => 'required',
            'sallary' => 'required',
            'tahun' => 'required',
        ]);
        try {
            $data['created_by'] = $user_id;
            $data['updated_by'] = $user_id;
            $todos = hr_sallary::create($data);

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
            $todo = hr_sallary::findOrFail($id);

            hr_sallary::where('id', $id)->update(['deleted_by' => $user_id]);
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
            $todos = hr_sallary::findOrFail($id);

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
        $user_id = $this->getCurrentUser();
        $data = $this->validate($request, [
            'cabang_id' => 'required',
            'sallary' => 'required',
            'tahun' => 'required',
        ]);
        try {
            $todos = hr_sallary::findOrFail($id);
            $todos->fill($data);
            $todos->save();

            hr_sallary::where('id', $id)->update(['updated_by' => $user_id, 'updated_at' => date('Y-m-d H:m:s')]);

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
            $getAllData = hr_sallary::all();
            return response()->json($getAllData, 200);
        } catch (Exception $e) {
            return response()->json([$e->getMessage()], 400);
        }
    }
}