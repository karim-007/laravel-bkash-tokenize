<?php

namespace Karim007\LaravelBkashTokenize\Traits;


trait Helpers
{
    /**
     * @return string|null
     */
    public function getIp()
    {
        return request()->ip();
    }

    protected function getUrlToken($url,$refresh_token=null, $account=null)
    {
        session()->forget('bkash_token');
        session()->forget('bkash_token_type');
        session()->forget('bkash_refresh_token');
        $post_token = array(
            'app_key' => config("bkash.bkash_app_key$account"),
            'app_secret' => config("bkash.bkash_app_secret$account"),
            'refresh_token' => $refresh_token,
        );
        $url = curl_init($this->baseUrl.$url);
        $post_token = json_encode($post_token);

        $username = config("bkash.bkash_username$account");
        $password = config("bkash.bkash_password$account");

        $header = array(
            'Content-Type:application/json',
            "password:$password",
            "username:$username"
        );
        curl_setopt($url,CURLOPT_HTTPHEADER, $header);
        curl_setopt($url,CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url,CURLOPT_POSTFIELDS, $post_token);
        curl_setopt($url,CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        $resultdata = curl_exec($url);
        curl_close($url);

        $response = json_decode($resultdata, true);
        if (array_key_exists('msg', $response)) {
            return $response;
        }
        if (isset($response['id_token']) && isset($response['token_type']) && isset($response['refresh_token'])){
            session()->put('bkash_token', $response['id_token']);
            session()->put('bkash_token_type', $response['token_type']);
            session()->put('bkash_refresh_token', $response['refresh_token']);
        }
        return $response;
    }

    protected function getUrl($url, $method, $data=null, $account=null)
    {
        $token = session()->get('bkash_token');
        $app_key = config("bkash.bkash_app_key$account");

        $url = curl_init($this->baseUrl.$url);
        $header = array(
            'Content-Type:application/json',
            "authorization: $token",
            "x-app-key: $app_key"
        );

        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        if ($data) curl_setopt($url, CURLOPT_POSTFIELDS, $data);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $resultdata = curl_exec($url);
        curl_close($url);
        return json_decode($resultdata, true);
    }

    protected function getUrl2($paymentID, $url, $account=null){
        $post_token = array(
            'paymentID' => $paymentID
        );
        $url = curl_init($this->baseUrl.$url);
        $posttoken = json_encode($post_token);
        $app_key = config("bkash.bkash_app_key$account");
        $header = array(
            'Content-Type:application/json',
            'Authorization:' . session()->get('bkash_token'),
            'X-APP-Key:'.$app_key
        );
        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $posttoken);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $resultdata = curl_exec($url);
        curl_close($url);

        return json_decode($resultdata, true);
    }

    protected function getUrl3($url,$data, $account=null){
        $url = curl_init($this->baseUrl.$url);
        $app_key = config("bkash.bkash_app_key$account");
        $header = array(
            'Content-Type:application/json',
            'Authorization:' . session()->get('bkash_token'),
            'x-app-key:'.$app_key
        );
        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        if($data) curl_setopt($url, CURLOPT_POSTFIELDS, $data);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $resultdata = curl_exec($url);
        curl_close($url);

        return json_decode($resultdata, true);
    }

    protected function getToken($account=null)
    {
        return $this->getUrlToken('/checkout/token/grant',null, $account);
    }
}
