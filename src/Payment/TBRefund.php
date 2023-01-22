<?php

namespace Karim007\LaravelBkashTokenize\Payment;
use Illuminate\Support\Facades\Facade;
class TBRefund extends TBBaseApi
{
    public function index()
    {
        return view('bkash::bkash-refund');
    }

    public function refund($post_fields)
    {
        //(new BPayment())->getToken();

        $token = session()->get('bkash_token');

        $refund_response = $this->refundCurl($token, $post_fields);

        if (array_key_exists('transactionStatus', $refund_response) && ($refund_response['transactionStatus'] === 'Completed')) {

            // IF REFUND PAYMENT SUCCESS THEN YOU CAN APPLY YOUR CONDITION HERE

            // THEN YOU CAN REDIRECT TO YOUR ROUTE

            return back()->with('successMsg', 'bKash Fund has been Refunded Successfully');
        }

        return back()->with('error', $refund_response['errorMessage']);
    }

    public function refundCurl($token, $post_fields)
    {
        $url = curl_init("$this->baseUrl/checkout/payment/refund");
        $app_key = config("bkash.bkash_app_key");
        $header = array(
            'Content-Type:application/json',
            "authorization:$token",
            "x-app-key:$app_key"
        );

        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_POSTFIELDS, json_encode($post_fields));
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        $resultdata = curl_exec($url);
        curl_close($url);

        return json_decode($resultdata, true);
    }
}
