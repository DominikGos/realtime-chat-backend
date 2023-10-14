<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'text'
    ];

    public function files(): HasMany
    {
        return $this->hasMany(MessageFile::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
