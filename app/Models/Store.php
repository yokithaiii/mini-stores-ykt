<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasUuids, HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
