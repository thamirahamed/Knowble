<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResourceShare extends Model
{
    protected $fillable = ['fileLocation', 'tutor_id', 'fileName'];
    public function tutor(): BelongsTo
    {
        return $this->belongsTo(Tutor::class);
    }
}
