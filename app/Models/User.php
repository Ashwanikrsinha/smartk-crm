<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guarded = [];

    // public $timestamps = false;

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'is_disable'    => 'boolean',
        'inactive_date' => 'datetime',
    ];

    // -------------------------------------------------------
    // RELATIONSHIPS
    // -------------------------------------------------------
    public static function generateEmpCode(): string
    {
        $year = date('Y');
        $last = self::whereYear('created_at', $year)->max('id') ?? 0;
        $seq  = str_pad($last + 1, 4, '0', STR_PAD_LEFT);
        return "EMP-{$year}-{$seq}";
    }
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function reportiveTo()
    {
        return $this->belongsTo(User::class, 'reportive_id');
    }

    /** SPs that report to this SM */
    public function teamMembers()
    {
        return $this->hasMany(User::class, 'reportive_id');
    }

    public function logs()
    {
        return $this->hasMany(Log::class, 'user_id')->orderBy('login_time', 'DESC');
    }

    public function customers()
    {
        return $this->hasMany(Customer::class, 'user_id');
    }

    public function visits()
    {
        return $this->hasMany(Visit::class, 'user_id');
    }

    // -------------------------------------------------------
    // PERMISSION SYSTEM
    // -------------------------------------------------------

    public function hasPermission(string $name): bool
    {
        return isset($this->role->permissions)
            && $this->role->permissions->where('name', $name)->count() > 0;
    }

    /**
     * Returns the user IDs this user is authorised to see data for.
     *
     * Admin       → every user in the system
     * Manager(SM) → themselves + their direct SP reports
     * Operator(SP)→ only themselves
     * Accounts    → all users (they need to see all POs)
     * Warehouse   → empty (they only see POs, not user-scoped data)
     */
    public function teamMemberIds(): array
    {
        // Admin and BM — full system scope
        if ($this->hasPermission('view_all_data') || $this->isBusinessManager()) {
            return User::pluck('id')->toArray();
        }

        // Sales Manager — self + direct SP reports
        if ($this->hasPermission('view_team_data')) {
            // Sales Manager — self + direct reports
            return User::where('reportive_id', $this->id)
                ->pluck('id')
                ->prepend($this->id)
                ->toArray();
        }

        // Accounts — all users (they see all approved POs)
        if ($this->hasPermission('view_all_orders')) {
            // Accounts team — all users
            return User::pluck('id')->toArray();
        }

        // Sales Person — own data only
        return [$this->id];
    }

    /**
     * Convenience: is this user a Sales Manager?
     */
    public function isSalesManager(): bool
    {
        return $this->role?->name === 'Manager';
    }

    /**
     * Convenience: is this user a Sales Person?
     */
    public function isSalesPerson(): bool
    {
        return $this->role?->name === 'Operator';
    }

    /**
     * Convenience: is this user an Accounts team member?
     */
    public function isAccounts(): bool
    {
        return $this->role?->name === 'Accounts';
    }

    /**
     * Convenience: is this user Administrator?
     */
    public function isAdmin(): bool
    {
        return $this->role?->name === 'Administrator';
    }

    /**
     * Is this user a Business Manager?
     * BM sees ALL data system-wide — no team scoping.
     */
    public function isBusinessManager(): bool
    {
        return $this->role?->name === 'BusinessManager';
    }

    public function scopeActive($query)
    {
        return $query->where('is_disable', 0);
    }

    /** Scope to Sales Persons only (for SM dropdown) */
    public function scopeSalesPersons($query)
    {
        return $query->whereHas('role', fn($q) => $q->where('name', 'Operator'));
    }

    /** Scope to Sales Managers only */
    public function scopeSalesManagers($query)
    {
        return $query->whereHas('role', fn($q) => $q->where('name', 'Manager'));
    }

    // -------------------------------------------------------
    // STATIC HELPERS
    // -------------------------------------------------------

    public static function maritalStatus(): array
    {
        return ['single', 'married'];
    }

    public static function createUser(array $data): void
    {
        $data['password'] = bcrypt($data['password']);
        $data['is_disable'] = isset($data['is_disable']) ? 1 : 0;
        self::create($data);
    }

    // -------------------------------------------------------
    // VISIT HELPERS (kept from original)
    // -------------------------------------------------------

    public function todayVisits()
    {
        return $this->visits()->where('visit_date', date('Y-m-d'));
    }

    public function currentMonthVisits()
    {
        return $this->visits()
            ->whereYear('visit_date', date('Y'))
            ->whereMonth('visit_date', date('m'));
    }
}
