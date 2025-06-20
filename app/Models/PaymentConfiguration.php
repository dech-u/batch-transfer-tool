<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentConfiguration extends Model
{
    use HasFactory;
    protected $fillable = [
        'payment_method',
        'api_key',
        'secret_key',
        'webhook_secret_key',
        'status',
        'currency_code',
        'currency_symbol',
        'school_id',
        'bank_name',
        'account_name',
        'account_no',
    ];

    public function getConnectionName()
    {
        // Replace this with your logic to determine the connection name
        // For example, you might get it from session or config
        return session('db_connection_name') ?? config('database.default');
    }

    public function scopeOwner($query)
    {
        if(Auth::user()){
            if (Auth::user()->hasRole('Super Admin')) {
                return $query->where('school_id',null);
            }

            if (Auth::user()->hasRole('School Admin')) {
                return $query->where('school_id', Auth::user()->school_id);
            }

            if (Auth::user()->hasRole('Student')) {
                return $query->where('school_id', Auth::user()->school_id);
            }

            if (Auth::user()->hasRole('Guardian')) {
                if(request('child_id')){
                $childId = request('child_id');
                $studentAuth = Students::where('id',$childId)->first();
                return $query->where('school_id', $studentAuth->school_id);
                }
                return $query;
            }
        }

        return $query;
    }
}
