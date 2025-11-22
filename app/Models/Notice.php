<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    protected $fillable = ['complaint_id','description'];

    public function complaint(){
        return $this->belongsTo(Complaint::class);
    }
    
}
