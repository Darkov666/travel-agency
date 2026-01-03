<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChangeRequest extends Model
{
    protected $fillable = [
        'organization_id',
        'user_id',
        'model_type',
        'model_id',
        'request_type',
        'payload',
        'status',
        'admin_feedback'
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    // Helper to get the actual model instance
    public function subject()
    {
        return $this->model_type::find($this->model_id);
    }
}
