<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use App\Traits\Auditable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, Auditable;

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
        'telephone',
        'sex',
        'signature',
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


    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    public function isAccountant(): bool
    {
        return $this->role === 'accountant';
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole(string|array $roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles);
        }

        return $this->role === $roles;
    }

    /**
     * Check if user has a specific permission
     */
    public function can($permission, $arguments = []): bool
    {
        // Admin has all permissions
        if ($this->isAdmin()) {
            return true;
        }

        // Get role permissions
        $rolePermissions = $this->getPermissions();

        return in_array($permission, $rolePermissions);
    }

    /**
     * Get all permissions for user's role
     */
    public function getPermissions(): array
    {
        $roleModel = \App\Models\Role::where('name', $this->role)->first();

        if ($roleModel && $roleModel->permissions) {
            return $roleModel->permissions;
        }

        // Fallback to default permissions based on role
        return match($this->role) {
            'admin' => \App\Enums\Permission::adminPermissions(),
            'teacher' => \App\Enums\Permission::teacherPermissions(),
            'accountant' => \App\Enums\Permission::accountantPermissions(),
            default => [],
        };
    }

    /**
     * Get role display name
     */
    public function getRoleDisplayName(): string
    {
        return \App\Enums\UserRole::getDisplayName($this->role);
    }

    /**
     * Get the teacher's assigned class ID
     */
    public function getAssignedClassId(): ?string
    {
        if (!$this->isTeacher()) {
            return null;
        }
        
        return $this->classRoom?->id;
    }

    public function classRoom()
    {
        return $this->hasOne(ClassRoom::class, 'teacher_id');
    }
}
