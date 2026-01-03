<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActionLog extends Model
{
    protected $fillable = [
        'user_id',
        'organization_id',
        'action',
        'description',
        'subject_type',
        'subject_id',
        'ip_address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper to log an action
     */
    public static function log($action, $description = null, $subject = null)
    {
        $user = Auth::user();

        return self::create([
            'user_id' => $user ? $user->id : null,
            'organization_id' => $user ? $user->organization_id : (app()->bound('tenant') ? app('tenant')->id : null),
            'action' => $action,
            'description' => $description,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject ? $subject->id : null,
            'ip_address' => Request::ip(),
        ]);
    }
}
