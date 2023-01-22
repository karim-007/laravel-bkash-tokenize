# Bkash Payment Gateway for PHP/Laravel Framework

[![Downloads](https://img.shields.io/packagist/dt/karim007/laravel-bkash)](https://packagist.org/packages/karim007/laravel-bkash)
[![Starts](https://img.shields.io/packagist/stars/karim007/laravel-bkash)](https://packagist.org/packages/karim007/laravel-bkash)

## Features

This is a php/laravel wrapper package for [Bkash](https://developer.bka.sh/)

## Requirements

- PHP >=7.4
- Laravel >= 6


## Installation

```bash
composer require karim007/laravel-bkash-tokenize
```

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
```
Route::group(['middleware' => ['web']], function () {
    // Payment Routes for bKash
    Route::get('/bkash/payment', [App\Http\Controllers\BkashTokenizePaymentController::class,'index']);
    Route::get('/bkash/create-payment', [App\Http\Controllers\BkashTokenizePaymentController::class,'createPayment'])->name('bkash-create-payment');
    Route::get('/bkash/callback', [App\Http\Controllers\BkashTokenizePaymentController::class,'callBack'])->name('bkash-callBack');
});
```

### 3. you can also override the methods

#must be included in your controller
```
use Karim007\LaravelBkashTokenize\Facade\BkashPaymentTokenize;
```

### 4. payment page
```
public function index()
{
    return view('bkashT::bkash-payment');
}
```


### 4. create payment

```
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

### 5. callback function

```
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


```
### 5. execute payment
```php
    public function executePayment($paymentID)
    {
        return BkashPaymentTokenize::executePayment($paymentID);
    }
```

### 6. query payment

```
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

```

### 7. success,cancel,failure

```
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
