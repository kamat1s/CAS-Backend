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

    public function academicBackgrounds()
    {
        return $this->hasMany(AcademicBackground::class, 'userID');
    }
}
