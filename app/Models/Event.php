<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Event extends Model
{
    protected $table = 'events';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'description',
        'image_url',
        'start_date',
        'end_date',
        'start_time',
        'end_time',

        'creator_id',
        'voting_type',
        'categories_enabled',
        'juara_harapan_enabled',
        'juara_harapan_count',
        'pemenang_kategori_enabled',
        'determining_category_id',
    ];

    public function getStatusAttribute()
    {
        $now = Carbon::now();

        $start = Carbon::parse($this->start_date . ' ' . $this->start_time);
        $end   = Carbon::parse($this->end_date . ' ' . $this->end_time);

        if ($now < $start) {
            return 'upcoming';
        } elseif ($now > $end) {
            return 'finished';
        } else {
            return 'active';
        }
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function categories()
{
    return $this->hasMany(Category::class);
}

public function participants()
{
    return $this->hasMany(Participant::class);
}

public function getFormattedStartDateAttribute()
{
    return Carbon::parse($this->start_date)->format('d-m-Y');
}

public function getFormattedEndDateAttribute()
{
    return Carbon::parse($this->end_date)->format('d-m-Y');
}

public function candidates()
{
    return $this->hasMany(Candidate::class, 'event_id');
}
}