<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_date',
        'user_id',
        'user_code',
        'account_number',
        'customer_name',
        'transaction_type',
        'description',
        'status',
        'created_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function files()
    {
        return $this->hasMany(AuditFile::class, 'audit_transaction_id');
    }

    public function responses()
    {
        return $this->hasMany(AuditResponse::class, 'audit_transaction_id');
    }

    public function comments()
    {
        return $this->hasMany(AuditComment::class, 'audit_transaction_id');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class, 'audit_transaction_id');
    }
}
