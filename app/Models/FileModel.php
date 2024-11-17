<?php

namespace App\Models;

use CodeIgniter\Model;

class FileModel extends Model
{
    protected $table      = 'files'; // Nama tabel di database
    protected $primaryKey = 'id';

    protected $allowedFields = ['filename', 'encrypted_content', 'is_encrypted'];

    // Anda dapat menambahkan metode lain sesuai kebutuhan
}
