<?php

namespace App\Http\Controllers;

use App\Models\TempPosDetail;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\TemptReportSobaPk;
use App\Models\BobotPkSoba;
use Illuminate\Support\Facades\Log;
use Carbon\carbon;




class TempPosDetailController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TempPosDetail  $tempPosDetail
     * @return \Illuminate\Http\Response
     */
    public function show(TempPosDetail $tempPosDetail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TempPosDetail  $tempPosDetail
     * @return \Illuminate\Http\Response
     */
    public function edit(TempPosDetail $tempPosDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TempPosDetail  $tempPosDetail
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TempPosDetail $tempPosDetail)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TempPosDetail  $tempPosDetail
     * @return \Illuminate\Http\Response
     */
    public function destroy(TempPosDetail $tempPosDetail)
    {
        //
    }

    public function getDetails(Request $request): JsonResponse
    {
        try {
            $model = new TempPosDetail();
            $data = $model->getAverageCalculation();

            // Get active bobot
            $activeBobot = BobotPkSoba::getActiveBobot();
            $bobotInfo = $activeBobot ?
                "PK: {$activeBobot->bobot_pk}% | SOBA: {$activeBobot->bobot_soba}%" :
                "Default (40:60)";

            // Pagination
            $limit = $request->input('limit', 10);
            $offset = $request->input('offset', 0);
            $total = count($data);

            // Slice data for pagination
            $paginatedData = array_slice($data, $offset, $limit);

            // Map data untuk format yang sesuai dengan grid
            $formattedData = array_map(function ($item) use ($bobotInfo) {  // Tambahkan use ($bobotInfo) di sini
                $bbTbInfo = $item->latest_bb && $item->latest_tb ?
                    "BB: " . $item->latest_bb .
                    " / TB: " . $item->latest_tb : 'N/A';

                // Format angka dengan 2 desimal jika ada nilai desimal
                $avgResult = isset($item->avg_result) ?
                    (floor($item->avg_result) == $item->avg_result ?
                        number_format($item->avg_result, 0) :
                        number_format($item->avg_result, 2)) :
                    'N/A';

                return [
                    'id' => $item->id ?? '',
                    'cabang_id' => $item->cabang_id ?? '',
                    'profiles_id' => $item->profiles_id ?? '',
                    'pk' => isset($item->final_pk) ? round($item->final_pk) : 0,
                    'achv_siba' => isset($item->final_siba) ? round($item->final_siba) : 0,
                    'achv_soba' => isset($item->final_soba) ? round($item->final_soba) : 0,
                    'result' => $item->final_result ?? 'N/A',
                    'kriteria' => $item->kriteria . " (" . $bbTbInfo . ")",
                    'avg_result' => $avgResult,
                    'bobot_info' => $bobotInfo  // Sekarang bisa menggunakan $bobotInfo
                ];
            }, $paginatedData);

            return response()->json([
                'status' => true,
                'code' => 200,
                'results' => $formattedData,
                'count' => $total,
                'nomorBaris' => $offset + 1,
                'bobot_info' => $bobotInfo
            ]);
        } catch (\Exception $e) {
            Log::error("Error in getDetails:", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch data: ' . $e->getMessage()
            ], 500);
        }
    }
    public function getResultByProfileId($profileId): JsonResponse
    {
        try {
            $result = TempPosDetail::where('profiles_id', $profileId)
                ->whereNull('deleted_at')
                ->select('result')
                ->first();

            return response()->json([
                'status' => true,
                'result' => $result ? $result->result : null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch result: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteByProfileId($profileId): JsonResponse
    {
        try {
            // Decode URL-encoded profile_id
            $decodedProfileId = urldecode($profileId);

            // Soft delete dari temp_pos_detail
            TempPosDetail::where('profiles_id', $decodedProfileId)
                ->update(['deleted_at' => Carbon::now()]);

            // Soft delete dari tempt_report_soba_pk
            TemptReportSobaPk::where('profiles_id', $decodedProfileId)
                ->update(['deleted_at' => Carbon::now()]);

            return response()->json([
                'status' => true,
                'message' => 'Data berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error("Error in deleteByProfileId:", [
                'profileId' => $profileId,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }
}