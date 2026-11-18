<?php

namespace App\Models;

use App\Models\Concerns\HasStatus;
use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    use HasStatus;

    protected $guarded = ['id'];
}
