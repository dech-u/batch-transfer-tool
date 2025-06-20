<?php

namespace App\Repositories\Base;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface BaseInterface {
    /**
     * Get all models.
     *
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function all(array $columns = ['*'], array $relations = []): Collection;

    /**
     * Get all trashed models.
     *
     * @return Collection
     */
    public function allTrashed(): Collection;

    /**
     * Find model by id.
     *
     * @param int $modelId
     * @param array $columns
     * @param array $relations
     * @param array $appends
     * @return Model|null
     */
    public function findById(
        int $modelId,
        array $columns = ['*'],
        array $relations = [],
        array $appends = []
    ): ?Model;

    /**
     * Find trashed model by id.
     *
     * @param int $modelId
     * @return Model|null
     */
    public function findTrashedById(int $modelId): ?Model;

    /**
     * Find only trashed model by id.
     *
     * @param int $modelId
     * @return Model|null
     */
    public function findOnlyTrashedById(int $modelId): ?Model;

    /**
     * Create a model.
     *
     * @param array $payload
     * @return Model|null
     */
    public function create(array $payload): ?Model;

    /**
     * Create a model.
     *
     * @param array $payload
     * @return bool
     */
    public function createBulk(array $payload): bool;

    /**
     * Update existing model.
     *
     * @param int $modelId
     * @param array $payload
     * @return Model|null
     */
    public function update(int $modelId, array $payload): ?Model;

    /**
     * Update existing model.
     * @param array $uniqueColumns
     * @param array $updatingColumn Names of the columns which will be updated
     * @return Model
     */
    public function updateOrCreate(array $uniqueColumns, array $updatingColumn);

    /**
     * Bulk Update existing model.
     *
     * @param array $payloads
     * @param array $uniqueColumns
     * @param array $updatingColumn Names of the columns which will be updated
     * @return bool
     */
    public function upsert(array $payloads, array $uniqueColumns, array $updatingColumn): bool;

    public function upsertProfile(array $payloads, array $uniqueColumns, array $updatingColumn): bool;


    /**
     * Delete model by id.
     *
     * @param int $modelId
     * @return bool
     */
    public function deleteById(int $modelId): bool;

    /**
     * Restore model by id.
     *
     * @param int $modelId
     * @return void
     */
    public function restoreById(int $modelId): void;

    /**
     * Permanently delete model by id.
     *
     * @param int $modelId
     * @return bool
     */
    public function permanentlyDeleteById(int $modelId): bool;

    public function builder(): Model|Builder;

    public function model(): Model;

}
