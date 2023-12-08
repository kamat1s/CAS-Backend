<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
        'created_at',
        'updated_at'
    ];

    public function preferredSchedule1()
    {
        return $this->belongsTo(Schedule::class, 'preferredSchedule1ID');
    }

    public function preferredSchedule2()
    {
        return $this->belongsTo(Schedule::class, 'preferredSchedule2ID');
    }

    public function preferredSchedule3()
    {
        return $this->belongsTo(Schedule::class, 'preferredSchedule3ID');
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'scheduleID');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userID');
    }
}
