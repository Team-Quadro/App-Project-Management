<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'job_title',
        'tenant_id',
        'approval_status',
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

    public const ROLE_SUPERADMIN = 'superadmin';
    public const ROLE_PIC = 'pic';
    public const ROLE_MEMBER = 'member';

    public function isAdmin(): bool
    {
        return in_array($this->role, [self::ROLE_PIC, self::ROLE_SUPERADMIN], true);
    }

    // PERBAIKAN: Menggunakan constant ROLE_SUPERADMIN, bukan 'admin'
    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPERADMIN;
    }

    public function isCompanyAdmin(): bool
    {
        return $this->role === self::ROLE_PIC;
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            self::ROLE_SUPERADMIN => 'Superadmin',
            self::ROLE_PIC => 'PIC Company',
            default => 'Member',
        };
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

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Projects this user is a member of.
     */
    public function memberProjects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class , 'project_members')
            ->withPivot('tenant_id')
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
