<?php

namespace Karim007\LaravelBkashTokenize\Payment;
use Karim007\LaravelBkashTokenize\Traits\Helpers;

class TBRefund extends TBBaseApi
{
    use Helpers;

    public function refund($paymentID,$trxID,$amount,$reason='refined amount',$sku='abc')
    {
        $post_token = array(
            'paymentID' => $paymentID,
            'amount' => $amount,
            'trxID' => $trxID,
            'reason' => $reason,
            'sku' => $sku,
        );
        $posttoken = json_encode($post_token);
        $this->getToken();
        return $this->getUrl3("/checkout/payment/refund",$posttoken);
    }

    public function refundStatus($paymentID,$trxID)
    {
        $post_token = array(
            'paymentID' => $paymentID,
            'trxID' => $trxID,
        );
        $posttoken = json_encode($post_token);
        $this->getToken();
        return $this->getUrl3("/checkout/payment/refund",$posttoken);
    }
}
