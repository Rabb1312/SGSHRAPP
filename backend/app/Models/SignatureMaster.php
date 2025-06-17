<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SignatureMaster extends Model
{
    use SoftDeletes;

    protected $table = 'signature_masters';
    protected $guarded = [];

    protected $fillable = [
        'name',
        'position',
        'is_active',
        'created_by',
        'updated_by'
    ];

    public static function getActiveSignature()
    {
        return self::where('is_active', true)->first();
    }
}