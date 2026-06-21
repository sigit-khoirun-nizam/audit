# Sistem Informasi Audit Transaksi

Sistem Audit Transaksi berbasis Laravel untuk monitoring temuan audit, upload evidence, approval auditor, dan notifikasi WhatsApp.

---

# Technology Stack

- Laravel 12
- PHP 8.2+
- MySQL
- Spatie Laravel Permission
- Laravel Queue
- WhatsApp Gateway API
- Storage File Upload

---

# User Role

Menggunakan package:

```bash
composer require spatie/laravel-permission
```

Publish:

```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

Migration:

```bash
php artisan migrate
```

---

## Role System

Terdapat 3 role:

### Superadmin

Permission:

- Dashboard
- Manage User
- Manage Role Permission
- Semua data audit
- Laporan


---

### Auditor

Permission:

- Input Audit Transaksi
- Upload Lampiran Audit
- Review Jawaban User
- Approve Audit
- Reject Audit


---

### User

Permission:

- Melihat audit miliknya
- Upload bukti penyelesaian
- Update keterangan


---

# Database Design

## 1. users

Menyimpan semua user sistem.

```sql
id

name

email

password

phone

user_code

created_at
updated_at
```

Contoh:

```
17800T60 = Teller kode 60

17800CS63 = CS kode 63
```

Role menggunakan:

```
Spatie model_has_roles
```


---

# 2. audit_transactions

Data utama audit dari auditor.

```sql
id


transaction_date


user_id


user_code


account_number


customer_name


transaction_type


description


status


created_by


created_at
updated_at
```


Status:

```
PENDING

ON_REVIEW

REVISION

DONE
```


Contoh data:

```
Tanggal:
28-04-2026


ID User:
17800T60


Rekening:
000003


Nasabah:
GEMILANG


Jenis:
TELLER 1780060


Keterangan:
PEMBAWA CEK BELUM ADA


Status:
PENDING

```


---

# 3. audit_files


Lampiran auditor.

```sql
id


audit_transaction_id


file_name

file_path


uploaded_by


created_at
updated_at
```


Support:

- JPG
- PNG
- PDF



---

# 4. audit_responses


Jawaban user terhadap audit.


```sql
id


audit_transaction_id


user_id


note


status


created_at
updated_at
```



---

# 5. response_files


Foto bukti user.


```sql
id


response_id


file_name


file_path


created_at
updated_at
```


---

# 6. audit_comments


Diskusi auditor dengan user.


```sql
id


audit_transaction_id


user_id


message


created_at
updated_at
```



---

# 7. activity_logs


Log aktivitas.


```sql
id


user_id


audit_transaction_id


action


ip_address


created_at
```



---

# Relationship Database


```
users
 |
 |
audit_transactions
 |
 +---- audit_files
 |
 +---- audit_responses
             |
             |
       response_files


audit_comments


activity_logs

```



---

# Workflow System


## Auditor Create Audit


Auditor input:

- Tanggal transaksi
- ID User
- Nomor rekening
- Nama nasabah
- Jenis transaksi
- Keterangan transaksi
- Upload Foto


Setelah Submit:


Status:

```
PENDING
```


Kirim WhatsApp otomatis ke User.



---

# WhatsApp Notification


Ketika auditor membuat audit baru:


Message:

```
Halo {nama}


Ada temuan audit baru:


Tanggal:
{tanggal}


Nasabah:
{nama_nasabah}


Keterangan:
{keterangan}


Silahkan login sistem audit untuk upload bukti penyelesaian.

```



---

Ketika User Upload Bukti:


Kirim WA Auditor:


```
Halo Auditor


User {nama_user}

sudah upload bukti audit.


Audit:
{kode}


Silahkan review.

```


---

Ketika Auditor Approve:


Kirim WA User:


```
Audit anda sudah selesai.


Status:

SELESAI


Terima kasih.
```



---


# Dashboard


## Superadmin


Card:

- Total Audit
- Pending
- On Review
- Done
- Overdue


Chart:

Audit per bulan


---

## Auditor Dashboard


Menampilkan:

- Audit dibuat
- Menunggu review
- Selesai


---

## User Dashboard


Menampilkan:


```
Audit Saya


Pending : 10

Review : 5

Selesai : 20

```



---

# Feature Upload


Storage:

```
storage/app/public/audit
```


Command:

```bash
php artisan storage:link
```


Validation:


Foto maksimal 5MB.


Support:


- jpg
- jpeg
- png
- pdf


---

# Routing


Example:


```
/dashboard


/users


/audit


/audit/create


/audit/{id}


/audit/review


/report

```



---

# Middleware Example


```php
Route::middleware(['role:Auditor'])
->group(function(){

});



Route::middleware(['role:User'])
->group(function(){

});
```


---

# Additional Feature


Future update:


- Export Excel
- Export PDF
- Reminder WhatsApp H-1 deadline
- Filter tanggal audit
- Search nomor rekening
- Audit history
- Dark mode


---

# Goal System


Membantu proses audit transaksi:

Auditor menemukan masalah

↓

User menerima temuan

↓

User upload bukti

↓

Auditor verifikasi

↓

Audit selesai

```