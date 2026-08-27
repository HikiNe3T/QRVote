<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    /*
    |--------------------------------------------------------------------------
    | NAMA TABEL
    |--------------------------------------------------------------------------
    | Pastikan sesuai dengan database kamu
    */
    protected $table = 'categories';

    /*
    |--------------------------------------------------------------------------
    | PRIMARY KEY (UUID)
    |--------------------------------------------------------------------------
    | Kalau kamu pakai UUID seperti event & user
    */
    public $incrementing = false;
    protected $keyType = 'string';

    /*
    |--------------------------------------------------------------------------
    | FIELD YANG BOLEH DIISI
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'event_id',
        'name',
        'weight'
    ];

    /*
    |--------------------------------------------------------------------------
    | AUTO GENERATE UUID
    |--------------------------------------------------------------------------
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

    /*
    |--------------------------------------------------------------------------
    | RELASI KE EVENT
    |--------------------------------------------------------------------------
    */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}