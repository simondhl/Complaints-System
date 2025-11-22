<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint_document extends Model
{
    protected $fillable = ['complaint_id', 'document_path', 'mime_type'];

    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }
}
