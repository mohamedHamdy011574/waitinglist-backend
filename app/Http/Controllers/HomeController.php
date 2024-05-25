<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index() {
    // { die('aayu');
        // return redirect()->route('home');
        return view('welcome');
    }
    public function sendHTTP2Push($http2ch, $http2_server, $apple_cert, $app_bundle_id, $message, $token) {

        $milliseconds = round(microtime(true) * 1000);

        // url (endpoint)
        $url = "{$http2_server}/3/device/{$token}";
        

        // certificate
        $cert = realpath('iosCertificates/waitinglist.pem');
        if(!$cert || !is_readable($cert)){
            die("error: myfile.pem is not readable! realpath: \"{$cert}\" - working dir: \"".getcwd()."\" effective user: ".print_r(posix_getpwuid(posix_geteuid()),true));
        }
        // headers
        $headers = array(
            "apns-topic: {$app_bundle_id}",
            "User-Agent: My Sender"
        );

        // other curl options
        curl_setopt_array($http2ch, array(
            CURLOPT_URL => "{$url}",
            CURLOPT_PORT => 443,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POST => TRUE,
            CURLOPT_POSTFIELDS => $message,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSLCERT => $cert,
            CURLOPT_HEADER => 1,
	    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
        ));

        // go...
        $result = curl_exec($http2ch);
        if ($result === FALSE) {
            throw new \Exception('Curl failed with error: ' . curl_error($http2ch));
        }


        // get respnse
        $status = curl_getinfo($http2ch, CURLINFO_HTTP_CODE);

        $duration = round(microtime(true) * 1000) - $milliseconds;

        return $status;
    }
    public function xyz()
    {
        // open connection
        if (!defined('CURL_HTTP_VERSION_2_0')) {
            define('CURL_HTTP_VERSION_2_0', 3);
        }
        $http2ch = curl_init();
        curl_setopt($http2ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);

        // send push
        $apple_cert = __DIR__ . '\iosCertificates\waitinglist.pem';
        $message = '{"aps":{"alert":"from local!","sound":"default"}}';
        $token = '55cd5d158d2c9c5ef4c5b32b120bcb01b384868ed897fe976a67002e593e9353';
        $http2_server = 'https://api.development.push.apple.com:443';   // or 'api.push.apple.com' if production
        $app_bundle_id = 'clueapps.com.WaitingList';

        // close connection
        for ($i = 0; $i < 1; $i++) {
            $status = $this->sendHTTP2Push($http2ch, $http2_server, $apple_cert, $app_bundle_id, $message, $token);
            echo "Response from apple -> {$status}\n";
        }

        curl_close($http2ch);
        # code...
    }
}
