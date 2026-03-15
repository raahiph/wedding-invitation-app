<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rsvp extends Model
{
    protected $fillable = ['guest_id', 'full_name', 'attending', 'dietary'];

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }
}
