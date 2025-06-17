<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\carbon;
use Exception;
use App\Models\profiles;
use App\Models\ProfilesDetail;
use App\Models\hr_kontrak;
use App\Models\PublicModel;
use App\Rules\CheckContractType;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use App\Models\Users;
use DateTime;


class HrKontrakController extends Controller
{
    protected $judul_halaman_notif;
    public function __construct()
    {
        $this->judul_halaman_notif = 'MASTER KONTRAK';
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
        // Tambahkan filter cabang di sini
        $count = (new hr_kontrak);
        if ($request->has('cabang_id')) {
            $count = $count->where('cabang_id', $request->cabang_id);
        }
        $count = $count->count();
        
        $arr_pagination = (new PublicModel())->pagination_without_search($URL, $request->limit, $request->offset);
        $todos = (new hr_kontrak())->get_data_($request->search, $arr_pagination, $request->cabang_id); // Tambahkan parameter cabang_id
    } else {
        $arr_pagination = (new PublicModel())->pagination_without_search(
            $URL,
            $request->limit,
            $request->offset,
            $request->search
        );
        $todos = (new hr_kontrak())->get_data_($request->search, $arr_pagination, $request->cabang_id); // Tambahkan parameter cabang_id
        $count = $todos->count();
    }

    // Tambahkan log untuk debugging
    Log::info('Filter Query:', [
        'cabang_id' => $request->cabang_id,
        'count' => $count,
    ]);

    return response()->json(
        (new PublicModel())->array_respon_200_table($todos, $count, $arr_pagination),
        200
    );
}

    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        $user_id = $this->getCurrentUser();

        // Validasi input
        $data = $this->validate($request, [
            'profiles_id' => 'required',
            'cabang_id' => 'required',
            'no_kontrak' => 'required|unique:hr_kontrak,no_kontrak',
            'tipe' => 'required',
            'tgl_masuk' => 'required|date_format:Y-m-d',
            'tgl_keluar' => 'required|date_format:Y-m-d',
            'tahun' => 'required|integer',
            'bulan' => 'required|integer',
            'hari' => 'required|integer',
            'tgl_take_out' => 'nullable',
            'tgl_make_kontrak' => 'nullable|date',
            'ket' => 'nullable',
            'ket_take_out' => 'nullable',
        ]);

        try {
            // Simpan data ke tabel hr_kontrak
            $data['created_by'] = $user_id;
            $data['updated_by'] = $user_id;

            // Periksa tipe kontrak
            if ($data['tipe'] == 'PKWT') {
                // Jika tipe adalah 'PKWT', simpan juga ke tabel profiles_detail
                $totalKontrak = hr_kontrak::where('profiles_id', $data['profiles_id'])->count();

                ProfilesDetail::create([
                    'profiles_id' => $data['profiles_id'],
                    'tahun' => $data['tahun'],
                    'bulan' => $data['bulan'],
                    'hari' => $data['hari'],
                    'masa_kerja_ke' => $totalKontrak,
                    'sts_take_out' => '',
                    'ket' => '',
                    'created_by' => $user_id,
                    'updated_by' => $user_id,
                ]);
                Log::info('Data berhasil disimpan ke tabel profiles_detail untuk tipe PKWT.');
            } else {
                // Jika tipe adalah 'Magang', hanya simpan ke hr_kontrak
                Log::info('Data tipe Magang hanya disimpan ke hr_kontrak, tidak disimpan ke profiles_detail.');
            }
            $hrKontrak = hr_kontrak::create($data);
            DB::commit();
            return response()->json([
                'code' => 201,
                'status' => true,
                'message' => 'Created Successfully.',
            ], 201);
        } catch (\Exception $e) {
            // Rollback transaksi jika ada kesalahan
            DB::rollback();
            return response()->json([
                'code' => 409,
                'status' => false,
                'message' => 'Created Failed',
                'error' => $e->getMessage(),
            ], 409);
        }
    }




    public function destroy(int $id)
    {

        DB::beginTransaction();
        $user_id = $this->getCurrentUser();

        try {
            $todo = hr_kontrak::findOrFail($id);

            hr_kontrak::where('id', $id)->update(['deleted_by' => $user_id]);
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
            $todos = hr_kontrak::findOrFail($id);

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
            'profiles_id' => 'required',
            'cabang_id' => 'required',
            'no_kontrak' => 'required',
            'tipe' => 'required',
            'tgl_masuk' => 'required',
            'tgl_keluar' => 'required',
            'tahun' => 'required',
            'bulan' => 'required',
            'hari' => 'required',
            'tgl_take_out' => 'nullable',
            'tgl_make_kontrak' => 'nullable|date', 
            'ket' => 'required',
            'ket_take_out' => 'nullable',
        ]);
        try {
            $todos = hr_kontrak::findOrFail($id);
            $todos->fill($data);
            $todos->save();

            hr_kontrak::where('id', $id)->update(['updated_by' => $user_id, 'updated_at' => date('Y-m-d H:m:s')]);

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
            $getAllData = hr_kontrak::all();
            return response()->json($getAllData, 200);
        } catch (Exception $e) {
            return response()->json([$e->getMessage()], 400);
        }
    }

    public function getLastContractNumber(Request $request): JsonResponse
    {
        // Ambil temp_profile_id dan tipe dari request
        $profileId = $request->input('temp_profile_id');  // Menggunakan temp_profile_id sebagai gantinya
        $tipe = $request->input('tipe', 'PKWT'); // Default ke PKWT jika tipe tidak diberikan

        // Validasi apakah temp_profile_id disediakan
        if (!$profileId) {
            return response()->json([
                'error' => 'temp_profile_id is required'
            ], 400);
        }

        // Cari profile_name berdasarkan temp_profile_id
        $profile = DB::table('profiles')
            ->where('id', $profileId)
            ->first();

        if (!$profile) {
            return response()->json([
                'error' => 'Profile not found'
            ], 404);
        }

        // Query untuk mendapatkan kontrak terakhir menggunakan profile_name sebagai profiles_id
        $lastContract = DB::table('hr_kontrak')
            ->where('profiles_id', $profile->profile_name)
            ->where('tipe', $tipe)
            ->orderBy('id', 'desc')
            ->first();

        // Hitung jumlah kontrak PKWT yang sudah ada untuk profile ini
        $count = DB::table('hr_kontrak')
            ->where('profiles_id', $profile->profile_name)
            ->where('tipe', $tipe)
            ->count();

        return response()->json([
            'profiles_id' => $profile->profile_name,
            'temp_profile_id' => $profileId,
            'tipe' => $tipe,
            'last_contract' => $lastContract,
            'count' => $count + 1
        ]);
    }

    public function getLastExitDate(Request $request)
    {
        $profileId = $request->input('profiles_id');

        // Ambil kontrak terakhir berdasarkan profile_id
        $lastContract = DB::table('hr_kontrak')
            ->where('profiles_id', $profileId)
            ->orderBy('tgl_keluar', 'desc')
            ->first();

        // Jika ada kontrak terakhir, kirimkan tanggal keluar terakhir
        if ($lastContract) {
            return response()->json(['tgl_keluar' => $lastContract->tgl_keluar]);
        }

        // Jika tidak ada kontrak, kirimkan respons kosong
        return response()->json(['tgl_keluar' => null]);
    }

    // Controller di HrKontrakController.php
    public function getTotalContractYears(Request $request)
    {
        $profileId = $request->query('profiles_id');

        // Ambil total tahun dan bulan dari kontrak
        $totalYears = hr_kontrak::where('profiles_id', $profileId)->sum('tahun');
        $totalMonths = hr_kontrak::where('profiles_id', $profileId)->sum('bulan');

        // Konversi bulan menjadi tahun jika total bulan mencapai atau melebihi 12
        $additionalYears = intdiv($totalMonths, 12);
        $remainingMonths = $totalMonths % 12;

        // Tambahkan tahun tambahan ke total tahun
        $totalYears += $additionalYears;

        return response()->json([
            'totalYears' => $totalYears,
            'remainingMonths' => $remainingMonths
        ]);
    }

    public function checkExpiringContracts()
    {
        try {
            $today = Carbon::now()->startOfDay();
            $oneMonthFromNow = Carbon::now()->addDays(30)->endOfDay();

            // Dapatkan semua kontrak yang akan berakhir dalam 30 hari
            $expiringContracts = DB::table('hr_kontrak as k')
                ->join('profiles as p', 'k.profiles_id', '=', 'p.profile_name')
                ->whereNull('k.deleted_at')
                ->whereBetween('k.tgl_keluar', [$today, $oneMonthFromNow])
                ->where(function ($query) {
                    $query->whereNotExists(function ($q) {
                        $q->select(DB::raw(1))
                            ->from('hr_kontrak as k2')
                            ->whereRaw('k2.profiles_id = k.profiles_id')
                            ->whereRaw('k2.tgl_keluar > k.tgl_keluar')
                            ->whereNull('k2.deleted_at');
                    });
                })
                ->select([
                    'k.id',
                    'k.profiles_id',
                    'k.cabang_id',
                    'k.no_kontrak',
                    'k.tipe',
                    'k.tgl_masuk',
                    'k.tgl_keluar',
                    'p.jabatan_id'
                ])
                ->get();

            return response()->json($expiringContracts);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function storeBulky(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user_id = $this->getCurrentUser();
            $data_csv = json_decode(json_encode($request->csv), true);

            if (empty($data_csv)) {
                return response()->json([
                    'code' => 400,
                    'status' => false,
                    'message' => 'CSV data is empty'
                ], 400);
            }

            // Process each row
            foreach ($data_csv as $index => $row) {
                try {
                    // Konversi format tanggal dari m/d/Y ke Y-m-d
                    $tgl_masuk = DateTime::createFromFormat('m/d/Y', $row['tgl_masuk']);
                    $tgl_keluar = DateTime::createFromFormat('m/d/Y', $row['tgl_keluar']);

                    if (!$tgl_masuk || !$tgl_keluar) {
                        throw new \Exception("Format tanggal tidak valid pada baris " . ($index + 1));
                    }

                    $tgl_masuk = $tgl_masuk->format('Y-m-d');
                    $tgl_keluar = $tgl_keluar->format('Y-m-d');

                    // Pastikan nilai numerik dengan default 0 jika null
                    $tahun = is_numeric($row['tahun']) ? (int)$row['tahun'] : 0;
                    $bulan = is_numeric($row['bulan']) ? (int)$row['bulan'] : 0;
                    $hari = is_numeric($row['hari']) ? (int)$row['hari'] : 0;

                    // Validasi data required
                    if (empty(trim($row['profiles_id'])) || empty(trim($row['cabang_id'])) || empty(trim($row['no_kontrak']))) {
                        throw new \Exception("profiles_id, cabang_id, dan no_kontrak tidak boleh kosong pada baris " . ($index + 1));
                    }

                    $kontrakData = [
                        'profiles_id' => trim($row['profiles_id']),
                        'cabang_id' => trim($row['cabang_id']),
                        'no_kontrak' => trim($row['no_kontrak']),
                        'tipe' => strtoupper(trim($row['tipe'] ?? 'PKWT')), // Default ke PKWT jika kosong
                        'tgl_masuk' => $tgl_masuk,
                        'tgl_keluar' => $tgl_keluar, // Menggunakan tanggal yang sudah dikonversi
                        'tahun' => $tahun,
                        'bulan' => $bulan,
                        'hari' => $hari,
                        'ket' => $row['ket'] ?? '',
                        'created_by' => $user_id,
                        'updated_by' => $user_id
                    ];

                    // Create kontrak
                    $hrKontrak = hr_kontrak::create($kontrakData);

                    // If PKWT, create profiles_detail
                    if ($kontrakData['tipe'] === 'PKWT') {
                        $totalKontrak = hr_kontrak::where('profiles_id', $kontrakData['profiles_id'])->count();

                        ProfilesDetail::create([
                            'profiles_id' => $kontrakData['profiles_id'],
                            'tahun' => $tahun,
                            'bulan' => $bulan,
                            'hari' => $hari,
                            'masa_kerja_ke' => $totalKontrak,
                            'ket' => $row['ket'] ?? 'N/A',
                            'created_by' => $user_id,
                            'updated_by' => $user_id,
                            'updated_at' => Carbon::now(),
                            'created_at' => Carbon::now()
                        ]);
                    }
                } catch (\Exception $e) {
                    throw new \Exception("Error processing row " . ($index + 1) . ": " . $e->getMessage());
                }
            }

            DB::commit();
            return response()->json([
                'code' => 201,
                'status' => true,
                'message' => 'Data kontrak berhasil diimport',
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'code' => 409,
                'status' => false,
                'message' => 'Gagal import data kontrak: ' . $e->getMessage(),
            ], 409);
        }
    }
}