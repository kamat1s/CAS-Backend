<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
        'created_at',
        'updated_at'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'studentID')->with('user');
    }

    public function referrer()
    {
        return $this->belongsTo(Employee::class, 'referrerID')->with('user');
    }
}
