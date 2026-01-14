<?php
/**
 * Exam Mark Model
 */

namespace App\Models;

use App\Core\Model;

class ExamMark extends Model
{
    protected string $table = 'exam_marks';
    
    protected array $fillable = [
        'exam_id', 'user_id', 'obtained_marks', 'is_finalized',
        'graded_by', 'graded_at'
    ];
}

