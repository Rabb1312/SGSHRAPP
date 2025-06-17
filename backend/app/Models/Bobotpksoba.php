<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BobotPkSoba extends Model
{
    use SoftDeletes;

    protected $table = 'bobot_pk_soba';

    protected $fillable = [
        'name',
        'bobot_pk',
        'bobot_soba',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public static function getActiveBobot()
    {
        return self::where('is_active', true)->first();
    }
}