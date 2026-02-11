<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutomationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'command',
        'command_name',
        'status',
        'started_at',
        'finished_at',
        'duration_seconds',
        'output',
        'error_message',
        'meta_data',
        'trigger',
    ];

    protected $casts = [
        'meta_data' => 'array',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'duration_seconds' => 'decimal:2',
    ];

    public static function startLog(string $command, string $trigger = 'schedule'): self
    {
        $commandName = explode(' ', trim($command))[0];

        return self::create([
            'command' => $command,
            'command_name' => $commandName,
            'status' => 'running',
            'started_at' => now(),
            'trigger' => $trigger,
        ]);
    }

    public function markSuccess(string $output = null, array $metaData = null): self
    {
        $this->update([
            'status' => 'success',
            'finished_at' => now(),
            'duration_seconds' => $this->started_at ? $this->started_at->diffInSeconds(now()) : 0,
            'output' => $output,
            'meta_data' => $metaData,
        ]);
        return $this;
    }

    public function markFailed(string $errorMessage, string $output = null, array $metaData = null): self
    {
        $this->update([
            'status' => 'failed',
            'finished_at' => now(),
            'duration_seconds' => $this->started_at ? $this->started_at->diffInSeconds(now()) : 0,
            'error_message' => $errorMessage,
            'output' => $output,
            'meta_data' => $metaData,
        ]);
        return $this;
    }

    public function scopeByCommand($query, string $commandName)
    {
        return $query->where('command_name', $commandName);
    }

    public function scopeRunning($query)
    {
        return $query->where('status', 'running');
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}
