<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'employeeID',
        'employeeType',
        'name',
        'email',
    ];

    public function college()
    {
        return $this->belongsTo(College::class, 'collegeID', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userID', 'id');
    }
}
