<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResponseFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'response_id',
        'file_name',
        'file_path',
    ];

    public function response()
    {
        return $this->belongsTo(AuditResponse::class, 'response_id');
    }
}
