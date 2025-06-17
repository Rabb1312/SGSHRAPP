<?php

namespace App\Http\Controllers;

use App\Models\hr_rapor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HrRaporController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->input('limit', 10);
        $offset = $request->input('offset', 0);
        $search = $request->input('search', '');

        $query = hr_rapor::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('profiles_id', 'LIKE', "%$search%")
                    ->orWhere('grooming', 'LIKE', "%$search%")
                    ->orWhere('pk', 'LIKE', "%$search%")
                    ->orWhere('soba', 'LIKE', "%$search%")
                    ->orWhere('disiplin', 'LIKE', "%$search%")
                    ->orWhere('admin', 'LIKE', "%$search%")
                    ->orWhere('mop', 'LIKE', "%$search%")
                    ->orWhere('yop', 'LIKE', "%$search%");
            });
        }

        $total = $query->count();
        $results = $query->skip($offset)->take($limit)->get();

        return response()->json([
            'count' => $total,
            'results' => $results,
            'nomorBaris' => $offset
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'profiles_id' => 'required',
            'grooming' => 'nullable',
            'pk' => 'nullable',
            'soba' => 'nullable',
            'disiplin' => 'nullable',
            'admin' => 'nullable',
            'mop' => 'required',
            'yop' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->all();
        $data['created_by'] = $request->input('userid');

        $hrRapor = hr_rapor::create($data);

        return response()->json([
            'message' => 'Data created successfully',
            'data' => $hrRapor
        ], 201);
    }

    public function show($id)
    {
        $hrRapor = hr_rapor::find($id);

        if (!$hrRapor) {
            return response()->json(['message' => 'Data not found'], 404);
        }

        return response()->json([
            'data' => $hrRapor
        ]);
    }

    public function update(Request $request, $id)
    {
        $hrRapor = hr_rapor::find($id);

        if (!$hrRapor) {
            return response()->json(['message' => 'Data not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'profiles_id' => 'required',
            'grooming' => 'nullable',
            'pk' => 'nullable',
            'soba' => 'nullable',
            'disiplin' => 'nullable',
            'admin' => 'nullable',
            'mop' => 'required',
            'yop' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->all();
        $data['updated_by'] = $request->input('userid');

        $hrRapor->update($data);

        return response()->json([
            'message' => 'Data updated successfully',
            'data' => $hrRapor
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $hrRapor = hr_rapor::find($id);

        if (!$hrRapor) {
            return response()->json(['message' => 'Data not found'], 404);
        }

        $hrRapor->deleted_by = $request->input('userid');
        $hrRapor->save();

        $hrRapor->delete();

        return response()->json([
            'message' => 'Data deleted successfully'
        ]);
    }
}