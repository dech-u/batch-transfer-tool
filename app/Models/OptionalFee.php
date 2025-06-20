<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class OptionalFee extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'student_id',
        'class_id',
        'payment_transaction_id',
        'fees_class_id',
        'mode',
        'cheque_no',
        'amount',
        'fees_paid_id',
        'date',
        'session_year_id',
        'school_id',
        'created_at',
        'updated_at'
    ];
    protected $appends = ['mode_name'];

    public function scopeOwner($query)
    {
        if(Auth::user()){
            if (Auth::user()->hasRole('Super Admin')) {
                return $query;
            }

            if (Auth::user()->hasRole('School Admin') || Auth::user()->hasRole('Teacher')) {
                return $query->where('school_id', Auth::user()->school_id);
            }

            if (Auth::user()->hasRole('Student')) {
                return $query->where('school_id', Auth::user()->school_id);
            }
        }

        return $query;
    }

    public function fees_paid() {
        return $this->belongsTo(FeesPaid::class, 'fees_paid_id')->withTrashed();
    }

    public function student(){
        return $this->belongsTo(User::class, 'student_id')->withTrashed();
    }

    public function fees_class_type(){
        return $this->belongsTo(FeesClassType::class, 'fees_class_id');
    }

    public function getModeNameAttribute(){
        if($this->mode == "1"){
            return 'Cash';
        }elseif($this->mode == "2"){
            return 'Cheque';
        }else{
            return 'Online';
        }
    }

}
