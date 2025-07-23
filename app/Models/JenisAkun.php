<?php
// File: app/Models/JenisAkun.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisAkun extends Model
{
    use HasFactory;

    // Pastikan nama tabel ini sesuai dengan yang ada di migrasi Anda
    protected $table = 'jenis_akuns';

    protected $fillable = [
        'nama_akun',
        'keterangan',
        'jenis',
        'status',
    ];
}
