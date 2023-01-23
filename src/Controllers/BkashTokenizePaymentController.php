<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Karim007\LaravelBkashTokenize\Facade\BkashPaymentTokenize;
use Karim007\LaravelBkashTokenize\Facade\BkashRefundTokenize;

class BkashTokenizePaymentController extends Controller
{
    public function index()
    {
        return view('bkashT::bkash-payment');
    }
    public function createPayment(Request $request)
    {
        $inv = uniqid();
        $request['intent'] = 'sale';
        $request['mode'] = '0011'; //0011 for checkout
        $request['payerReference'] = $inv;
        $request['currency'] = 'BDT';
        $request['amount'] = 10;
        $request['merchantInvoiceNumber'] = $inv;
        $request['callbackURL'] = config("bkash.callbackURL");;

        $request_data_json = json_encode($request->all());

        $response =  BkashPaymentTokenize::cPayment($request_data_json);
        /*array[
          "statusCode" => "0000"
          "statusMessage" => "Successful"
          "paymentID" => "TR0011WQ1674418613025"
          "bkashURL" => "https://sandbox.payment.bkash.com/redirect/tokenized/?paymentID=TR0011WQ1674418613025&hash=t1-54Dtkmi*wr1KeWV55Z8fl5_DqsaW2q.zQWAQrPtpMsg*5zhuy3w17ZbXEvQ)qU7IT_ ▶"
          "callbackURL" => "base_url/bkash/callback"
          "successCallbackURL" => "base_url/bkash/callback?paymentID=TR0011WQ1674418613025&status=success"
          "failureCallbackURL" => "base_url/bkash/callback?paymentID=TR0011WQ1674418613025&status=failure"
          "cancelledCallbackURL" => "base_url/bkash/callback?paymentID=TR0011WQ1674418613025&status=cancel"
          "amount" => "100"
          "intent" => "sale"
          "currency" => "BDT"
          "paymentCreateTime" => "2023-01-23T02:16:57:784 GMT+0600"
          "transactionStatus" => "Initiated"
          "merchantInvoiceNumber" => "63cd99abe6bae"
        ]*/
        if (isset($response['bkashURL'])) return redirect()->away($response['bkashURL']);
        else return redirect()->back()->with('error-alert2', $response['statusMessage']);
    }

    public function callBack(Request $request)
    {
        //paymentID=TR00117B1674409647770&status=success&apiVersion=1.2.0-beta

        if ($request->status == 'success'){
            $response = BkashPaymentTokenize::executePayment($request->paymentID);
            if (!$response){ //if executePayment payment not found call queryPayment
                $response = BkashPaymentTokenize::queryPayment($request->paymentID);
            }

            if (isset($response['statusCode']) && $response['statusCode'] == "0000" && $response['transactionStatus'] == "Completed") {
                /*
                 * for refund need to store
                 * paymentID and trxID
                 * */
                return BkashPaymentTokenize::success('Thank you for your payment', $response['trxID']);
            }
            return BkashPaymentTokenize::failure($response['statusMessage']);
        }else if ($request->status == 'cancel'){
            return BkashPaymentTokenize::cancel('Your payment is canceled');
        }else{
            return BkashPaymentTokenize::failure('Your transaction is failed');
        }
    }

    public function searchTnx($trxID)
    {
        //response
        /*{
            "trxID":"AAN60A8IOQ",
           "initiationTime":"2023-01-23T12:06:05:000 GMT+0600",
           "completedTime":"2023-01-23T12:06:05:000 GMT+0600",
           "transactionType":"bKash Tokenized Checkout via API",
           "customerMsisdn":"01877722345",
           "transactionStatus":"Completed",
           "amount":"20",
           "currency":"BDT",
           "organizationShortCode":"50022",
           "statusCode":"0000",
           "statusMessage":"Successful"
        }*/
        return BkashPaymentTokenize::searchTransaction($trxID);
    }

    public function refund(Request $request)
    {
        $paymentID='TR0011WI1674466909042';//TR0011WI1674466909042
        $trxID='AAN30A8M4T'; //AAN30A8M4T
        $amount=5;
        $reason='this is test reason';
        $sku='abc';
        //response
        /*{
            "statusCode":"0000",
           "statusMessage":"Successful",
           "originalTrxID":"AAN30A8M4T",
           "refundTrxID":"AAN30A8M5N",
           "transactionStatus":"Completed",
           "amount":"5",
           "currency":"BDT",
           "charge":"0.00",
           "completedTime":"2023-01-23T15:53:29:120 GMT+0600"
        }*/
        return BkashRefundTokenize::refund($paymentID,$trxID,$amount,$reason,$sku);
    }
    public function refundStatus(Request $request)
    {
        $paymentID='TR0011WI1674466909042';
        $trxID='AAN30A8M4T';
        /*{
            "statusCode":"0000",
           "statusMessage":"Successful",
           "originalTrxID":"AAN30A8M4T",
           "refundTrxID":"AAN30A8M5N",
           "transactionStatus":"Completed",
           "amount":"5",
           "currency":"BDT",
           "charge":"0.00",
           "completedTime":"2023-01-23T15:53:29:120 GMT+0600"
        }*/
        return BkashRefundTokenize::refundStatus($paymentID,$trxID);
    }
}
