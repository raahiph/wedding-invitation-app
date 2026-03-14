<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected $fillable = ['guest_id', 'dropbox_path', 'thumb_path', 'approved'];

    protected $casts = ['approved' => 'boolean'];

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function thumbUrl(): string
    {
        return asset('storage/' . $this->thumb_path);
    }
}
