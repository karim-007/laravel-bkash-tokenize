<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Karim007\LaravelBkashTokenize\Facade\BkashPaymentTokenize;

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
        $request['mode'] = '0011';
        $request['payerReference'] = $inv;
        $request['currency'] = 'BDT';
        $request['amount'] = 100;
        $request['merchantInvoiceNumber'] = $inv;
        $request['callbackURL'] = config("bkash.callbackURL");;

        $request_data_json = json_encode($request->all());

        $response =  BkashPaymentTokenize::cPayment($request_data_json);
        /*array[
          "statusCode" => "0000"
          "statusMessage" => "Successful"
          "paymentID" => "TR0011WQ1674418613025"
          "bkashURL" => "https://sandbox.payment.bkash.com/redirect/tokenized/?paymentID=TR0011WQ1674418613025&hash=t1-54Dtkmi*wr1KeWV55Z8fl5_DqsaW2q.zQWAQrPtpMsg*5zhuy3w17ZbXEvQ)qU7IT_ ▶"
          "callbackURL" => "http://127.0.0.1:8000/bkash/callback"
          "successCallbackURL" => "http://127.0.0.1:8000/bkash/callback?paymentID=TR0011WQ1674418613025&status=success"
          "failureCallbackURL" => "http://127.0.0.1:8000/bkash/callback?paymentID=TR0011WQ1674418613025&status=failure"
          "cancelledCallbackURL" => "http://127.0.0.1:8000/bkash/callback?paymentID=TR0011WQ1674418613025&status=cancel"
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
            $response = $this->executePayment($request->paymentID);
            if (!$response){
                $response =  $this->queryPayment($request->paymentID);
            }
            if (isset($response['statusCode']) && $response['statusCode'] == "0000") return $this->success('Thank you for your payment',$response['trxID']);
            return $this->failure($response['statusMessage']);
        }else if ($request->status == 'cancel'){
            return $this->cancel('Your payment is canceled');
        }else{
            return $this->failure('Your transaction is failed');
        }
    }

    public function executePayment($paymentID)
    {
        /*{
           "statusCode":"0000",
           "statusMessage":"Successful",
           "paymentID":"TR0011FN1674417661851",
           "payerReference":"485605798",
           "customerMsisdn":"01877722345",
           "trxID":"AAN20A8HOI",
           "amount":"100",
           "transactionStatus":"Completed",
           "paymentExecuteTime":"2023-01-23T02:04:05:736 GMT+0600",
           "currency":"BDT",
           "intent":"sale",
           }*/
        return BkashPaymentTokenize::executePayment($paymentID);
    }
    public function queryPayment($paymentID)
    {
        /*{
            "paymentID":"TR0011FN1674417661851",
           "mode":"0011",
           "paymentCreateTime":"2023-01-23T02:01:06:713 GMT+0600",
           "paymentExecuteTime":"2023-01-23T02:04:05:736 GMT+0600",
           "amount":"100",
           "currency":"BDT",
           "intent":"sale",
           "merchantInvoice":"485605798",
           "trxID":"AAN20A8HOI",
           "transactionStatus":"Completed",
           "verificationStatus":"Complete",
           "statusCode":"0000",
           "statusMessage":"Successful",
           "payerReference":"485605798"
        }*/
        return BkashPaymentTokenize::queryPayment($paymentID);
    }
    private function success($message,$transId)
    {
        return view('bkashT::success',compact('message','transId'));
    }
    private function cancel($message,$transId=null)
    {
        return view('bkashT::failed',compact('message','transId'));
    }
    private function failure($message,$transId=null)
    {
        return view('bkashT::failed',compact('message','transId'));
    }
}
