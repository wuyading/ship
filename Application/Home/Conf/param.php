<?php
$arr_1 = array(
//发送信息模板
    'MSG_MODEL'       => array(
        'REGISTER'                  => '您好，欢迎注册筑牛网帐号，您的验证码为$code$，请勿将验证码提供给他人。【筑牛网】', //注册时的验证码
        'FINDPWD'                   => '您正找回密码，验证码为$code$，请勿将验证码提供给他人。【筑牛网】', //找回密码的验证码
        'sendcode_for_changemobile' => '您正在修改联系手机号，验证码为$code$，请勿将验证码提供给他人。【筑牛网】', //修改手机号验证码
        'sendcode_for_addbankcard'  => '您正要添加一张新的银行卡，验证码为$code$，请勿将验证码提供给他人。【筑牛网】', //添加新银行卡
        'PAY'                       => '您好,您正在进行支付操作，支付验证码为$code$，请勿将验证码提供给他人。【筑牛网】',
        'code'                      => '您好,您本次操作的验证码为$code$，请勿将验证码提供给他人。【筑牛网】',
        'CHOOSEN'                   => '尊敬的 $power$ 用户，恭喜您被选为名称为 “$enqInfo$” 采购单的 $offertype$，请及时登陆平台查看。【筑牛网】',
        'NEW_MOBILE'               => '您的新手机号验证码为$code$，请勿将验证码提供给他人。【筑牛网】',
        'SPEED_SUCCESS'            => '您的极速采购单已发布成功，请登陆筑牛网查看供应商报价。登录名：$code$，默认密码：$pwd$。（为了您的账号安全，登陆后请及时修改密码）【筑牛网】',
        'SET_PAY_PASS'             => '您好,您正在进行支付密码设置操作，验证码为$code$，请勿将验证码提供给他人。【筑牛网】',
        'DRAW_DOWM'             => '您好,您正在进行金额转出操作，验证码为$code$，请勿将验证码提供给他人。【筑牛网】',
        'YAOQING_BAOJIA'        => '尊敬的先生/女士，您好！你的采购商：$companyName$，邀请您对采购单“$purName$”进行报价，查看详情请登录筑牛网。【筑牛网】',
        'insurance'             => '您好，您正在申请投保操作，验证码为$code$，请勿将验证码提供给他人。【筑牛网】',
    ),
//短信验证码保存session
    'SESSION_ARRAY'   => array(
        'SESSION_TIME'    => 1200, //发送短信保存Redis时间
        'SESSION_REGISTER_MSGCODE'    => 'session_register_msgcode', //短信验证码保存到session中
        'session_findpwdcode_msgcode' => 'session_findpwdcode_msgcode', //找回密码验证码保存到session中
        'set_Change_MobileCode'       => 'set_Change_MobileCode', //修改手机号验证码保存到session中
        'set_add_BankCode'            => 'set_add_BankCode', //添加新银行卡验证码保存到session中
        'set_new_MobileCode'       => 'set_new_MobileCode', //保存新手机验证码到session中
    ),

    //筑牛nav
    'NAV_ARRAY' => array(
            'uc_base' => array('url'=> 'usercenter/index','name'=>'我的筑牛'),

    )
);

$cache = array(
    'HTML_CACHE_ON'    => HTML_CACHE_ON,
    'HTML_CACHE_TIME'  => 7200,
    //'HTML_FILE_SUFFIX' => '.html',
    'HTML_CACHE_RULES' => array(),
);
return array_merge($arr_1, $cache);
