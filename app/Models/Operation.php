<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Operation extends Model
{
    protected $fillable = [
        'notice_id',
        'employee_id',
        'complaint_id',
        'operation_date',
        'details',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }
}
