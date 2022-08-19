<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'value',
    ];

    public function conditions(): HasMany
    {
        return $this->hasMany(Condition::class);
    }
}
