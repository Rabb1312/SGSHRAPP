<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class DocList extends Model
{
    use SoftDeletes;
    protected $dates  = ['deleted_at'];
    protected $table = 'doc_list';
    protected $guarded = [];
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getData()
    {
        $data = DB::select('SELECT * FROM ' . $this->table . " WHERE  deleted_at IS NULL");
        return $data;
    }

    public function get_data_($search, $arr_pagination)
    {
        if (!empty($search)) {
            $arr_pagination['offset'] = 0;
        }

        $search = strtolower($search);

        $data = DocList::whereRaw("
            (lower(doc_name) LIKE '%$search%' 
            OR lower(doc_desc) LIKE '%$search%'
            OR lower(template_name) LIKE '%$search%'
            OR (CAST(total_input AS TEXT) LIKE '%$search%'))
                AND deleted_by IS NULL
            ")
            ->select('id', 'doc_name', 'doc_desc', 'template_name', 'total_input')
            ->offset($arr_pagination['offset'])
            ->limit($arr_pagination['limit'])
            ->orderBy('id', 'ASC')
            ->get();


        return $data;
    }

    public function dynamicInputs()
{
    return $this->hasMany(DocListD1::class, 'doc_list_id');
}

}