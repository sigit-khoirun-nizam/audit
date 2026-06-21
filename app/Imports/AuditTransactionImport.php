<?php

namespace App\Imports;

use App\Models\AuditTransaction;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class AuditTransactionImport implements ToModel, WithHeadingRow
{
    protected $rowsCount = 0;
    protected $skippedRows = [];

    public function model(array $row)
    {
        $date = $row['tanggal_transaksi'] ?? $row['transaction_date'] ?? null;
        $userCode = $row['kode_user'] ?? $row['user_code'] ?? null;
        $accountNumber = $row['nomor_rekening'] ?? $row['account_number'] ?? null;
        $customerName = $row['nama_nasabah'] ?? $row['customer_name'] ?? null;
        $transactionType = $row['jenis_transaksi'] ?? $row['transaction_type'] ?? null;
        $description = $row['deskripsi'] ?? $row['description'] ?? $row['description_temuan'] ?? null;

        // Skip completely empty rows
        if (!$date && !$userCode && !$accountNumber && !$customerName) {
            return null;
        }

        // Find the assigned user based on user_code
        $assignedUser = null;
        if ($userCode) {
            $assignedUser = User::where('user_code', trim($userCode))->first();
        }

        if (!$assignedUser) {
            $this->skippedRows[] = [
                'row' => $row,
                'reason' => "User Code '" . ($userCode ?? 'EMPTY') . "' tidak ditemukan di database."
            ];
            return null;
        }

        // Handle Excel Serialized date numbers
        if (is_numeric($date)) {
            try {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date)->format('Y-m-d');
            } catch (\Exception $e) {
                // Keep original if parsing fails
            }
        } else if ($date) {
            // Standard parse
            $date = date('Y-m-d', strtotime(str_replace('/', '-', $date)));
        }

        $transaction = AuditTransaction::create([
            'transaction_date' => $date ?: date('Y-m-d'),
            'user_id'          => $assignedUser->id,
            'user_code'        => $assignedUser->user_code ?? '',
            'account_number'   => $accountNumber,
            'customer_name'    => $customerName,
            'transaction_type' => $transactionType,
            'description'      => $description ?? '',
            'status'           => 'PENDING',
            'created_by'       => Auth::id(),
        ]);

        $this->rowsCount++;

        // Activity Log
        ActivityLog::create([
            'user_id'              => Auth::id(),
            'audit_transaction_id' => $transaction->id,
            'action'               => 'IMPORT_AUDIT_EXCEL',
            'ip_address'           => request()->ip(),
        ]);

        // WhatsApp Notification to User (simulate)
        $this->sendWhatsAppToUser($assignedUser, $transaction);

        return $transaction;
    }

    public function getImportedCount()
    {
        return $this->rowsCount;
    }

    public function getSkippedRows()
    {
        return $this->skippedRows;
    }

    private function sendWhatsAppToUser(User $user, AuditTransaction $transaction)
    {
        $phone = $user->phone;
        if (!$phone) {
            return;
        }

        $message = "Halo {$user->name}\n\n"
                 . "Ada temuan audit baru (Imported via Excel):\n\n"
                 . "Tanggal: {$transaction->transaction_date}\n"
                 . "Nasabah: {$transaction->customer_name}\n"
                 . "Keterangan: {$transaction->description}\n\n"
                 . "Silahkan login sistem audit untuk upload bukti penyelesaian.";

        Log::info("SIMULATING WA GATEWAY SEND TO {$phone} (EXCEL IMPORT): \n{$message}");
    }
}
