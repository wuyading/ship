<?php
namespace Home\Controller;
use DebugBar\ZilfDebugbar;
use Home\Service\CommonService;

class MemberController extends BaseController {

    public function _initialize(){
        $this->isLogin();
        parent::_initialize();
    }

    //后台首页
    public function lists(){
        $page = I('get.p', '1', 'int');
        $search = I('get.search','');
        $search = trim($search);
        if(isset($search)){
            $where['username'] = array('like','%'.$search.'%');
        }else{
            $where = [];
        }
        $pageSize = 20;
        if ($page <= 0) {
            $page = 1;
        }

        //查看总数
        $totalElements = M('user')->where($where)->count();

        //查询列表
        $lists = M('user')->where($where)->order('id desc')->limit($pageSize * ($page - 1), $pageSize)->select();
        $this->assign('list',$lists);

        $Page = new \Think\Page($totalElements, $pageSize);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $this->assign('page', $show);

        $this->display();
    }

    /**
     * 修改密码页面
     */
    public function update_pwd(){
        $this->display();
    }

    /**
     * 保存密码
     */
    public function ajax_save_pwd(){
        $id = $this->userInfo['id'];

        $oldpwd = I('post.oldpwd');
        $newpwd = I('post.newpwd');
        $ensurpwd = I('post.ensurpwd');

        if(empty($oldpwd) || empty($newpwd) || empty($ensurpwd)){
            die(messageFormat(2001,'请填写完整的参数！'));
        }

        if($newpwd != $ensurpwd){
            die(messageFormat(2002,'请保持两次输入的密码是一致的！'));
        }

        if($oldpwd == $newpwd){
            die(messageFormat(2002,'旧密码和新密码是一样的，不允许操作！'));
        }

        $password = md5($newpwd);
        $re = M('user')->where(['id'=>$id,'password'=>md5($oldpwd)])->save(['password'=>$password]);
        if($re){
            die(messageFormat(1001,'保存成功！'));
        }else{
            die(messageFormat(2003,'保存失败，请重试！'));
        }
    }

    ///////////////////////////////////////////////////////////////////////
    /**
     * 修改用户密码
     */
    public function update_user_pwd(){
        $this->display();
    }


    /**
     * 修改用户的密码
     */
    public function ajax_save_user_pwd(){
        $user_id = I('post.user_id');
        $newpwd = I('post.newpwd');
        $ensurpwd = I('post.ensurpwd');

        if(empty($user_id) || empty($newpwd) || empty($ensurpwd)){
            die(messageFormat(2001,'请填写完整的参数！'));
        }

        if($newpwd != $ensurpwd){
            die(messageFormat(2002,'请保持两次输入的密码是一致的！'));
        }

        $password = md5($newpwd);
        $re = M('user')->where(['id'=>$user_id])->save(['password'=>$password]);
        if($re){
            die(messageFormat(1001,'保存成功！'));
        }else{
            die(messageFormat(2003,'保存失败，请重试！'));
        }
    }


    ///////////////////////////////////////////////////////////////////////
    /**
     * 添加用户
     */
    public function ajax_save_user(){
        $user_name = trim(I('post.user_name'));
        $newpwd = I('post.newpwd');
        $ensurpwd = I('post.ensurpwd');
        $group_id = I('post.group_id','','int');

        if(empty($user_name) || empty($newpwd) || empty($ensurpwd)){
            die(messageFormat(2001,'请填写完整的参数！'));
        }

        if($newpwd != $ensurpwd){
            die(messageFormat(2002,'请保持两次输入的密码是一致的！'));
        }

        //先判断用户名是否已经存在
        $is_exist = M('user')->where(['username'=>$user_name])->count();
        if($is_exist){
            die(messageFormat(2003,'该用户已经存在！'));
        }else{
            $access = [
                'b_access' => I('post.b_access','','int'),
                'b_add' => I('post.b_add','','int'),
                'u_add' => I('post.u_add','','int'),
            ];

            $password = md5($newpwd);
            $data = [
                'username' => $user_name,
                'password' => $password,
                'group_id' => $group_id,
                'access'=> json_encode($access),
            ];

            $re = M('user')->add($data);
            if($re){
                $datas = [
                    'user_id' => $this->userInfo['id'],
                    'operate' => 'add',
                    'content' => '添加用户'.$user_name,
                    'add_time' => time()
                ];
                M('operate_log')->add($datas);
                die(messageFormat(1001,'保存成功！'));
            }else{
                die(messageFormat(2003,'保存失败，请重试！'));
            }
        }
    }

    /**
     * 修改用户权限
     */
    public function ajax_save_access(){
        $user_id = trim(I('post.m_id'));
        if(empty($user_id)){
            die(messageFormat(2001,'请填写完整的参数！'));
        }

        $access = [
            'b_access' => I('post.b_access','','int'),
            'b_add' => I('post.b_add','','int'),
            'u_add' => I('post.u_add','','int'),
        ];

        $re = M('user')->where(['id'=>$user_id])->save(['access'=>json_encode($access)]);
        if($re){
            die(messageFormat(1001,'保存成功！'));
        }else{
            die(messageFormat(2003,'保存失败，请重试！'));
        }
    }

    //删除用户
    public function delete_user()
    {
        $id = I('post.id', '', 'int');

        if (empty($id)) {
            die(messageFormat(2001, '参数错误！'));
        }
        $username = M('user')->where(['id' => $id])->find();
        $is_success = M('user')->where(['id' => $id])->delete();
        if ($is_success) {
            $datas = [
                'user_id' => $this->userInfo['id'],
                'operate' => 'add',
                'content' => '删除用户'.$username['username'],
                'add_time' => time()
            ];
            M('operate_log')->add($datas);
            die(messageFormat(1001, '删除成功！'));
        } else {
            die(messageFormat(2001, '删除失败，请重试！'));
        }
    }
}