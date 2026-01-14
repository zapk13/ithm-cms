<?php
/**
 * Attendance Record Model
 */

namespace App\Models;

use App\Core\Model;

class AttendanceRecord extends Model
{
    protected string $table = 'attendance_records';
    
    protected array $fillable = [
        'attendance_session_id', 'user_id', 'status', 'checkin_time', 'remarks'
    ];
}

