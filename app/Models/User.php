<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /*
    |--------------------------------------------------------------------------
    | TABLE NAME
    |--------------------------------------------------------------------------
    | Menentukan nama tabel di database
    | (sebenarnya default "users", tapi kita buat eksplisit biar aman)
    */
    protected $table = 'users';

    /*
    |--------------------------------------------------------------------------
    | PRIMARY KEY SETTING (UUID)
    |--------------------------------------------------------------------------
    | Karena kita pakai UUID (VARCHAR), bukan auto increment
    */
    public $incrementing = false;   // non auto increment
    protected $keyType = 'string';  // tipe primary key string

    /*
    |--------------------------------------------------------------------------
    | FILLABLE (FIELD YANG BOLEH DIISI)
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'full_name',
        'email',
        'password_hash',
        'phone'
    ];

    /*
    |--------------------------------------------------------------------------
    | HIDDEN (TIDAK DITAMPILKAN KE FRONTEND)
    |--------------------------------------------------------------------------
    */
    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    /*
    |--------------------------------------------------------------------------
    | CASTS (TIPE DATA OTOMATIS)
    |--------------------------------------------------------------------------
    */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | CUSTOM PASSWORD FIELD
    |--------------------------------------------------------------------------
    | Laravel default pakai 'password'
    | Kita override supaya pakai 'password_hash'
    */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    /*
    |--------------------------------------------------------------------------
    | AUTO GENERATE UUID
    |--------------------------------------------------------------------------
    | Saat data user dibuat, otomatis isi ID dengan UUID
    */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }
}