<?php
namespace Home\Controller;
use DebugBar\ZilfDebugbar;
use Home\Service\CommonService;

class OperateController extends BaseController {

    public function _initialize(){
        $this->isLogin();
        parent::_initialize();
    }

    //操作日志
    public function lists(){
        $page = I('get.p', '1', 'int');
        $start_time = I('get.start_time','');
        $end_time = I('get.end_time','');
        if($start_time && $end_time){
            $where['add_time'] = [['egt',strtotime($start_time)],['lt',strtotime($end_time)+86400],'and'];
        }elseif($start_time && !$end_time){
            $where['add_time'] = [['egt',strtotime($start_time)]];
        }elseif(!$start_time && $end_time){
            $where['add_time'] = [['lt',strtotime($end_time)+86400]];
        }else{
            $where = [];
        }
        $pageSize = 20;
        if ($page <= 0) {
            $page = 1;
        }

        //查看总数
        $totalElements = M('operate_log')->where($where)->count();

        //查询列表
        $lists = M('operate_log')->where($where)->order('id desc')->limit($pageSize * ($page - 1), $pageSize)->select();
        foreach ($lists as $key=>$v){
            $lists[$key]['username'] = M('user')->field('username')->where(['id'=>$v['user_id']])->find();
        }
        $this->assign('list',$lists);
        $Page = new \Think\Page($totalElements, $pageSize);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $this->assign('page', $show);
        $this->display();
    }
}