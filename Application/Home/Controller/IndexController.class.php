<?php
namespace Home\Controller;

use DebugBar\ZilfDebugbar;
use Home\Service\CommonService;

class IndexController extends BaseController
{

    public function _initialize()
    {
        parent::_initialize();
        $this->isLogin();
    }

    //后台首页
    public function index()
    {
        $this->display('Index/welcome');
    }

    /**
     * 添加或修改编制输入页
     */
    public function add()
    {
        $id = I('get.id');

        //判断权限
        $acc_arr = json_decode($this->userInfo['access'],true);
        if($acc_arr['b_add'] != 1){
            $this->error('对不起，你没有权限操作，请联系管理员！');
        }

        if($id){
            $info = M('info')->where(['id'=>$id])->find();
            $this->assign('info',$info);
            //获取车型id
            $ids['third'] = $info['category_id'];
            //获取车型的父类id
            $type_parent = M('category')->where(['id'=>$ids['third']])->field('parent_id')->find();
            $ids['second'] = $type_parent['parent_id'];
            //获取车型的父类id的父类id
            $base_id = M('category')->where(['id'=>$ids['second']])->field('parent_id')->find();
            $ids['first'] = $base_id['parent_id'];
            $this->assign('ids',$ids);
            $lists = M('info_extend')->where(['info_id'=>$id])->select();
            $this->assign('lists',$lists);
        }
        $userId = $this->userInfo['id'];
        $this->assign('userId', $userId);
        //主分类
        $category = M('category')->where(['parent_id' => 1])->select();
        $this->assign('category', $category);

        $this->display();
    }



    /**
     * 添加或修改编制输入页
     */
    public function add_cevt()
    {
        $id = I('get.id');

        //判断权限
        $acc_arr = json_decode($this->userInfo['access'],true);
        if($acc_arr['b_add'] != 1){
            $this->error('对不起，你没有权限操作，请联系管理员！');
        }

        if($id){
            $info = M('info_cevt')->where(['id'=>$id])->find();
            $this->assign('info',$info);
            //获取车型id
            $ids['third'] = $info['category_id'];
            //获取车型的父类id
            $type_parent = M('category')->where(['id'=>$ids['third']])->field('parent_id')->find();
            $ids['second'] = $type_parent['parent_id'];
            //获取车型的父类id的父类id
            $base_id = M('category')->where(['id'=>$ids['second']])->field('parent_id')->find();
            $ids['first'] = $base_id['parent_id'];
            $this->assign('ids',$ids);
            $lists = M('info_extend_cevt')->where(['info_id'=>$id])->select();
            $this->assign('lists',$lists);
        }
        $userId = $this->userInfo['id'];
        $this->assign('userId', $userId);
        //主分类
        $category = M('category')->where(['parent_id' => 1])->select();
        $this->assign('category', $category);

        $this->display();
    }

    /**
     * 审批编制详情页
     */
    public function detail_approve()
    {
        $id = I('get.id');

        //判断权限
        $acc_arr = json_decode($this->userInfo['access'],true);
        if($acc_arr['b_add'] != 1){
            $this->error('对不起，你没有权限操作，请联系管理员！');
        }

        if($id){
            $info = M('info')->where(['id'=>$id])->find();
            $this->assign('info',$info);
            //获取车型id
            $third_name = M('category')->where(['id'=>$info['category_id']])->field('parent_id,name')->find();
            $names['third'] = $third_name['name'];
            //获取车型的父类id
            $second_name = M('category')->where(['id'=>$third_name['parent_id']])->field('parent_id,name')->find();
            $names['second'] = $second_name['name'];
            //获取车型的父类id的父类id
            $first_name = M('category')->where(['id'=>$second_name['parent_id']])->field('name')->find();
            $names['first'] = $first_name['name'];
            $this->assign('names',$names);
            $lists = M('info_extend')->where(['info_id'=>$id])->select();
            $this->assign('lists',$lists);
        }

        //主分类
        $category = M('category')->where(['parent_id' => 1])->select();
        $this->assign('category', $category);

        $this->display();
    }

    /**
     * 审批编制详情页CEVT
     */
    public function detail_approve_cevt()
    {
        $id = I('get.id');

        //判断权限
        $acc_arr = json_decode($this->userInfo['access'],true);
        if($acc_arr['b_add'] != 1){
            $this->error('对不起，你没有权限操作，请联系管理员！');
        }

        if($id){
            $info = M('info_cevt')->where(['id'=>$id])->find();
            $this->assign('info',$info);
            //获取车型id
            $third_name = M('category')->where(['id'=>$info['category_id']])->field('parent_id,name')->find();
            $names['third'] = $third_name['name'];
            //获取车型的父类id
            $second_name = M('category')->where(['id'=>$third_name['parent_id']])->field('parent_id,name')->find();
            $names['second'] = $second_name['name'];
            //获取车型的父类id的父类id
            $first_name = M('category')->where(['id'=>$second_name['parent_id']])->field('name')->find();
            $names['first'] = $first_name['name'];
            $this->assign('names',$names);
            $lists = M('info_extend_cevt')->where(['info_id'=>$id])->select();
            $this->assign('lists',$lists);
        }

        //主分类
        $category = M('category')->where(['parent_id' => 1])->select();
        $this->assign('category', $category);

        $this->display();
    }

    /**
     * 保存编制信息
     */
    public function ajax_save_data()
    {
        $id = I('post.id');  //id
        $info_data = $this->get_info_params();
        if(!empty($id)){  //修改信息
            //修改
            $is_success = M('info')->where(['id'=>$id])->save($info_data);

            //删除
            M('info_extend')->where(['info_id'=>$id])->delete();
            $extend_data = $this->get_list_params($id);
            $is_success = M('info_extend')->addAll($extend_data);
            $datas = [
                'user_id' => $this->userInfo['id'],
                'operate' => 'edit',
                'content' => '编辑编号为'.$info_data['h_no'].'的编制',
                'add_time' => time()
            ];
            M('operate_log')->add($datas);

        }else{  //保存信息

            //判断添加人身份，如果是admin则设置审批状态为已审核
            if($info_data['user_id'] == 1){
                $info_data['is_approved'] = 2;
            }
            $is_success = M('info')->add($info_data);

            $data = $this->get_list_params($is_success);
            $is_success = M('info_extend')->addAll($data);
            $datas = [
                'user_id' => $this->userInfo['id'],
                'operate' => 'add',
                'content' => '添加编号为'.$info_data['h_no'].'的编制',
                'add_time' => time()
            ];
            M('operate_log')->add($datas);
        }

        if ($is_success) {
            echo messageFormat('1001', "保存成功！");
        } else {
            echo messageFormat('2002', '保存失败！');
        }
    }


    /**
     * 保存CEVT编制信息
     */
    public function ajax_save_data_cevt()
    {
        $id = I('post.id');  //id
        $info_data = $this->get_info_params_cevt();
        if(!empty($id)){  //修改信息
            //修改
            $is_success = M('info_cevt')->where(['id'=>$id])->save($info_data);

            //删除
            M('info_extend_cevt')->where(['info_id'=>$id])->delete();
            $extend_data = $this->get_list_params_cevt($id);
            $is_success = M('info_extend_cevt')->addAll($extend_data);
            $datas = [
                'user_id' => $this->userInfo['id'],
                'operate' => 'edit',
                'content' => '编辑编号为'.$info_data['change'].'的编制',
                'add_time' => time()
            ];
            M('operate_log')->add($datas);

        }else{  //保存信息

            //判断添加人身份，如果是admin则设置审批状态为已审核
            if($info_data['user_id'] == 1){
                $info_data['is_approved'] = 2;
            }
            $is_success = M('info_cevt')->add($info_data);
            $data = $this->get_list_params_cevt($is_success);
            $is_success = M('info_extend_cevt')->addAll($data);
            $datas = [
                'user_id' => $this->userInfo['id'],
                'operate' => 'add',
                'content' => '添加编号为'.$info_data['change'].'的编制',
                'add_time' => time()
            ];
            M('operate_log')->add($datas);
        }

        if ($is_success) {
            die (messageFormat('1001', "保存成功！"));
        } else {
            die (messageFormat('2002', '保存失败！'));
        }
    }

    /**
     * 获取info的数据
     *
     * @return array
     */
    private function get_info_params()
    {
        $chejian_id = I('post.chejian_id');  //分类
        $jidi_id = I('post.jidi_id');  //分类
        $category_id = I('post.category_id');  //分类
        $project = I('post.project');  //项目/功能
        $require = I('post.require');  //要求
        $h_no = I('post.h_no');  //工序编号
        $h_project = I('post.h_project');  //要求
        $h_c_y = I('post.h_c_y');  //要求
        $h_group = I('post.h_group');  //要求
        $h_design = I('post.h_design');  //要求
        $h_key_time = I('post.h_key_time');  //要求
        $h_edit_people = I('post.h_edit_people');  //要求
        $h_pfmea_start = I('post.h_pfmea_start');  //要求
        $h_pfmea_end = I('post.h_pfmea_end');  //要求
        $h_total_pages = I('post.h_total_pages');  //要求
        $h_page_time = I('post.h_page_time');  //要求

        if (empty($category_id)) {
            die(messageFormat(2001, '请选择分类！'));
        }
        if (empty($project)) {
            die(messageFormat(2001, '项目/功能不能为空！'));
        }

        if(!empty($h_key_time)){
            $h_key_time = str_ireplace('.','-',$h_key_time);
            $h_key_time = strtotime($h_key_time);
        }
        if(!empty($h_pfmea_start)){
            $h_pfmea_start = str_ireplace('.','-',$h_pfmea_start);
            $h_pfmea_start = strtotime($h_pfmea_start);
        }
        if(!empty($h_pfmea_end)){
            $h_pfmea_end = str_ireplace('.','-',$h_pfmea_end);
            $h_pfmea_end = strtotime($h_pfmea_end);
        }else{
            $h_pfmea_end = time();
        }

        $data = [
            'chejian_id' => $chejian_id,
            'jidi_id' => $jidi_id,
            'category_id' => $category_id,
            'user_id' => $this->userInfo['id'],
            'h_no' => $h_no,
            'project' => $project,
            'require' => $require,
            'h_project' => $h_project,
            'h_c_y' => $h_c_y,
            'h_group' => $h_group,
            'h_design' => $h_design,
            'h_key_time' => $h_key_time,
            'h_edit_people' => $h_edit_people,
            'h_pfmea_start' => $h_pfmea_start,
            'h_pfmea_end' => $h_pfmea_end,
            'h_total_pages' => $h_total_pages,
            'h_page_time' => $h_page_time,
        ];

        return $data;
    }

    /**
     * 获取info的数据
     *
     * @return array
     */
    private function get_info_params_cevt()
    {
        $chejian_id = I('post.chejian_id');  //车间
        $jidi_id = I('post.jidi_id');  //基地
        $category_id = I('post.category_id');  //分类
        $type = I('post.type');  //type
        $variant = I('post.variant');  //variant
        $plant = I('post.plant');  //plant
        $issuer = I('post.issuer');  //issuer
        $date = I('post.date');  //date
        $function = I('post.function');  //function
        $layout = I('post.layout');  //process
        $reg = I('post.reg');  //reg
        $issue = I('post.issue');  //issue
        $page = I('post.page');  //page
        $change = I('post.change');  //change
        $part_no = I('post.part_no');  //part_no
        $part_name = I('post.part_name');  //part_name

        $drawing = I('post.drawing');  //drawing
        $supplier = I('post.supplier');  //supplier

        if (empty($category_id)) {
            die(messageFormat(2001, '请选择分类！'));
        }

        if(!empty($date)){
            $dates = str_ireplace('.','-',$date);
            $date = strtotime($dates);
        }else{
            $date = time();
        }

        $data = [
            'chejian_id' => $chejian_id,
            'jidi_id' => $jidi_id,
            'category_id' => $category_id,
            'user_id' => $this->userInfo['id'],
            'type' => $type,
            'variant' => $variant,
            'plant' => $plant,
            'issuer' => $issuer,
            'date' => $date,
            'function' => $function,
            'layout' => $layout,
            'issue' => $issue,
            'reg' => $reg,
            'page' => $page,
            'change' => $change,
            'part_no' => $part_no,
            'part_name' => $part_name,
            'drawing' => $drawing,
            'supplier' => $supplier
        ];

        return $data;
    }

    /**
     * 获取列表的数据
     */
    private function get_list_params($info_id){
        $mode = I('post.mode');  //潜在失效模式
        $mode_effect = I('post.mode_effect');  //失效模式的潜在影响
        $severity = I('post.severity');  //严重度
        $category = I('post.category');  //分类
        $cause = I('post.cause');  //失效潜在原因
        $prevent = I('post.prevent');  //现行设计控制预防
        $incidence = I('post.incidence');  //发生率
        $survey = I('post.survey');  //现行设计控制探测
        $detectivity = I('post.detectivity');  //探测度
        $RPN = I('post.RPN');  //RPN
        $measures = I('post.measures');  //推荐措施
        $over_time = I('post.over_time');  //职责&目标完成日期

        $t_use_time = I('post.t_use_time');  //实际成果 采取措施&有效日期
        $t_severity = I('post.t_severity');  //实际成果 严重度
        $t_incidence = I('post.t_incidence');  //实际成果 发生度
        $t_detectivity = I('post.t_detectivity');  //实际成果 探测度
        $t_RPN = I('post.t_RPN');  //实际成果 RPN

        $data = [];
        foreach ($mode as $key => $row) {

            if(trim($over_time[$key])){
                $over_time[$key] = str_ireplace('.','-',$over_time[$key]);
                $over_time[$key] = strtotime($over_time[$key]);
            }else{
                $over_time[$key] = '';
            }

            if(trim($t_use_time[$key])){
                $t_use_time[$key] = str_ireplace('.','-',$t_use_time[$key]);
                $t_use_time[$key] = strtotime($t_use_time[$key]);
            }else{
                $t_use_time[$key] = '';
            }

            $data[] = [
                'info_id' => $info_id,
                'mode' => $mode[$key],
                'mode_effect' => $mode_effect[$key],
                'severity' => $severity[$key],
                'category' => $category[$key],
                'cause' => $cause[$key],
                'prevent' => $prevent[$key],
                'incidence' => $incidence[$key],
                'survey' => $survey[$key],
                'detectivity' => $detectivity[$key],
                'RPN' => $RPN[$key],
                'measures' => $measures[$key],
                'over_time' => $over_time[$key],

                't_use_time' => $t_use_time[$key],
                't_severity' => $t_severity[$key],
                't_incidence' => $t_incidence[$key],
                't_detectivity' => $t_detectivity[$key],
                't_RPN' => $t_RPN[$key],
            ];
        }

        return $data;
    }

    /**
     * 获取列表的数据
     */
    private function get_list_params_cevt($info_id){
        $no = I('post.no');  //no
        $process = I('post.process');  //process
        $failure = I('post.failure');  //failure
        $effect = I('post.effect');  //effect
        $sev = I('post.sev');  //sev
        $cause = I('post.cause');  //cause
        $occur = I('post.occur');  //occur
        $class = I('post.class');  //class
        $prevention = I('post.prevention');  //prevention
        $detection = I('post.detection');  //detection

        $detec = I('post.detec');  //detec
        $rpn = I('post.rpn');  //rpn
        $recommended = I('post.recommended');  //recommended
        $responsibility = I('post.responsibility');  //responsibilit
        $a_actions = I('post.a_actions');  //a_actions
        $a_sev = I('post.a_sev');  //a_sev
        $a_occur = I('post.a_occur');  //a_occur
        $a_detec = I('post.a_detec');  //a_detec
        $a_rpn = I('post.a_rpn');  //a_rpn

        $data = [];
        foreach ($no as $key => $row) {

            $data[] = [
                'info_id' => $info_id,
                'no' => $no[$key],
                'process' => $process[$key],
                'failure' => $failure[$key],
                'effect' => $effect[$key],
                'sev' => $sev[$key],
                'cause' => $cause[$key],
                'occur' => $occur[$key],
                'class' => $class[$key],
                'prevention' => $prevention[$key],
                'detection' => $detection[$key],
                'detec' => $detec[$key],
                'rpn' => $rpn[$key],

                'recommended' => $recommended[$key],
                'responsibility' => $responsibility[$key]?strtotime($responsibility[$key]):'',
                'a_actions' => $a_actions[$key]?strtotime($a_actions[$key]):'',
                'a_sev' => $a_sev[$key],
                'a_occur' => $a_occur[$key],
                'a_detec' => $a_detec[$key],
                'a_rpn' => $a_rpn[$key],
            ];
        }

        return $data;
    }

    /**
     * 编制信息列表管理
     */
    public function info_ku()
    {
        $page = I('get.p', '1', 'int');
        $pageSize = 20;
        if ($page <= 0) {
            $page = 1;
        }

        //主分类
        $category = M('category')->where(['parent_id' => 1])->select();
        $this->assign('category', $category);
        foreach($category as $item){
            $ids[] = $item['id'];
        }
        //二级分类
        $category2 = M('category')->where(['parent_id' => ['in',$ids]])->select();
        $cate2 = [];
        foreach ($category2 as $v){
            $cate2[$v['id']] = $v['name'];
        }
        $b = [];
        $c = [];
        foreach($cate2 as $k=>$v){
            if(in_array($v,$c)){
                $b[$cate2[$k]] .= ','.$k;
            }else{
                $c[$k] = $v;
                $b[$v] = $k;
            }
        }
        if($b){
            $category2 = array_flip($b);
        }
        $this->assign('category2', $category2);
        //三级分类
        foreach($category2 as $key=>$item){
            $idss[] = $key;
        }
        foreach($idss as $v){
            if(strpos($v,',')){
                $array[] = explode(',',$v);
            }else{
                $new[] = $v;
            }
        }
        $array = $this->merge_array($array);
        foreach($array as $k=>$v){
            $array[$k] = (int)$v;
        }
        $new_array = array_merge($array,$new);
        $category3 = M('category')->where(['parent_id' => ['in',$new_array]])->select();
        $cate3 = [];
        foreach ($category3 as $v){
            $cate3[$v['id']] = $v['name'];
        }
        $b2 = [];
        $c2 = [];
        foreach($cate3 as $k=>$v){
            if(in_array($v,$c2)){
                $b2[$cate3[$k]] .= ','.$k;
            }else{
                $c2[$k] = $v;
                $b2[$v] = $k;
            }
        }
        if($b2){
            $category3 = array_flip($b2);
        }
        $this->assign('category3', $category3);
        //搜索
//        $where = ['user_id'=>$this->userInfo['id'],'is_history'=>1,'is_delete'=>1];
        $where = ['is_history'=>1,'is_delete'=>1];

        $chejian_id = I('get.chejian_id');
        if (!empty($chejian_id)) {
            $where['chejian_id'] = $chejian_id;
        }

        $jidi_id = I('get.jidi_id');
        if (!empty($jidi_id)) {
            $where['jidi_id'] = ['in',$jidi_id];
        }

        $project = I('get.project');
        if (!empty($project)) {
            $cate_id = M('category')->field('id')->where(['name'=>['like',$project]])->select();
            $map1['project|h_no'] = ['like', '%' . $project . '%'];
            $map['_logic'] = 'and';
            $map['_complex'] = $map1;
        }
        $cates = '';
        if($cate_id){
            foreach($cate_id as $v){
                $cates .= $v['id'].',';
            }
            if($cates){
                $cate_id = substr($cates,0,-1);
            }
        }
        $category_id = I('get.category_id');
        if (!empty($category_id)) {
            if(!empty($cate_id)){
                $map['category_id'] = [['in',$category_id],['in',$cate_id],'or'];
            }else{
                $map['category_id'] = ['in',$category_id];
            }
        }else{
            if(!empty($cate_id)){
                $map['category_id'] = ['in',$cate_id];
            }
        }
        if($map){
            $where['_logic'] = 'and';
            $where['_complex'] = $map;
        }

        $this->assign('search', I('get.'));

        //查看总数
        $totalElements = M('info')->where($where)->count();

        //查询列表
        $lists = M('info')->where($where)->order('info.id desc')->limit($pageSize * ($page - 1), $pageSize)->select();
//        echo M()->_sql();die;
        foreach ($lists as $key => $row) {
            //添加车间
            if ($row['chejian_id']) {
                $cat_arr = M('category')->where(['id' => $row['chejian_id']])->find();
                $lists[$key]['chejian_name'] = $cat_arr['name'];
            } else {
                $lists[$key]['chejian_name'] = '';
            }
            //添加基地
            if ($row['category_id']) {
                $cat_arr = M('category')->where(['id' => $row['jidi_id']])->find();
                $lists[$key]['jidi_name'] = $cat_arr['name'];
            } else {
                $lists[$key]['jidi_name'] = '';
            }
            //添加车型
            if ($row['category_id']) {
                $cat_arr = M('category')->where(['id' => $row['category_id']])->find();
                $lists[$key]['chexing_name'] = $cat_arr['name'];
            } else {
                $lists[$key]['chexing_name'] = '';
            }
        }
        $this->assign('list', $lists);

        $Page = new \Think\Page($totalElements, $pageSize);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $this->assign('page', $show);

        $this->assign('is_history', 1);
        $this->display('Index/info_ku');
    }

    /**
     * 编制信息列表管理
     */
    public function info_ku_cevt()
    {
        $page = I('get.p', '1', 'int');
        $pageSize = 20;
        if ($page <= 0) {
            $page = 1;
        }

        //主分类
        $category = M('category')->where(['parent_id' => 1])->select();
        $this->assign('category', $category);
        foreach($category as $item){
            $ids[] = $item['id'];
        }
        //二级分类
        $category2 = M('category')->where(['parent_id' => ['in',$ids]])->select();
        $cate2 = [];
        
        foreach ($category2 as $v){
            $cate2[$v['id']] = $v['name'];
        }
        $b = [];
        $c = [];
        foreach($cate2 as $k=>$v){
            if(in_array($v,$c)){
                $b[$cate2[$k]] .= ','.$k;
            }else{
                $c[$k] = $v;
                $b[$v] = $k;
            }
        }
        if($b){
            $category2 = array_flip($b);
        }
        $this->assign('category2', $category2);
        //三级分类
        foreach($category2 as $key=>$item){
            $idss[] = $key;
        }
        foreach($idss as $v){
            if(strpos($v,',')){
                $array[] = explode(',',$v);
            }else{
                $new[] = $v;
            }
        }
        $array = $this->merge_array($array);
        foreach($array as $k=>$v){
            $array[$k] = (int)$v;
        }
        $new_array = array_merge($array,$new);
        $category3 = M('category')->where(['parent_id' => ['in',$new_array]])->select();
        $cate3 = [];
        foreach ($category3 as $v){
            $cate3[$v['id']] = $v['name'];
        }
        $b2 = [];
        $c2 = [];
        foreach($cate3 as $k=>$v){
            if(in_array($v,$c2)){
                $b2[$cate3[$k]] .= ','.$k;
            }else{
                $c2[$k] = $v;
                $b2[$v] = $k;
            }
        }
        if($b2){
            $category3 = array_flip($b2);
        }
        $this->assign('category3', $category3);
        //搜索
//        $where = ['user_id'=>$this->userInfo['id'],'is_history'=>1,'is_delete'=>1];
        $where = ['is_history'=>1,'is_delete'=>1];

        $chejian_id = I('get.chejian_id');
        if (!empty($chejian_id)) {
            $where['chejian_id'] = $chejian_id;
        }

        $jidi_id = I('get.jidi_id');
        if (!empty($jidi_id)) {
            $where['jidi_id'] = ['in',$jidi_id];
        }

        $project = I('get.project');
        if (!empty($project)) {
            $cate_id = M('category')->field('id')->where(['name'=>['like',$project]])->select();
            $map1['pss|issuer'] = ['like', '%' . $project . '%'];
            $map['_logic'] = 'and';
            $map['_complex'] = $map1;
        }
        $cates = '';
        if($cate_id){
            foreach($cate_id as $v){
                $cates .= $v['id'].',';
            }
            if($cates){
                $cate_id = substr($cates,0,-1);
            }
        }
        $category_id = I('get.category_id');
        if (!empty($category_id)) {
            if(!empty($cate_id)){
                $map['category_id'] = [['in',$category_id],['in',$cate_id],'or'];
            }else{
                $map['category_id'] = ['in',$category_id];
            }
        }else{
            if(!empty($cate_id)){
                $map['category_id'] = ['in',$cate_id];
            }
        }
        if($map){
            $where['_logic'] = 'and';
            $where['_complex'] = $map;
        }

        $this->assign('search', I('get.'));

        //查看总数
        $totalElements = M('info_cevt')->where($where)->count();

        //查询列表
        $lists = M('info_cevt')->where($where)->order('id desc')->limit($pageSize * ($page - 1), $pageSize)->select();
//        echo M()->_sql();die;
        foreach ($lists as $key => $row) {
            //添加车间
            if ($row['chejian_id']) {
                $cat_arr = M('category')->where(['id' => $row['chejian_id']])->find();
                $lists[$key]['chejian_name'] = $cat_arr['name'];
            } else {
                $lists[$key]['chejian_name'] = '';
            }
            //添加基地
            if ($row['category_id']) {
                $cat_arr = M('category')->where(['id' => $row['jidi_id']])->find();
                $lists[$key]['jidi_name'] = $cat_arr['name'];
            } else {
                $lists[$key]['jidi_name'] = '';
            }
            //添加车型
            if ($row['category_id']) {
                $cat_arr = M('category')->where(['id' => $row['category_id']])->find();
                $lists[$key]['chexing_name'] = $cat_arr['name'];
            } else {
                $lists[$key]['chexing_name'] = '';
            }
        }
        $this->assign('list', $lists);

        $Page = new \Think\Page($totalElements, $pageSize);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $this->assign('page', $show);

        $this->assign('is_history', 1);
        $this->display('Index/info_ku_cevt');
    }

    /*
     * 编制提交审批
     */
    public function ajax_to_approve(){
        $id = I('post.id');
        if(!$id){
            die(messageFormat(1002,'参数错误！'));
        }
        $return = M('info')->where(['id'=>$id])->save(['is_approved'=>1]);
        if($return){
            die(messageFormat(1001,'审批提交成功！'));
        }else{
            die(messageFormat(1003,'审批提交失败！'));
        }
    }

    /*
     * 编制提交审批
     */
    public function ajax_to_approve_cevt(){
        $id = I('post.id');
        if(!$id){
            die(messageFormat(1002,'参数错误！'));
        }
        $return = M('info_cevt')->where(['id'=>$id])->save(['is_approved'=>1]);
        if($return){
            die(messageFormat(1001,'审批提交成功！'));
        }else{
            die(messageFormat(1003,'审批提交失败！'));
        }
    }


    /**
     * 待我审批编制信息列表管理
     */
    public function info_approve()
    {
        $page = I('get.p', '1', 'int');
        $pageSize = 20;
        if ($page <= 0) {
            $page = 1;
        }
        $type = I('get.type','1','int');

        //主分类
        $category = M('category')->where(['parent_id' => 1])->select();
        $this->assign('category', $category);

        //根据用户id查询父类
        $parents = M('user_relations')->where(['user_id'=>$this->userInfo['id']])->find();
        $users = M('user_relations')->where(['parent_id'=>$parents['id']])->field('user_id')->select();
        if($users){
            foreach($users as $value){
                $user[] = $value['user_id'];
            }
            $user = implode(',',$user);

            //搜索
            $where = ['user_id'=>['in',$user],'is_history'=>1,'is_approved'=>$type,'is_delete'=>1];
            //查看总数
            $totalElements = M('info')->where($where)->count();
            //查询列表
            $lists = M('info')->where($where)->order('info.id desc')->limit($pageSize * ($page - 1), $pageSize)->select();
            foreach ($lists as $key => $row) {
                //添加车间
                if ($row['chejian_id']) {
                    $cat_arr = M('category')->where(['id' => $row['chejian_id']])->find();
                    $lists[$key]['chejian_name'] = $cat_arr['name'];
                } else {
                    $lists[$key]['chejian_name'] = '';
                }
                //添加基地
                if ($row['category_id']) {
                    $cat_arr = M('category')->where(['id' => $row['jidi_id']])->find();
                    $lists[$key]['jidi_name'] = $cat_arr['name'];
                } else {
                    $lists[$key]['jidi_name'] = '';
                }
                //添加车型
                if ($row['category_id']) {
                    $cat_arr = M('category')->where(['id' => $row['category_id']])->find();
                    $lists[$key]['chexing_name'] = $cat_arr['name'];
                } else {
                    $lists[$key]['chexing_name'] = '';
                }
            }
            $Page = new \Think\Page($totalElements, $pageSize);// 实例化分页类 传入总记录数和每页显示的记录数
            $show = $Page->show();// 分页显示输出
            $this->assign('page', $show);
        }else{
            $lists = [];
        }
        $this->assign('list', $lists);
        $this->assign('is_history', 1);
        $this->display('Index/info_approve');
    }

    /**
     * 待我审批编制信息列表管理
     */
    public function info_approve_cevt()
    {
        $page = I('get.p', '1', 'int');
        $pageSize = 20;
        if ($page <= 0) {
            $page = 1;
        }
        $type = I('get.type','1','int');

        //主分类
        $category = M('category')->where(['parent_id' => 1])->select();
        $this->assign('category', $category);

        //根据用户id查询父类
        $parents = M('user_relations')->where(['user_id'=>$this->userInfo['id']])->find();
        $users = M('user_relations')->where(['parent_id'=>$parents['id']])->field('user_id')->select();
        if($users){
            foreach($users as $value){
                $user[] = $value['user_id'];
            }
            $user = implode(',',$user);

            //搜索
            $where = ['user_id'=>['in',$user],'is_history'=>1,'is_approved'=>$type,'is_delete'=>1];
            //查看总数
            $totalElements = M('info_cevt')->where($where)->count();
            //查询列表
            $lists = M('info_cevt')->where($where)->order('info_cevt.id desc')->limit($pageSize * ($page - 1), $pageSize)->select();
            foreach ($lists as $key => $row) {
                //添加车间
                if ($row['chejian_id']) {
                    $cat_arr = M('category')->where(['id' => $row['chejian_id']])->find();
                    $lists[$key]['chejian_name'] = $cat_arr['name'];
                } else {
                    $lists[$key]['chejian_name'] = '';
                }
                //添加基地
                if ($row['category_id']) {
                    $cat_arr = M('category')->where(['id' => $row['jidi_id']])->find();
                    $lists[$key]['jidi_name'] = $cat_arr['name'];
                } else {
                    $lists[$key]['jidi_name'] = '';
                }
                //添加车型
                if ($row['category_id']) {
                    $cat_arr = M('category')->where(['id' => $row['category_id']])->find();
                    $lists[$key]['chexing_name'] = $cat_arr['name'];
                } else {
                    $lists[$key]['chexing_name'] = '';
                }
            }
            $Page = new \Think\Page($totalElements, $pageSize);// 实例化分页类 传入总记录数和每页显示的记录数
            $show = $Page->show();// 分页显示输出
            $this->assign('page', $show);
        }else{
            $lists = [];
        }
        $this->assign('list', $lists);
        $this->assign('is_history', 1);
        $this->display('Index/info_approve_cevt');
    }


    /*
     * 审批通过
     */
    public function ajax_approve_pass(){
        $id_arr = I('post.i_select');
        $remake = I('post.remake');
        if(!empty($id_arr)){
            if(empty(trim($remake))){
                die(messageFormat(1002,'请填写审批备注！'));
            }
            foreach ($id_arr as $row){
                M('info')->where(['id'=>$row])->save(['is_approved'=>2,'remake'=>$remake]);
            }
            die(messageFormat(1001,'保存成功！'));
        }else{
            die(messageFormat(1002,'请选择数据！'));
        }
    }

    /*
     * 审批通过
     */
    public function ajax_approve_pass_cevt(){
        $id_arr = I('post.i_select');
        $remake = I('post.remake');
        if(!empty($id_arr)){
            if(empty(trim($remake))){
                die(messageFormat(1002,'请填写审批备注！'));
            }
            foreach ($id_arr as $row){
                M('info_cevt')->where(['id'=>$row])->save(['is_approved'=>2,'remake'=>$remake]);
            }
            die(messageFormat(1001,'保存成功！'));
        }else{
            die(messageFormat(1002,'请选择数据！'));
        }
    }

    /*
     * 审批驳回
     */
    public function ajax_approve_refuse(){
        $id_arr = I('post.i_select');
        $remake = I('post.remake');
        if(!empty($id_arr)){
            if(empty(trim($remake))){
                die(messageFormat(1002,'请填写审批备注！'));
            }
            foreach ($id_arr as $row){
                M('info')->where(['id'=>$row])->save(['is_approved'=>4,'remake'=>$remake]);
            }
            die(messageFormat(1001,'保存成功！'));
        }else{
            die(messageFormat(1002,'请选择数据！'));
        }
    }

    /*
     * 审批驳回
     */
    public function ajax_approve_refuse_cevt(){
        $id_arr = I('post.i_select');
        $remake = I('post.remake');
        if(!empty($id_arr)){
            if(empty(trim($remake))){
                die(messageFormat(1002,'请填写审批备注！'));
            }
            foreach ($id_arr as $row){
                M('info_cevt')->where(['id'=>$row])->save(['is_approved'=>4,'remake'=>$remake]);
            }
            die(messageFormat(1001,'保存成功！'));
        }else{
            die(messageFormat(1002,'请选择数据！'));
        }
    }


    /*
     * 编制详情审批通过
     */
    public function ajax_detail_pass(){
        $id = I('post.id');
        $remake = I('post.remake');
        if(!empty($id)){
            if(empty(trim($remake))){
                die(messageFormat(1002,'请填写审批备注！'));
            }
                M('info')->where(['id'=>$id])->save(['is_approved'=>2,'remake'=>$remake]);
            die(messageFormat(1001,'保存成功！'));
        }else{
            die(messageFormat(1002,'请选择数据！'));
        }
    }

    /*
     * 编制详情审批通过
     */
    public function ajax_detail_pass_cevt(){
        $id = I('post.id');
        $remake = I('post.remake');
        if(!empty($id)){
            if(empty(trim($remake))){
                die(messageFormat(1002,'请填写审批备注！'));
            }
            M('info_cevt')->where(['id'=>$id])->save(['is_approved'=>2,'remake'=>$remake]);
            die(messageFormat(1001,'保存成功！'));
        }else{
            die(messageFormat(1002,'请选择数据！'));
        }
    }
    /*
     * 编制详情审批驳回
     */
    public function ajax_detail_refuse(){
        $id = I('post.id');
        $remake = I('post.remake');
        if(!empty($id)){
            if(empty(trim($remake))){
                die(messageFormat(1002,'请填写审批备注！'));
            }
                M('info')->where(['id'=>$id])->save(['is_approved'=>4,'remake'=>$remake]);
            die(messageFormat(1001,'保存成功！'));
        }else{
            die(messageFormat(1002,'请选择数据！'));
        }
    }

    /*
     * 编制详情审批驳回
     */
    public function ajax_detail_refuse_cevt(){
        $id = I('post.id');
        $remake = I('post.remake');
        if(!empty($id)){
            if(empty(trim($remake))){
                die(messageFormat(1002,'请填写审批备注！'));
            }
            M('info_cevt')->where(['id'=>$id])->save(['is_approved'=>4,'remake'=>$remake]);
            die(messageFormat(1001,'保存成功！'));
        }else{
            die(messageFormat(1002,'请选择数据！'));
        }
    }


    /**
     * 历史版本查询
     */
    public function info_ku_history()
    {
        $page = I('get.p', '1', 'int');
        $pageSize = 20;
        if ($page <= 0) {
            $page = 1;
        }

        //主分类
        $category = M('category')->where(['parent_id' => 1])->select();
        $this->assign('category', $category);

        foreach($category as $item){
            $ids[] = $item['id'];
        }
        //二级分类
        $category2 = M('category')->where(['parent_id' => ['in',$ids]])->select();
        $cate2 = [];
        foreach ($category2 as $v){
            $cate2[$v['id']] = $v['name'];
        }
        $b = [];
        $c = [];
        foreach($cate2 as $k=>$v){
            if(in_array($v,$c)){
                $b[$cate2[$k]] .= ','.$k;
            }else{
                $c[$k] = $v;
                $b[$v] = $k;
            }
        }
        if($b){
            $category2 = array_flip($b);
        }
        $this->assign('category2', $category2);

        //三级分类
        foreach($category2 as $key=>$item){
            $idss[] = $key;
        }
        foreach($idss as $v){
            if(strpos($v,',')){
                $array[] = explode(',',$v);
            }else{
                $new[] = $v;
            }
        }
        $array = $this->merge_array($array);
        foreach($array as $key=>$v){
            $array[$key] = (int)$v;
        }
        $new_array = array_merge($array,$new);
        $category3 = M('category')->where(['parent_id' => ['in',$new_array]])->select();
        $cate3 = [];
        foreach ($category3 as $v){
            $cate3[$v['id']] = $v['name'];
        }
        $b2 = [];
        $c2 = [];
        foreach($cate3 as $k=>$v){
            if(in_array($v,$c2)){
                $b2[$cate3[$k]] .= ','.$k;
            }else{
                $c2[$k] = $v;
                $b2[$v] = $k;
            }
        }
        if($b2){
            $category3 = array_flip($b2);
        }
        $this->assign('category3', $category3);

        //搜索 去掉根据user_id筛选编制
        $where = ['is_history'=>2];

        $chejian_id = I('get.chejian_id');
        if (!empty($chejian_id)) {
            $where['chejian_id'] = $chejian_id;
        }

        $jidi_id = I('get.jidi_id');
        if (!empty($jidi_id)) {
            $where['jidi_id'] = ['in',$jidi_id];
        }

        $project = I('get.project');
        if (!empty($project)) {
            $cate_id = M('category')->field('id')->where(['name'=>['like',$project]])->select();
            $map1['project|h_no'] = ['like', '%' . $project . '%'];
            $map['_logic'] = 'and';
            $map['_complex'] = $map1;
        }
        $cates = '';
        if($cate_id){
            foreach($cate_id as $v){
                $cates .= $v['id'].',';
            }
            if($cates){
                $cate_id = substr($cates,0,-1);
            }
        }
        $category_id = I('get.category_id');
        if (!empty($category_id)) {
            if(!empty($cate_id)){
                $map['category_id'] = [['in',$category_id],['in',$cate_id],'or'];
            }else{
                $map['category_id'] = ['in',$category_id];
            }
        }else{
            if(!empty($cate_id)){
                $map['category_id'] = ['in',$cate_id];
            }
        }
        if($map){
            $where['_logic'] = 'and';
            $where['_complex'] = $map;
        }

        $this->assign('search', I('get.'));

        //查看总数
        $totalElements = M('info')->where($where)->count();

        //查询列表
        $lists = M('info')->where($where)->order('info.h_no desc')->limit($pageSize * ($page - 1), $pageSize)->select();
        foreach ($lists as $key => $row) {
            //添加车间
            if ($row['chejian_id']) {
                $cat_arr = M('category')->where(['id' => $row['chejian_id']])->find();
                $lists[$key]['chejian_name'] = $cat_arr['name'];
            } else {
                $lists[$key]['chejian_name'] = '';
            }
            //添加基地
            if ($row['category_id']) {
                $cat_arr = M('category')->where(['id' => $row['jidi_id']])->find();
                $lists[$key]['jidi_name'] = $cat_arr['name'];
            } else {
                $lists[$key]['jidi_name'] = '';
            }
            //添加车型
            if ($row['category_id']) {
                $cat_arr = M('category')->where(['id' => $row['category_id']])->find();
                $lists[$key]['chexing_name'] = $cat_arr['name'];
            } else {
                $lists[$key]['chexing_name'] = '';
            }
        }
        $this->assign('list', $lists);


        $Page = new \Think\Page($totalElements, $pageSize);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $this->assign('page', $show);

        $this->assign('is_history', 2);
        $this->display('Index/info_ku');
    }

    /**
     * 历史版本查询-CEVT
     */
    public function info_ku_history_cevt()
    {
        $page = I('get.p', '1', 'int');
        $pageSize = 20;
        if ($page <= 0) {
            $page = 1;
        }

        //主分类
        $category = M('category')->where(['parent_id' => 1])->select();
        $this->assign('category', $category);

        foreach($category as $item){
            $ids[] = $item['id'];
        }
        //二级分类
        $category2 = M('category')->where(['parent_id' => ['in',$ids]])->select();
        $cate2 = [];
        foreach ($category2 as $v){
            $cate2[$v['id']] = $v['name'];
        }
        $b = [];
        $c = [];
        foreach($cate2 as $k=>$v){
            if(in_array($v,$c)){
                $b[$cate2[$k]] .= ','.$k;
            }else{
                $c[$k] = $v;
                $b[$v] = $k;
            }
        }
        if($b){
            $category2 = array_flip($b);
        }
        $this->assign('category2', $category2);

        //三级分类
        foreach($category2 as $key=>$item){
            $idss[] = $key;
        }
        foreach($idss as $v){
            if(strpos($v,',')){
                $array[] = explode(',',$v);
            }else{
                $new[] = $v;
            }
        }
        $array = $this->merge_array($array);
        foreach($array as $key=>$v){
            $array[$key] = (int)$v;
        }
        $new_array = array_merge($array,$new);
        $category3 = M('category')->where(['parent_id' => ['in',$new_array]])->select();
        $cate3 = [];
        foreach ($category3 as $v){
            $cate3[$v['id']] = $v['name'];
        }
        $b2 = [];
        $c2 = [];
        foreach($cate3 as $k=>$v){
            if(in_array($v,$c2)){
                $b2[$cate3[$k]] .= ','.$k;
            }else{
                $c2[$k] = $v;
                $b2[$v] = $k;
            }
        }
        if($b2){
            $category3 = array_flip($b2);
        }
        $this->assign('category3', $category3);

        //搜索 去掉根据user_id筛选编制
        $where = ['is_history'=>2];

        $chejian_id = I('get.chejian_id');
        if (!empty($chejian_id)) {
            $where['chejian_id'] = $chejian_id;
        }

        $jidi_id = I('get.jidi_id');
        if (!empty($jidi_id)) {
            $where['jidi_id'] = ['in',$jidi_id];
        }

        $project = I('get.project');
        if (!empty($project)) {
            $cate_id = M('category')->field('id')->where(['name'=>['like',$project]])->select();
            $map1['change|issuer'] = ['like', '%' . $project . '%'];
            $map['_logic'] = 'and';
            $map['_complex'] = $map1;
        }
        $cates = '';
        if($cate_id){
            foreach($cate_id as $v){
                $cates .= $v['id'].',';
            }
            if($cates){
                $cate_id = substr($cates,0,-1);
            }
        }
        $category_id = I('get.category_id');
        if (!empty($category_id)) {
            if(!empty($cate_id)){
                $map['category_id'] = [['in',$category_id],['in',$cate_id],'or'];
            }else{
                $map['category_id'] = ['in',$category_id];
            }
        }else{
            if(!empty($cate_id)){
                $map['category_id'] = ['in',$cate_id];
            }
        }
        if($map){
            $where['_logic'] = 'and';
            $where['_complex'] = $map;
        }

        $this->assign('search', I('get.'));

        //查看总数
        $totalElements = M('info_cevt')->where($where)->count();

        //查询列表
        $lists = M('info_cevt')->where($where)->order('id desc')->limit($pageSize * ($page - 1), $pageSize)->select();
        foreach ($lists as $key => $row) {
            //添加车间
            if ($row['chejian_id']) {
                $cat_arr = M('category')->where(['id' => $row['chejian_id']])->find();
                $lists[$key]['chejian_name'] = $cat_arr['name'];
            } else {
                $lists[$key]['chejian_name'] = '';
            }
            //添加基地
            if ($row['category_id']) {
                $cat_arr = M('category')->where(['id' => $row['jidi_id']])->find();
                $lists[$key]['jidi_name'] = $cat_arr['name'];
            } else {
                $lists[$key]['jidi_name'] = '';
            }
            //添加车型
            if ($row['category_id']) {
                $cat_arr = M('category')->where(['id' => $row['category_id']])->find();
                $lists[$key]['chexing_name'] = $cat_arr['name'];
            } else {
                $lists[$key]['chexing_name'] = '';
            }
        }
        $this->assign('list', $lists);


        $Page = new \Think\Page($totalElements, $pageSize);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $this->assign('page', $show);

        $this->assign('is_history', 2);
        $this->display('Index/info_ku_cevt');
    }

    /**
     * 当前版本编制
     */
    public function info_ku_current()
    {
        $page = I('get.p', '1', 'int');
        $pageSize = 20;
        if ($page <= 0) {
            $page = 1;
        }

        //主分类
        $category = M('category')->where(['parent_id' => 1])->select();
        $this->assign('category', $category);

        foreach($category as $item){
            $ids[] = $item['id'];
        }
        //二级分类
        $category2 = M('category')->where(['parent_id' => ['in',$ids]])->select();
        $cate2 = [];
        foreach ($category2 as $v){
            $cate2[$v['id']] = $v['name'];
        }
        $b = [];
        $c = [];
        foreach($cate2 as $k=>$v){
            if(in_array($v,$c)){
                $b[$cate2[$k]] .= ','.$k;
            }else{
                $c[$k] = $v;
                $b[$v] = $k;
            }
        }
        if($b){
            $category2 = array_flip($b);
        }
        $this->assign('category2', $category2);

        //三级分类
        foreach($category2 as $key=>$item){
            $idss[] = $key;
        }
        foreach($idss as $v){
            if(strpos($v,',')){
                $array[] = explode(',',$v);
            }else{
                $new[] = $v;
            }
        }
        $array = $this->merge_array($array);
        foreach($array as $key=>$v){
            $array[$key] = (int)$v;
        }
        $new_array = array_merge($array,$new);
        $category3 = M('category')->where(['parent_id' => ['in',$new_array]])->select();
        $cate3 = [];
        foreach ($category3 as $v){
            $cate3[$v['id']] = $v['name'];
        }
        $b2 = [];
        $c2 = [];
        foreach($cate3 as $k=>$v){
            if(in_array($v,$c2)){
                $b2[$cate3[$k]] .= ','.$k;
            }else{
                $c2[$k] = $v;
                $b2[$v] = $k;
            }
        }
        if($b2){
            $category3 = array_flip($b2);
        }
        $this->assign('category3', $category3);
        //搜索 去掉根据用户id显示对应的编制
//        $where = ['user_id'=>$this->userInfo['id'],];

        $chejian_id = I('get.chejian_id');
        if (!empty($chejian_id)) {
            $where['chejian_id'] = $chejian_id;
        }

        $jidi_id = I('get.jidi_id');
        if (!empty($jidi_id)) {
            $where['jidi_id'] = ['in',$jidi_id];
        }

        $project = I('get.project');
        if (!empty($project)) {
            $cate_id = M('category')->field('id')->where(['name'=>['like',$project]])->select();
            $map1['project|h_no'] = ['like', '%' . $project . '%'];
            $map['_logic'] = 'or';
            $map['_complex'] = $map1;
        }
        $cates = '';
        if($cate_id){
            foreach($cate_id as $v){
                $cates .= $v['id'].',';
            }
            if($cates){
                $cate_id = substr($cates,0,-1);
            }
        }
        $category_id = I('get.category_id');
        if (!empty($category_id)) {
            if(!empty($cate_id)){
                $map['category_id'] = [['in',$category_id],['in',$cate_id],'or'];
            }else{
                $map['category_id'] = ['in',$category_id];
            }
        }else{
            if(!empty($cate_id)){
                $map['category_id'] = ['in',$cate_id];
            }
        }
        if($map){
            $where['_logic'] = 'and';
            $where['_complex'] = $map;
        }

        $where['is_delete'] = 1;

        $this->assign('search', I('get.'));
        //查看总数
        $totalElements = M('info')->where($where)->count();
        //查询列表
        $lists = M('info')->where($where)->order('info.id desc')->limit($pageSize * ($page - 1), $pageSize)->select();
        foreach ($lists as $key => $row) {
            //添加车间
            if ($row['chejian_id']) {
                $cat_arr = M('category')->where(['id' => $row['chejian_id']])->find();
                $lists[$key]['chejian_name'] = $cat_arr['name'];
            } else {
                $lists[$key]['chejian_name'] = '';
            }
            //添加基地
            if ($row['category_id']) {
                $cat_arr = M('category')->where(['id' => $row['jidi_id']])->find();
                $lists[$key]['jidi_name'] = $cat_arr['name'];
            } else {
                $lists[$key]['jidi_name'] = '';
            }
            //添加车型
            if ($row['category_id']) {
                $cat_arr = M('category')->where(['id' => $row['category_id']])->find();
                $lists[$key]['chexing_name'] = $cat_arr['name'];
            } else {
                $lists[$key]['chexing_name'] = '';
            }
        }
        $this->assign('list', $lists);

        $Page = new \Think\Page($totalElements, $pageSize);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $this->assign('page', $show);

        $this->assign('is_history', 3);
        $this->display('Index/info_ku');
    }

    /**
     * 当前版本编制-CEVT
     */
    public function info_ku_current_cevt()
    {
        $page = I('get.p', '1', 'int');
        $pageSize = 20;
        if ($page <= 0) {
            $page = 1;
        }

        //主分类
        $category = M('category')->where(['parent_id' => 1])->select();
        $this->assign('category', $category);

        foreach($category as $item){
            $ids[] = $item['id'];
        }
        //二级分类
        $category2 = M('category')->where(['parent_id' => ['in',$ids]])->select();
        $cate2 = [];
        foreach ($category2 as $v){
            $cate2[$v['id']] = $v['name'];
        }
        $b = [];
        $c = [];
        foreach($cate2 as $k=>$v){
            if(in_array($v,$c)){
                $b[$cate2[$k]] .= ','.$k;
            }else{
                $c[$k] = $v;
                $b[$v] = $k;
            }
        }
        if($b){
            $category2 = array_flip($b);
        }
        $this->assign('category2', $category2);

        //三级分类
        foreach($category2 as $key=>$item){
            $idss[] = $key;
        }
        foreach($idss as $v){
            if(strpos($v,',')){
                $array[] = explode(',',$v);
            }else{
                $new[] = $v;
            }
        }
        $array = $this->merge_array($array);
        foreach($array as $key=>$v){
            $array[$key] = (int)$v;
        }
        $new_array = array_merge($array,$new);
        $category3 = M('category')->where(['parent_id' => ['in',$new_array]])->select();
        $cate3 = [];
        foreach ($category3 as $v){
            $cate3[$v['id']] = $v['name'];
        }
        $b2 = [];
        $c2 = [];
        foreach($cate3 as $k=>$v){
            if(in_array($v,$c2)){
                $b2[$cate3[$k]] .= ','.$k;
            }else{
                $c2[$k] = $v;
                $b2[$v] = $k;
            }
        }
        if($b2){
            $category3 = array_flip($b2);
        }
        $this->assign('category3', $category3);
        //搜索 去掉根据用户id显示对应的编制
//        $where = ['user_id'=>$this->userInfo['id'],];

        $chejian_id = I('get.chejian_id');
        if (!empty($chejian_id)) {
            $where['chejian_id'] = $chejian_id;
        }

        $jidi_id = I('get.jidi_id');
        if (!empty($jidi_id)) {
            $where['jidi_id'] = ['in',$jidi_id];
        }

        $project = I('get.project');
        if (!empty($project)) {
            $cate_id = M('category')->field('id')->where(['name'=>['like',$project]])->select();
            $map1['change|issuer'] = ['like', '%' . $project . '%'];
            $map['_logic'] = 'and';
            $map['_complex'] = $map1;
        }
        $cates = '';
        if($cate_id){
            foreach($cate_id as $v){
                $cates .= $v['id'].',';
            }
            if($cates){
                $cate_id = substr($cates,0,-1);
            }
        }
        $category_id = I('get.category_id');
        if (!empty($category_id)) {
            if(!empty($cate_id)){
                $map['category_id'] = [['in',$category_id],['in',$cate_id],'or'];
            }else{
                $map['category_id'] = ['in',$category_id];
            }
        }else{
            if(!empty($cate_id)){
                $map['category_id'] = ['in',$cate_id];
            }
        }
        if($map){
            $where['_logic'] = 'and';
            $where['_complex'] = $map;
        }

        $where['is_delete'] = 1;

        $this->assign('search', I('get.'));
        //查看总数
        $totalElements = M('info_cevt')->where($where)->count();
        //查询列表
        $lists = M('info_cevt')->where($where)->order('id desc')->limit($pageSize * ($page - 1), $pageSize)->select();
        foreach ($lists as $key => $row) {
            //添加车间
            if ($row['chejian_id']) {
                $cat_arr = M('category')->where(['id' => $row['chejian_id']])->find();
                $lists[$key]['chejian_name'] = $cat_arr['name'];
            } else {
                $lists[$key]['chejian_name'] = '';
            }
            //添加基地
            if ($row['category_id']) {
                $cat_arr = M('category')->where(['id' => $row['jidi_id']])->find();
                $lists[$key]['jidi_name'] = $cat_arr['name'];
            } else {
                $lists[$key]['jidi_name'] = '';
            }
            //添加车型
            if ($row['category_id']) {
                $cat_arr = M('category')->where(['id' => $row['category_id']])->find();
                $lists[$key]['chexing_name'] = $cat_arr['name'];
            } else {
                $lists[$key]['chexing_name'] = '';
            }
        }
        $this->assign('list', $lists);

        $Page = new \Think\Page($totalElements, $pageSize);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $this->assign('page', $show);

        $this->assign('is_history', 3);
        $this->display('Index/info_ku_cevt');
    }

    /**
     * 锁定到历史版本
     */
    public function ajax_save_history(){
        $id_arr = I('post.i_select');
        $datas = [
            'user_id' => $this->userInfo['id'],
            'operate' => 'add',
            'add_time' => time()
        ];
        if(!empty($id_arr)){
            foreach ($id_arr as $row){
                $is_success = M('info')->where(['id'=>$row])->save(['is_history'=>2]);
                $name = M('info')->field('h_no')->where(['id'=>$row])->find();
                $datas['content'] = '锁定编号为'.$name['h_no'].'的编制到历史版本';
                M('operate_log')->add($datas);
            }
            die(messageFormat(1001,'保存成功！'));
        }else{
            die(messageFormat(2006,'请先选择数据！'));
        }
    }

    /**
     * 锁定到历史版本-CEVT
     */
    public function ajax_save_history_cevt(){
        $id_arr = I('post.i_select');
        $datas = [
            'user_id' => $this->userInfo['id'],
            'operate' => 'add',
            'add_time' => time()
        ];
        if(!empty($id_arr)){
            foreach ($id_arr as $row){
                $is_success = M('info_cevt')->where(['id'=>$row])->save(['is_history'=>2]);
                $name = M('info_cevt')->field('change')->where(['id'=>$row])->find();
                $datas['content'] = '锁定编号为'.$name['change'].'的编制到历史版本';
                M('operate_log')->add($datas);
            }
            die(messageFormat(1001,'保存成功！'));
        }else{
            die(messageFormat(2006,'请先选择数据！'));
        }
    }

    /*
     * 回退历史版本
     */
    public function ajax_save_back()
    {
        $id_arr = I('post.i_select');
        $datas = [
            'user_id' => $this->userInfo['id'],
            'operate' => 'add',
            'add_time' => time()
        ];
        if(!empty($id_arr)){
            foreach ($id_arr as $row){
                $is_success = M('info')->where(['id'=>$row])->save(['is_history'=>1]);
                $name = M('info')->field('h_no')->where(['id'=>$row])->find();
                $datas['content'] = '回退编号为'.$name['h_no'].'的编制';
                M('operate_log')->add($datas);
            }

            die(messageFormat(1001,'保存成功！'));
        }else{
            die(messageFormat(2006,'请先选择数据！'));
        }
    }

    /**
     * 复制工序
     */
    public function ajax_copy_process(){
        $id_arr = I('post.i_select');
        if(!empty($id_arr)){
            foreach ($id_arr as $row){
                $is_success = M('info')->where(['id'=>$row])->find();
                unset($is_success['id']);
                $is_success['is_approved'] = 3;
                $info_id = M('info')->add($is_success);
                $is_extend = M('info_extend')->where(['info_id'=>$row])->select();
                foreach($is_extend as $extend){
                    unset($extend['e_id']);
                    $extend['info_id'] = $info_id;
                    M('info_extend')->add($extend);
                }
            }
            die(messageFormat(1001,'保存成功！'));
        }else{
            die(messageFormat(2006,'请先选择数据！'));
        }
    }

    /**
     * 复制工序cevt
     */
    public function ajax_copy_process_cevt(){
        $id_arr = I('post.i_select');
        if(!empty($id_arr)){
            foreach ($id_arr as $row){
                $is_success = M('info_cevt')->where(['id'=>$row])->find();
                unset($is_success['id']);
                $is_success['is_approved'] = 3;
                $info_id = M('info_cevt')->add($is_success);
                $is_extend = M('info_extend_cevt')->where(['info_id'=>$row])->select();
                foreach($is_extend as $extend){
                    unset($extend['e_id']);
                    $extend['info_id'] = $info_id;
                    M('info_extend_cevt')->add($extend);
                }
            }
            die(messageFormat(1001,'保存成功！'));
        }else{
            die(messageFormat(2006,'请先选择数据！'));
        }
    }

    /**
     * 删除工序
     */
    public function ajax_delete_process(){
        $id_arr = I('post.i_select');
        $datas = [
            'user_id' => $this->userInfo['id'],
            'operate' => 'delete',
            'add_time' => time()
        ];
        if(!empty($id_arr)){
            foreach ($id_arr as $row){
                M('info')->where(['id'=>$row,'user_id'=>$this->userInfo['id']])->save(['is_delete'=>2]);
                $name = M('info')->field('h_no')->where(['id'=>$row])->find();
                $datas['content'] = '删除编号为'.$name['h_no'].'的编制';
                M('operate_log')->add($datas);
            }
            die(messageFormat(1001,'保存成功！'));
        }else{
            die(messageFormat(2006,'请先选择数据！'));
        }
    }

    /**
     * 删除工序cevt
     */
    public function ajax_delete_process_cevt(){
        $id_arr = I('post.i_select');
        $datas = [
            'user_id' => $this->userInfo['id'],
            'operate' => 'delete',
            'add_time' => time()
        ];
        if(!empty($id_arr)){
            foreach ($id_arr as $row){
                M('info_cevt')->where(['id'=>$row,'user_id'=>$this->userInfo['id']])->save(['is_delete'=>2]);
                $name = M('info_cevt')->field('change')->where(['id'=>$row])->find();
                $datas['content'] = '删除编号为'.$name['change'].'的编制';
                M('operate_log')->add($datas);
            }
            die(messageFormat(1001,'保存成功！'));
        }else{
            die(messageFormat(2006,'请先选择数据！'));
        }
    }

    function merge_array($array){
        return call_user_func_array('array_merge',$array);
    }

}