<?php
namespace Home\Controller;
use DebugBar\ZilfDebugbar;
use Home\Service\CommonService;
use Home\Service\UserService;

class LoginController extends BaseController {

    public function _initialize(){
        parent::_initialize();
    }

    //后台首页
    public function index(){
        $info = $this->isLogin('false');
        if($info){
            $this->redirect('/index/index');
        }else{
            $this->display();
        }
    }

    /**
     * 登录验证
     */
    public function login_in()
    {
        $loginName = I('post.username');
        $password = I('post.password');

        if (empty($loginName) || empty($password)) {
            $this->error('数据不完整，请重新填写！！');
        }

        //验证用户登录是否存在
        $password = md5($password);
        $result = M('user')->where(['username'=>$loginName,'password'=>$password])->find();
        if ($result) {
            //设置登录状态
            (new UserService())->setLoginCookie($result);

            jumpPage(U('index/index'),'header');
        } else {
            $this->error('账号或密码不正确，请重新登录！');
        }
    }

    /**
     * 退出登录
     */
    public function logout()
    {
        (new UserService())->clearLoginInfo();
        $this->redirect('/login/index');
    }
}