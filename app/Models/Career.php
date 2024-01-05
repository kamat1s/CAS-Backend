<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Career extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
        'created_at',
        'updated_at'
    ];

    public function firstCourse()
    {
        return $this->belongsTo(Course::class, 'firstCourseID', 'id');
    }

    public function secondCourse()
    {
        return $this->belongsTo(Course::class, 'secondCourseID', 'id');
    }

    public function thirdCourse()
    {
        return $this->belongsTo(Course::class, 'thirdCourseID', 'id');
    }
}
