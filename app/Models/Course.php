<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'category', 'status'];

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
