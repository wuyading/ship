<?php
/**
 * Created by PhpStorm.
 * User: lilei
 * Date: 17-1-14
 * Time: 上午9:06
 */

namespace Home\Controller;


class TemplateController extends BaseController
{
    function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 信息库维护首页，展示一些分类列表
     */
    public function index(){
        $this->display();
    }
}