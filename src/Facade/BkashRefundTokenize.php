<?php

namespace Karim007\LaravelBkashTokenize\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * @method static refund($paymentID,$trxID,$amount,$reason=null,$sku=null)
 * @method static refundStatus($paymentID,$trxID)
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
        return 'tbrefund';
    }
}
