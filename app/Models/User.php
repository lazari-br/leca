<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type_id',
        'commission',
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
            'commission' => 'decimal:2',
        ];
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(UserType::class, 'user_type_id', 'id');
    }

    // Relacionamentos para vendedores
    public function sellerStocks(): HasMany
    {
        return $this->hasMany(SellerStock::class, 'seller_id', 'id');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class, 'seller_id', 'id');
    }

    // Escopo para vendedores
    public function scopeSellers($query)
    {
        return $query->whereHas('type', function ($q) {
            $q->where('id', 2)->orWhere('name', 'vendedor');
        });
    }

    // Verificar se é vendedor
    public function isSeller(): bool
    {
        return $this->user_type_id == 2 ||
            ($this->type && strtolower($this->type->name) === 'vendedor');
    }
}
