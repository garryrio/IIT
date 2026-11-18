<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    public const STATUSES = ['unread' => 'Belum dibaca', 'read' => 'Sudah dibaca', 'archived' => 'Diarsipkan'];

    protected $guarded = ['id'];
}
