<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MCC extends Model
{
    protected $table = 'mcc';
    protected $fillable = [
        'code',
        'title'
    ];
}
