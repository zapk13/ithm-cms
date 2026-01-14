<?php
/**
 * Exam Registration Model
 */

namespace App\Models;

use App\Core\Model;

class ExamRegistration extends Model
{
    protected string $table = 'exam_registrations';
    
    protected array $fillable = [
        'exam_id', 'user_id', 'status', 'remarks'
    ];
}

