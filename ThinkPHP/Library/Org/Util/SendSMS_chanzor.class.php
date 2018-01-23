<?php
/*--------------------------------
function Post($data, $target) {
    $url_info = parse_url($target);
    $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
    $httpheader .= "Host:" . $url_info['host'] . "\r\n";
    $httpheader .= "Content-Type:application/x-www-form-urlencoded\r\n";
    $httpheader .= "Content-Length:" . strlen($data) . "\r\n";
    $httpheader .= "Connection:close\r\n\r\n";
    //$httpheader .= "Connection:Keep-Alive\r\n\r\n";
    $httpheader .= $data;

    $fd = fsockopen($url_info['host'], 80);
    fwrite($fd, $httpheader);
    $gets = "";
    while(!feof($fd)) {
        $gets .= fread($fd, 128);
    }
    fclose($fd);
    return $gets;
}[/code]参数整理及调用：

代码示例2：调用发送

include_once('sms.php');

$target = "http://sms.chanzor.com:8001/sms.aspx";
http://sms.chanzor.com:8001/sms.aspx
//替换成自己的测试账号,参数顺序和wenservice对应
$post_data = "action=send&userid=&account=账号&password=密码&mobile=手机号&sendTime=&content=".rawurlencode("短信内容");
//$binarydata = pack("A", $post_data);
$gets = Post($post_data, $target);
$start=strpos($gets,"&lt;?xml");
$data=substr($gets,$start);
$xml=simplexml_load_string($data);
var_dump(json_decode(json_encode($xml),TRUE));
//请自己解析$gets字符串并实现自己的逻辑
//&lt;State&gt;0&lt;/State&gt;表示成功,其它的参考文档
--------------------------------*/
class SendSMS_chanzor{
    private $url='http://sms.chanzor.com:8001/sms.aspx';           //接口地址
    private $ac='zhuniuwangluo';		                             //用户帐号
    private $authkey = 'zhuniu123';		         //认证密钥
    private $mb = array('register'=>'您好，欢迎注册筑牛网帐号，验证码为 @ 【筑牛网络】');


    public function __construct(){
    }
    public function send($m,$c,$t=''){
        $url = $this->url;
        $ac = $this->ac;
        $authkey = $this->authkey;
        return $this->sendSMS($url,$ac,$authkey,$m,$c,$t);
    }

    //短信发送接口
    private function sendSMS($url,$ac,$authkey,$m,$c,$t)
    {
        $data = array
        (
            'action'=>'send',
            'account'=>$ac,
            'password'=>$authkey,
            'mobile'=>$m,
            'content'=>$c,
            'sendTime'=>$t
        );

        //$content    =   str_replace('@',$c,$this->mb['register']);
        //$content    =   $c.'【筑牛网络】';
        $content      =   $c;
        $post_data = "action=send&account=".$ac."&password=".$authkey."&mobile=".$m."&sendTime=&content=".($content);

        $xml= $this->postSMS($url,$post_data);			                     //POST方式提交
        $arr = array();
        $re =   simplexml_load_string($xml);
        if(trim($re->returnstatus)=='Success')
        {
            $arr['result'] = 1;
            $arr['taskID'] = (string)$re->taskID;
            $arr['info'] = '发送成功!';
            $arr['remainpoint'] =   (string)$re->remainpoint;
        }
        else  //发送失败的返回值
        {
            $arr['result'] = '-1';
            $arr['info'] = (string)$re->message.'-->'.(string)$re->taskID;
        }

        return $arr;
    }
    private function postSMS($target,$data) {
        $url_info = parse_url($target);
        $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
        $httpheader .= "Host:" . $url_info['host'] . "\r\n";
        $httpheader .= "Content-Type:application/x-www-form-urlencoded\r\n";
        $httpheader .= "Content-Length:" . strlen($data) . "\r\n";
        $httpheader .= "Connection:close\r\n\r\n";
        //$httpheader .= "Connection:Keep-Alive\r\n\r\n";
        $httpheader .= $data;
        $fd = fsockopen($url_info['host'], $url_info['port']);
        fwrite($fd, $httpheader);
        $gets = "";
        while(!feof($fd)) {
            $gets .= fread($fd, 128);
        }
        fclose($fd);
        $start=strpos($gets,"<?xml");
        $data=substr($gets,$start);
        return $data;
    }

    /**
     * 通过节点路径返回字符串的某个节点值
     * $res_data——XML 格式字符串
     * 返回节点参数
     */
    private function getDataForXML($res_data,$node){
        $xml = simplexml_load_string($res_data);
        $result = $xml->xpath($node);
        while(list( , $node) = each($result))
        {
            return $node;
        }
    }

    /**
     * @param string $task_id
     * @return bool
     * 畅卓接口 by lilei
     * 验证用户手机是否收到短信
     */
    function checkUserAccess($task_id=''){
        if(empty($task_id)){
            die('参数错误');
        }

        sleep(2); //暂停2s

        $url = 'http://sms.chanzor.com:8001/statusApi.aspx';
        $post_data = 'action=query&account='.$this->ac.'&password='.$this->authkey.'&taskid='.$task_id;

        $xml = $this->postSMS($url,$post_data);
        $start = strpos($xml,"<?xml");
        $data = substr($xml,$start);
        $xml_obj = simplexml_load_string($data);

        if($xml_obj->statusbox->status == 10){
            return array(
                'result' => 1,
                'info' => '二次验证-发送成功',
                'data' => array(
                    'mobile' => (string)$xml_obj->statusbox->mobile,
                    'taskid' => (string)$xml_obj->statusbox->taskid,
                    'status' => (string)$xml_obj->statusbox->status,
                    'errorcode' => (string)$xml_obj->statusbox->errorcode,
                ),
            );
        }elseif($xml_obj->statusbox->status == 20){
            return array(
                'result' => 2,
                'info' => '二次验证-发送失败-'.$xml_obj->statusbox->status,
                'data' => array(
                    'mobile' => (string)$xml_obj->statusbox->mobile,
                    'taskid' => (string)$xml_obj->statusbox->taskid,
                    'status' => (string)$xml_obj->statusbox->status,
                    'errorcode' => (string)$xml_obj->statusbox->errorcode,
                ),
            );
        }else{  //发送失败
            return array(
                'result' => 3,
                'info' => '二次验证-发送失败-'.(string)$xml_obj->errorstatus->remark,  //错误描述
                'error' => (string)$xml_obj->errorstatus->error,  //错误码
            );
        }
    }

}