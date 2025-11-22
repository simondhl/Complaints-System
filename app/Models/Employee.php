<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = ['user_id', 'government_sector_id', 'employee_number'];

    public function government_sector(){
        return $this->belongsTo(Government_sector::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function operation()
    {
        return $this->hasMany(Operation::class);
    }
}
