<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Concerns\UsesUuid;

class InstanceType extends Model
{
    use Concerns\UsesUuid, SoftDeletes;

    public $table = 'instance_types';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'updated_at',
        'deleted_at',
    ];

    public function Instance()
    {
    	return $this->hasMany(Instance::class);
    }
}
