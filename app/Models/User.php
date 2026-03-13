<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail, FilamentUser, HasName
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles,HasPanelShield;
    use SoftDeletes;

    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 0;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'attachment',
        'nombre1',
        'nombre2',
        'nombre3',
        'apellido1',
        'apellido2',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function sucursales()
    {
        return $this->belongsToMany(Sucursal::class, 'sucursales_usuario', 'id_usuario', 'id_sucursal');
    }


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // $model->created_by = auth()->id();
            $model->created_by = auth()->check() ? auth()->id() : null;
        });

        static::updating(function ($model) {
            // $model->updated_by = auth()->id();
            $model->updated_by = auth()->check() ? auth()->id() : null;
        });

        static::deleting(function ($model) {
            // $model->deleted_by = auth()->id();
            $model->timestamps = false;
            $model->estado = self::STATUS_DELETED;
            $model->deleted_by = auth()->check() ? auth()->id() : null;
            $model->save();
            $model->timestamps = true;
        });
    }

    /**
     * Scope para filtrar solo registros activos.
     */
    public function scopeActive($query)
    {
        return $query->where('estado', self::STATUS_ACTIVE);
    }

    public function scopeInactive($query)
    {
        return $query->where('estado', self::STATUS_DELETED);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true; // O tu lógica de autorización
    }

    public function getFilamentName(): string
    {
        return "{$this->nombre1} {$this->apellido1}";
    }
}
