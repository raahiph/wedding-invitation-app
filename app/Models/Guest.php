<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    protected $fillable = ['mobile', 'name', 'notes', 'side', 'attends_ceremony', 'session', 'plus_ones'];

    protected $casts = ['attends_ceremony' => 'boolean'];

    public function rsvp()
    {
        return $this->hasOne(Rsvp::class);
    }

    public function photos()
    {
        return $this->hasMany(Photo::class);
    }

    public static function normaliseMobile(string $raw): string
    {
        return preg_replace('/[^0-9]/', '', $raw);
    }
}
