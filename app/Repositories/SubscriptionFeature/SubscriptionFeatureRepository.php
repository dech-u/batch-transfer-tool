<?php

namespace App\Repositories\SubscriptionFeature;

use App\Models\SubscriptionFeature;
use App\Repositories\Base\BaseRepository;

class SubscriptionFeatureRepository extends BaseRepository implements SubscriptionFeatureInterface {
    public function __construct(SubscriptionFeature $model) {
        parent::__construct($model,'subscription_feature');
    }
}
