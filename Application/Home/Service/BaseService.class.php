<?php
/**
 * Created by PhpStorm.
 * User: Tmc
 * Date: 2016/3/3
 * Time: 14:37
 */

namespace Home\Service;

use Think\Cache\Driver\Redis;

class BaseService {
    /**
     * 用户登录产生的验证编码
     */
    const ZL_TOKEN = 'JL_token';

    /**
     * 筑牛注册的token
     */
    const ZHNIU_TOKEN = 'ZHNIU_token';

    /**
     * 筑牛注册，保存的手机号
     */
    const ZHNIU_MOBILE = 'ZHNIU_mobile';

    /**
     * 筑牛注册，保存的验证码
     */
    const ZHNIU_YZM = 'ZHNIU_yzm';

    //注册的时候验证码失效的时间
    const REGISTER_TIMEOUT = 60 * 15;

    public $redis;
    public $session_id;

    public function __construct() {
        if(!$this->redis){
            $this->redis = new Redis();
        }
	}
    /**
     * 获取当前应用的域名
     *
     */
	static public function get_host(){
        $ret_arr = array();
        if( I('server.REDIRECT_STATUS') == 200)
        {
            $cur_host = I('server.HTTP_HOST');
            if(checkIp($cur_host)){
                $domain = $cur_host;
            }else{
                $domain = substr($cur_host,stripos($cur_host,'.'));
            }

            $ret_arr['COOKIE_DOMAIN'] = $domain;
            $ret_arr['HOST'][ 'THIS_HOST']= $cur_host ? 'http://'.I('server.HTTP_HOST') : '';
        }

        return $ret_arr;
    }

    /**
     * 设置config
     * 支持tp<2级配置
     *
     */
    static public function set_host( $config = array() ){
        if(is_array($config)){
            foreach ($config as $ke =>$va ){
                if(is_array($va)){
                    foreach ($va as $k=>$v){
                        C($ke.".".$k,$v);
                    }
                }else{
                    C($ke,$va);
                }
            }
        }
    }
}
