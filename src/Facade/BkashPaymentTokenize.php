<?php

namespace Karim007\LaravelBkashTokenize\Facade;
use Illuminate\Support\Facades\Facade;

/**
 * @method static cPayment($request_data_json)
 * @method static executePayment($paymentID)
 * @method static queryPayment($paymentID)
 * @method static bkashSuccess($pay_success)
 * @method static refreshToken()
 */
class BkashPaymentTokenize extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'tbpayment';
    }
}
