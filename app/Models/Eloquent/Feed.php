<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;

class Feed extends Model
{
    protected $fillable = [
        'partner_id', 'name', 'base_url', 'uri', 'token'
    ];
}
