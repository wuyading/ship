<?php
namespace Home\Controller;

class ApiController extends BaseController {

    public function _initialize(){
        parent::_initialize();
    }

    /**
     * 获取子分类的列表
     */
    public function get_category(){
        $category_id = I('get.category_id','','int');
        if(empty($category_id)){
            return ;
        }

        $result = M('category')->where(['parent_id'=>$category_id])->select();
        echo messageFormat(1001,['data'=>$result]);
    }
}