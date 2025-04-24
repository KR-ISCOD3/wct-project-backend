<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $table = 'courses'; // Ensure it matches your database table name
    protected $primaryKey = 'id';
    public $timestamps = false; // If you don't have created_at and updated_at columns

    protected $fillable = ['name', 'category','status', 'create_date'];

    protected $casts = [
        'create_date' => 'datetime',
    ];

    // Convert PostgreSQL array to PHP array
    public function getCategoryAttribute($value)
    {
        // Convert PostgreSQL array literal to PHP array
        return explode(',', trim($value, '{}'));
    }

    public function setCategoryAttribute($value)
{
    $allowedCategories = ['programming', 'networking', 'design', 'office', 'custom'];

    if (is_array($value) && !empty($value)) {
        // Ensure all categories are allowed
        $value = array_map(function ($item) use ($allowedCategories) {
            if (!in_array($item, $allowedCategories)) {
                throw new \InvalidArgumentException("Invalid category: $item");
            }
            return $item;
        }, $value);

        // Wrap each element with double quotes if it contains spaces
        $formatted = array_map(function ($item) {
            if (strpos($item, ' ') !== false) {
                return '"' . $item . '"'; // add double quotes
            }
            return $item;
        }, $value);

        $this->attributes['category'] = '{' . implode(',', $formatted) . '}';
    } else {
        // Default to 'custom' if no valid category is provided
        $this->attributes['category'] = '{custom}';
    }
}



}
