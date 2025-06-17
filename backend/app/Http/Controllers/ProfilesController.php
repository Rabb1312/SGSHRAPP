<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\carbon;
use Exception;

use App\Models\hr_bpjs;
use App\Models\hr_rek_karyawan;
use App\Models\hr_npwp;
use App\Models\profiles;
use App\Models\PublicModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use App\Models\Users;

class ProfilesController extends Controller
{
    protected $judul_halaman_notif;
    public function __construct()
    {
        $this->judul_halaman_notif = 'MASTER PROFILE';
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

        $query = DB::table('profiles as p')
            ->select(
                'p.*',
                'n.npwp_no',
                'n.npwp_address',
                'b.bpjs_no',
                'b.bpjs_ket',
                'r.bank_id',
                'r.karyawan_rek',
                'r.karyawan_rek_kcp',
                'r.karyawan_name_rek'
            )
            ->leftJoin('hr_npwp as n', 'n.profiles_id', '=', 'p.profile_name')
            ->leftJoin('hr_bpjs as b', 'b.profiles_id', '=', 'p.profile_name')
            ->leftJoin('hr_rek_karyawan as r', 'r.profiles_id', '=', 'p.profile_name')
            ->whereNull('p.deleted_at');

             // Tambahkan filter cabang_id
    if ($request->has('cabang_id')) {
        $query->where('p.cabang_id', $request->cabang_id);
    }

        if (!empty($request->search)) {
            $search = strtolower($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(p.profile_name) LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('LOWER(p.users_id) LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('LOWER(n.npwp_no) LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('LOWER(b.bpjs_no) LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('LOWER(r.karyawan_rek) LIKE ?', ['%' . $search . '%']);
            });
        }

        
        
        $count = $query->count();
        $todos = $query->offset($request->offset)
            ->limit($request->limit)
            ->get();

 // Untuk debugging
 Log::info('Filter Query:', [
    'cabang_id' => $request->cabang_id,
    'count' => $count,
    'sql' => $query->toSql(),
    'bindings' => $query->getBindings()
]);

            
        return response()->json([
            'results' => $todos,
            'count' => $count,
            'nomorBaris' => (int)$request->offset + 1
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        $user_id = $this->getCurrentUser();
        $data = $this->validate($request, [
            // 'users_id' => 'required',
            'cabang_id' => 'required',
            'kode_cabang' => 'nullable',
            'kode_lokasi' => 'nullable',
            'jabatan_id' => 'required',
            'unit_id' => 'required',
            'nik' => 'nullable',
            'profile_name' => 'required',
            'profile_gender' => 'required',
            'profile_address' => 'required',
            'postalcode' => 'required',
            'email' => 'required',
            'profile_phones' => 'required|unique:profiles,profile_phones',
            'profile_phones2' => 'nullable',
            'identity_number' => 'required',
            'birthplace' => 'required',
            'birthdate' => 'required',
            'marital_status' => 'required',
            'nama_ibu' => 'required',
            'nama_ayah' => 'nullable',
            'nama_pasangan' => 'nullable',
            'nama_anak' => 'nullable',
            'blood_type' => 'required',
            'join_date' => 'required',
            'religion' => 'required',
            'education_status' => 'required',
            'is_active' => 'required',
        ]);
        try {
            $data['created_by'] = $user_id;
            $data['updated_by'] = $user_id;
            $todos = profiles::create($data);

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
            $todo = profiles::findOrFail($id);

            profiles::where('id', $id)->update(['deleted_by' => $user_id]);
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
            $todos = profiles::findOrFail($id);

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
            // 'users_id' => 'required',
            'cabang_id' => 'required',
            'kode_cabang' => 'nullable',
            'kode_lokasi' => 'nullable',
            'jabatan_id' => 'required',
            'unit_id' => 'required',
            'nik' => 'nullable',
            'profile_name' => 'required',
            'profile_gender' => 'required',
            'profile_address' => 'required',
            'postalcode' => 'required',
            'email' => 'required',
            'profile_phones' => 'required',
            'profile_phones2' => 'nullable',
            'identity_number' => 'required',
            'birthplace' => 'required',
            'birthdate' => 'required',
            'marital_status' => 'required',
            'nama_ibu' => 'required',
            'nama_ayah' => 'nullable',
            'nama_pasangan' => 'nullable',
            'nama_anak' => 'nullable',
            'blood_type' => 'required',
            'join_date' => 'required',
            'religion' => 'required',
            'education_status' => 'required',
            'is_active' => 'required',
        ]);
        try {
            $todos = profiles::findOrFail($id);
            $todos->fill($data);
            $todos->save();

            profiles::where('id', $id)->update(['updated_by' => $user_id, 'updated_at' => date('Y-m-d H:m:s')]);

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
            $getAllData = profiles::all();
            return response()->json($getAllData, 200);
        } catch (Exception $e) {
            return response()->json([$e->getMessage()], 400);
        }
    }

    public function getLastNIK($cabang_id)
    {
        try {
            // Query untuk mengambil NIK terakhir berdasarkan cabang_id
            $lastNIK = DB::table('profiles')
                ->where('cabang_id', $cabang_id)
                ->orderBy('nik', 'desc')
                ->first();

            if ($lastNIK) {
                // Mengambil 5 digit terakhir dari NIK sebagai urutan
                $lastUrutan = substr($lastNIK->nik, -2);
                return response()->json(['nik' => $lastNIK->nik], 200);
            } else {
                return response()->json(['nik' => null], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan'], 500);
        }
    }

    // ProfileController
    public function getProfileId($profileName)
    {
        $profile = profiles::where('profile_name', $profileName)->first();
        if ($profile) {
            return response()->json(['id' => $profile->id]);
        }
        return response()->json(['message' => 'Profile tidak ditemukan'], 404);
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

            // Validate required fields for each row
            foreach ($data_csv as $index => $row) {
                $requiredFields = [
                    'users_id',
                    'cabang_id',
                    'kode_cabang',
                    'kode_lokasi',
                    'jabatan_id',
                    'unit_id',
                    'profile_name',
                    'profile_gender',
                    'profile_address',
                    'postalcode',
                    'email',
                    'profile_phones',
                    'identity_number',
                    'birthplace',
                    'birthdate',
                    'marital_status',
                    'nama_ibu',
                    'blood_type',
                    'join_date',
                    'religion',
                    'education_status',
                    'is_active'
                ];
                $optionalFields = [
                    'kode_cabang',
                    'kode_lokasi',
                    'profile_phones2',
                    'nama_ayah',
                    'nama_pasangan',
                    'nama_anak'
                ];
                $row['is_active'] = trim($row['is_active']);

                foreach ($requiredFields as $field) {
                    if (empty(trim($row[$field] ?? ''))) {
                        throw new \Exception("Field '$field' is required in row " . ($index + 1));
                    }
                }

                foreach ($optionalFields as $field) {
                    if (!isset($row[$field])) {
                        $row[$field] = null;
                    }
                }

                // Clean up is_active value
                $is_active = strtoupper(trim($row['is_active']));
                if (!in_array($is_active, ['AKTIF', ' AKTIF ', 'TIDAK AKTIF', ' TIDAK AKTIF '])) {
                    throw new \Exception("Invalid is_active value '{$is_active}' in row " . ($index + 1) . ". Must be 'Aktif' or 'Tidak Aktif'");
                }
            }

            // Process each row
            foreach ($data_csv as $key => $value) {
                try {
                    // Clean and normalize is_active value
                    $is_active = strtoupper(trim($value['is_active']));
                    $is_active = str_contains($is_active, 'AKTIF') ?
                        (str_contains($is_active, 'TIDAK') ? 'Tidak Aktif' : 'Aktif') :
                        'Tidak Aktif';

                    // Validate unique phone number
                    $existingPhone = profiles::where('profile_phones', $value['profile_phones'])
                        ->whereNull('deleted_at')
                        ->first();

                    if ($existingPhone) {
                        throw new \Exception("Phone number {$value['profile_phones']} already exists");
                    }

                    // Format identity number dari scientific notation ke string
                    $identityNumber = is_numeric($value['identity_number'])
                        ? (string) number_format($value['identity_number'], 0, '.', '')
                        : $value['identity_number'];

                    // Prepare profile data
                    $profileData = [
                        'users_id' => strtoupper(trim($value['users_id'])),
                        'cabang_id' => strtoupper(trim($value['cabang_id'])),
                        'kode_cabang' => trim($value['kode_cabang'] ?? ''),
                        'kode_lokasi' => trim($value['kode_lokasi'] ?? ''),
                        'jabatan_id' => strtoupper(trim($value['jabatan_id'])),
                        'unit_id' => strtoupper(trim($value['unit_id'])),
                        'nik' => trim($value['nik']),
                        'profile_name' => strtoupper(trim($value['profile_name'])),
                        'profile_gender' => strtoupper(trim($value['profile_gender'])),
                        'profile_address' => strtoupper(trim($value['profile_address'])),
                        'postalcode' => trim($value['postalcode']),
                        'email' => strtolower(trim($value['email'])), // email lowercase
                        'profile_phones' => trim($value['profile_phones']),
                        'profile_phones2' => null,
                        'identity_number' => $identityNumber,
                        'birthplace' => strtoupper(trim($value['birthplace'])),
                        'birthdate' => trim($value['birthdate']),
                        'marital_status' => strtoupper(trim($value['marital_status'])),
                        'nama_ayah' => isset($value['nama_ayah']) ? strtoupper(trim($value['nama_ayah'])) : null,
                        'nama_ibu' => strtoupper(trim($value['nama_ibu'])),
                        'nama_pasangan' => isset($value['nama_pasangan']) ? strtoupper(trim($value['nama_pasangan'])) : null,
                        'nama_anak' => isset($value['nama_anak']) ? strtoupper(trim($value['nama_anak'])) : null,
                        'blood_type' => strtoupper(trim($value['blood_type'])),
                        'join_date' => trim($value['join_date']),
                        'religion' => strtoupper(trim($value['religion'])),
                        'education_status' => strtoupper(trim($value['education_status'])),
                        'is_active' => $is_active,
                        'created_by' => $user_id,
                        'updated_by' => $user_id,
                    ];

                    // Create profile
                    $profile = profiles::create($profileData);

                    // Create NPWP data if provided
                    if (!empty($value['npwp_no']) || !empty($value['npwp_address'])) {
                        hr_npwp::create([
                            'profiles_id' => $profile->profile_name,
                            'npwp_no' => trim($value['npwp_no'] ?? ''),
                            'npwp_address' => strtoupper(trim($value['npwp_address'] ?? '')),
                            'created_by' => $user_id,
                        ]);
                    }

                    // Create BPJS data if provided
                    if (!empty($value['bpjs_no']) || !empty($value['bpjs_ket'])) {
                        hr_bpjs::create([
                            'profiles_id' => $profile->profile_name,
                            'bpjs_no' => trim($value['bpjs_no'] ?? ''),
                            'bpjs_ket' => strtoupper(trim($value['bpjs_ket'] ?? '')),
                            'created_by' => $user_id,
                        ]);
                    }

                    // Create bank account data if provided
                    if (!empty($value['karyawan_rek']) || !empty($value['karyawan_rek_kcp'])) {
                        hr_rek_karyawan::create([
                            'profiles_id' => $profile->profile_name,
                            'bank_id' => strtoupper(trim($value['bank_id'] ?? '')),
                            'karyawan_rek' => trim($value['karyawan_rek'] ?? ''),
                            'karyawan_rek_kcp' => trim($value['karyawan_rek_kcp'] ?? ''),
                            'karyawan_name_rek' => strtoupper(trim($value['karyawan_name_rek'] ?? '')),
                            'created_by' => $user_id,
                        ]);
                    }
                } catch (\Exception $e) {
                    throw new \Exception("Error processing row " . ($key + 1) . ": " . $e->getMessage());
                }
            }

            DB::commit();
            return response()->json([
                'code' => 201,
                'status' => true,
                'message' => 'Profile data imported successfully',
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'code' => 409,
                'status' => false,
                'message' => 'Failed to import profile data: ' . $e->getMessage(),
            ], 409);
        }
    }

    public function getProfilesByCabang($cabang_id)
    {
        try {
            $profiles = profiles::where('cabang_id', $cabang_id)
                ->whereNull('deleted_at')
                ->select('id', 'profile_name', 'join_date')
                ->get();

            return response()->json([
                'status' => true,
                'data' => $profiles
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}