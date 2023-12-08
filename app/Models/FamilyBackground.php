<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyBackground extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
        'created_at',
        'updated_at'
    ];

    public function father()
    {
        return $this->belongsTo(Guardian::class, 'fatherID');
    }

    public function mother()
    {
        return $this->belongsTo(Guardian::class, 'motherID');
    }

    public function guardian()
    {
        return $this->belongsTo(Guardian::class, 'guardianID');
    }

    public function siblings()
    {
        return $this->hasMany(Sibling::class, 'familyBackgroundID');
    }
}
