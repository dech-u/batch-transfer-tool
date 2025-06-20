<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role as BaseRole;

class Role extends BaseRole {
    use HasFactory;

    protected static function booted() {
        static::addGlobalScope('school', static function (Builder $builder) {
            if (Auth::check()) {
                if (empty(Auth::user()->school_id) || Auth::user()->hasRole('Super Admin')) {
                    $builder->where('school_id');
                }
                if (!empty(Auth::user()->school_id) || Auth::user()->hasRole('School Admin')) {
                    $builder->where('school_id', Auth::user()->school_id);
                }
            }
        });
    }
}
