<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
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
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
        ];
    }

    /* --------------------------------------------------------
     | Helpers
     | -------------------------------------------------------- */

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /* --------------------------------------------------------
     | Relationships
     | -------------------------------------------------------- */

    /**
     * Projects this user owns.
     */
    public function ownedProjects(): HasMany
    {
        return $this->hasMany(Project::class , 'owner_id');
    }

    /**
     * Projects this user is a member of.
     */
    public function memberProjects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class , 'project_members')
            ->withTimestamps();
    }

    /**
     * All accessible projects (owned + member).
     */
    public function accessibleProjects()
    {
        return Project::where('owner_id', $this->id)
            ->orWhereHas('members', fn($q) => $q->where('users.id', $this->id));
    }

    /**
     * Tasks assigned to this user.
     */
    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class , 'assigned_to');
    }
}