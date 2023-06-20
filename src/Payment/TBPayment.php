<?php

namespace Karim007\LaravelBkashTokenize\Payment;

use Karim007\LaravelBkashTokenize\Traits\Helpers;

class TBPayment extends TBBaseApi
{
    use Helpers;

    public function cPayment($request_data_json, $account=1)
    {
        if ($account == 1) $account=null;
        else $account="_$account";
        $response = $this->getToken($account);

        if (isset($response['id_token']) && $response['id_token']){
            return $this->getUrl('/checkout/create','POST',$request_data_json, $account);
        }
        return $response;
    }
    public function executePayment($paymentID, $account=1)
    {
        if ($account == 1) $account=null;
        else $account="_$account";

        $token = session()->get('bkash_token');
        if (!$token) $this->getToken($account);
        return $this->getUrl2($paymentID,'/checkout/execute', $account);
    }
    public function queryPayment($paymentID, $account=1)
    {
        if ($account == 1) $account=null;
        else $account="_$account";

        $token = session()->get('bkash_token');
        if (!$token) $this->getToken($account);
        return $this->getUrl2($paymentID,'/checkout/payment/status', $account);
    }
    public function refreshToken($refresh_token, $account=1)
    {
        if ($account == 1) $account=null;
        else $account="_$account";

        return $this->getUrlToken("/checkout/token/refresh",$refresh_token, $account);
    }
    public function searchTransaction($trxID, $account=1)
    {
        if ($account == 1) $account=null;
        else $account="_$account";

        $post_token = array(
            'trxID' => $trxID
        );
        $posttoken = json_encode($post_token);
        $this->getToken($account);
        return $this->getUrl3("/checkout/general/searchTransaction",$posttoken, $account);
    }
    public function success($message,$transId)
    {
        return view('bkashT::success',compact('message','transId'));
    }
    public function cancel($message,$transId=null)
    {
        return view('bkashT::failed',compact('message','transId'));
    }
    public function failure($message,$transId=null)
    {
        return view('bkashT::failed',compact('message','transId'));
    }

}
