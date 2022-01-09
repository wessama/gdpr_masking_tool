<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Konekt\Enum\Eloquent\CastsEnums;
use Concerns\UsesUuid;

class Request extends Model
{
    use Concerns\UsesUuid, SoftDeletes, CastsEnums;

    public $table = 'requests';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'email',
        'instance_id',
        'is_processed',
        'updated_at',
        'deleted_at',
    ];

    protected $enums = [
        'is_processed' => RequestStatus::class
    ];

    public function Instance()
    {
        return $this->belongsTo(Instance::class);
    }

    public function RequestLog()
    {
        return $this->hasMany(RequestLog::class);
    }
}
