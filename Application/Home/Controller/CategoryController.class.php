<?php
namespace Home\Controller;

class CategoryController extends BaseController
{

    public function _initialize()
    {
        parent::_initialize();
    }

    //信息分类管理
    public function index()
    {
        $this->isLogin();

        //获取所有分类
        $category_arr = M('category')->where('parent_id = 0')->select();
        foreach ($category_arr as $key => $row) {
            $category_arr[$key]['children'] = $this->get_sub_category($row['id']);
        }
//        dump($category_arr);die;

        $this->assign('category', $category_arr);

        $this->display();
    }

    public function ajax_save()
    {
        $update_id = I('post.update_id', '', 'int');
        $category_id = I('post.category_id', '', 'int');
        $category_name = I('post.category_name');

        if ($category_id !== 0 && empty($category_id)) {
            die(messageFormat(2001, '请选择分类！'));
        }
        if (empty($category_name)) {
            die(messageFormat(2001, '分类名称不能为空！'));
        }

        if ($update_id) { //修改分类
            $is_success = M('category')->where(['id' => $update_id])->save(['parent_id' => $category_id, 'name' => $category_name]);
        } else { //添加分类
            $is_success = M('category')->add(['name' => $category_name, 'parent_id' => $category_id]);
        }

        if ($is_success) {
            die(messageFormat(1001, '保存成功！'));
        } else {
            die(messageFormat(2001, '保存失败！'));
        }
    }


    /**
     * @param $id
     * @return array|bool
     * 递归循环，获取子分类
     */
    private function get_sub_category($id)
    {
        $cat = $this->get_category($id);
        if (empty($cat)) {
            return false;
        } else {
            foreach ($cat as $key => $value) {
                $cat[$key]['children'] = $this->get_sub_category($value['id']);
            }
        }
        return $cat;
    }

    /**
     * @param string $category_id 父类id
     * @return mixed
     * 获取某个根节点的所有子节点
     */
    function get_category($category_id = '')
    {
        $category_arr = M('category')->where('parent_id = ' . $category_id)->select();
        return $category_arr;
    }

    //删除分类
    public function ajax_delete_cate()
    {
        $id = I('post.id', '', 'int');
        if($id){
            $info = $this->get_category($id);
            if($info){
                die(messageFormat(2001, '该分类下有子类，请先删除子类！'));
            }else{
                $is_success = M('category')->where(['id' => $id])->delete();
                if ($is_success) {
                    die(messageFormat(1001, '删除成功！'));
                } else {
                    die(messageFormat(2001, '删除失败，请重试！'));
                }
            }
        }else{
            die(messageFormat(2001, '参数错误！'));
        }
    }
}