<?php

namespace Karim007\LaravelBkashTokenize\Facade;
use Illuminate\Support\Facades\Facade;

/**
 * @method static cPayment($request_data_json)
 * @method static executePayment($paymentID)
 * @method static queryPayment($paymentID)
 * @method static bkashSuccess($pay_success)
 * @method static refreshToken($refresh_token)
 * @method static searchTransaction($trxID)
 * @method static success($message,$transId)
 * @method static cancel($message,$transId=null)
 * @method static failure($message,$transId=null)
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
