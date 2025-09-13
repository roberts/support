<?php

namespace Roberts\Support\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Roberts\Support\Traits\HasSubscriptionStatus;

class TestSubscriptionModel extends Model
{
    use HasSubscriptionStatus;

    protected $table = 'test_subscription_models';

    protected $fillable = ['name', 'status'];
}
