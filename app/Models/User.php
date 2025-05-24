<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class User extends Model
{
    use HasFactory,HasApiTokens;

    // Specify the fillable attributes to allow mass assignment
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'gender_id',
        'image',
        'work_status',
        'shift',
        'position',
        'phone_number',
        'status',
        'deleted_status',
        'create_date',
        'update_date',
    ];

    // If you're using timestamps other than 'created_at' and 'updated_at',
    // you can specify them like this:
    const CREATED_AT = 'create_date';
    const UPDATED_AT = 'update_date';
    // Relationship to Gender model (assuming one-to-many relationship)
    public function gender()
    {
        return $this->belongsTo(Gender::class);
    }
}
