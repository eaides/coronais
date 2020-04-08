<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Statistic extends Model
{
    protected $fillable = [
        'country',
        'qty',
        'actives',
        'death',
        'dateis',
        'percent',
        'diff',
        'active_percent',
        'diff_actives',
        'death_percent',
    ];
}
