<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['name', 'slug', 'is_active','document','email','phone','address'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function projects() {
        return $this->hasMany(Project::class);
    }
}