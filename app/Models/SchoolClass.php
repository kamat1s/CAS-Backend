<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $guarded = [
        'id',
        'created_at',
        'updated_at'
    ];

    public function students()
    {
        return $this->hasMany(Student::class, 'blockID', 'blockID');
    }
}
