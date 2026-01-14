<?php
/**
 * Attendance Session Model
 */

namespace App\Models;

use App\Core\Model;

class AttendanceSession extends Model
{
    protected string $table = 'attendance_sessions';
    
    protected array $fillable = [
        'course_id', 'instructor_id', 'session_date', 'start_time',
        'end_time', 'session_type', 'topic', 'status'
    ];
}

