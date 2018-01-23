<?php
return array(
	//'配置项'=>'配置值'

    'TEL_400' => '4008-791-799', //400电话
    //HOST路径
    'HOST'            => array(
       // 'THIS_HOST'     => 'http://www.zhuniu30.com',
        'IMG_HOST'      => 'http://file001.zhuniu.com/',
    ),
    //图片上传地址
    'UPLOAD_PATH'     => array(
        'APPROVE'        => 'imageupload', //认证图片
    ),
    //编辑器加载
    'TAGLIB_PRE_LOAD' => 'ViewTag',//自动加载tag
    'LOAD_EXT_CONFIG' => 'param,route',

    /**
     * 单点登录，允许用户登录的网址
     */
    'LOGIN_SITE_URL' => [
        'zhuniu.com',
        'www.zhuniu.com',
        'lc.zhuniu.com',
        'huizhan.zhuniu.com',
        'debug.zhuniu.com',

        //测试环境的域名
        'www.zhuniu30.com',
        'debug.zhuniu30.com',
        'www.zhuniu30.me',
        'debug.zhuniu30.me',
        'lc.zhuniu30.com',
        'test.zhuniu30.com',
        'localhost.zhuniu30.com',
    ],
);