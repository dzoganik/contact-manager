<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Contact extends Model
{
    use Searchable;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'surname',
        'email',
    ];

    public function toSearchableArray(): array
    {
        return [
            'name' => $this->name,
            'surname' => $this->surname,
            'email' => $this->email,
        ];
    }
}
