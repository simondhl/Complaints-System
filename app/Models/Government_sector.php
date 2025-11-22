<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Government_sector extends Model
{
    protected $fillable = ['name'];

    public function employee(){
        return $this->hasMany(Employee::class);
    }
    public function complaint(){
        return $this->hasMany(Complaint::class);
    }
}
