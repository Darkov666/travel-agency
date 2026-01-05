<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteFeedback extends Model
{
    use HasFactory;

    protected $table = 'website_feedbacks';

    protected $fillable = [
        'token',
        'rating',
        'comments',
        'user_id',
        'is_reviewed',
    ];

    protected $casts = [
        'is_reviewed' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
