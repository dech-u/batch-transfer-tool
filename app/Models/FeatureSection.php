<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Storage;

class FeatureSection extends Model
{
    use HasFactory;
    protected $fillable = ['title','heading','rank'];


    protected static function boot() {
        parent::boot();
        static::deleting(static function ($feature_section) { // before delete() method call this
            if ($feature_section->feature_section_list) {
                foreach ($feature_section->feature_section_list as $section_feature) {
                    if (Storage::disk('public')->exists($section_feature->getRawOriginal('image'))) {
                        Storage::disk('public')->delete($section_feature->getRawOriginal('image'));
                    }
                }
                $feature_section->feature_section_list()->delete();
            }
        });
    }

    /**
     * Get all of the feature_section_list for the FeatureSection
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function feature_section_list()
    {
        return $this->hasMany(FeatureSectionList::class);
    }
}
