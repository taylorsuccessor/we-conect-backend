<?php

namespace App\Repository;

use Illuminate\Support\Facades\DB;

abstract class AbstractRepository
{
    /**
     * Validate the attributes.
     *
     * @param  AbstractModel $model
     * @param  array         $attributes
     * @return array
     */
    abstract protected function validate($model, $attributes);

    /**
     * Store the given entity
     *
     * @param  AbstractModel $model
     * @param  array         $attributes
     * @return mixed
     */
    abstract protected function store($model, $attributes);

    /**
     * Delete the given entity.
     *
     * @param  AbstractModel $model
     * @return void
     */
    abstract public function delete($model);

    /**
     * Create a new entity.
     *
     * @param  AbstractModel $model
     * @param  array         $attributes
     * @return AbstractModel
     */
    public function create($model, $attributes)
    {
        return $this->update($model, $attributes);
    }

    /**
     * Update an existing entity.
     *
     * @param  AbstractModel $model
     * @param  array         $attributes
     * @return AbstractModel
     */
    public function update($model, $attributes)
    {
        $data = $this->validate($model, $attributes);
        
        DB::transaction(
            function () use ($model, $data) {
                $this->store($model, $data);
            }
        );

        $this->afterStorage($model);

        return $model->fresh();
    }

    /**
     * Run after storage
     *
     * @param AbstractModel $model
     */
    public function afterStorage($model)
    {
        //
    }

    /**
     * Restore an entity.
     *
     * @param  AbstractModel $model
     * @throws \Exception
     */
    public function restore($model)
    {
        $model->restore();
    }
}
