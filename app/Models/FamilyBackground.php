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
        return $this->belongsTo(Parent::class, 'fatherID');
    }

    public function mother()
    {
        return $this->belongsTo(Parent::class, 'motherID');
    }

    public function guardian()
    {
        return $this->belongsTo(Parent::class, 'guardianID');
    }

    public function siblings()
    {
        return $this->hasMany(Sibling::class, 'familyBackgroundID');
    }
}
