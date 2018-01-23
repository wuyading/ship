<?php
/**
 * Created by PhpStorm.
 * User: sOxOs
 * Date: 2014/12/19
 * Time: 16:19
 */

namespace Common\Lib;

use Common\Lib\Alipay\AlipayCore;

/**
 * Alipay interface class
 *
 * @package Common\Lib
 * @version 1.1 For details, see comments
 */
class Alipay
{
    const MODE_BUILDER = 0x0;
    const MODE_NOTIFY = 0x1;
    const MODE_RECEIVER = 0x2;

    const BUILD_JS = 0x0;
    const BUILD_FORM = 0x1;

    const DEVICE_AUTO = 0x0;
    const DEVICE_PC = 0x1;
    const DEVICE_MOBILE = 0x2;

    private static $instance;

    /**
     * Build payment
     * @param $device int Device type
     * @return Alipay Payment builder
     */
    public static function asPaymentBuilder($device)
    {
        return self::$instance ? self::$instance : (self::$instance = new self(self::MODE_BUILDER, $device));
    }

    /**
     * Handle notify
     * @param $device int Device type
     * @return Alipay Notify handler
     */
    public static function asNotifyHandler($device)
    {
        return self::$instance ? self::$instance : (self::$instance = new self(self::MODE_NOTIFY, $device));
    }

    /**
     * Receive Response
     * @param $device int Device type
     * @return Alipay Response receiver
     */
    public static function asResponseReceiver($device)
    {
        return self::$instance ? self::$instance : (self::$instance = new self(self::MODE_RECEIVER, $device));
    }

    private $RunMode, $DeviceType, $Config;

    /**
     * Initialize Alipay gateway
     * @param $mode int Run mode
     * @param $device int Device type
     */
    private function __construct($mode, $device)
    {
        $this->RunMode = $mode;
        $this->DeviceType = $device;

        $this->Config = C('ALIPAY_CONFIG');

        $this->Init();
    }

    /**
     * Initialize
     */
    private function Init() {

        switch ($this->RunMode) {
            case self::MODE_BUILDER:
                $this->InitBuilder();
                break;
            case self::MODE_NOTIFY:
                $this->InitReceiver();
                break;
            case self::MODE_RECEIVER:
                $this->InitReceiver();
                break;
        }
    }

    /**
     * Is mobile access
     * @return bool Is mobile
     */
    private function isMobile() {

        if($this->DeviceType == self::DEVICE_MOBILE) return true;
        elseif($this->DeviceType == self::DEVICE_PC) return false;

        if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) return true;
        if (isset($_SERVER['HTTP_VIA'])) return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $clientFlags = array('nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod',
                                    'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc',
                                    'midp', 'wap', 'mobile');
            if (preg_match("/(" . implode('|', $clientFlags) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT'])))
                return true;
        }

        if (isset($_SERVER['HTTP_ACCEPT']))
            return ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false)
                    && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false
                        || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))));

        return false;
    }


    /**
     * Initialize builder mode
     */
    private function InitBuilder() {
        if($this->isMobile())
            $this->Parameters =  array(
                "service" => "alipay.wap.trade.create.direct",
                "partner" => $this->Config['partner'],
                "sec_id" => $this->Config['sign_type'],
                "format"	=> $this->Config['format'],
                "v"	=> $this->Config['version'],
                "req_id"	=> date('YmdHis'),
                "req_data"	=> '',  // Required
                "_input_charset"	=> $this->Config['input_charset'],
                'Compatible'        => array() // Compatible with old version
            );
        else
            $this->Parameters =  array(
                "service" => "create_direct_pay_by_user",
                "partner" => $this->Config['partner'],
                "payment_type"	=> '1', // Require
                "notify_url"	=> '', // Require
                "return_url"	=> '', // Require
                "seller_email"	=> '', // Require
                "out_trade_no"	=> '', // Require
                "subject"	=> '', // Require
                "total_fee"	=> '', // Require
                "body"	=> '', // Require
                "show_url"	=> '', // Optional
                "anti_phishing_key"	=> '', // Optional
                "exter_invoke_ip"	=> $_SERVER['REQUEST_ADDR'],
                "_input_charset"	=> $this->Config['input_charset'],
                "extra_common_param"    =>  ''
            );
    }

    /**
     * Initialize receiver mode
     */
    private function InitReceiver() {
        $this->IsValid = false;
        if(IS_POST) return;
        if(IS_POST) return;
    }

    private $Parameters = array();

    /**
     * Set data for key or set all value in array
     * @param $kOrA mixed Array with key-value pair or Key
     * @param null $v mixed Value if 1st parameter is a single key
     * @return $this
     */
    public function Set($kOrA, $v = null)
    {
        if (self::MODE_BUILDER != $this->RunMode) return $this;
        return $this->isMobile() ? $this->_Set_Mobile($kOrA, $v) : $this->_Set_PC($kOrA, $v);

    }
    private function _Set_Mobile($kOrA, $v = null)
    {
        if(is_array($kOrA)) {
            $a = $kOrA;
            $this->Parameters['Compatible'] = array_merge($this->Parameters['Compatible'], $a);
        } else {
            $k = $kOrA;
            $this->Parameters['Compatible'][$k] = $v;
        }
        return $this;
    }
    private function _Set_PC($kOrA, $v = null)
    {
        if(is_array($kOrA)) {
            $a = $kOrA;
            $this->Parameters = array_merge($this->Parameters, $a);
        } else {
            $k = $kOrA;
            $this->Parameters[$k] = $v;
        }
        return $this;
    }

    public function SetService($v) { return $this->Set('service', $v); }
    public function SetPaymentType($v) { return $this->Set('payment_type', $v); }
    public function SetNotifyUrl($v) { return $this->Set('notify_url', $v); }
    public function SetReturnUrl($v) { return $this->Set('return_url', $v); }
    public function SetSellerEmail($v) { return $this->Set('seller_email', $v); }
    public function SetOrderId($v) { return $this->Set('out_trade_no', $v); }
    public function SetTitle($v) { return $this->Set('subject', $v); }
    public function SetPrice($v) { return $this->Set('total_fee', $v); }
    public function SetDescription($v) { return $this->Set('body', $v); }
    public function SetLaunchUrl($v) { return $this->Set('show_url', $v); }
    public function SetAntiPhishingKey($v) { return $this->Set('anti_phishing_key', $v); }
    public function SetExtraParam($v) { return $this->Set('extra_common_param', $v); }

    /**
     * Get value for key or fill array with values using array-value as key
     * @param $kOrA mixed Key or Array
     * @param $v mixed false or needle
     * @return mixed Array with Key-Value pairs or $this or value of key
     */
    public function Get(&$kOrA, &$v = false) {
        return $this->isMobile() ? $this->_Get_Mobile($kOrA, $v) : $this->_Get_PC($kOrA, $v);
    }
    private function _Get_Mobile(&$kOrA, &$v = false) {
        if (is_array($kOrA)) {
            $a = $kOrA;
            $result = array();
            foreach ($a as $k) {
                $result[$k] = $this->Parameters['Compatible'][$k];
            }
            $kOrA = $result;
        } else if ($v !== false) {
            $v = $this->Parameters['Compatible'][$kOrA];
        } else {
            return $this->Parameters['Compatible'][$kOrA];
        }
        return $this;
    }
    private function _Get_PC(&$kOrA, &$v = false) {
        if (is_array($kOrA)) {
            $a = $kOrA;
            $result = array();
            foreach ($a as $k) {
                $result[$k] = $this->Parameters[$k];
            }
            $kOrA = $result;
        } else if ($v !== false) {
            $v = $this->Parameters[$kOrA];
        } else {
            return $this->Parameters[$kOrA];
        }
        return $this;
    }

    // Builder

    /**
     * Build payment request
     * 2015-01-22 New! Now this method will automatically detect user device type for providing a fit way to pay
     * @param $method int Method to submit
     * @param $debug bool Use debug output
     * @return string HTML code
     */
    public function Build($method, $debug = false) {

        if (self::MODE_BUILDER != $this->RunMode) return null;
        return $this->isMobile() ? $this->BuildMobile($method, $debug) :  $this->BuildPC($method, $debug);
    }

    /**
     * Build payment request for PC
     * @param $method int Method to submit
     * @param $debug bool Use debug output
     * @return string HTML code
     */
    private function BuildPC($method, $debug = false) {
        $requiredFields = array('notify_url', 'return_url', 'seller_email', 'out_trade_no', 'subject', 'total_fee', 'show_url');

        $missing = false;
        foreach ($requiredFields as $keyInParameters) {
            if(empty($this->Parameters[$keyInParameters])) $missing = $keyInParameters;
        }

        if($missing) return $missing;

        $preparedParameters = $this->PrepareParameters();

        switch($method) {
            case self::BUILD_JS:
                break;
            case self::BUILD_FORM:
                return $this->BuildForm($preparedParameters, $debug);
                break;
            default:
                return '';
        }
    }

    /**
     * Build payment request for mobile
     * @param $method int Method to submit
     * @param $debug bool Use debug output
     * @return string HTML code
     */
    private function BuildMobile($method, $debug = false) {

        // Check missing
        $requiredFields = array('notify_url', 'return_url', 'seller_email', 'out_trade_no', 'subject', 'total_fee', 'show_url');

        $missing = false;
        foreach ($requiredFields as $keyInParameters) {
            if(empty($this->Parameters['Compatible'][$keyInParameters])) $missing = $keyInParameters;
        }

        if($missing) return $missing;
        // Convert request to xml
        $this->XMLizeMobileParameters();

        $preparedParameters = $this->PrepareParameters();

        // Convert parameters to access token
        if(!($accessToken = $this->RequestAccessToken($preparedParameters)))
            return 'Error to create access token';

        // Rebuild parameters use access token
        $preparedParameters = $this->RebuildParameters($accessToken);
        $preparedParameters = $this->PrepareParameters($preparedParameters);

        switch($method) {
            case self::BUILD_JS:
                break;
            case self::BUILD_FORM:
                return $this->BuildForm($preparedParameters, $debug);
                break;
            default:
                return '';
        }
    }

    /**
     * Convert common request to access token
     * @param $preparedParameters Array  Common request parameters
     * @return NULL|String Access token if success
     */
    private function RequestAccessToken($preparedParameters) {
        $sslCertPath = $this->Config['cacert'];
        $responseHTML = AlipayCore::postUrl($this->Config['gateway_mobile'], $sslCertPath, $preparedParameters);

        $accessTokenData = array();
        parse_str(urldecode($responseHTML), $accessTokenData);

        if(!isset($accessTokenData['res_data'])) return 'Error for getting access token';
        $accessToken = strip_tags($accessTokenData['res_data']);
        if(strlen($accessToken) != 40) return null;
        return $accessToken;
    }

    /**
     * Rebuild request with access token
     * @param $accessToken String Access token
     * @return Array
     */
    private function RebuildParameters($accessToken) {
        $requestId = $this->Parameters['req_id'];
        $requestParameters = array(
            "service" => "alipay.wap.auth.authAndExecute",
            "partner" => $this->Config['partner'],
            "sec_id" => $this->Config['sign_type'],
            "format"	=> $this->Config['format'],
            "v"	=> $this->Config['version'],
            "req_id"	=> $requestId,
            "req_data"	=> "<auth_and_execute_req><request_token>{$accessToken}</request_token></auth_and_execute_req>",
            "_input_charset"	=> $this->Config['input_charset']
        );
        return $requestParameters;
    }

    /**
     * Turn parameters into xml type for mobile
     */
    private function XMLizeMobileParameters() {
        /**
         * These variables will be created
         * @var string $notify_url
         * @var string $return_url
         * @var string $seller_email
         * @var string $out_trade_no
         * @var string $subject
         * @var float $total_fee
         * @var string $show_url
         */
        foreach($this->Parameters['Compatible'] as $parameterKey => $parameterValue)
            $$parameterKey = $parameterValue;

        $show_url = urldecode($show_url);

        unset($this->Parameters['Compatible']);
        $this->Parameters['req_data'] = <<< EOX
<direct_trade_create_req><notify_url>{$notify_url}</notify_url><call_back_url>{$return_url}</call_back_url><seller_account_name>{$seller_email}</seller_account_name><out_trade_no>{$out_trade_no}</out_trade_no><subject>{$subject}</subject><total_fee>{$total_fee}</total_fee><merchant_url>{$show_url}</merchant_url></direct_trade_create_req>
EOX;
    }

    /**
     * Prepare Parameters
     *
     * @param $parameters NULL|Array Specified parameter or use $this->Parameters
     * @return array Prepared Parameters
     *
     * @version 1.1 argument $parameters added
     */
    private function PrepareParameters($parameters = null) {

        if(is_null($parameters)) $parameters = $this->Parameters;

        // Format parameters
        $submitParamters = AlipayCore::FilterParameters($parameters);
        $sortedSubmitParameters = AlipayCore::SortArray($submitParamters);

        // Sign parameters
        $signature = $this->SignParameters($sortedSubmitParameters);

        //签名结果与签名方式加入请求提交参数组中
        $sortedSubmitParameters['sign'] = $signature;
        if(strstr($sortedSubmitParameters['service'], 'alipay.wap') === false)
            $sortedSubmitParameters['sign_type'] = $this->Config['sign_type'];

        return $sortedSubmitParameters;
    }

    /**
     * Sign parameters
     * @param $sortedSubmitParameters array Formatted parameters
     * @return string Parameters signature
     */
    private function SignParameters($sortedSubmitParameters) {
        $unsignedStr = AlipayCore::LinkUrlParameters($sortedSubmitParameters);
        $signature = '';
        switch ($this->Config['sign_type']) {
            case "MD5" :
                $signature = AlipayCore::sign($unsignedStr, $this->Config['key']);
                break;
            default :
                $signature = '';
        }

        return $signature;
    }

    /**
     * Build HTML Form with parameters
     * @param $preparedParameters array Parameters prepared to be used
     * @param $debug bool Enable debug mode
     * @return string HTML Form code
     */
    private function BuildForm($preparedParameters, $debug = false){
        if (self::MODE_BUILDER != $this->RunMode) return null;
        $gateway = $this->isMobile() ? $this->Config['gateway_mobile'] : $this->Config['gateway'];
        $method = $this->isMobile() ? 'get' : 'post';

        $formCode = "<form id='alipay' name='alipay' action='{$gateway}_input_charset={$this->Config['input_charset']}' method='{$method}'>\r\n";
        foreach ($preparedParameters as $paramKey => $paramValue) {
            if($debug) $formCode.= "{$paramKey}: <input type='text' name='{$paramKey}' value='{$paramValue}'/><br />\r\n";
            else $formCode.= "<input type='hidden' name='{$paramKey}' value='{$paramValue}'/>\r\n";
        }
        $formCode .= '</form>';
        if($debug) $formCode .= "<a href='javascript:;' onclick='document.forms[0].submit();'>Submit</a>";
        else $formCode .= "<script>document.forms[0].submit();</script>";

        return $formCode;
    }

    // Return & Notify

    private $NotifyId, $Sign;
    private $IsValid, $ValidResult;

    /**
     * Verify the request is valid or not
     * @param $data array Data from server
     * @return $this
     */
    public function Verify($data) {
        if(!isset($data['notify_id'], $data['sign'], $data['sign_type'])) {
            $this->ValidResult = 'Missing parameters.';
        } else {
            $this->NotifyId = $data['notify_id'];
            $this->Sign = $data['sign'];
            $this->Parameters = $data;
            $sortedPureParameters = $this->PureParameters();
            $this->IsValid = false;
            if(!$this->VerifyParameterSignature($sortedPureParameters, $this->Sign)) {
                $this->ValidResult = 'Validating signature failed.';
            }
            else if(!$this->IsSentByServer($this->NotifyId)) {
                $this->ValidResult = 'Remote validating failed.';
            }else {
                $this->IsValid = true;
                $this->ValidResult = 'Validation success.';
            }
        }

        return $this;
    }

    /**
     * Unset signature and other unnecessary item
     */
    private function PureParameters() {
        $pureParameters = AlipayCore::FilterParameters($this->Parameters);
        $sortedPureParameters = AlipayCore::SortArray($pureParameters);

        return $sortedPureParameters;
    }

    /**
     * Sign parameters
     * @param $sortedSubmitParameters array Formatted parameters
     * @return string Parameters signature
     */
    private function VerifyParameterSignature($sortedSubmitParameters) {
        $unsignedStr = AlipayCore::LinkUrlParameters($sortedSubmitParameters);
        switch ($this->Config['sign_type']) {
            case 'MD5':
                return AlipayCore::veritySign($unsignedStr, $this->Sign, $this->Config['key']);
                break;
            default:
                break;
        }

        return false;
    }

    /**
     * @return bool Is valid request or not
     */
    public function IsValid() {
        return $this->IsValid;
    }

    /**
     * @return string Description of validation result
     */
    public function ValidResult() {
        return $this->ValidResult;
    }

    private function IsSentByServer () {
        $validatorUrl = $this->Config['validator'];
        $sslCertPath = $this->Config['cacert'];
        $partner = $this->Config['partner'];

        $postData = array(
            'partner' => $partner,
            'notify_id' => urlencode(trim($this->NotifyId))
        );

        $responseText = AlipayCore::getUrl("{$validatorUrl}partner={$partner}&notify_id={$this->NotifyId}", $sslCertPath);
        $result = preg_match("/true$/i",$responseText);
        return $result;
    }


}