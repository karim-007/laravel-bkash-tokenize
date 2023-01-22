<?php

namespace Karim007\LaravelBkashTokenize\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * @method static index()
 * @method static refund($post_fields)
 */
class BkashRefundTokenize extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'tbrefundPayment';
    }
}
