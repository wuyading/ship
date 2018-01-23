<?php
namespace Home\Controller;

class FileController extends BaseController {
    public function __construct()
    {
        parent::__construct();
        $this->isLogin();
    }

    /**
     * 上传
     */
    public function upload(){
        set_time_limit(0);//设置不超时

        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     31457280 ;// 设置附件上传大小
        $upload->exts      =     array('xls','xlsx','xlsm');// 设置附件上传类型
        $upload->rootPath  =     RUNTIME_PATH.'/Excel/'; // 设置附件上传根目录
        $upload->savePath  =     ''; // 设置附件上传（子）目录

        //文件夹不存在创建文件夹
        if(!file_exists($upload->rootPath) && !is_dir($upload->rootPath)){
            mkdir($upload->rootPath,0777,true);
        }

        // 上传文件
        $info   =   $upload->upload();//var_dump($info);die;
        if(!$info) {// 上传错误提示错误信息
            echo $json = json_encode(['status'=>2001,'message'=>$upload->getError(),'data'=>'']);
        }else{// 上传成功
            $file = $info['file']['savepath'] . $info['file']['savename'];
            echo $json = json_encode(['status'=>1001,'message'=>'success','data'=>$upload->rootPath.$file]);
        }
    }

    /**
     * 下载文件
     */
    public function download(){
        $file = I('get.file');
        if(empty($file)){
            return;
        }

        $file_path = ROOT_PATH.DIRECTORY_SEPARATOR.$file;
        downloadFile($file_path);
    }
}