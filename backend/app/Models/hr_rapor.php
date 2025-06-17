<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class hr_rapor extends Model
{
    use SoftDeletes;

    protected $table = 'hr_rapor';
    protected $fillable = [
        'profiles_id',
        'grooming',
        'pk',
        'soba',
        'disiplin',
        'admin',
        'mop',
        'yop',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $dates = ['deleted_at'];
}