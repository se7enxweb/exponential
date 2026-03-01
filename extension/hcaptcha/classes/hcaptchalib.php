<?php
/*
 * This is a PHP library that handles calling reCAPTCHA.
 *    - Documentation and latest version
 *          http://recaptcha.net/plugins/php/
 *    - Get a reCAPTCHA API Key
 *          https://www.google.com/recaptcha/admin/create
 *    - Discussion group
 *          http://groups.google.com/group/recaptcha
 *
 * Copyright (c) 2007 reCAPTCHA -- http://recaptcha.net
 * AUTHORS:
 *   Mike Crawford
 *   Ben Maurer
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

//require_once 'extension/recaptcha/classes/recaptcha/src/ReCaptcha/ReCaptcha.php';

/**
 * The hCAPTCHA server URL's
 */
define("HCAPTCHA_API_SERVER", "http://js.hcaptcha.com/1/api");
define("HCAPTCHA_API_SECURE_SERVER", "https://js.hcaptcha.com/1/api");
define("HCAPTCHA_VERIFY_SERVER", "www.hcaptcha.com");

class hCaptchaLibrary {

/**
 * Encodes the given data into a query string format
 * @param $data - array of string elements to be encoded
 * @return string - encoded request
 */
public static function _recaptcha_qsencode ($data) {
        $req = "";
        foreach ( $data as $key => $value )
                $req .= $key . '=' . urlencode( stripslashes($value) ) . '&';

        // Cut the last '&'
        $req=substr($req,0,strlen($req)-1);
        return $req;
}



/**
 * Submits an HTTP POST to a hCaptcha server
 * @param string $host
 * @param string $path
 * @param array $data
 * @param int port
 * @return array response
 */
public static function _recaptcha_http_post($host, $path, $data, $port = 80) {

        $req = _recaptcha_qsencode ($data);

        $http_request  = "POST $path HTTP/1.0\r\n";
        $http_request .= "Host: $host\r\n";
        $http_request .= "Content-Type: application/x-www-form-urlencoded;\r\n";
        $http_request .= "Content-Length: " . strlen($req) . "\r\n";
        $http_request .= "User-Agent: hCaptcha/PHP\r\n";
        $http_request .= "\r\n";
        $http_request .= $req;

        $response = '';
        if( false == ( $fs = @fsockopen($host, $port, $errno, $errstr, 10) ) ) {
                die ('Could not open socket');
        }

        fwrite($fs, $http_request);

        while ( !feof($fs) )
                $response .= fgets($fs, 1160); // One TCP-IP packet
        fclose($fs);
        $response = explode("\r\n\r\n", $response, 2);

        return $response;
}



/**
 * Gets the challenge HTML (javascript and non-javascript version).
 * This is called from the browser, and the resulting hCaptcha HTML widget
 * is embedded within the HTML form it was called from.
 * @param string $pubkey A public key for hCaptcha
 * @param string $error The error given by hCaptcha (optional, default is null)
 * @param boolean $use_ssl Should the request be made over ssl? (optional, default is false)

 * @return string - The HTML to be embedded in the user's form.
 */
public static function get_html ($pubkey, $error = null, $use_ssl = false)
{
        if ($pubkey == null || $pubkey == '') {
                die ("To use hCaptcha you must get an API key from <a href='https://www.google.com/recaptcha/admin/create'>https://www.google.com/recaptcha/admin/create</a>");
        }

        if ($use_ssl) {
                $server = HCAPTCHA_API_SECURE_SERVER;
        } else {
                $server = HCAPTCHA_API_SERVER;
        }

        $errorpart = "";
        if ($error) {
           $errorpart = "&amp;error=" . $error;
        }

        return '<script src="'. $server . '.js" async defer></script>'
               . '<div class="h-captcha" data-sitekey="' . $pubkey . '"></div>';
}

/**
 * A ReCaptchaResponse is returned from recaptcha_check_answer()
 */
/*
class ReCaptchaResponse {
        var $is_valid;
        var $error;
}
*/

/**
  * Calls an HTTP POST public static function to verify if the user's guess was correct
  * @param string $privatekey
  * @param string $remoteip
  * @param string $challenge
  * @param string $response
  * @param array $extra_params an array of extra variables to post to the server
  * @return ReCaptchaResponse
  */
public static function check_answer ($privatekey, $remoteip, $challenge, $response, $extra_params = array())
{
        if ($privatekey == null || $privatekey == '') {
                die ("To use hCaptcha you must get an API key from <a href='https://www.google.com/recaptcha/admin/create'>https://www.google.com/recaptcha/admin/create</a>");
        }

        if ($remoteip == null || $remoteip == '') {
                die ("For security reasons, you must pass the remote ip to hCaptcha");
        }

        // Discard spam submissions

        //$recaptcha = new \ReCaptcha\ReCaptcha( $privatekey );
        //$resp = $recaptcha->setExpectedHostname( $_SERVER['SERVER_NAME'] )
        //                  ->verify( $response, $remoteip );

        $data = array(
            'secret' => $privatekey,
            'response' => $_POST['h-captcha-response']
        );
        $verify = curl_init();
        curl_setopt($verify, CURLOPT_URL, "https://hcaptcha.com/siteverify");
        curl_setopt($verify, CURLOPT_POST, true);
        curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($verify);
        // var_dump($response);
        $responseData = json_decode($response);

        if($responseData->success) {
            // your success code goes here
        } 
        else {
           // return error to user; they did not pass
        }
/*
        if ( $resp->isSuccess() )
        {
            //$recaptcha_response->is_valid = true;
            $resp->is_valid = true;
        }
        else
        {
            //$recaptcha_response->is_valid = false;
            //$resp->is_valid = false;
            //$recaptcha_response->error = $resp->error-codes;
            //$resp->error = $resp->error-codes;
        }
*/
        return $responseData;
}

/**
 * gets a URL where the user can sign up for hCaptcha. If your application
 * has a configuration page where you enter a key, you should provide a link
 * using this function.
 * @param string $domain The domain where the page is hosted
 * @param string $appname The name of your application
 */
public static function get_signup_url ($domain = null, $appname = null) {
        return "https://www.google.com/recaptcha/admin/create?" .  _recaptcha_qsencode (array ('domains' => $domain, 'app' => $appname));
}

public static function _recaptcha_aes_pad($val) {
        $block_size = 16;
        $numpad = $block_size - (strlen ($val) % $block_size);
        return str_pad($val, strlen ($val) + $numpad, chr($numpad));
}

/* Mailhide related code */

public static function _recaptcha_aes_encrypt($val,$ky) {
        if (! function_exists ("mcrypt_encrypt")) {
                die ("To use hCaptcha Mailhide, you need to have the mcrypt php module installed.");
        }
        $mode=MCRYPT_MODE_CBC;   
        $enc=MCRYPT_RIJNDAEL_128;
        $val=_recaptcha_aes_pad($val);
        return mcrypt_encrypt($enc, $ky, $val, $mode, "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0");
}


public static function _recaptcha_mailhide_urlbase64 ($x) {
        return strtr(base64_encode ($x), '+/', '-_');
}

/* gets the hCaptcha Mailhide url for a given email, public key and private key */
public static function recaptcha_mailhide_url($pubkey, $privatekey, $email) {
        if ($pubkey == '' || $pubkey == null || $privatekey == "" || $privatekey == null) {
                die ("To use hCaptchay Mailhide, you have to sign up for a public and private key, " .
                     "you can do so at <a href='http://www.google.com/recaptcha/mailhide/apikey'>http://www.google.com/recaptcha/mailhide/apikey</a>");
        }
        

        $ky = pack('H*', $privatekey);
        $cryptmail = _recaptcha_aes_encrypt ($email, $ky);
        
        return "http://www.google.com/recaptcha/mailhide/d?k=" . $pubkey . "&c=" . _recaptcha_mailhide_urlbase64 ($cryptmail);
}

/**
 * gets the parts of the email to expose to the user.
 * eg, given johndoe@example,com return ["john", "example.com"].
 * the email is then displayed as john...@example.com
 */
public static function _recaptcha_mailhide_email_parts ($email) {
        $arr = preg_split("/@/", $email );

        if (strlen ($arr[0]) <= 4) {
                $arr[0] = substr ($arr[0], 0, 1);
        } else if (strlen ($arr[0]) <= 6) {
                $arr[0] = substr ($arr[0], 0, 3);
        } else {
                $arr[0] = substr ($arr[0], 0, 4);
        }
        return $arr;
}

/**
 * Gets html to display an email address given a public an private key.
 * to get a key, go to:
 *
 * http://www.google.com/recaptcha/mailhide/apikey
 */
public static function recaptcha_mailhide_html($pubkey, $privatekey, $email) {
        $emailparts = _recaptcha_mailhide_email_parts ($email);
        $url = recaptcha_mailhide_url ($pubkey, $privatekey, $email);
        
        return htmlentities($emailparts[0]) . "<a href='" . htmlentities ($url) .
                "' onclick=\"window.open('" . htmlentities ($url) . "', '', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=500,height=300'); return false;\" title=\"Reveal this e-mail address\">...</a>@" . htmlentities ($emailparts [1]);

    }

}

?>