<?php
/**
 * Exam Term Model
 */

namespace App\Models;

use App\Core\Model;

class ExamTerm extends Model
{
    protected string $table = 'exam_terms';
    
    protected array $fillable = [
        'name', 'code', 'start_date', 'end_date', 'status'
    ];
}

