<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'audit_transaction_id',
        'file_name',
        'file_path',
        'uploaded_by',
    ];

    public function transaction()
    {
        return $this->belongsTo(AuditTransaction::class, 'audit_transaction_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
