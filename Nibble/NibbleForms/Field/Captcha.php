<?php
namespace Nibble\NibbleForms\Field;

use Nibble\NibbleForms\Field;

class Captcha extends Field
{

    public $error = array();
    protected $label;

    public function __construct($label = 'Humanity Check')
    {
        $this->label = $label;
    }

    public function returnField($form_name, $name, $value = '')
    {
        $field = <<<FIELD
<script type="text/javascript"
 src="http://www.google.com/recaptcha/api/challenge?k=%1\$s">
</script>
<noscript>
 <iframe src="http://www.google.com/recaptcha/api/noscript?k=%1\$s"
     height="300" width="500" frameborder="0"></iframe><br>
 <textarea name="recaptcha_challenge_field" rows="3" cols="40">
 </textarea>
 <input type="hidden" name="{$form_name}[recaptcha_response_field]"
     value="manual_challenge">
</noscript>
FIELD;
        $class = !empty($this->error) ? ' class="error"' : '';

        return array(
            'messages' => !empty($this->custom_error) && !empty($this->error) ? $this->custom_error : $this->error,
            'label' => $this->label == false ? false : sprintf('<label for="%s"%s>%s</label>', $name, $class, $this->label),
            'field' => sprintf($field, 'YOUR_PUBLIC_KEY'),
            'html' => $this->html
        );
    }

    public function validate($val)
    {

        $url = 'http://www.google.com/recaptcha/api/verify';
        $data = array(
            'privatekey' => urlencode('YOUR_PRIVATE_KEY'),
            'remoteip' => urlencode($_SERVER['REMOTE_ADDR']),
            'challenge' => urlencode($_REQUEST['recaptcha_challenge_field']),
            'response' => urlencode($_REQUEST['recaptcha_response_field']),
        );

        $data_string = '';
        foreach ($data as $key => $val) {
            $data_string .= $key . '=' . $val . '&';
        }
        $data_string = rtrim($data_string, '&');

        $host = 'www.google.com';
        $http_request = "POST /recaptcha/api/verify HTTP/1.0\r\n";
        $http_request .= "Host: $host\r\n";
        $http_request .= "Content-Type: application/x-www-form-urlencoded;\r\n";
        $http_request .= "Content-Length: " . strlen($data_string) . "\r\n";
        $http_request .= "User-Agent: reCAPTCHA/PHP\r\n";
        $http_request .= "\r\n";
        $http_request .= $data_string;

        $response = '';
        if (false == ( $fs = @fsockopen($host, 80, $errno, $errstr, 10) )) {
            die('Could not open socket');
        }

        fwrite($fs, $http_request);

        while (!feof($fs)) {
            $response .= fgets($fs, 1160); // One TCP-IP packet
        }
        fclose($fs);
        $response = explode("\r\n\r\n", $response);
        $response = explode("\n", $response[1]);
        if (!isset($response[0]) || $response[0] != 'true') {
            $this->error[] = 'You failed to prove you humanity';
        }
        //curl_close($ch);
        return empty($this->error) ? true : false;
    }

}
