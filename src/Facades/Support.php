<?php

namespace Roberts\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Roberts\Support\Support
 */
class Support extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Roberts\Support\Support::class;
    }
}
