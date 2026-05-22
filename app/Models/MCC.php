<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MCC extends Model
{
    /**
     * @var string
     */
    protected $table = 'mcc';

    /**
     * @var string[]
     */
    protected $fillable = [
        'code',
        'title'
    ];
}
