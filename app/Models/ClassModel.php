<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassModel extends Model
{
    protected $table = 'classes'; // Explicitly map to the 'classes' table

    public $timestamps = false; // We are using 'create_date' manually

    protected $fillable = [
        'teacher_id',
        'course_id',
        'building_id',
        'time',
        'study_term',
        'chapter',
        'status',
        'status_class',
        'total_students',
        'create_date',
    ];
}
