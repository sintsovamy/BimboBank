<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'code',
        'numeric_code',
        'title'
    ];
}
