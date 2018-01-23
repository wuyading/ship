<?php

namespace Home\Controller;

use Think\Upload;

class ReadexcelController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->isLogin();
    }

    public function read_excel()
    {
        set_time_limit(0);//设置不超时
        @ini_set('memory_limit', '512M'); //设置PHP能使用的内存大小

        $success = false;
        $files = I('post.path');
        if(is_array($files)){
            foreach ($files as $row){
                $success = $this->import_db($row);
            }
        }
        if ($success) {
            $this->success('导入成功', U('index/info_ku'));
        } else {
            $this->error('导入失败，文件格式错误，请重新上传！');
        }
    }

    /**
     * CEVT
     */
    public function read_excel_cevt()
    {
        set_time_limit(0);//设置不超时
        @ini_set('memory_limit', '512M'); //设置PHP能使用的内存大小

        $success = false;
        $files = I('post.path');
        $files_name = I('post.filename');
        if(is_array($files)){
            foreach ($files as $key=>$row){
                $success = $this->import_db_cevt($row,$this->cut_str($files_name[$key],'-',1));
            }
        }
        if ($success) {
            $this->success('导入成功', U('index/info_ku_cevt'));
        } else {
            $this->error('导入失败，文件格式错误，请重新上传！');
        }
    }

    private function import_db($input_file){
        $objPHPExcel = \PHPExcel_IOFactory::load($input_file);
        $sheet = $objPHPExcel->getSheet(0);
//        $highestRow = $sheet->getHighestRow(); // 取得总行数
        $sheetData = $sheet->toArray(null, true, true, true);
        foreach ($sheetData as $k => $data) {
            if (trim($data['A']) == '潜在失效模式与影响分析（过程FMEA)') {
                $keys[] = $k;
            }
        }

        for ($k = 0; $k < count($keys); $k++) {
            if ($k == count($keys) - 1) {
                $sheetDatas[] = array_slice($sheetData, $keys[$k]);
            } else {
                $sheetDatas[] = array_slice($sheetData, $keys[$k], $keys[$k + 1] - $keys[$k] - 1);
            }
        }

        foreach ($sheetDatas as $kee => $value) {
            $success[] = $this->save_data($value, count($value)-1);
        }

        if (isset($success)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $input_file
     * @return bool
     * CEVT
     */
    private function import_db_cevt($input_file,$pss){
        $objPHPExcel = \PHPExcel_IOFactory::load($input_file);
        $sheet = $objPHPExcel->getSheet(0);
//        $highestRow = $sheet->getHighestRow(); // 取得总行数
        $sheetData = $sheet->toArray(null, true, true, true);
        foreach ($sheetData as $k => $data) {
            if (trim(strip_tags($data['A'])) == 'FMEA - Failure Mode and Effects Analysis') {
                $keys[] = $k;
            }
        }

        for ($k = 0; $k < count($keys); $k++) {
            if ($k == count($keys) - 1) {
                $sheetDatas[] = array_slice($sheetData, $keys[$k]);
            } else {
                $sheetDatas[] = array_slice($sheetData, $keys[$k], $keys[$k + 1] - $keys[$k] - 1);
            }
        }

        foreach ($sheetDatas as $kee => $value) {
            $success[] = $this->save_data_cevt($value, count($value)-1,$pss,$objPHPExcel);
        }

        if (isset($success)) {
            return true;
        } else {
            return false;
        }
    }

    private function upload()
    {
        set_time_limit(0);//设置不超时

        $upload = new Upload(); // 实例化上传类
        $upload->maxSize = 31457280;// 设置附件上传大小
        $upload->exts = array('xls', 'xlsx');// 设置附件上传类型
        $upload->rootPath = RUNTIME_PATH . '/Excel/'; // 设置附件上传根目录
        $upload->savePath = ''; // 设置附件上传（子）目录

        //文件夹不存在创建文件夹
        if (!file_exists($upload->rootPath) && !is_dir($upload->rootPath)) {
            mkdir($upload->rootPath, 0777, true);
        }

        $info = $upload->upload(); // 上传文件
        if (!$info) {// 上传错误提示错误信息
            return ['status' => 2001, 'message' => $upload->getError(), 'data' => ''];
        } else {// 上传成功
            $file = $info['upfile']['savepath'] . $info['upfile']['savename'];
            return ['status' => 1001, 'message' => 'success', 'data' => $file];
        }
    }


    /**
     * @param $data
     * @param $highestRow
     * @return array
     */
    private function save_data($data, $highestRow)
    {
        $h_no = $data[1]['O'];
        $h_total_pages = $data[2]['O'];
        $h_page_time = $data[2]['R'];

        $h_project = $data[3]['B']; //项目
        $h_design = $data[3]['H'];   //设计职责
        $h_edit_people = $data[3]['O'];   //编制人

        $h_c_y = $data[4]['B'];  //车型/年项目
        $h_key_time = $data[4]['H'];  //关键日期
        $h_pfmea_start = $data[4]['P'];  //关键日期

        $h_group = $data[5]['B'];  //核心小组
        $h_pfmea_end = $data[5]['P'];  //关键日期

        $project = $data[9]['A'];
        $require = $data[9]['B'];

        if(strpos($h_key_time,'-') || strpos($h_pfmea_start,'-')  || strpos($h_pfmea_end,'-')){
            $this->error('时间请以 年.月.日 格式！');
        }

        if (!empty($h_key_time)) {
            $h_key_time = str_ireplace('.', '-', $h_key_time);
            $h_key_time = strtotime($h_key_time);
        }
        if (!empty($h_pfmea_start)) {
            $h_pfmea_start = str_ireplace('.', '-', $h_pfmea_start);
            $h_pfmea_start = strtotime($h_pfmea_start);
        }
        if (!empty($h_pfmea_end)) {
            $h_pfmea_end = str_ireplace('.', '-', $h_pfmea_end);
            $h_pfmea_end = strtotime($h_pfmea_end);
        }else{
            $h_pfmea_end = time();
        }

        //车间 基地 车型
        $chejian_id = I('post.chejian_id');
        $jidi_id = I('post.jidi_id');
        $category_id = I('post.category_id');

        if (empty($category_id) || empty($chejian_id) || empty($jidi_id)) {
            $this->error('请选择分类!');
        }
        if (empty($project)) {
            $this->error('项目/功能不能为空！');
        }
        if (empty($require)) {
            $this->error('要求不能为空！');
        }
        $main_data = [
            'user_id' => $this->userInfo['id'],
            'chejian_id' => $chejian_id,
            'jidi_id' => $jidi_id,
            'category_id' => $category_id,

            'project' => $project,
            'require' => $require,

            'h_no' => $h_no,
            'h_total_pages' => $h_total_pages,
            'h_page_time' => $h_page_time,

            'h_project' => $h_project,
            'h_design' => $h_design,
            'h_edit_people' => $h_edit_people,

            'h_key_time' => $h_key_time,

            'h_c_y' => $h_c_y,
            'h_group' => $h_group,

            'h_pfmea_start' => $h_pfmea_start,
            'h_pfmea_end' => $h_pfmea_end,
        ];
        if ($this->userInfo['id'] == 1) {
            $main_data['is_approved'] = 2;
        }
        for ($i = 9; $i <= $highestRow; $i++) {
            if ( strpos($data[$i]['N'],'-') || strpos($data[$i]['O'],'-')) {
                $this->error('时间请以 年.月.日 格式！');
            }
        }
        //保存主表信息
        $info_id = M('info')->add($main_data);
        if($info_id){
            $datas = [
                'user_id' => $this->userInfo['id'],
                'operate' => 'add',
                'add_time' => time()
            ];
            $datas['content'] = '导入编号为'.$h_no.'的编制';
            M('operate_log')->add($datas);
        }

        for ($i = 9; $i <= $highestRow; $i++) {
            if (!empty($data[$i]['N'])) {
                $over_time[] = strtotime(str_ireplace('.', '-', $data[$i]['N']));
            }else{
                $over_time[] = '';
            }
            if (!empty($data[$i]['O'])) {
                $t_use_time[] = strtotime(str_ireplace('.', '-', $data[$i]['O']));
            }else{
                $t_use_time[] = '';
            }
            $empty1[] = $data[$i]['A'];
            $empty2[] = $data[$i]['B'];
            $mode[] = $data[$i]['C'];
            $mode_effect[] = $data[$i]['D'];
            $severity[] = trim($data[$i]['E'])?$data[$i]['E']:'';
            $category[] = $data[$i]['F'];
            $cause[] = $data[$i]['G'];
            $prevent[] = $data[$i]['H'];
            $incidence[] = trim($data[$i]['I'])?$data[$i]['I']:'';
            $survey[] = $data[$i]['J'];
            $detectivity[] = trim($data[$i]['K'])?$data[$i]['K']:'';
            $RPN[] = $data[$i]['L'];
            $measures[] = $data[$i]['M'];
            $t_severity[] = trim($data[$i]['P'])?$data[$i]['P']:'';
            $t_incidence[] = trim($data[$i]['Q'])?$data[$i]['Q']:'';
            $t_detectivity[] = trim($data[$i]['R'])?$data[$i]['R']:'';
            $t_RPN[] = $data[$i]['S'];
        }
        $c_data = [];
        foreach ($mode as $key => $row) {
            if( !isset($mode[$key]) && !isset($mode_effect[$key]) && !isset($severity[$key]) && !isset($category[$key])
                && !isset($cause[$key]) && !isset($prevent[$key]) && !isset($incidence[$key]) && !isset($survey[$key])
                && !isset($detectivity[$key]) && !isset($RPN[$key]) && !isset($measures[$key]) && !isset($t_severity[$key])
                && !isset($t_incidence[$key]) && !isset($t_detectivity[$key]) && !isset($t_RPN[$key]) ){
                continue;
            }else{
                $c_data[] = [
                    'info_id' => $info_id,
                    'mode' => $mode[$key],
                    'mode_effect' => $mode_effect[$key],
                    'severity' => isset($severity[$key])?$severity[$key]:'',
                    'category' => $category[$key],
                    'cause' => $cause[$key],
                    'prevent' => $prevent[$key],
                    'incidence' => isset($incidence[$key])?$incidence[$key]:'',
                    'survey' => $survey[$key],
                    'detectivity' => isset($detectivity[$key])?$detectivity[$key]:'',
                    'RPN' => $RPN[$key],
                    'measures' => $measures[$key],
                    'over_time' => $over_time[$key],

                    't_use_time' => $t_use_time[$key],
                    't_severity' => '',
                    't_incidence' => '',
                    't_detectivity' => '',
                    't_RPN' => $t_RPN[$key],
                ];
            }
        }

        $is_success[] = M('info_extend')->addAll($c_data);
        return $is_success;
    }

    /**
     * @param $data
     * @param $highestRow
     * @return array
     */
    private function save_data_cevt($data, $highestRow,$pss,$objPHPExcel)
    {
        $issuer = $data[1]['A'];
        $date = $data[1]['G'];
        $function = $data[1]['H'];

        $layout = $data[1]['J']; //
        $reg = $data[1]['H'];   //
        $issue = $data[1]['R'];   //

        $page = $data[1]['S'];  //
        $change = $data[3]['A'];  //
        $part_no = $data[3]['F'];  //

        $part_name = $data[3]['H'];  //
        $drawing = $data[3]['P'];  //

        $supplier = $data[3]['R'];
        $pss = $pss;
        $type = '';

        //车间 基地 车型
        $chejian_id = I('post.chejian_id');
        $jidi_id = I('post.jidi_id');
        $category_id = I('post.category_id');

        if (empty($category_id) || empty($chejian_id) || empty($jidi_id)) {
            $this->error('请选择分类!');
        }
        $jidi = M('category')->where(['id'=>$jidi_id])->find();
        $category = M('category')->where(['id'=>$category_id])->find();
        $variant = $category['name'];
        $plant = $jidi['name'];

        if(strpos($date,'-')){
            $this->error('时间请以 年.月.日 格式！');
        }
        if (!empty($date)) {
            $date = str_ireplace('.', '-', $date);
            $date = strtotime($date);
        }else{
            $date = time();
        }

        $main_data = [
            'user_id' => $this->userInfo['id'],
            'chejian_id' => $chejian_id,
            'jidi_id' => $jidi_id,
            'category_id' => $category_id,
            'type' => $type,
            'pss' => $pss,
            'variant' => $variant,
            'plant' => $plant,
            'issuer' => $issuer,
            'date' => $date,
            'function' => $function,
            'layout' => $layout,
            'reg' => $reg,
            'issue' => $issue,
            'page' => $page,
            'change' => $change,
            'part_no' => $part_no,

            'part_name' => $part_name,
            'drawing' => $drawing,
            'supplier' => $supplier,
        ];
        if ($this->userInfo['id'] == 1) {
            $main_data['is_approved'] = 2;
        }
        for ($i = 6; $i <= $highestRow; $i++) {
            if ( strpos($data[$i]['Q'],'-') || strpos($data[$i]['R'],'-')) {
                $this->error('时间请以 年.月.日 格式！');
            }
        }
        //保存主表信息
        $info_id = M('info_cevt')->add($main_data);
        if($info_id){
            $datas = [
                'user_id' => $this->userInfo['id'],
                'operate' => 'add',
                'add_time' => time()
            ];
            $datas['content'] = '导入编号为'.$change.'的编制';
            M('operate_log')->add($datas);
        }

        for ($i = 6; $i <= $highestRow; $i++) {
            if (!empty($data[$i]['Q'])) {
                $responsibility[] = strtotime(str_ireplace('.', '-', $data[$i]['Q']));
            }else{
                $responsibility[] = '';
            }
            if (!empty($data[$i]['R'])) {
                $a_actions[] = strtotime(str_ireplace('.', '-', $data[$i]['R']));
            }else{
                $a_actions[] = '';
            }
            $no[] = $data[$i]['A'];
            $process[] = $data[$i]['E'];
            $failure[] = $data[$i]['F'];
            $effect[] = $data[$i]['G'];
            $sev[] = $data[$i]['H'];
            $cause[] = $data[$i]['I'];
            $occur[] = $data[$i]['J'];
            $class[] = $data[$i]['K'];
            $prevention[] = $data[$i]['L'];
            $detection[] = $data[$i]['M'];
            $detec[] = $data[$i]['N'];
            $rpn[] = $data[$i]['O'];
            $recommended[] = $data[$i]['P'];
            $a_sev[] = $data[$i]['S'];
            $a_occur[] = $data[$i]['T'];
            $a_detec[] = $data[$i]['U'];
            $a_rpn[] = $data[$i]['V'];
        }
        $c_data = [];
        foreach ($process as $key => $row) {
            if( !isset($no[$key]) && !isset($process[$key]) && !isset($failure[$key]) && !isset($effect[$key]) && !isset($sev[$key]) && !isset($cause[$key])
                && !isset($occur[$key]) && !isset($class[$key]) && !isset($prevention[$key]) && !isset($detection[$key])
                && !isset($detec[$key]) && !isset($rpn[$key]) && !isset($recommended[$key]) && !isset($a_sev[$key])
                && !isset($a_occur[$key]) && !isset($a_detec[$key]) && !isset($a_rpn[$key]) ){
                continue;
            }else{
                $c_data[] = [
                    'info_id' => $info_id,
                    'no' => $no[$key],
                    'process' => $process[$key],
                    'failure' => $failure[$key],
                    'effect' => $effect[$key],
                    'sev' => isset($sev[$key])?$sev[$key]:'',
                    'cause' => $cause[$key],
                    'occur' => isset($occur[$key])?$occur[$key]:'',
                    'class' => $class[$key],
                    'prevention' => $prevention[$key],
                    'detection' => $detection[$key],
                    'detec' => isset($detec[$key])?$detec[$key]:'',
                    'rpn' => isset($rpn[$key])?$rpn[$key]:'',

                    'recommended' => $recommended[$key],
                    'responsibility' => $responsibility[$key],
                    'a_actions' => $a_actions[$key],
                    'a_sev' => isset($a_sev[$key])?$a_sev[$key]:'',
                    'a_occur' => isset($a_occur[$key])?$a_occur[$key]:'',
                    'a_detec' => isset($a_detec[$key])?$a_detec[$key]:'',
                    'a_rpn' => isset($a_rpn[$key])?$a_rpn[$key]:'',
                ];
            }
        }

        $is_success[] = M('info_extend_cevt')->addAll($c_data);
        return $is_success;
    }

    //截取字符串
    public function cut_str($str,$sign,$number){
        $array=explode($sign, $str);
        $length=count($array);
        if($number<0){
            $new_array=array_reverse($array);
            $abs_number=abs($number);
            if($abs_number>$length){
                return 'error';
            }else{
                return $new_array[$abs_number-1];
            }
        }else{
            if($number>=$length){
                return 'error';
            }else{
                return $array[$number];
            }
        }
    }
}