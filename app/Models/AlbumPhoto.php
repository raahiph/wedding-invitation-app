<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlbumPhoto extends Model
{
    protected $fillable = ['album_id', 'thumb_path', 'type', 'dropbox_path', 'group_key', 'is_group_cover', 'sort_order', 'hidden'];

    protected $casts = ['is_group_cover' => 'boolean', 'hidden' => 'boolean'];

    public function isVideo(): bool { return $this->type === 'video'; }
    public function isGif(): bool   { return $this->type === 'gif'; }

    public function album()
    {
        return $this->belongsTo(Album::class);
    }

    public function thumbUrl(): string
    {
        return asset('storage/' . $this->thumb_path);
    }

    public function downloadUrl(): string
    {
        return route('album.download', $this->id);
    }
}
