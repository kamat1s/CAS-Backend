<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userID', 'id');
    }

    public function personalInformation()
    {
        return $this->belongsTo(PersonalInformation::class, 'personalInformationID', 'id')->with('emergencyContact');
    }

    public function familyBackground()
    {
        return $this->belongsTo(FamilyBackground::class, 'familyBackgroundID', 'id');
    }

    public function physicalHealthInfo()
    {
        return $this->belongsTo(PhysicalHealthInfo::class, 'physicalHealthInfoID', 'id');
    }

    public function career()
    {
        return $this->belongsTo(Career::class, 'careerID', 'id');
    }

    public function block()
    {
        return $this->belongsTo(Block::class, 'blockID', 'id')->with('course');
    }

    public function academicBackgrounds()
    {
        return $this->hasMany(AcademicBackground::class, 'userID');
    }
}
