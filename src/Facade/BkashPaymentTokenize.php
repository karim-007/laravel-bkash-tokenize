<?php

namespace Karim007\LaravelBkashTokenize\Facade;
use Illuminate\Support\Facades\Facade;

/**
 * @method static cPayment($request_data_json, $account=1)
 * @method static executePayment($paymentID, $account=1)
 * @method static queryPayment($paymentID, $account=1)
 * @method static refreshToken($refresh_token, $account=1)
 * @method static searchTransaction($trxID, $account=1)
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
