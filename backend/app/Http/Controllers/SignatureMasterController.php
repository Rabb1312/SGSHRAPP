<?php

namespace App\Http\Controllers;

use App\Models\SignatureMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PublicModel;
use Illuminate\Support\Facades\URL;

class SignatureMasterController extends Controller
{
    public function index(Request $request)
    {
        try {
            $URL = URL::current();

            if (!isset($request->search)) {
                $count = SignatureMaster::count();
                $arr_pagination = (new PublicModel())->pagination_without_search($URL, $request->limit, $request->offset);

                $results = SignatureMaster::whereNull('deleted_at')
                    ->orderBy('id', 'ASC')
                    ->offset($arr_pagination['offset'])
                    ->limit($arr_pagination['limit'])
                    ->get();
            } else {
                $arr_pagination = (new PublicModel())->pagination_without_search(
                    $URL,
                    $request->limit,
                    $request->offset,
                    $request->search
                );

                $results = SignatureMaster::whereNull('deleted_at')
                    ->where(function ($query) use ($request) {
                        $query->where('name', 'LIKE', '%' . $request->search . '%')
                            ->orWhere('position', 'LIKE', '%' . $request->search . '%');
                    })
                    ->orderBy('id', 'ASC')
                    ->offset($arr_pagination['offset'])
                    ->limit($arr_pagination['limit'])
                    ->get();

                $count = $results->count();
            }

            return response()->json([
                'code' => 200,
                'status' => true,
                'count' => $count,
                'results' => $results,
                'nomorBaris' => $arr_pagination['nomorBaris'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'position' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            // If is_active is true, deactivate all other signatures
            if ($request->is_active) {
                SignatureMaster::where('is_active', true)
                    ->update(['is_active' => false]);
            }

            $signature = SignatureMaster::create([
                'name' => $request->name,
                'position' => $request->position,
                'is_active' => $request->is_active ?? false,
                'created_by' => $request->userid,
                'updated_by' => $request->userid,
            ]);

            DB::commit();
            return response()->json([
                'message' => 'Signature created successfully',
                'data' => $signature
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Error creating signature',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SignatureMaster  $signatureMaster
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $signature = SignatureMaster::findOrFail($id);
            return response()->json($signature);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Signature not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SignatureMaster  $signatureMaster
     * @return \Illuminate\Http\Response
     */
    public function edit(SignatureMaster $signatureMaster)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SignatureMaster  $signatureMaster
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
{
    // Jika hanya update status aktif
    if (isset($request->is_active) && count($request->all()) === 2) { // 2 karena ada userid juga
        DB::beginTransaction();
        try {
            $signature = SignatureMaster::findOrFail($id);

            // If setting this signature as active, deactivate others
            if ($request->is_active) {
                SignatureMaster::where('id', '!=', $id)
                    ->where('is_active', true)
                    ->update(['is_active' => false]);
            }

            $signature->update([
                'is_active' => $request->is_active,
                'updated_by' => $request->userid,
            ]);

            DB::commit();
            return response()->json([
                'message' => 'Status updated successfully',
                'data' => $signature
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Error updating status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Untuk update lengkap
    $this->validate($request, [
        'name' => 'required|string',
        'position' => 'required|string',
    ]);

    DB::beginTransaction();
    try {
        $signature = SignatureMaster::findOrFail($id);

        // If setting this signature as active, deactivate others
        if ($request->is_active) {
            SignatureMaster::where('id', '!=', $id)
                ->where('is_active', true)
                ->update(['is_active' => false]);
        }

        $signature->update([
            'name' => $request->name,
            'position' => $request->position,
            'is_active' => $request->is_active ?? false,
            'updated_by' => $request->userid,
        ]);

        DB::commit();
        return response()->json([
            'message' => 'Signature updated successfully',
            'data' => $signature
        ]);
    } catch (\Exception $e) {
        DB::rollback();
        return response()->json([
            'message' => 'Error updating signature',
            'error' => $e->getMessage()
        ], 500);
    }
}

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SignatureMaster  $signatureMaster
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            $signature = SignatureMaster::findOrFail($id);
            $signature->update(['deleted_by' => $request->userid]);
            $signature->delete();

            return response()->json([
                'message' => 'Signature deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting signature',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getActive()
    {
        $signature = SignatureMaster::where('is_active', true)->first();
        return response()->json($signature);
    }
}