<?php

namespace App\Models;

use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'name',
    'company_name',
    'email',
    'phone',
    'address',
    'source',
    'status',
    'campaign_id',
    'follow_up_date',
    'notes',
])]
class Customer extends Model
{
    /** @use HasFactory<CustomerFactory> */
    use HasFactory;

    public const string STATUS_PROSPECT = 'prospect';

    public const string STATUS_CUSTOMER = 'customer';

    public const string STATUS_INACTIVE = 'inactive';

    public const array STATUSES = [
        self::STATUS_PROSPECT => 'Prospek',
        self::STATUS_CUSTOMER => 'Pelanggan',
        self::STATUS_INACTIVE => 'Tidak aktif',
    ];

    public const array STATUS_COLORS = [
        self::STATUS_PROSPECT => 'warning',
        self::STATUS_CUSTOMER => 'success',
        self::STATUS_INACTIVE => 'gray',
    ];

    public const array SOURCES = [
        'website' => 'Website',
        'social_media' => 'Media sosial',
        'campaign' => 'Kampanye pemasaran',
        'referral' => 'Referensi',
        'event' => 'Event / pameran',
        'sales_team' => 'Tim penjualan',
        'other' => 'Lainnya',
    ];

    protected function casts(): array
    {
        return [
            'follow_up_date' => 'date',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}
