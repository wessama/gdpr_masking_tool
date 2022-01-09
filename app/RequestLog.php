<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Konekt\Enum\Eloquent\CastsEnums;

class RequestLog extends Model
{
    use Concerns\UsesUuid, SoftDeletes, CastsEnums;

    public $table = 'request_logs';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'request_id',
        'log',
        'status_code',
        'updated_at',
        'deleted_at',
    ];

    public static function Log($request_id, $status_code, $log)
    {
        $data = array(
            'request_id'  => $request_id,
            'status_code' => $status_code,
            'log'         => $log,
            'updated_at'  => Carbon::now());

        return RequestLog::updateOrCreate(['request_id' => $request_id], $data);
    }

    public function Request()
    {
        return $this->belongsTo(Request::class);
    }
}
