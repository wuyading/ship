<?php
/**
 * Created by PhpStorm.
 * User: sOxOs
 * Date: 2014/12/19
 * Time: 15:24
 * Packed from alipay_core
 */

namespace Common\Lib\Alipay;

class AlipayCore
{
    /**
     * Convert parameter array into URL parameter
     * @param $params array Array with key and value
     * @return string Key-Value Pair in URL form
     */
    public static function LinkUrlParameters($params)
    {
        $urlParams = "";
        while (list ($key, $val) = each($params)) {
            $urlParams .= $key . "=" . $val . "&";
        }
        $urlParams = substr($urlParams, 0, count($urlParams) - 2);
        if (get_magic_quotes_gpc()) $urlParams = stripslashes($urlParams);
        return $urlParams;
    }

    /**
     * Convert parameter array into URL parameter
     * @param $params array Array with key and value
     * @return string Key-Encoded Value Pair in URL form
     */
    public static function LinkEncodedUrlParameters($params)
    {
        $urlParams = "";
        while (list ($key, $val) = each($params)) {
            $urlParams .= $key . "=" . urlencode($val) . "&";
        }
        $urlParams = substr($urlParams, 0, count($urlParams) - 2);

        if (get_magic_quotes_gpc()) $urlParams = stripslashes($urlParams);

        return $urlParams;
    }

    /**
     * Filter EMPTY value and sign in parameters
     * @param $params array Array with key and encoded value
     * @return array Parameters without EMPTY
     */
    public static function FilterParameters($params)
    {
        $filteredParams = array();
        while (list ($key, $value) = each($params)) {
            if ($key == "sign" || $key == "sign_type" || $key == "s" || $value == "") continue;
            else $filteredParams[$key] = $params[$key];
        }
        return $filteredParams;
    }

    /**
     * Sort array by key
     * @param $arr array Array need to be sort
     * @return array Sorted array
     */
    public static function SortArray($arr)
    {
        ksort($arr);
        return $arr;
    }

    /**
     * HTTP Post
     * @param $url string POST url
     * @param $sslCert string SSL Cert
     * @param $postData array POST data
     * @param $sourceEncode string Submit encoding (Default=Empty String)
     * @return string HTTP server response
     */
    public static function postUrl($url, $sslCert, $postData, $sourceEncode = '')
    {
        if (trim($sourceEncode) != '') $url = $url . "_input_charset=" . $sourceEncode;
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_CAINFO, $sslCert);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
        $responseText = curl_exec($curl);
        curl_close($curl);

        return $responseText;
    }

    /**
     * HTTP Get
     * @param $url string GET url
     * @param $sslCert string SSL Cert
     * @return string HTTP server response
     */
    public static function getUrl($url, $sslCert)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_CAINFO, $sslCert);
        $responseText = curl_exec($curl);
        curl_close($curl);

        return $responseText;
    }

    /**
     * Convert encoding
     * @param $str string Raw string
     * @param $sourceEncode string Source encoding
     * @param $destEncode string Destination encoding
     * @return string Encode converted string
     */
    public static function encodingConvert($str, $sourceEncode, $destEncode)
    {
        $output = "";
        if ($sourceEncode == $destEncode || $str == null)
            $output = $str;
        elseif (function_exists("mb_convert_encoding"))
            $output = mb_convert_encoding($str, $destEncode, $sourceEncode);
        elseif (function_exists("iconv"))
            $output = iconv($sourceEncode, $destEncode, $str);
        else return null;

        return $output;
    }

    /**
     * Sign with MD5
     * @param $rawStr string String to sign
     * @param $key string Signing key
     * @return string Signed string
     */
    public static function sign($rawStr, $key) {
        return md5($rawStr . $key);
    }

    /**
     * Verify MD5 signing
     * @param $rawStr string String to sign
     * @param $signedStr string Signed string
     * @param $key string Signing key
     * @return boolean Is valid or not
     */
    public static function veritySign($rawStr, $signedStr, $key) {
        $curSignedStr = md5($rawStr . $key);
        return ($curSignedStr == $signedStr);
    }
}