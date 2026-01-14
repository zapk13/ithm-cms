<?php
/**
 * Course Result Model
 */

namespace App\Models;

use App\Core\Model;

class CourseResult extends Model
{
    protected string $table = 'course_results';
    
    protected array $fillable = [
        'course_id', 'user_id', 'attempt', 'total_marks', 'percentage',
        'grade', 'status', 'published_at'
    ];
}

