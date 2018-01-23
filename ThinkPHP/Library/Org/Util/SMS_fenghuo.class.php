<?php
/**
 * 烽火信通-短信平台接口说明V2.0
 * Created by PhpStorm.
 * Author:Tmc
 * User: Administrator
 * Date: 2017/1/6
 * Time: 10:00
 */
class SMS_fenghuo{


    private $baseurl  = 'http://120.55.198.58:27011/'; //接口地址
    private $username = 'zhuniu'; //发送帐号
    private $password = 'zhuniu@0103#'; //发送密码
    private $timestamp= ''; //时间戳

    public function __construct(){
        $this->timestamp = date(YmdHis).mt_rand(1000,9999); //时间戳:YYYYMMDDHHMMSS+4位流水号，不足4位前面补0
        $this->md5_password = $this->_encryption();
    }

    /**
     * 加密方式
     * username=test
     * password=123456
     * timestamp=201212211345060001
     * MD5(“test”+”123456”+“201212211345060001”)
     * 注意：输出密文长度必须是32位字符，字母统一大写
     */
    private function _encryption(){
        $md5_pass = MD5($this->username.$this->password.$this->timestamp);
        $md5_password = strtoupper($md5_pass);
        return $md5_password;
    }
    /**
     * $Content
     * Gbk-URL编码
     */
    private function _transcode($Content){
        $Content = mb_convert_encoding($Content,'gbk','utf8');
        $Content = urlencode($Content);
        return $Content;
    }

    /**
     * Json Post请求
     */
    private function _JsonPost($url,$data){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_TIMEOUT,30);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
        curl_close($ch);
        //记录非正常接口日志信息
        if($httpCode != 200){
            \Think\Log::write('接口状态：'.$httpCode.' URL地址：'.$url.', 参数:'.json_encode($data));
        }
        return json_decode($result,true);
    }


    /**
     * 发送短信
     * $Mobile  接受短信手机号
     * $Content 发送短信内容 Gbk-URL编码
     * $type    接口类型
     */
    public function sendSMS($Mobile , $Content , $type){
        if(empty($Mobile)){
            return $result = array(
                'Result' => '2001',
                'ResultDesc' => 'ERROR',
                'Message' => '手机号不能为空！',
            );
            exit;
        }
        if(empty($Content)){
            return $result = array(
                'Result' => '2002',
                'ResultDesc' => 'ERROR',
                'Message' => '短信内容不能为空！',
            );
            exit;
        }
        $post_url = $this->baseurl.'smshttp?action='.$type.'&username='.$this->username.'&password='.$this->md5_password.'&timestamp='.$this->timestamp;
        $data = array(
            'MessageList' => array(
                array(
                    'Content'  => $this->_transcode($Content),
                    'Mobile'  => $Mobile,
                ),
            ),
        );
        $json_data = json_encode($data);
        $result = $this->_JsonPost($post_url,$json_data);
        return $result;
    }



}