<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\Saas\SaaSRepository;
use JetBrains\PhpStorm\Pure;

class UserRepository extends SaaSRepository implements UserInterface {

    public function __construct(User $model) {
        parent::__construct($model, 'user');
        $this->model = $model;
    }

    public function getTrashedAdminData($email = null) {
        if ($email) {
            return User::onlyTrashed()->whereHas('roles', function ($query) {
                $query->where('name', 'School Admin');
            })->where(function ($query) use ($email) {
                $query->where('email', 'like', '%' . $email . '%')
                    ->orWhere('first_name', 'like', '%' . $email . '%')
                    ->orWhere('last_name', 'like', '%' . $email . '%');
            })->get();
        }

        return User::whereHas('roles', static function ($query) {
            $query->where('name', 'School Admin');
        })->onlyTrashed()->get();
    }

    public function guardian() {
        return $this->model->role('Guardian');
    }

}
