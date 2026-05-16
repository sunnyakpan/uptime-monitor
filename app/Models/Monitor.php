<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Monitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'url',
        'check_interval',
        'threshold',
        'status',
        'last_checked_at',
        'consecutive_failures',
    ];

    protected $casts = [
        'last_checked_at' => 'datetime',
        'check_interval'  => 'integer',
        'threshold'       => 'integer',
        'consecutive_failures' => 'integer',
    ];

    public function checks(): HasMany
    {
        return $this->hasMany(MonitorCheck::class);
    }

    /**
     * Calculate uptime percentage from check history.
     */
    public function getUptimePercentageAttribute(): ?float
    {
        $total = $this->checks()->count();

        if ($total === 0) {
            return null;
        }

        $up = $this->checks()->where('is_up', true)->count();

        return round(($up / $total) * 100, 2);
    }
}