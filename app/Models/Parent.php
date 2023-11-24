<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class _Parent extends Model
{
    use HasFactory;

    protected $table = 'parents';

    protected $guarded = [
        'id',
        'created_at',
        'updated_at'
    ];
}
