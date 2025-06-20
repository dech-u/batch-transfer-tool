<?php

namespace App\Repositories\Base;

use App\Repositories\User\UserInterface;
use App\Services\UploadService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Storage;

class BaseRepository implements BaseInterface {
    /**
     * @var Model
     */
    protected Model $model;
    protected string $uploadFolder;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     * @param string $folder
     */
    public function __construct(Model $model, string $folder = '/') {
        $this->model = $model;
        $this->uploadFolder = $folder;
    }

    public function defaultModel() {
        return $this->model;
    }

    /**
     * @param array $columns
     * @param array $relations
     * @param array $where
     * @return Collection
     */
    public function all(array $columns = ['*'], array $relations = [], array $where = []): Collection {
        return $this->defaultModel()->with($relations)->where($where)->get($columns);
    }

    /**
     * Get all trashed models.
     *
     * @return Collection
     */
    public function allTrashed(): Collection {
        return $this->defaultModel()->onlyTrashed()->get();
    }

    /**
     * Find model by id.
     *
     * @param int $modelId
     * @param array $columns
     * @param array $relations
     * @param array $appends
     * @return Model|null
     */
    public function findById(int $modelId, array $columns = ['*'], array $relations = [], array $appends = []): ?Model {
        return $this->defaultModel()->select($columns)->with($relations)->findOrFail($modelId)->append($appends);
    }

    /**
     * Find trashed model by id.
     *
     * @param int $modelId
     * @return Model|null
     */
    public function findTrashedById(int $modelId): ?Model {
        return $this->defaultModel()->withTrashed()->findOrFail($modelId);
    }

    /**
     * Find only trashed model by id.
     *
     * @param int $modelId
     * @return Model|null
     */
    public function findOnlyTrashedById(int $modelId): ?Model {
        return $this->defaultModel()->onlyTrashed()->findOrFail($modelId);
    }

    /**
     * Create a model.
     *
     * @param array $payload
     * @return Model|null
     */
    public function create(array $payload): ?Model {

        foreach ($payload as $column => $value) {
            if ($value instanceof UploadedFile) {
                $payload[$column] = UploadService::upload($value, $this->uploadFolder);
            }
        }
        return $this->defaultModel()->create($payload);
        //        ->fresh()
    }


    /**
     * Create a model.
     *
     * @param array $payload
     * @return bool
     */
    public function createBulk(array $payload): bool {
        foreach ($payload as $key => $arr) {
            foreach ($arr as $column => $value) {
                if ($value instanceof UploadedFile) {
                    $payload[$key][$column] = UploadService::upload($value, $this->uploadFolder);
                }
            }
            $payload[$key]['created_at'] = now();
            $payload[$key]['updated_at'] = now();
        }
        return $this->defaultModel()->insert($payload);
    }

    /**
     * Update existing model.
     *
     * @param int $modelId
     * @param array $payload
     * @return Model|null
     */
    public function update(int $modelId, array $payload): ?Model {
        $model = $this->findById($modelId);

        foreach ($payload as $column => $value) {
            if ($value instanceof UploadedFile) {
                if ($model->getAttributes()[$column]) {
                    UploadService::delete($model->getAttributes()[$column]);
                }
                $payload[$column] = UploadService::upload($value, $this->uploadFolder);
            }
        }
        $model->update($payload);
        return $model;
    }

    /**
     * Update existing model.
     *
     * @param array $uniqueColumns
     * @param array $updatingColumn Names of the columns which will be updated
     * @return Model
     */
    public function updateOrCreate(array $uniqueColumns, array $updatingColumn): Model {
        foreach ($updatingColumn as $column => $value) {
            if ($value instanceof UploadedFile) {
                $updatingColumn[$column] = UploadService::upload($value, $this->uploadFolder);
            }
        }
        return $this->defaultModel()->updateOrCreate($uniqueColumns, $updatingColumn);
    }

    /**
     * Update existing model.
     *
     * @param array $payloads
     * @param array $uniqueColumns
     * @param array $updatingColumn Names of the columns which will be updated
     * @return bool
     */
    public function upsert(array $payloads, array $uniqueColumns, array $updatingColumn): bool {
        foreach ($payloads as $key => $payload) {
            foreach ($payload as $column => $value) {
                if ($value instanceof UploadedFile) {
                    $payloads[$key][$column] = UploadService::upload($value, $this->uploadFolder);
                }
            }
        }

        return $this->defaultModel()->upsert($payloads, $uniqueColumns, $updatingColumn);
    }

    /**
     * Delete model by id.
     *
     * @param int $modelId
     * @return bool
     */
    public function deleteById(int $modelId): bool {
        return $this->findById($modelId)->delete();
    }

    /**
     * Restore model by id.
     *
     * @param int $modelId
     * @return void
     */
    public function restoreById(int $modelId): void {
        $this->findOnlyTrashedById($modelId)->restore();
    }

    /**
     * Permanently delete model by id.
     *
     * @param int $modelId
     * @return bool
     */
    public function permanentlyDeleteById(int $modelId): bool {
        return $this->findTrashedById($modelId)->forceDelete();
    }


    /**
     * Returns the builder so that you can use your own conditions and functions.
     *
     * @return Model|Builder
     */
    public function builder(): Model|Builder {
        return $this->defaultModel();
    }

    /**
     * Returns the new model instance so that you can use your own conditions and functions.
     *
     * @return Model
     */
    public function model(): Model {
        return new $this->model();
    }

    public function upsertProfile(array $payloads, array $uniqueColumns, array $updatingColumn): bool
    {
        $existingRecords = $this->defaultModel()->whereIn($uniqueColumns[0], array_column($payloads, $uniqueColumns[0]))->get();

        foreach ($existingRecords as $key => $row) {
            if ($row->image && $row->getRawOriginal('image')) {
                if (Storage::disk('public')->exists($row->getRawOriginal('image'))) {
                    Storage::disk('public')->delete($row->getRawOriginal('image'));
                }
            }
        }
        foreach ($payloads as $key => $payload) {
            foreach ($payload as $column => $value) {
                if ($value instanceof UploadedFile) {
                    $payloads[$key][$column] = UploadService::upload($value, $this->uploadFolder);
                }
            }
        }
        return $this->defaultModel()->upsert($payloads, $uniqueColumns, $updatingColumn);
    }
}
