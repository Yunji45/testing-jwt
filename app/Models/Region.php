<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function wilayah()
    {
        return $this->hasMany(Wilayah::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

}
