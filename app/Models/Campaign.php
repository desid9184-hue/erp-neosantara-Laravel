<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'barang_id',
    'channel',
    'objective',
    'target_audience',
    'budget',
    'start_date',
    'end_date',
    'status',
])]
class Campaign extends Model
{
    public const array CHANNELS = [
        'email' => 'Email',
        'social_media' => 'Media sosial',
        'search_ads' => 'Iklan pencarian',
        'event' => 'Event / pameran',
        'sms' => 'SMS',
        'other' => 'Lainnya',
    ];

    public const array STATUSES = [
        'draft' => 'Draft',
        'scheduled' => 'Terjadwal',
        'active' => 'Berjalan',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ];

    public const array STATUS_COLORS = [
        'draft' => 'gray',
        'scheduled' => 'info',
        'active' => 'success',
        'completed' => 'primary',
        'cancelled' => 'danger',
    ];

    protected function casts(): array
    {
        return [
            'budget' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}
