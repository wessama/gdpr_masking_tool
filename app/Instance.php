<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Concerns\UsesUuid;

class Instance extends Model
{
    use Concerns\UsesUuid, SoftDeletes;

    public $table = 'instances';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'url',
        'key',
        'instance_type_id',
        'updated_at',
        'deleted_at',
    ];

    public function InstanceType()
    {
    	return $this->belongsTo(InstanceType::class);
    }

    public function Requests()
    {
        return $this->hasMany(Request::class);
    }
}
