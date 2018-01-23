<?php
namespace Home\Controller;

use Think\Model;

class SqlController extends BaseController {

    public function _initialize(){
        parent::_initialize();
    }

    public function sql_exec(){
        $arr = [
            'ALTER TABLE `pfmea`.`info` ADD COLUMN `user_id` INT UNSIGNED NOT NULL COMMENT \'用户id\' AFTER `id`',
            'update info set user_id = 1 where id >=1',
            'ALTER TABLE `pfmea`.`info` ADD COLUMN `is_delete` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT \'默认 1 正常显示 2 删除\' AFTER `remake`',
            'ALTER TABLE info ADD COLUMN is_approve TINYINT UNSIGNED NOT NULL COMMENT \'是否审批 1未审批 2 已审批 3 草稿箱\'',
            'ALTER TABLE info ADD COLUMN remake text COMMENT \'审批备注\'',
        ];

        foreach ($arr as $row){
            $model = new Model();
            $model->execute($row);
        }
    }

    /**
     * 删除缓存
     */
    public function del_cache(){
        delFile(RUNTIME_PATH);
        echo 'success';
        echo '<pre>';
        print_r(get_loaded_extensions());
    }
}