<?php
/**
 * Exam Model
 */

namespace App\Models;

use App\Core\Model;

class Exam extends Model
{
    protected string $table = 'exams';
    
    protected array $fillable = [
        'exam_term_id', 'course_id', 'title', 'exam_type',
        'exam_date', 'start_time', 'end_time', 'venue',
        'total_marks', 'weightage', 'status'
    ];
}

