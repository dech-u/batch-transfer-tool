<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class School extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $fillable = [
        'name',
        'address',
        'support_phone',
        'support_email',
        'tagline',
        'logo',
        'admin_id',
        'status',
        'domain',
        'database_name',
        'code',
        'type',
        'domain_type'
    ];

    protected $hidden = ['database_name'];

    //Getter Attributes
    public function getLogoAttribute($value) {
        return url(Storage::url($value));
    }

    public function user(){
        return $this->belongsTo(User::class,'admin_id')->withTrashed();
    }

    public function subscription()
    {
        return $this->hasMany(Subscription::class);
    }

    public function addon()
    {
        $today_date = Carbon::now()->format('Y-m-d');
        return $this->hasManyThrough(Feature::class,AddonSubscription::class,'school_id','id','id','feature_id')
        ->where('start_date','<=',$today_date)->where('end_date','>=',$today_date);
    }

    public function features()
    {
        $today_date = Carbon::now()->format('Y-m-d');
        return $this->hasManyThrough(SubscriptionFeature::class,Subscription::class)->where('start_date','<=',$today_date)->where('end_date','>=',$today_date);
    }

    public function test() {
        return $this->features->merge($this->addon);
    }

    public function extra_school_details()
    {
        return $this->hasMany(ExtraSchoolData::class, 'school_id', 'id'); 
    }


//    public function features() {
////        return $this->subscription()->union($this->addon());
//        return ["subscriptions" => $this->subscription(), "addon" => $this->addon()];
//    }

}
