<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalInformation extends Model
{
    use HasFactory;

    protected $table = 'personal_informations';

    protected $guarded = [
        'id',
        'created_at',
        'updated_at'
    ];

    public function emergencyContact()
    {
        return $this->belongsTo(EmergencyContact::class, 'emergencyContactID', 'id');
    }
}
