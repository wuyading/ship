<?php
/**
 * Auther:Tmc
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/3
 * Time: 14:37
 */
namespace Home\Service;

use Think\Log;

class UserService extends BaseService
{
    public function __construct()
    {
        parent::__construct();
    }

    ///////////////////////////////////// 登录信息 //////////////////////////////////////////////////////
    /**
     *  设置用户登录的cookie
     *
     * @param int $user_id 用户的id
     * @param string $auto_login 是否自动登录
     * @return array
     */
    function setLoginCookie($info,$auto_login='')
    {
        //356天自动登录
        if ($auto_login == 1) {
            $_auto_time = 365 * 86400;
        } else {
            $_auto_time = C('session_id.cookie_time');
        }

        $this->setLoginToken($_auto_time);

        //设置登录的用户信息
        $encrypt = cookie(self::ZL_TOKEN);
        $this->redis->set($encrypt . '_userInfo', $info, C('session_id.cookie_time'));

        return [self::ZL_TOKEN=>$encrypt,'limitTime'=>$_auto_time];
    }

    /**
     * 验证用户是否已经登录
     *
     * @return bool|mixed
     */
    public function checkLoginToken($encrypt = '')
    {
        $is_success = false;
        //验证token信息
        if (empty($encrypt)) {
            $encrypt = cookie(self::ZL_TOKEN);
        }

        if ($encrypt) {
            $rand_num = $this->redis->get($encrypt);
            if ($rand_num) {
                $userAgent = $_SERVER['HTTP_USER_AGENT'];
                $ip = getRealIp();
                $encrypt_new = md5(sha1(md5($userAgent . $ip . $rand_num)));
                if ($encrypt_new === $encrypt) {
                    $info = $this->redis->get($encrypt . '_userInfo');
                    if ($info) {
                        $is_success = $info;
                    }
                }

                \Think\Log::write('【ip】'.$ip.' , '.'【useragent】'.$userAgent);
                \Think\Log::write('【原来的key】'.$encrypt.' , '.'【当前的key】'.$encrypt_new);
                if($is_success){
                    \Think\Log::write('登录成功！');
                }else{
                    \Think\Log::write('登录失败！');
                }

            }
        }
        return $is_success;
    }

    /**
     * 用户退出登录，清除已经保存的登录信息
     */
    public function clearLoginInfo($encrypt = '')
    {
        if (empty($encrypt)) {
            $encrypt = cookie(self::ZL_TOKEN);
        }

        cookie('ZL_username', null);
        cookie(self::ZL_TOKEN, null);
        $this->redis->rm($encrypt . '_userInfo');
    }

    /**
     * 设置登录的验证token
     *
     * @param string $time
     */
    public function setLoginToken($time = '')
    {
        if (empty($time)) {
            $time = C('session_id.cookie_time');
        }

        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $ip = getRealIp();
        $rand_num = mt_rand(11111, 99999) . microtime();
        $encrypt = md5(sha1(md5($userAgent . $ip . $rand_num)));
        cookie(self::ZL_TOKEN, $encrypt,['expire'=>$time,'httponly'=>true]);
        $this->redis->set($encrypt, $rand_num, $time); //保存随机值，防止别人破解cookie
    }

    //获取用户信息
    function _get_user()
    {
        return $this->checkLoginToken();
    }

    //判断用户是否登录
    function _check_user_login()
    {
        return $this->checkLoginToken();
    }


    ////////////////////////////////// 注册信息 ///////////////////////////////////////////////////////
    /**
     * 验证token的合法性
     *
     * @param $type
     * @return bool
     */
    public function checkRegisterToken($type, $data = '')
    {
        $is_success = false;

        //验证token信息
        $encrypt = cookie(self::ZHNIU_TOKEN);
        if ($encrypt) {
            $rand_num = $this->redis->get($encrypt);
            if ($rand_num) {
                $referer = $_SERVER['HTTP_REFERER'];
                if ($referer) {
                    $userAgent = $_SERVER['HTTP_USER_AGENT'];
                    $ip = getRealIp();
                    $encrypt_new = sha1(md5($userAgent . $ip . $rand_num));
                    if ($encrypt_new === $encrypt) {

                        if ($type == 'token') {  //验证token是否正确
                            $is_success = true;

                        } elseif ($type == 'mobile') {  //验证填写手机号是否正确
                            $redis_mobile = $this->redis->get($encrypt . '_mobile');
                            if (!empty($redis_mobile) && $data == $redis_mobile) {
                                $is_success = true;
                            }
                        } elseif ($type == 'yzm') {
                            if (!empty($this->redis->get($encrypt . '_yzm'))) {
                                $is_success = true;
                            }
                        }
                    }
                }
            }
        }

        return $is_success;
    }

    /**
     * 检测验证码信息
     *
     * @return array
     */
    public function checkQrcode()
    {
        if ($this->checkRegisterToken('token')) {
            $encrypt = cookie(self::ZHNIU_TOKEN);
            $startTime = $this->redis->get($encrypt . '_start');
            $endTime = $this->redis->get($encrypt . '_end');
            if (empty($startTime) && empty($endTime)) {
                $message = array('status' => 3001, 'message' => '请按住滑块，拖动到最右边！');
            } else {
                if ($endTime - $startTime >= 0.02) {
                    $message = array('status' => 1001, 'message' => '验证成功！');
                } else {
                    $message = array('status' => 3002, 'message' => '验证失败！');
                }
            }
        } else {
            $message = array('status' => 3003, 'message' => '验证失败！');
        }

        return $message;
    }

    /**
     * 根据当前用户特征，生成随机cookie值
     */
    public function setRegisterToken()
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $ip = getRealIp();
        $rand_num = mt_rand(11111, 99999) . microtime();

        $encrypt = sha1(md5($userAgent . $ip . $rand_num));

        //保存cookie值
        cookie(self::ZHNIU_TOKEN, $encrypt, self::REGISTER_TIMEOUT);
        //保存随机值，防止别人破解cookie
        $this->redis->set($encrypt, $rand_num, self::REGISTER_TIMEOUT);
    }

    /**
     * 清除cookie
     */
    public function clearRegisterToken()
    {
        $encrypt = cookie(self::ZHNIU_TOKEN);
        if ($encrypt) {
            //清除唯一值
            cookie(self::ZHNIU_TOKEN, '');
            cookie(self::ZHNIU_MOBILE, '');
            cookie(self::ZHNIU_YZM, '');
            $this->redis->rm(self::ZHNIU_TOKEN);

            //清除验证码
            $this->redis->rm($encrypt . '_start');
            $this->redis->rm($encrypt . '_end');

            //清除手机号
            $this->redis->rm($encrypt . '_mobile');

            //验证码
            $this->redis->rm($encrypt . '_yzm');
        }
    }
}