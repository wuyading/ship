<?php

/* * 基类
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/2
 * Time: 15:21
 */

namespace Home\Controller;

use DebugBar\ZilfDebugbar;
use Home\Service\UserService;
use Think\Cache\Driver\Redis;
use Think\Controller;
use Think\Log;
use Zilf\Curl\Curl;

class BaseController extends Controller
{
    const ZL_TOKEN = 'JL_token';  //用户登录产生的验证编码

    protected $checkAuthHandle = false;

    /**'
     * @var Redis
     */
    public $redis;
    public $userInfo = '';
    public $msg_num = '';
    public $uc_base = '';
    protected $region_r;

    public function _initialize()
    {
        $this->set_hosts();
        header('Content-type:text/html;charset=utf-8;');
        //初始化redis
        $this->redis = new Redis();

        if($info = $this->isLogin(false)){
            //获取用户信息
            $this->userInfo = $info;
            $this->assign('userInfo',$info);
            $this->assign('user_access',json_decode($info['access'],true));
        }
    }


    public function checkAuth()
    {
        if ($this->getCheckAuthHandle()) {
            $auth = new \Think\Auth();
            if (!$auth->check(MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME, session('user_id'))) {
                echo '您没有权限';
            }
        }
    }

    /**
     * @param boolean $checkAuthHandle
     */
    public function setCheckAuthHandle($checkAuthHandle)
    {
        $this->checkAuthHandle = $checkAuthHandle;
    }

    /**
     * @return boolean
     */
    public function getCheckAuthHandle()
    {
        return $this->checkAuthHandle;
    }

    public function show_404($params = array())
    {
        header("HTTP/1.0 404 Not Found"); //使HTTP返回404状态码
        $this->assign('params', $params);
        $this->display('Public:404');
        die();
    }

    /**
     * 判断用户是否已经登录
     * 使用方法
     * 1/ isLogin()  如果没有登录则会跳转到登录页面并加上当前的url
     * 2/ isLogin($url) 会跳转到$url的网址
     * 3/ isLogin(false) 则不会跳转直接返回当前的用户信息，没有登录则返回空
     *
     * @param string $back_url
     * @return bool|mixed
     */
    function isLogin($back_url = '')
    {
        $info = (new UserService())->checkLoginToken();
        if (empty($info)) {
            if($back_url === false || $back_url == 'false'){
            }else{
                if ($back_url) {
                    if (stripos('?', $back_url) === false) {
                        $back_url = '?back_url=' . $back_url;
                    } else {
                        $back_url = '&back_url=' . $back_url;
                    }
                }else{
                    $back_url = '?back_url='.curPageURL();
                }
                toPage(U('/login/index') . $back_url);
            }
        }

        return $info;
    }

    /**
     *
     * 获取用户的验证信息
     * authResultKey
     * 返回查询结果对应依次
     *    NOT,DOING,APPROVE,REJECT
     *    未认证，待审核（审核中），通过，驳回
     */
    function getAuthResult()
    {
        $result = curlGet(C('j_user_findAuthResult'), array('userBaseId' => $this->userInfo['userBaseId']));
        if ($result) {
            return json_decode($result);
        }
    }

    /**
     * 设置唯一的cookie
     */
    private function _setCookie()
    {
        //设置sessionID
        $s_id = Cookie(C('session_id.cookie_name'));
        $session_id = isset($s_id) ? $s_id : '';
        if (empty($session_id)) {
            $SessionService = new \Home\Service\SessionService();
            $session_id = $SessionService->_set_id();
        }
        $this->session_id = $session_id;
        //设置ID结束
    }

    /**
     * 获取省市县等信息
     */
    public function get_region_lists()
    {
        //地区获取
        if (is_array($this->region_r) && isset($this->region_r['data']) && count($this->region_r['data'])) {
            $this->assign('regionList', $this->region_r['data']);
        } else {
            $this->region_r = curlGet(C('j_region_parentId') . '/100000');
            if ($this->region_r['result']) {
                $this->assign('regionList', $this->region_r['data']);
            } else {
                $this->assign('regionList', '');
            }
        }
    }

    /**
     * 判断是否是采购商
     */
    public function check_isPurchaser()
    {
        $this->isLogin();
        //判断是否是采购商
//        if (($this->userInfo['authResultKey'] != 'APPROVE') || ($this->userInfo['isPurchaser'] != 1)) {
//            return false;
//            $this->redirect('approve/index');
//        }
    }

    /**
     * 判断是否是供应商
     */
    public function check_isSupplier()
    {
        $this->isLogin();
        //判断是否是供应商
//        if (($this->userInfo['authResultKey'] != 'APPROVE') || ($this->userInfo['isSupplier'] != 1)) {
//            $this->redirect('approve/index');
//        }
    }

    /**
     * 获取未读消息总数
     */
    protected function _getMessageNum()
    {
        if (isset($this->userInfo['userBaseId'])) {
            $msg_num = \Home\Service\CommonService::getMesssageNum($this->userInfo['userBaseId']);
            return $msg_num;
        }
    }

    /**
     * 获取收藏关注数量
     */
    protected function _getCollectsAndFansNum()
    {
        if (isset($this->userInfo['userBaseId'])) {
            $collect_arr = \Home\Service\CommonService::getCollectsAndFansNum($this->userInfo['userBaseId']);
            return $collect_arr;
        }
    }

    /**
     * 获取采购商订单的类型数量
     *
     */
    protected function _getOrdersDatas_pharser()
    {
        if (isset($this->userInfo['userBaseId'])) {
            $orders_arr = \Home\Service\CommonService::getOrderDataNum_pharser($this->userInfo['userBaseId']);
            return $orders_arr;
        }
    }

    /**
     * 获取采购商订单的类型数量
     *
     */
    protected function _getOrdersDatas_supplier()
    {
        if (isset($this->userInfo['userBaseId'])) {
            $orders_arr = \Home\Service\CommonService::getOrderDataNum_supplier($this->userInfo['userBaseId']);
            return $orders_arr;
        }
    }

    /**
     * 收藏产品,关注商家
     * type: 1产品，2商家，3收藏采购单相关
     * ep: href="{:U('/base/ajax_add_attention')}" data-req='{$dangan.userBaseId}' data-type="2">关注商家
     *
     */
    function ajax_add_attention()
    {
        if (!IS_AJAX) {
            exit(toJson(array('status' => 1001, 'info' => "非法请求")));
        }
        if (empty($this->userInfo)) {
            exit(toJson(array('status' => 1001, 'info' => "你没有登录")));
        } else {
            $type = I('get.type', '' . 'trim'); //获取类型 1：收藏，2位关注
            if (!$type) {
                exit(toJson(array('status' => 1001, 'info' => "请求参数错误")));
            }
            $data = array();
            $id = I('get.id', '' . 'trim');
            if ($type == 1) {
                $data['type'] = 1;
                $data['productId'] = $id;
            } else if ($type == 2) {
                $data['type'] = 2;
                $data['userBaseId'] = $id;
            } elseif ($type == 3) {
                $purchaseType = I('get.purchase_type', '', 'int'); //获取类型
                $data['type'] = 1;
                $data['purchaseType'] = $purchaseType;//1：询价单  2：竞价单
                $data['purchaseId'] = $id;
            }

            $data['attentionUserBaseId'] = $this->userInfo['userBaseId'];
            $res = \Home\Service\CommonService::addAttention($data);
            if ($res['result']) {
                $msg = array('status' => 1001, 'info' => $res['message']);
            } else {
                $msg = array('status' => 2003, 'info' => $res['message']);
            }
            exit(toJson($msg));
        }
    }

    /**
     * 设置面包屑的我的筑牛链接
     */
    public function set_nav()
    {
        if (isset($this->userInfo['userBaseId'])) {
            if (strlen($this->uc_base) == 0) {
                $nav_arr = C("NAV_ARRAY");
                $this->uc_base = "<a href='" . U($nav_arr['uc_base']['url']) . "'>" . $nav_arr['uc_base']['name'] . "</a>&gt;";
            }
            $this->assign("uc_base", $this->uc_base);
        }
    }

    /**
     * 普通的post请求，和页面的form请求的方式一样
     *
     * @param $url
     * @param string $params
     * @param string $message_type
     * @param int $cache_time 缓存时间
     * @param null $curl
     * @return mixed|string
     */
    public function curl_post($url, $params = '', $message_type = 'array', $cache_time = 0, $curl = null)
    {
        if (empty($curl)) {
            $curl = new Curl();
        }

        if (empty($message_type)) {
            $message_type = 'array';
        }

        if (empty($cache_time)) {
            $result = $this->request_url($url, $params, 'post', $message_type, $curl);
        } else {
            $param_str = is_array($params) ? implode(',', $params) : $params;
            $key = md5($param_str . $url);

            $result = $this->redis->get($key);
            if (empty($result)) {
                $result = $this->request_url($url, $params, 'post', $message_type, $curl);
                $this->redis->set($key, $result, $cache_time);
            }
        }

        return $result;
    }

    /**
     *  以json数据流的方式请求
     * post 请求
     *
     * @param $url
     * @param string $params
     * @param string $mime_type
     * @param string $message_type
     * @param int $cache_time 缓存时间
     * @param null $curl
     * @return mixed|string
     */
    public function curl_stream($url, $params = '', $mime_type = 'application/json', $message_type = 'array', $cache_time = 0, $curl = null)
    {
        if (empty($curl)) {
            $curl = new Curl();

            //设置内容类型
            if (empty($mime_type)) {
                $mime_type = 'application/json';
            }

            if (is_array($params)) {
                $params = json_encode($params);
            }

            $curl->set_headers(
                array(
                    'Content-Type' => $mime_type,
                    //'Content-Length' => strlen($params)
                )
            );
        }

        if (empty($message_type)) {
            $message_type = 'array';
        }

        if (empty($cache_time)) {
            $result = $this->request_url($url, $params, 'post', $message_type, $curl);
        } else {
            $param_str = is_array($params) ? implode(',', $params) : $params;
            $key = md5($param_str . $url);

            $result = $this->redis->get($key);
            if (empty($result)) {
                $result = $this->request_url($url, $params, 'post', $message_type, $curl);
                $this->redis->set($key, $result, $cache_time);
            }
        }

        return $result;
    }

    /**
     * 普通的get请求，headers和页面直接请求的方式一直
     *
     * @param $url
     * @param string $params
     * @param string $message_type
     * @param int $cache_time 缓存时间
     * @param null $curl
     * @return mixed|string
     */
    public function curl_get($url, $params = '', $message_type = 'array', $cache_time = 0, $curl = null)
    {
        if (empty($curl)) {
            $curl = new Curl();
        }

        if (empty($message_type)) {
            $message_type = 'array';
        }

        if (empty($cache_time)) {
            $result = $this->request_url($url, $params, 'get', $message_type, $curl);
        } else {
            $param_str = is_array($params) ? implode(',', $params) : $params;
            $key = md5($param_str . $url);

            $result = $this->redis->get($key);
            if (empty($result)) {
                $result = $this->request_url($url, $params, 'get', $message_type, $curl);
                $this->redis->set($key, $result, $cache_time);
            }
        }

        return $result;
    }

    /**
     * 获取curl 请求的对象
     *
     * @param $curl
     * @param $url
     * @param string $params
     * @param string $method
     * @param string $message_type
     * @return mixed|string
     */
    public function request_url($url, $params = '', $method = 'POST', $message_type = 'json', $curl = null)
    {
        $method = strtoupper($method);

        if (APP_DEBUG) {
            ZilfDebugbar::get('time')->startMeasure('java', '接口执行时间：' . $url);
        }

        if ($method == 'POST') {
            $response = $curl->post($url, $params)->exec();
        } elseif ($method == 'GET') {
            $response = $curl->get($url, $params)->exec();
        } else {
            die('请求方式不正确！');
        }

        $result = $this->_get_back_message($response, $message_type);

        if (APP_DEBUG) {
            $message = array('url' => $url, 'params' => $params, 'method' => $method, 'code' => $response->get_http_code(), 'result' => $response->get_response());
            if ($response->get_http_code() == 200) {
                $st = 'info';
            } else {
                $st = 'error';
            }
            ZilfDebugbar::debug($message, $st);

            ZilfDebugbar::get('time')->stopMeasure('java');
        }

        return $result;
    }

    /**
     * 返回请求的参数信息
     *
     * @param $response
     * @param string $type
     * @return mixed|string
     */
    private function _get_back_message($response, $type = 'json')
    {
        if ($type == 'CurlResponse') {
            return $response;
        }

        if ($response->get_curl_error_code() == 0) {  //curl 请求的状态

            if ($response->get_http_code() == 200) {  //http返回状态
                //返回接口信息 为json数据
                return $this->_get_response($response->get_response(), $type);

            } else { //请求页面错误
                $message = ['result' => false, 'message' => 'java错误号：' . $response->get_http_code(),
                    'http_code' => $response->get_http_code(),
                ];

                if (APP_DEBUG) {  //如果是调试状态直接返回错误原因
                    return $this->_get_response($message, $type);
                } else {
                    unset($message['message']);
                    return $this->_get_response($message, $type);
                }

                Log::record('java接口错误，错误号是：' . $response->get_http_code() . '，接口是：' . $this->_url);
            }

        } else {
            Log::record('服务器错误，错误号是：' . $response->get_curl_error_code() . '，错误原因是：' . $response->get_curl_error_message());

            $message = ['result' => false, 'message' => '服务器错误，请求的接口服务器异常！',];
            return $this->_get_response($message, $type);
        }
    }

    /**
     * 返回不同格式类型的结果数据
     *
     * @param $back_result
     * @param string $type json  jsonp  array
     * @return mixed|string
     */
    private function _get_response($back_result, $type = 'json')
    {
        if ($type == 'jsonp') {

            $callback = I('get.callback_func');
            $callback = $callback ? $callback : 'callback_func';

            if (is_array($back_result)) {
                $back_result = json_encode($back_result);
            }
            $back_result = $callback . '(' . $back_result . ')';

        } elseif ($type == 'array') {
            if (is_string($back_result)) {
                $back_result = json_decode($back_result, true);
            }
        } else {  //默认就是json格式
            if (is_array($back_result)) {
                $back_result = json_encode($back_result);
            }
        }

        return $back_result;
    }


    /**
     * 发送手机验证码
     *
     * 使用方法：
     * 1/ params为数组时，畅卓的短信的key $符号开始和结束
     * 如：
     * [
     *  '$code$' =>'',
     *  '$username$' => '',
     * ]
     * 2/ params 为数字时，是发送验证码，并将验证码保存到redis缓存中，有效期15分钟
     *
     * @param $mobile //手机号
     * @param $type //配置文件 短信配置名称
     * @param $params //发送的数据  数字 字符串 数组都支持  如果为数字，则默认为验证码
     * @param int $time //验证码保存的有效期 默认是60秒
     * @return array
     */
    public function send_SMS($mobile, $type, $params, $time = 60)
    {
        //手机号码不合格，返回
        if (!\Home\Service\ValidateService::isMobileNumber($mobile)) {
            return array('status' => 2001, 'info' => '手机号码不合格!');
        }

        //短信模板 如： FINDPWD 找回密码
        $stringModel = C('MSG_MODEL.' . $type);
        if (empty($stringModel)) {
            return array('status' => 2002, 'info' => '配置文件中，短信模板【' . $type . '】不存在!');
        }

        //通过手机号，检测验证码发送的频率，必须是60秒之内
        if ($time > 0) {
            $key = 'mobile_' . $mobile;
            if ($start_time = $this->redis->get($key)) {
                $limit_time = time() - $start_time;
                if ($limit_time < $time) {
                    return array('status' => 2003, 'info' => '请点击的太快了，请 ' . ($time - $limit_time) . 's 后再试！', 'time' => $time - $limit_time);
                }
            } else {
                $this->redis->set($key, time(), $time);
            }
        }

        //发送的是验证码
        if (is_numeric($params)) {
            $code = $params; //随机产生的6位验证码
            $data = array(
                '$code$' => $code,
            );
            \Home\Service\SessionService::setnewMobileCode($code, $mobile); //code写入redis

        } elseif (is_string($params)) {
            $data = array(
                '$code$' => $params,
            );

        } else {
            $data = $params;
        }

        $search = array_keys($data);
        $replace = array_values($data);
        $content = str_replace($search, $replace, $stringModel);

        $result = \Home\Service\MobilemsgService::sendmsg($mobile, $content);
        if ($result['result'] == 1) {
            return array('status' => 1001, 'info' => '校验码已发送至你的手机，请查收！', 'data' => $result);
        } else {
            if (APP_DEBUG) { //firephp debug调试
                @fb($result, 'sms');
            }

            return array('status' => 2004, 'info' => '发送失败!', 'data' => $result);
        }
    }

    /**
     * 设置cookie作用域和静态host
     */
    public function set_hosts($param = array())
    {
        if (!C('HOST.THIS_HOST') || !C('HOST.COOKIE_DOMAIN')) {
            $host_arr = \Home\Service\BaseService::get_host();
            \Home\Service\BaseService::set_host($host_arr);
        }
    }
}
