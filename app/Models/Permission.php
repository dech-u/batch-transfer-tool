<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission as BasePermission;

class Permission extends BasePermission {
    use HasFactory;

    protected static function booted() {
        static::addGlobalScope('school', static function (Builder $builder) {
            if (Auth::check()) {
                if (Auth::user()->hasRole('Super Admin')) {
                    // Show only permissions only which are assigned Super Admin
                    $builder->whereHas('roles', function ($q) {
                        $q->where('name', 'Super Admin');
                    });
                }
                if (Auth::user()->hasRole('School Admin')) {
                    // Show only permissions which are not assigned to Super Admin
                    $builder->whereHas('roles', function ($q) {
                        $q->where('name', '!=', 'Super Admin')->where(function ($q) {
                            $q->where('school_id', Auth::user()->school_id)->orWhere('name', 'School Admin');
                        });
                    });
                }
                // School related staffs
                if (Auth::user()->school_id && !Auth::user()->hasRole('School Admin')) {
                    $builder->whereHas('roles', function ($q) {
                        $q->where(function ($q) {
                            $q->where('school_id', Auth::user()->school_id)->whereIn('name', Auth::user()->getRoleNames());
                        });
                    });
                } else if(!Auth::user()->hasRole('Super Admin') && !Auth::user()->school_id) {
                    // Super admin related staffs
                    $builder->whereHas('roles', function ($q) {
                        $q->where(function ($q) {
                            $q->whereNull('school_id')->whereIn('name', Auth::user()->getRoleNames());
                        });
                    });
                }
            }
        });
    }
}
