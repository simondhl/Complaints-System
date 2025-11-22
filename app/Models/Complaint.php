<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $fillable=['user_id','government_sector_id','location','description',
     'complaint_type', 'complaint_number'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function govenment_sector(){
        return $this->belongsTo(Government_sector::class);
    }
    public function notice(){
        return $this->hasMany(Notice::class);
    }
    function operation()
    {
        return $this->hasMany(Operation::class);
    }
    public function complaint_document()
    {
        return $this->hasMany(Complaint_document::class);
    }
}
