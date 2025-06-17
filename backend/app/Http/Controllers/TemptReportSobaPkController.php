<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\carbon;
use Exception;
use App\Models\TemptReportSobaPk;
use App\Models\TempPosDetail;
use App\Models\PublicModel;
use App\Models\profiles;
use App\Models\hr_cabang;
use App\Models\Users;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class TemptReportSobaPkController extends Controller
{
    protected $judul_halaman_notif;
    public function __construct()
    {
        $this->judul_halaman_notif = 'TEMPT REPORT SOBA PK';
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

    protected function convertMonthNameToNumber($monthName)
    {
        $months = [
            'JANUARI' => '01',
            'FEBRUARI' => '02',
            'MARET' => '03',
            'APRIL' => '04',
            'MEI' => '05',
            'JUNI' => '06',
            'JULI' => '07',
            'AGUSTUS' => '08',
            'SEPTEMBER' => '09',
            'OKTOBER' => '10',
            'NOVEMBER' => '11',
            'DESEMBER' => '12'
        ];

        return $months[strtoupper($monthName)] ?? null;
    }

    protected function calculateKlasifikasi($bb, $tb)
    {
        // Jika bb atau tb null, return null
        if (empty($bb) || empty($tb)) {
            return ['bmi' => null, 'klasifikasi' => null];
        }

        // Konversi ke decimal
        $bbDecimal = floatval(str_replace([',', ' '], '', $bb));
        $tbDecimal = floatval(str_replace([',', ' '], '', $tb)) / 100; // Konversi cm ke meter

        // Hitung BMI
        $bmi = $bbDecimal / ($tbDecimal * $tbDecimal);
        $bmi = number_format($bmi, 2); // Bulatkan ke 2 desimal

        // Tentukan klasifikasi
        if ($bmi < 18.5) {
            return ['bmi' => $bmi, 'klasifikasi' => 'Kurus'];
        } elseif ($bmi >= 18.5 && $bmi < 25) {
            return ['bmi' => $bmi, 'klasifikasi' => 'Normal'];
        } elseif ($bmi >= 25 && $bmi < 30) {
            return ['bmi' => $bmi, 'klasifikasi' => 'Gemuk'];
        } else {
            return ['bmi' => $bmi, 'klasifikasi' => 'Obesitas'];
        }
    }

    public function paging(Request $request): JsonResponse
    {
        $URL = URL::current();
        if (!isset($request->search)) {
            $count = (new TemptReportSobaPk)->count();
            $arr_pagination = (new PublicModel())->pagination_without_search($URL, $request->limit, $request->offset);
            $todos = (new TemptReportSobaPk())->get_data_($request->search, $arr_pagination);
        } else {
            $arr_pagination = (new PublicModel())->pagination_without_search(
                $URL,
                $request->limit,
                $request->offset,
                $request->search
            );
            $todos = (new TemptReportSobaPk())->get_data_($request->search, $arr_pagination);
            $count = $todos->count();
        }

        $data = $todos->map(function ($item) {
            $months = [
                '01' => 'Januari',
                '02' => 'Februari',
                '03' => 'Maret',
                '04' => 'April',
                '05' => 'Mei',
                '06' => 'Juni',
                '07' => 'Juli',
                '08' => 'Agustus',
                '09' => 'September',
                '10' => 'Oktober',
                '11' => 'November',
                '12' => 'Desember'
            ];

            // Konversi angka bulan ke nama bulan untuk tampilan
            if (isset($item->mop)) {
                $item->mop = $months[$item->mop] ?? $item->mop;
            }

            return $item;
        });

        return response()->json(
            (new PublicModel())->array_respon_200_table($todos, $count, $arr_pagination),
            200
        );
    }

    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user_id = $this->getCurrentUser();

            // Log incoming request
            Log::info('Incoming request data:', $request->all());

            // Validasi dengan custom messages untuk Lumen
            $validator = Validator::make($request->all(), [
                'cabang_id' => 'required|string',
                'profiles_id' => 'required|string',
                'mop' => 'required|string',
                'yop' => 'required|string',
                'nama_toko' => 'required|string',
                'channel' => 'required|string',
                'tb' => 'nullable|string',
                'bb' => 'nullable|string',
                'bmi' => 'nullable|string',
                'klasifikasi' => 'nullable|string',
                'pk' => 'nullable|string',
                'target_siba' => 'nullable|string',
                'target_soba' => 'nullable|string',
                'siba' => 'nullable|string',
                'soba' => 'nullable|string',
                'achv_siba' => 'nullable|string',
                'achv_soba' => 'nullable|string',
            ], [
                'cabang_id.required' => 'Cabang harus dipilih',
                'profiles_id.required' => 'Karyawan harus dipilih',
                'mop.required' => 'Bulan harus dipilih',
                'yop.required' => 'Tahun harus dipilih',
                'nama_toko.required' => 'Nama toko harus diisi'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();

            // Hitung BMI dan klasifikasi jika BB dan TB ada
            if (!empty($data['bb']) && !empty($data['tb'])) {
                $result = $this->calculateKlasifikasi($data['bb'], $data['tb']);
                $data['bmi'] = $result['bmi'];
                $data['klasifikasi'] = $result['klasifikasi'];
            }

            // Bersihkan data
            $cleanData = array_map(function ($value) {
                return is_string($value) ? trim($value) : $value;
            }, $data);

            // Tambahkan created_by dan updated_by
            $cleanData['created_by'] = $user_id;
            $cleanData['updated_by'] = $user_id;

            // Log data yang akan disimpan
            Log::info('Data yang akan disimpan:', $cleanData);

            // Simpan data
            $todo = TemptReportSobaPk::create($cleanData);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Data berhasil disimpan',
                'data' => $todo
            ], 201);
        } catch (Exception $e) {
            DB::rollback();
            Log::error('Error saving data:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(int $id)
    {
        DB::beginTransaction();
        $user_id = $this->getCurrentUser();

        try {
            // Cari data di TemptReportSobaPk berdasarkan ID
            $todo = TemptReportSobaPk::findOrFail($id);

            // Cari data terkait di TempPosDetail berdasarkan id yang sama
            $tempPosDetail = TempPosDetail::where('id', $id)->first();

            // Hapus hanya data TempPosDetail yang terkait jika ditemukan
            if ($tempPosDetail) {
                $tempPosDetail->delete();
            }

            // Tandai sebagai dihapus oleh user dan hapus data di TemptReportSobaPk
            $todo->deleted_by = $user_id;
            $todo->save();
            $todo->delete();

            DB::commit();
            return response()->json([
                'code' => 201,
                'status' => true,
                'message' => 'Deleted successfully',
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete: ' . $e->getMessage()
            ], 409);
        }
    }


    public function show(int $id)
    {
        try {
            $todos = TemptReportSobaPk::findOrFail($id);

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
            'cabang_id' => 'nullable|string',
            'profiles_id' => 'nullable|string',
            'mop' => 'nullable|string',
            'yop' => 'nullable|string',
            'nama_toko' => 'nullable|string',
            'channel' => 'nullable|string',
            'tb' => 'nullable|string',
            'bb' => 'nullable|string',
            'bmi' => 'nullable|string',
            'klasifikasi' => 'nullable|string',
            'pk' => 'nullable|string',
            'target_siba' => 'nullable|string',
            'target_soba' => 'nullable|string',
            'siba' => 'nullable|string',
            'soba' => 'nullable|string',
            'achv_siba' => 'nullable|string',
            'achv_soba' => 'nullable|string',
        ]);

        try {
            // Temukan record yang akan diupdate
            $todos = TemptReportSobaPk::findOrFail($id);

            // Konversi dan bersihkan data
            if (isset($data['mop'])) {
                $monthNumber = $this->convertMonthNameToNumber($data['mop']);
                if (!$monthNumber) {
                    $monthNumber = str_pad($data['mop'], 2, '0', STR_PAD_LEFT);
                }
                $data['mop'] = $monthNumber;
            }

            // Bersihkan data dari spasi dan karakter khusus
            $pk = trim(str_replace([',', ' '], '', $data['pk'] ?? $todos->pk ?? '0'));
            $achv_soba = trim(str_replace([',', ' ', '%'], '', $data['achv_soba'] ?? $todos->achv_soba ?? '0'));
            $achv_siba = trim(str_replace([',', ' ', '%'], '', $data['achv_siba'] ?? $todos->achv_siba ?? '0'));

            // Pembersihan dan pengecekan BB dan TB
            $bb = isset($data['bb']) ? trim($data['bb']) : $todos->bb;
            $tb = isset($data['tb']) ? trim($data['tb']) : $todos->tb;

            // Hitung BMI dan klasifikasi jika ada BB dan TB
            if (!empty($bb) && !empty($tb)) {
                $result = $this->calculateKlasifikasi($bb, $tb);
                $bmi = $result['bmi'];
                $klasifikasi = $result['klasifikasi'];
            } else {
                $bmi = null;
                $klasifikasi = null;
            }

            // Persiapkan data yang akan diupdate untuk TemptReportSobaPk
            $data_report = [
                'cabang_id' => trim($data['cabang_id'] ?? $todos->cabang_id),
                'profiles_id' => trim($data['profiles_id'] ?? $todos->profiles_id),
                'mop' => $data['mop'] ?? $todos->mop,
                'yop' => trim($data['yop'] ?? $todos->yop),
                'nama_toko' => trim($data['nama_toko'] ?? $todos->nama_toko),
                'channel' => trim($data['channel'] ?? $todos->channel),
                'tb' => $tb,
                'bb' => $bb,
                'bmi' => $bmi,
                'klasifikasi' => $klasifikasi,
                'pk' => $pk,
                'target_siba' => trim(str_replace([',', ' '], '', $data['target_siba'] ?? $todos->target_siba)),
                'target_soba' => trim(str_replace([',', ' '], '', $data['target_soba'] ?? $todos->target_soba)),
                'siba' => trim(str_replace([',', ' '], '', $data['siba'] ?? $todos->siba)),
                'soba' => trim(str_replace([',', ' '], '', $data['soba'] ?? $todos->soba)),
                'achv_siba' => $achv_siba,
                'achv_soba' => $achv_soba,
                'updated_by' => $user_id,
                'updated_at' => Carbon::now()
            ];

            // Update TemptReportSobaPk
            $todos->update($data_report);

            // Hitung rata-rata untuk result
            $pk_value = floatval($pk);
            $achv_soba_value = floatval($achv_soba);
            $average = ($pk_value + $achv_soba_value) / 2;

            // Tentukan result berdasarkan rata-rata
            $result = 'Tidak Diperpanjang';
            if ($average >= 75) {
                $result = 'Perpanjang';
            } elseif ($average >= 70 && $average <= 74) {
                $result = 'Pertimbangkan';
            }

            // Update atau create TempPosDetail
            $tempPosDetail = TempPosDetail::where('id', $id)->first();

            $data_pos = [
                'id' => $id,
                'cabang_id' => trim($data['cabang_id'] ?? $todos->cabang_id),
                'profiles_id' => trim($data['profiles_id'] ?? $todos->profiles_id),
                'pk' => $pk,
                'achv_siba' => $achv_siba,
                'achv_soba' => $achv_soba,
                'result' => $result,
                'updated_by' => $user_id,
                'updated_at' => Carbon::now()
            ];

            if ($tempPosDetail) {
                $tempPosDetail->update($data_pos);
            } else {
                $data_pos['created_by'] = $user_id;
                $data_pos['created_at'] = Carbon::now();
                TempPosDetail::create($data_pos);
            }

            DB::commit();
            return response()->json([
                'code' => 201,
                'status' => true,
                'message' => 'Updated successfully',
                'data' => $todos
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'code' => 409,
                'status' => false,
                'message' => 'Failed to update: ' . $e->getMessage(),
            ], 409);
        }
    }


    public function getAllData()
    {
        try {
            $getAllData = TemptReportSobaPk::all();
            return response()->json($getAllData, 200);
        } catch (Exception $e) {
            return response()->json([$e->getMessage()], 400);
        }
    }

    public function storeBulky(Request $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            if (!$request->csv) {
                return response()->json([
                    'status' => false,
                    'message' => 'Missing required data'
                ], 400);
            }

            Log::info('CSV Data:', ['data' => $request->csv]);

            $user_id = $this->getCurrentUser();
            $data_csv = json_decode(json_encode($request->csv), true);

            foreach ($data_csv as $value) {
                // Validasi data yang required
                if (!$value['cabang_id'] || !$value['profiles_id']) {
                    throw new \Exception('Required fields (cabang_id, profiles_id) cannot be empty');
                }

                $monthNumber = $this->convertMonthNameToNumber($value['mop']);
                if (!$monthNumber) {
                    $monthNumber = str_pad($value['mop'], 2, '0', STR_PAD_LEFT);
                }

                // Bersihkan data dari spasi dan karakter khusus
                $pk = trim(str_replace([',', ' '], '', $value['pk'] ?? '0'));
                $achv_soba = trim(str_replace([',', ' ', '%'], '', $value['achv_soba'] ?? '0'));
                $achv_siba = trim(str_replace([',', ' ', '%'], '', $value['achv_siba'] ?? '0'));
                $tb = trim($value['tb'] ?? '');
                $bb = trim($value['bb'] ?? '');

                // Hitung BMI dan klasifikasi
                $bmiResult = $this->calculateKlasifikasi($bb, $tb);

                // Data for tempt_report_soba_pk
                $data_report = [
                    'cabang_id' => trim($value['cabang_id']),
                    'profiles_id' => trim($value['profiles_id']),
                    'mop' => $monthNumber,
                    'yop' => trim($value['yop']),
                    'nama_toko' => trim($value['nama_toko'] ?? ''),
                    'channel' => trim($value['channel'] ?? ''),
                    'tb' => $tb,
                    'bb' => $bb,
                    'bmi' => $bmiResult['bmi'],         // Tambahkan nilai BMI
                    'klasifikasi' => $bmiResult['klasifikasi'], // Tambahkan klasifikasi
                    'pk' => $pk,
                    'target_siba' => trim(str_replace([',', ' '], '', $value['target_siba'] ?? '0')),
                    'target_soba' => trim(str_replace([',', ' '], '', $value['target_soba'] ?? '0')),
                    'siba' => trim(str_replace([',', ' '], '', $value['siba'] ?? '0')),
                    'soba' => trim(str_replace([',', ' '], '', $value['soba'] ?? '0')),
                    'achv_siba' => trim(str_replace([',', ' ', '%'], '', $value['achv_siba'] ?? '0')),
                    'achv_soba' => $achv_soba,
                    'created_by' => $user_id,
                    'updated_by' => $user_id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];

                // Log data sebelum insert
                Log::info('Data yang akan diinsert:', ['data_report' => $data_report]);

                // Create record in tempt_report_soba_pk
                $report = TemptReportSobaPk::create($data_report);

                // Parse nilai pk dan achv_soba
                $pk_value = floatval($pk);
                $achv_soba_value = floatval($achv_soba);

                // Hitung rata-rata
                $average = ($pk_value + $achv_soba_value) / 2;

                // Tentukan result berdasarkan rata-rata
                $result = 'Tidak Diperpanjang';
                if ($average >= 75) {
                    $result = 'Perpanjang';
                } elseif ($average >= 70 && $average <= 74) {
                    $result = 'Pertimbangkan';
                }

                // Data for temp_pos_detail
                $data_pos = [
                    'id' => $report->id,
                    'cabang_id' => trim($value['cabang_id']),
                    'profiles_id' => trim($value['profiles_id']),
                    'pk' => $pk,
                    'achv_siba' => $achv_siba,
                    'achv_soba' => $achv_soba,
                    'result' => $result,
                    'created_by' => $user_id,
                    'updated_by' => $user_id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];

                // Create record in temp_pos_detail
                TempPosDetail::create($data_pos);
            }

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Data imported successfully to both tables'
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat import:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Failed to import data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getTempReportSobaPK(Request $request)
    {
        try {
            // Perbaikan: menggunakan model TemptReportSobaPk, bukan controller
            $query = TemptReportSobaPk::query();

            if ($request->has('profiles_id')) {
                $query->where('profiles_id', $request->profiles_id);
            }

            if ($request->has('mop')) {
                $query->where('mop', $request->mop);
            }

            $results = $query->get();

            return response()->json([
                'status' => true,
                'message' => 'Data retrieved successfully',
                'results' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error retrieving data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}