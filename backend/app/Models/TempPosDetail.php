<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TempPosDetail extends Model
{
    use SoftDeletes;

    protected $table = 'temp_pos_detail';

    protected $fillable = [
        'cabang_id',
        'profiles_id',
        'pk',
        'achv_siba',
        'achv_soba',
        'result',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public function getAverageCalculation()
    {
        // Get active bobot configuration
        $bobot = BobotPkSoba::getActiveBobot();
        $bobotPk = $bobot ? $bobot->bobot_pk / 100 : 0.4;  // default to 60%
        $bobotSoba = $bobot ? $bobot->bobot_soba / 100 : 0.6; //Default to 40%
        return DB::select("
            WITH RankedData AS (
                SELECT 
                    profiles_id,
                    pk,
                    achv_siba,
                    achv_soba,
                    cabang_id,
                    ROW_NUMBER() OVER (PARTITION BY profiles_id ORDER BY id DESC) as rn
                FROM tempt_report_soba_pk
                WHERE deleted_at IS NULL
            ),
            AverageCalc AS (
                SELECT 
                    profiles_id,
                    cabang_id,
                    ROUND(AVG(CAST(NULLIF(REGEXP_REPLACE(pk, '[^0-9.]', '', 'g'), '') AS DECIMAL(10,2))), 0) as final_pk,
                    ROUND(AVG(CAST(NULLIF(REGEXP_REPLACE(achv_siba, '[^0-9.]', '', 'g'), '') AS DECIMAL(10,2))), 0) as final_siba,
                    ROUND(AVG(CAST(NULLIF(REGEXP_REPLACE(achv_soba, '[^0-9.]', '', 'g'), '') AS DECIMAL(10,2))), 0) as final_soba
                FROM RankedData
                WHERE rn <= 3
                GROUP BY profiles_id, cabang_id
            ),
            LatestBbTb AS (
                SELECT 
                    profiles_id,
                    bb,
                    tb,
                    CASE 
                        WHEN bb IS NOT NULL AND tb IS NOT NULL THEN
                            CAST(NULLIF(REGEXP_REPLACE(bb, '[^0-9.]', '', 'g'), '') AS DECIMAL(10,2)) / 
                            POWER(CAST(NULLIF(REGEXP_REPLACE(tb, '[^0-9.]', '', 'g'), '') AS DECIMAL(10,2)) / 100, 2)
                    END as bmi_value
                FROM (
                    SELECT 
                        profiles_id,
                        bb,
                        tb,
                        ROW_NUMBER() OVER (PARTITION BY profiles_id ORDER BY id DESC) as rn
                    FROM tempt_report_soba_pk
                    WHERE deleted_at IS NULL
                ) t
                WHERE rn = 1
            )
            SELECT 
            ac.*,
            p.id,
            p.is_active,
            ROUND((ac.final_soba * $bobotSoba + ac.final_pk * $bobotPk), 2) as avg_result,
            CASE 
                WHEN p.is_active = 'Tidak Aktif' THEN 'Sudah di TakeOut'
                WHEN (ac.final_soba * $bobotSoba + ac.final_pk * $bobotPk) >= 75 THEN 'Diperpanjang'
                WHEN (ac.final_soba * $bobotSoba + ac.final_pk * $bobotPk) < 75 
                    AND (ac.final_soba * $bobotSoba + ac.final_pk * $bobotPk) >= 70 THEN 'Dipertimbangkan'
                ELSE 'Tidak Diperpanjang'
            END as final_result,
            CASE
                WHEN bt.bmi_value IS NULL THEN 'N/A'
                WHEN bt.bmi_value < 18.5 THEN 'Kurus'
                WHEN bt.bmi_value >= 18.5 AND bt.bmi_value < 25 THEN 'Normal'
                WHEN bt.bmi_value >= 25 AND bt.bmi_value < 30 THEN 'Gemuk'
                ELSE 'Obesitas'
            END as kriteria,
            CASE
                WHEN bt.bmi_value IS NULL THEN NULL
                WHEN bt.bmi_value < 16.0 THEN 'Kurus tingkat berat'
                WHEN bt.bmi_value >= 16.0 AND bt.bmi_value < 18.5 THEN 'Kurus tingkat ringan'
                WHEN bt.bmi_value >= 18.5 AND bt.bmi_value < 25 THEN 'Normal'
                WHEN bt.bmi_value >= 25 AND bt.bmi_value < 30 THEN 'Gemuk'
                WHEN bt.bmi_value >= 30 AND bt.bmi_value < 35 THEN 'Obesitas tingkat I'
                WHEN bt.bmi_value >= 35 AND bt.bmi_value < 40 THEN 'Obesitas tingkat II'
                ELSE 'Obesitas tingkat III'
            END as kriteria_detail,
            bt.bb as latest_bb,
            bt.tb as latest_tb,
            ROUND(bt.bmi_value, 2) as bmi_value
            FROM AverageCalc ac
            JOIN profiles p ON p.profile_name = ac.profiles_id
            LEFT JOIN LatestBbTb bt ON bt.profiles_id = ac.profiles_id
            WHERE p.deleted_at IS NULL
        ");
    }
}