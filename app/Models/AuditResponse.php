<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'audit_transaction_id',
        'user_id',
        'note',
        'status',
    ];

    public function transaction()
    {
        return $this->belongsTo(AuditTransaction::class, 'audit_transaction_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function files()
    {
        return $this->hasMany(ResponseFile::class, 'response_id');
    }
}
