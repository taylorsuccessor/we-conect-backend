<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class AbstractModel extends Model
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Indicates that the model is soft-deletable.
     *
     * @var bool
     */
    public $softDeleting = true;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Perform the actual delete query on this model instance.
     *
     * @return void
     */
    protected function performDeleteOnModel()
    {
        if (! $this->softDeleting) {
            return parent::performDeleteOnModel();
        }

        $this->newModelQuery()->where($this->getKeyName(), $this->getKey())->update(
            [
            'deleted_at' => $this->freshTimestamp()
            ]
        );
    }

    /**
     * Restore a deleted model.
     *
     * @return bool
     */
    public function restore()
    {
        return $this->update(['deleted_at' => null]);
    }

    /**
     * Scope for records that are not deleted.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return mixed
     */
    public function scopeNotDeleted($query)
    {
        return $query->whereNull('deleted_at');
    }

    /**
     * Scope for records that are soft deleted.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  bool                                  $condition
     * @return mixed
     */
    public function scopeIsDeleted($query, $condition = true)
    {
        return $condition
            ? $query->whereNotNull('deleted_at')
            : $query->whereNull('deleted_at');
    }

    /**
     * Scope for records updated after a given date.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $date
     * @return mixed
     */
    public function scopeUpdatedAfter($query, $date)
    {
        return Str::contains($date, ' ')
            ? $query->where('updated_at', '>', $date)
            : $query->where('updated_at', '>', $date.' 23:59:59');
    }

    /**
     * Scope for records created on a given date.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $date
     * @return mixed
     */
    public function scopeCreatedOn($query, $date)
    {
        return $query->where('created_at', '>=', $date.' 00:00:00')
            ->where('created_at', '<=', $date.' 23:59:59');
    }

    /**
     * Scope for records updated on a given date.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $date
     * @return mixed
     */
    public function scopeUpdatedOn($query, $date)
    {
        return $query->where('updated_at', '>=', $date.' 00:00:00')
            ->where('updated_at', '<=', $date.' 23:59:59');
    }

    /**
     * Scope for records deleted on a given date.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $date
     * @return mixed
     */
    public function scopeDeletedOn($query, $date)
    {
        return $query->where('deleted_at', '>=', $date.' 00:00:00')
            ->where('deleted_at', '<=', $date.' 23:59:59');
    }

    /**
     * Scope for records has a specific term.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $term
     * @return mixed
     */
    public function scopeAny($query, $term)
    {
        return $query->where(
            function ($query) use ($term) {
                $searchables = $this->searchables;

                $query->where(array_pop($searchables), 'like', '%'.strtolower(trim($term)).'%');

                foreach ($searchables as $field) {
                    $query->orWhere($field, 'like', '%'.strtolower(trim($term)).'%');
                }
            }
        );
    }

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(
            function ($model) {
                $model->id = $model->id ?: Str::orderedUuid()->toString();
            }
        );
    }
}
