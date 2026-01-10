<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'organization_id',
        'provider_id', // Added
        'role',
        'phone',
        'operator_status',
        'gender',
        'profile_photo_path',
        'two_factor_code',
        'two_factor_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_code',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    public function getProfilePhotoUrlAttribute()
    {
        $disk = env('PUBLIC_FILESYSTEM_DISK', 'public');

        return $this->profile_photo_path
            ? \Illuminate\Support\Facades\Storage::disk($disk)->url($this->profile_photo_path)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_expires_at' => 'datetime',
        ];
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function savedPosts()
    {
        return $this->belongsToMany(BlogPost::class, 'saved_posts', 'user_id', 'blog_post_id');
    }
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function isRoot()
    {
        return $this->role === 'root';
    }

    public function isAdminTi()
    {
        return $this->role === 'admin_ti';
    }

    public function isPlatformAdmin()
    {
        return in_array($this->role, ['root', 'admin_ti']);
    }

    public function isOrgAdmin()
    {
        // Organization Admin is usually 'admin' role within an org
        return $this->role === 'admin';
    }

    public function isProvider()
    {
        return str_contains($this->role, 'provider') || $this->provider_id !== null;
    }

    public function isDriver()
    {
        return $this->role === 'driver';
    }

    public function scopeDrivers($query)
    {
        return $query->where('role', 'driver');
    }

    public function serviceOrders()
    {
        return $this->hasMany(ServiceOrder::class, 'driver_id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
