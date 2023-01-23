<?php

namespace Karim007\LaravelBkashTokenize\Payment;

use Karim007\LaravelBkashTokenize\Traits\Helpers;

class TBPayment extends TBBaseApi
{
    use Helpers;

    public function cPayment($request_data_json)
    {
        $response = $this->getToken();
        if ($response['id_token']){
            return $this->getUrl('/checkout/create','POST',$request_data_json);
        }
        return redirect()->back()->with('error-alert2', 'Invalid request try again');

    }
    public function executePayment($paymentID)
    {
        $token = session()->get('bkash_token');
        if (!$token) $this->getToken();
        return $this->getUrl2($paymentID,'/checkout/execute');
    }
    public function queryPayment($paymentID)
    {
        $token = session()->get('bkash_token');
        if (!$token) $this->getToken();
        return $this->getUrl2($paymentID,'/checkout/payment/status');
    }
    public function refreshToken($refresh_token)
    {
        return $this->getUrlToken("/checkout/token/refresh",$refresh_token);
    }
    public function searchTransaction($trxID)
    {
        $post_token = array(
            'trxID' => $trxID
        );
        $posttoken = json_encode($post_token);
        $this->getToken();
        return $this->getUrl3("/checkout/general/searchTransaction",$posttoken);
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
