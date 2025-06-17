<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\Models\DocList;
use App\Models\DocListD1;
use App\Models\PublicModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class DocListController extends Controller
{
    protected $judul_halaman_notif;
    public function __construct()
    {
        $this->judul_halaman_notif = 'MASTER TEMPLATE';
    }

    public function paging(Request $request): JsonResponse
    {
        $URL = URL::current();
        if (!isset($request->search)) {
            $count = (new DocList)->count();
            $arr_pagination = (new PublicModel())->pagination_without_search($URL, $request->limit, $request->offset);
            $todos = (new DocList())->get_data_($request->search, $arr_pagination);
        } else {
            $arr_pagination = (new PublicModel())->pagination_without_search(
                $URL,
                $request->limit,
                $request->offset,
                $request->search
            );
            $todos = (new DocList())->get_data_($request->search, $arr_pagination);
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
        $user_id = 'USER TEST';

        $data = $this->validate($request, [
            'doc_name' => 'required',
            'doc_desc' => 'required',
            'template_name' => 'required',
            'total_input' => 'required|integer',
            'dynamic_inputs' => 'nullable|array', // Tetap lakukan validasi untuk data dinamis
        ]);

        try {
            // Tambahkan data pengguna
            $data['created_by'] = $user_id;
            $data['updated_by'] = $user_id;

            // Hapus dynamic_inputs dari data yang akan disimpan ke tabel doc_list
            unset($data['dynamic_inputs']);

            // Simpan data utama ke tabel doc_list
            $docList = DocList::create($data);

            // Simpan data dinamis ke tabel doc_list_d1
            if (!empty($request->input('dynamic_inputs'))) {
                foreach ($request->input('dynamic_inputs') as $input) {
                    DocListD1::create([
                        'doc_list_id' => $docList->id,
                        'input_name' => $input['input_name'],
                        'mapping_number' => $input['mapping_number'],
                        'input_type' => $input['input_type'],
                        'created_by' => $user_id,
                        'updated_by' => $user_id,
                    ]);
                }
            }

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
                'status' => false,
                'message' => 'Created Failed',
                'error' => $e->getMessage()
            ], 409);
        }
    }





    public function destroy(int $id)
    {

        DB::beginTransaction();
        $user_id = 'USER TEST';

        try {
            $todo = DocList::findOrFail($id);

            DocList::where('id', $id)->update(['deleted_by' => $user_id]);
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
            $todos = DocList::findOrFail($id);

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
            'doc_name' => 'required',
            'doc_desc' => 'required',
            'template_name' => 'required',
            'total_input' => 'required',
        ]);
        try {
            $todos = DocList::findOrFail($id);
            $todos->fill($data);
            $todos->save();

            DocList::where('id', $id)->update(['updated_by' => $user_id, 'updated_at' => date('Y-m-d H:m:s')]);

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
            // Menggunakan eager loading untuk mengambil data dari doc_list dan doc_list_d1
            $getAllData = DocList::with('dynamicInputs')->get();

            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => 'Data fetched successfully',
                'data' => $getAllData
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 400,
                'status' => false,
                'message' => 'Failed to fetch data',
                'error' => $e->getMessage()
            ], 400);
        }
    }
}