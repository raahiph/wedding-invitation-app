<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    protected $fillable = ['name', 'token', 'dropbox_folder', 'gated'];

    protected $casts = ['gated' => 'boolean'];

    public function photos()
    {
        return $this->hasMany(AlbumPhoto::class);
    }

    public function shareUrl(): string
    {
        return url('/a/' . $this->token);
    }
}
