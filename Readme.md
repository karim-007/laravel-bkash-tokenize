# Bkash Payment Gateway for PHP/Laravel Framework

[![Downloads](https://img.shields.io/packagist/dt/karim007/laravel-bkash-tokenize)](https://packagist.org/packages/karim007/laravel-bkash-tokenize)
[![Starts](https://img.shields.io/packagist/stars/karim007/laravel-bkash-tokenize)](https://packagist.org/packages/karim007/laravel-bkash-tokenize)

## Features

This is a php/laravel wrapper package for [Bkash](https://developer.bka.sh/)

## Requirements

- PHP >=7.4
- Laravel >= 6


## Installation

```bash
composer require karim007/laravel-bkash-tokenize
```

## Examples
![]()<img src="example/bkash1.png" alt="bkash" width="150" height="150">
![]()<img src="example/bkash2.png" alt="bkash" width="150" height="150">
![]()<img src="example/bkash3.png" alt="bkash" width="150" height="150">
![]()<img src="example/bkash4.png" alt="bkash" width="150" height="150">
![]()<img src="example/bkash5.png" alt="bkash" width="150" height="150">


### vendor publish (config)

```bash
php artisan vendor:publish --provider="Karim007\LaravelBkashTokenize\BkashTokenizeServiceProvider" --tag="config"

```

After publish config file setup your credential. you can see this in your config directory bkash.php file

```
"sandbox"         => env("BKASH_SANDBOX", true),
"bkash_app_key"     => env("BKASH_APP_KEY", ""),
"bkash_app_secret" => env("BKASH_APP_SECRET", ""),
"bkash_username"      => env("BKASH_USERNAME", ""),
"bkash_password"     => env("BKASH_PASSWORD", ""),
"callbackURL"     => env("BKASH_CALLBACK_URL", "http://127.0.0.1:8000/bkash/callback"),
'timezone'        => 'Asia/Dhaka', 
```

### Set .env configuration

```
BKASH_SANDBOX=true  #for production use false
BKASH_APP_KEY=""
BKASH_APP_SECRET=""
BKASH_USERNAME=""
BKASH_PASSWORD=""
BKASH_CALLBACK_URL=""
```

## Usage
### 1. publish a controller
```
php artisan vendor:publish --provider="Karim007\LaravelBkashTokenize\BkashTokenizeServiceProvider" --tag="controllers"

```

### 2. you can override the routes (routes must be in authenticate bkash prefer it)
```php
Route::group(['middleware' => ['web']], function () {
    // Payment Routes for bKash
    Route::get('/bkash/payment', [App\Http\Controllers\BkashTokenizePaymentController::class,'index']);
    Route::get('/bkash/create-payment', [App\Http\Controllers\BkashTokenizePaymentController::class,'createPayment'])->name('bkash-create-payment');
    Route::get('/bkash/callback', [App\Http\Controllers\BkashTokenizePaymentController::class,'callBack'])->name('bkash-callBack');

    //search payment
    Route::get('/bkash/search/{trxID}', [App\Http\Controllers\BkashTokenizePaymentController::class,'searchTnx'])->name('bkash-serach');

    //refund payment routes
    Route::get('/bkash/refund', [App\Http\Controllers\BkashTokenizePaymentController::class,'refund'])->name('bkash-refund');
    Route::get('/bkash/refund/status', [App\Http\Controllers\BkashTokenizePaymentController::class,'refundStatus'])->name('bkash-refund-status');

});
```

### 3. payment page
you will find it App\Http\Controllers\BkashTokenizePaymentController
```
public function index()
{
    return view('bkashT::bkash-payment');
}
```


### 4. create payment

```php
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
        if (isset($response['bkashURL'])) return redirect()->away($response['bkashURL']);
        else return redirect()->back()->with('error-alert2', $response['statusMessage']);
    }

```
###create payment response
```array
array[
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
]
```

### 5. callback function

```php
public function callBack(Request $request)
    {
        //paymentID=TR00117B1674409647770&status=success&apiVersion=1.2.0-beta
        if ($request->status == 'success'){
            $response = BkashPaymentTokenize::executePayment($request->paymentID);
            if (!$response){
                $response =  BkashPaymentTokenize::queryPayment($request->paymentID);
            }
            if (isset($response['statusCode']) && $response['statusCode'] == "0000") return $this->success('Thank you for your payment',$response['trxID']);
            return BkashPaymentTokenize::failure($response['statusMessage']);
        }else if ($request->status == 'cancel'){
            return BkashPaymentTokenize::cancel('Your payment is canceled');
        }else{
            return BkashPaymentTokenize::failure('Your transaction is failed');
        }
    }
```
### 5. execute payment response
```json
{
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
   "intent":"sale"
}
```

### 6. query payment response

```json
{
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
}

```
### 7. search transaction

```php
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
```

### 8. refund transaction

```php
public function refund(Request $request)
    {
        $paymentID='paymentID';
        $trxID='trxID';
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
```
### 9. refund status check

```php
public function refundStatus(Request $request)
    {
        $paymentID='paymentID';
        $trxID='trxID';
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
```
#### Required APIs
0. **Developer Portal** (detail Product, workflow, API information): https://developer.bka.sh/docs/checkout-process-overview
1. **Grant Token :** https://developer.bka.sh/v1.2.0-beta/reference#gettokenusingpost
2. **Create Payment :** https://developer.bka.sh/v1.2.0-beta/reference#createpaymentusingpost
3. **Execute Payment :** https://developer.bka.sh/v1.2.0-beta/reference#executepaymentusingpost
4. **Query Payment :** https://developer.bka.sh/v1.2.0-beta/reference#querypaymentusingget
5. **Search Transaction Details :** https://developer.bka.sh/v1.2.0-beta/reference#searchtransactionusingget

### Checkout Demo
1. Go to https://merchantdemo.sandbox.bka.sh/frontend/checkout/version/1.2.0-beta
2. **Wallet Number:** 01877722345
3. **OTP:** 123456
4. **Pin:** 12121

Contributions to the Bkash Payment Gateway package are welcome. Please note the following guidelines before submitting your pull
request.

- Follow [PSR-4](http://www.php-fig.org/psr/psr-4/) coding standards.
- Read bkash API documentations first. Please contact with bkash for their api documentation and sandbox access.

## License

This repository is licensed under the [MIT License](http://opensource.org/licenses/MIT).

Copyright 2022 [md abdul karim](https://github.com/karim-007). We are not affiliated with bkash and don't give any guarantee. 
