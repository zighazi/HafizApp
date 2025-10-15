<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Atribut yang bisa diisi massal.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // ✅ tambahkan kolom role
    ];

    /**
     * Atribut yang disembunyikan dari serialisasi.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting atribut ke tipe data tertentu.
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

    /* ========================================================
       ==========  ROLE & RELASI ORANGTUA - SANTRI ============
       ======================================================== */

    /**
     * Cek apakah user adalah admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Cek apakah user adalah orangtua.
     */
    public function isOrangtua(): bool
    {
        return $this->role === 'orangtua';
    }

    /**
     * Ambil daftar NIS anak (string[]) milik user ini.
     */
    public function childNisList(): array
    {
        return DB::table('parent_santri')
            ->where('user_id', $this->id)
            ->pluck('nis')
            ->toArray();
    }

    /**
     * Relasi many-to-many ke model Santri (opsional, bila mau pakai Eloquent).
     */
    public function children()
    {
        return $this->belongsToMany(
            Santri::class,
            'parent_santri',
            'user_id',  // FK di pivot
            'nis',      // kolom yang mengacu ke santri.nis
            'id',       // PK di users
            'nis'       // PK di santri
        );
    }
}