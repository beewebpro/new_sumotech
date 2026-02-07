<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'api_type',
        'api_endpoint',
        'purpose',
        'description',
        'status',
        'error_message',
        'request_data',
        'response_data',
        'estimated_cost',
        'tokens_used',
        'characters_used',
        'duration_seconds',
        'project_id',
        'user_id',
        'ip_address',
    ];

    protected $casts = [
        'request_data' => 'array',
        'response_data' => 'array',
        'estimated_cost' => 'decimal:6',
        'duration_seconds' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function project()
    {
        return $this->belongsTo(DubSyncProject::class, 'project_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper method to log API usage
    public static function logUsage(array $data)
    {
        return self::create(array_merge([
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'status' => 'success',
        ], $data));
    }

    // Scope queries
    public function scopeByApiType($query, $type)
    {
        return $query->where('api_type', $type);
    }

    public function scopeByPurpose($query, $purpose)
    {
        return $query->where('purpose', $purpose);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }
}
