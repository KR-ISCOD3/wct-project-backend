<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegisterStudent extends Model
{
    use HasFactory;

    protected $table = "register_students";
    public $timestamps = false;

    // Include all new fields in the fillable array
    protected $fillable = [
        "student_name",
        "gender_id",
        "course_id",
        "custom_course",      // Add custom_course
        "price",
        "document_fee",       // Add document_fee
        "payment_method",     // Add payment_method
        "total_price",        // Add total_price
        "startdate",
        "status",
        "print_status"
    ];

    // Define relationship to Gender
    public function gender(){
        return $this->belongsTo(Gender::class,'gender_id');
    }

    // Define relationship to Course
    public function course(){
        return $this->belongsTo(Course::class,'course_id');
    }
}
