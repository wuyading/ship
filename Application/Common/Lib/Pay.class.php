<?php
/**
 * Created by PhpStorm.
 * User: sOxOs
 * Date: 2014/12/22
 * Time: 13:47
 */

namespace Common\Lib;

class Pay {

    const GATEWAY_ALIPAY        = 0x0;
    const GATEWAY_ALIPAY_MOBILE = 0x1;

    /**
     * 创建一个支付订单
     * @param $gateway int 支付网关ID
     * @param $price float 金额
     * @param $orderId string 订单ID（自做他用）
     * @param $title string 订单名称
     * @param $description string 订单描述
     * @param $returnUrl string 完成后地址（无论失败与否）
     * @param $addData array 附加数据（带回给 returnUrl）
     * @param $launchUrl string 请求来源地 (默认为当前请求来源页)
     * @return mixed 网关请求地址
     */
    public static function BuildPayment($gateway, $price, $orderId, $title, $description, $returnUrl, $addData, $launchUrl = null) {
        if (isset($addData['ReturnUrl'])) {return false;}
        if (is_null($launchUrl)) {
            $launchUrl = $_SERVER['HTTP_REFERER'];
        }

        $launchUrl = urlencode(urlencode($launchUrl));
        //$addData['ReturnUrl'] = $returnUrl;
        $addstr              = http_build_query($addData);

        $urlData = array(
            'gateway'   => $gateway,
            'price'     => sprintf('%0.2f', $price),
            'orderId'   => $orderId ? $orderId : time() + rand(),
            'title'     => $title,
            'desc'      => $description,
            'launchurl' => $launchUrl,
            'add'       => $addstr,
        );
        return U('Pay/Gateway/jump', $urlData, true);
    }

    /**
     * 签名一个返回请求
     *
     * @param $orderId string 订单识别码
     * @return string 签名
     */
    public static function Sign($orderId) {
        return md5(sha1($orderId . C('SECURITY_KEY')) . C('SECURITY_KEY'));
    }

    /**
     * 校验请求合法性
     *
     * @param $orderId string 订单识别码
     * @param $signature string 签名
     * @return bool 合法与否
     */
    public static function Verify($orderId, $signature) {
        return ($signature == md5(sha1($orderId . C('SECURITY_KEY')) . C('SECURITY_KEY')));
    }

    /**
     * 校验请求合法性(使用默认参数，附带有价格)
     *
     * @return bool 合法与否
     */
    public static function VerifyGet() {
        $orderId   = isset($_GET['orderId']) ? $_GET['orderId'] : null;
        $price     = isset($_GET['price']) ? $_GET['price'] : null;
        $signature = isset($_GET['Success']) ? $_GET['Success'] : null;
        return !(is_null($orderId) || is_null($price) || is_null($signature)) && ($signature == self::Sign($orderId . $price));
    }


    /**
     * 西弗认证
     */
    public static function XifuBuildPayment($gateway, $price, $orderId, $title, $description, $returnUrl, $addData, $launchUrl = null) {
        if (isset($addData['ReturnUrl'])) {return false;}
        if (is_null($launchUrl)) {
            $launchUrl = $_SERVER['HTTP_REFERER'];
        }

        $launchUrl = urlencode(urlencode($launchUrl));
        //$addData['ReturnUrl'] = $returnUrl;
        $addstr              = http_build_query($addData);

        $urlData = array(
            'gateway'   => $gateway,
            'price'     => sprintf('%0.2f', $price),
            'orderId'   => $orderId ? $orderId : time() + rand(),
            'title'     => $title,
            'desc'      => $description,
            'launchurl' => $launchUrl,
            'add'       => $addstr,
        );
        return U('Pay/Gateway/xifu', $urlData, true);
    }
}