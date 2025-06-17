<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use DateTimeInterface;

class ProfilesDetail extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'profiles_detail';
    protected $guarded = [];

    /**
     * Serialize the date for array/JSON.
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Mengambil semua data yang tidak terhapus (Soft Delete).
     */
    public function getData()
    {
        return $this->whereNull('deleted_at')->get();
    }

    /**
     * Mengambil data dengan pencarian dan pagination.
     */
    public function get_data_($search, $arr_pagination)
    {
        if (!empty($search)) {
            $arr_pagination['offset'] = 0;
        }

        $search = strtolower($search);

        $query = $this->with(['profiles' => function ($query) {
            $query->select('id', 'profile_name');
        }]);

        $data = $query->where(function ($query) use ($search) {
            $query->whereRaw("LOWER(CAST(profiles_id AS VARCHAR)) LIKE ?", ['%' . $search . '%'])
                ->orWhereRaw("LOWER(masa_kerja_ke) LIKE ?", ['%' . $search . '%'])
                ->orWhere('tahun', 'LIKE', "%$search%")
                ->orWhere('bulan', 'LIKE', "%$search%")
                ->orWhere('hari', 'LIKE', "%$search%")
                ->orWhereRaw("LOWER(sts_take_out) LIKE ?", ['%' . $search . '%'])
                ->orWhereRaw("LOWER(ket) LIKE ?", ['%' . $search . '%']);
        })
            ->whereNull('deleted_at')
            ->select(
                'profiles_detail.id',
                'profiles_detail.profiles_id',
                'profiles_detail.masa_kerja_ke',
                'profiles_detail.tahun',
                'profiles_detail.bulan',
                'profiles_detail.hari',
                'profiles_detail.sts_take_out',
                'profiles_detail.ket'
            )
            ->offset($arr_pagination['offset'])
            ->limit($arr_pagination['limit'])
            ->orderBy('profiles_detail.id', 'ASC');
    }


    private function normalisasiPeriode($totalTahun, $totalBulan, $totalHari)
    {
        // Konversi hari ke bulan
        $tambahBulan = floor($totalHari / 30);
        $sisaHari = $totalHari % 30;
        $totalBulan += $tambahBulan;

        // Konversi bulan ke tahun
        $tambahTahun = floor($totalBulan / 12);
        $sisaBulan = $totalBulan % 12;
        $totalTahun += $tambahTahun;

        return [
            'tahun' => $totalTahun,
            'bulan' => $sisaBulan,
            'hari' => $sisaHari
        ];
    }

    public function getTotalMasaKerja($profilesId)
    {
        return DB::table('hr_kontrak')
            ->select(
                DB::raw('COUNT(*) FILTER (WHERE tipe = \'PKWT\') as masa_kerja_ke'), // Only count PKWT
                DB::raw('COALESCE(SUM(CASE WHEN tipe = \'PKWT\' THEN tahun ELSE 0 END), 0) as total_tahun'),
                DB::raw('COALESCE(SUM(CASE WHEN tipe = \'PKWT\' THEN bulan ELSE 0 END), 0) as total_bulan'),
                DB::raw('COALESCE(SUM(CASE WHEN tipe = \'PKWT\' THEN hari ELSE 0 END), 0) as total_hari')
            )
            ->where('profiles_id', $profilesId)
            ->whereNull('deleted_at')
            ->first();
    }

    public function getDetailedData($search = null, $limit = 10, $offset = 0)
    {
        // Ambil data unique profile terlebih dahulu (bagian ini tetap sama)
        $uniqueProfiles = DB::table('profiles_detail as pd')
            ->select('pd.profiles_id')
            ->distinct()
            ->whereNull('pd.deleted_at')
            ->when($search, function ($query) use ($search) {
                return $query->where(function ($q) use ($search) {
                    $searchLower = strtolower($search);
                    $q->whereRaw('LOWER(pd.profiles_id) LIKE ?', ['%' . $searchLower . '%'])
                        ->orWhereRaw('LOWER(CAST(pd.masa_kerja_ke AS VARCHAR)) LIKE ?', ['%' . $searchLower . '%'])
                        ->orWhereRaw('LOWER(CAST(pd.tahun AS VARCHAR)) LIKE ?', ['%' . $searchLower . '%'])
                        ->orWhereRaw('LOWER(CAST(pd.bulan AS VARCHAR)) LIKE ?', ['%' . $searchLower . '%'])
                        ->orWhereRaw('LOWER(CAST(pd.hari AS VARCHAR)) LIKE ?', ['%' . $searchLower . '%']);
                });
            });

        // Modifikasi query utama untuk mengambil ket_take_out dan tgl_take_out
        $query = $this->with(['profile', 'hrKontrak'])
            ->select(
                'profiles_detail.id',
                'profiles_detail.profiles_id',
                'profiles.is_active',
                // Mengambil ket_take_out dan tgl_take_out dari hr_kontrak
                // Query untuk mengambil data take out terbaru
                DB::raw('(
                    SELECT ket_take_out 
                    FROM hr_kontrak 
                    WHERE profiles_id = profiles_detail.profiles_id 
                    AND deleted_at IS NULL 
                    AND ket_take_out IS NOT NULL 
                    ORDER BY tgl_take_out DESC, created_at DESC 
                    LIMIT 1
                ) as ket_take_out'),
                DB::raw('(
                    SELECT tgl_take_out 
                    FROM hr_kontrak 
                    WHERE profiles_id = profiles_detail.profiles_id 
                    AND deleted_at IS NULL 
                    AND tgl_take_out IS NOT NULL 
                    ORDER BY tgl_take_out DESC, created_at DESC 
                    LIMIT 1
                ) as tgl_take_out'),
                DB::raw('(
                    SELECT no_kontrak 
                    FROM hr_kontrak 
                    WHERE profiles_id = profiles_detail.profiles_id 
                    AND deleted_at IS NULL 
                    ORDER BY tgl_take_out DESC, created_at DESC 
                    LIMIT 1
                ) as last_no_kontrak')
            )
            ->joinSub($uniqueProfiles, 'unique_profiles', function ($join) {
                $join->on('profiles_detail.profiles_id', '=', 'unique_profiles.profiles_id');
            })
            ->leftJoin('profiles', 'profiles_detail.profiles_id', '=', 'profiles.profile_name')
            ->groupBy(
                'profiles_detail.id',
                'profiles_detail.profiles_id',
                'profiles.is_active'
            );

        $results = $query->skip($offset)->take($limit)->get();

        // Proses hasil (bagian ini tetap sama)
        $processedResults = $results->map(function ($item) {
            $masaKerja = $this->getTotalMasaKerja($item->profiles_id);
            $periode = $this->normalisasiPeriode(
                $masaKerja->total_tahun,
                $masaKerja->total_bulan,
                $masaKerja->total_hari
            );

            // Format tanggal Indonesia
            $formattedDate = "";
            if (!empty($item->tgl_take_out)) {
                $date = \Carbon\Carbon::parse($item->tgl_take_out);
                $namaBulan = [
                    'Januari',
                    'Februari',
                    'Maret',
                    'April',
                    'Mei',
                    'Juni',
                    'Juli',
                    'Agustus',
                    'September',
                    'Oktober',
                    'November',
                    'Desember'
                ];
                $formattedDate = $date->format('d') . ' ' . $namaBulan[$date->month - 1] . ' ' . $date->format('Y');
            }

            // Format keterangan takeout
            $keteranganTakeOut = "-";
            if ($item->is_active === "Tidak Aktif" && !empty($item->ket_take_out) && !empty($item->tgl_take_out)) {
                $keteranganTakeOut = "Take out - {$item->ket_take_out} - pada - {$formattedDate}";
            }


            return [
                'id' => $item->id,
                'profiles_id' => $item->profiles_id,
                'masa_kerja_ke' => $masaKerja->masa_kerja_ke,
                'tahun' => $periode['tahun'],
                'bulan' => $periode['bulan'],
                'hari' => $periode['hari'],
                'status_aktif' => $item->is_active,
                'sts_take_out' => $item->is_active,
                'keterangan_takeout' => $keteranganTakeOut,
                'ket_take_out' => $item->ket_take_out,
                'tgl_take_out' => $item->tgl_take_out,
                'last_no_kontrak' => $item->last_no_kontrak
            ];
        })
            ->unique('profiles_id')
            ->values();

        return [
            'results' => $processedResults,
            'count' => $uniqueProfiles->count()
        ];
    }
    public function profile()
    {
        return $this->belongsTo(Profiles::class, 'profiles_id', 'profile_name');
    }

    // Tambahkan relasi ke hr_kontrak
    public function hrKontrak()
    {
        return $this->belongsTo(hr_kontrak::class, 'profiles_id', 'profiles_id')
            ->whereNull('deleted_at');
    }
}