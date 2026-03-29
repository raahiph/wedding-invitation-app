<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    protected $fillable = ['mobile', 'name', 'nickname', 'notes', 'side', 'attends_ceremony', 'session', 'plus_ones', 'rsvp_sent', 'invitation_sent'];

    protected $casts = ['attends_ceremony' => 'boolean', 'rsvp_sent' => 'boolean', 'invitation_sent' => 'boolean'];

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
        $digits = preg_replace('/[^0-9]/', '', $raw);

        // Strip Maldives country code prefix (960) if present
        if (str_starts_with($digits, '960') && strlen($digits) > 7) {
            $digits = substr($digits, 3);
        }

        return $digits;
    }
}
