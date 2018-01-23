<?php
$arr_1 = array(
    //SESSION统一定义
    'session_name' => array(
        'user_login_info' => 'user_login_info',//会员
    ),
    'session_id' => array(
        'cookie_name' => 'zn_sessid',
        'expire' => time() + 3600,
        'cookie_time' => 3600*24*30,
    ),
);
return $arr_1;
