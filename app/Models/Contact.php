<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'surname',
        'email',
    ];
}
