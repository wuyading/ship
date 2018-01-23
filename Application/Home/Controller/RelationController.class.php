<?php
namespace Home\Controller;

/**
 * 人员关系
 *
 * Class RelationController
 * @package Home\Controller
 */

class RelationController extends BaseController
{
    function _initialize()
    {
        parent::_initialize();
        $this->isLogin();
    }

    /**
     * 列表
     */
    public function index()
    {
        //获取所有分类
        $category_arr = M('user_relations')->field('user_relations.id,user_relations.user_id,user_relations.parent_id as pId,user.username as name,user_relations.level')->
        join('user on user.id = user_relations.user_id')->select();

        foreach ($category_arr as $key => $row) {
            if($row['user_id'] == 1){
                $category_arr[$key]['name'] = '顶级分类';
            }
            $category_arr[$key]['pId'] = $category_arr[$key]['pid'];
            if ($row['level'] <= 3) {
                $category_arr[$key]['open'] = true;
            }
            unset($category_arr[$key]['pid']);
        }
        $this->assign('category_json', json_encode($category_arr));

        //获取用户列表信息
        $lists = M('user')->select();
        $this->assign('user_list', $lists);

        $this->display();
    }

    /**
     * 保存节点信息
     */
    public function ajax_save_add()
    {
        $category_id = I('post.pid', '', 'int');
        $level = I('post.level');
        $users = I('post.users');

        if ($category_id !== 0 && empty($category_id)) {
            die(messageFormat(2001, '请选择分类！'));
        }

        if (empty($users)) {
            die(messageFormat(2001, '请选择用户！'));
        }

        $is_success = false;
        $exist_user = [];
        $insert_user = [];
        foreach ($users as $row) {
            $arr = explode('_', $row);
            if (isset($arr[1]) && intval($arr[1])) {
//                $is_exist = M('user_relations')->where(['user_id' => $arr[1]])->count();
//                if ($is_exist) {
//                    $exist_user[] = $arr[0];
//                } else {
                    //获取用户信息，判断该用户是否存在
                    $user_info = M('user')->where(['id'=>$arr[1]])->find();
                    if($user_info){
                        $data = [
                            'user_id' => $user_info['id'],
                            'parent_id' => $category_id,
                            'level' => $level + 1,
                            'add_time' => time(),
                        ];
                        $is_success = M('user_relations')->add($data);

                        $name = $user_info['username'];
                        $insert_user[] = ['id' => $is_success, 'name' => $name];
                    }else{
                        die(messageFormat(2001, '保存失败,该用户不存在！ ' ));
                    }
//                }
            }
        }

        $remark = !empty($exist_user) ? implode(',', $exist_user) . '已经存在了' : '';
        if ($is_success) {
            die(messageFormat(1001, '保存成功！ ' . $remark, $insert_user));
        } else {
            die(messageFormat(2001, '保存失败！ ' . $remark));
        }
    }

    /**
     * 更新节点信息
     */
    public function ajax_save_update()
    {
        $update_id = I('post.id', '', 'int');
        $category_name = I('post.name');

        if (empty($update_id)) {
            die(messageFormat(2001, '请选择分类！'));
        }
        if (empty($category_name)) {
            die(messageFormat(2001, '分类名称不能为空！'));
        }

        $data = [
            'name' => $category_name,
        ];
        $is_success = M('relation')->where(['id' => $update_id])->save($data);

        if ($is_success) {
            die(messageFormat(1001, '保存成功！'));
        } else {
            die(messageFormat(2001, '保存失败，请重试！'));
        }
    }

    /**
     * 删除节点
     */
    public function ajax_delete()
    {
        $id = I('post.id', '', 'int');

        if (empty($id)) {
            die(messageFormat(2001, '请选择分类！'));
        }

        //先判断是否存在子分类
        $is_exist = M('user_relations')->where(['parent_id' => $id])->count();
        if ($is_exist) {
            die(messageFormat(2001, '存在子分类，不允许删除！'));
        }

        $is_success = M('user_relations')->where(['id' => $id])->delete();
        if ($is_success) {
            die(messageFormat(1001, '删除成功！'));
        } else {
            die(messageFormat(2001, '删除失败，请重试！'));
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
        $category_arr = M('relation')->where('parent_id = ' . $category_id)->select();
        return $category_arr;
    }
}