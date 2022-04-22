<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('completed', true);
    }

    /**
     * @return $this
     */
    public function markCompleted(): static
    {
        $this->update([
            'completed' => true
        ]);

        return $this;
    }

    /**
     * @return $this
     */
    public function markIncompleted(): static
    {
        $this->update([
            'completed' => false
        ]);

        return $this;
    }

    public function toggleCompletion(): static
    {
        return $this->isComplete() ? $this->markIncompleted() : $this->markCompleted();
    }

    /**
     * @return bool
     */
    public function isComplete(): bool
    {
        return !!$this->completed;
    }
}
