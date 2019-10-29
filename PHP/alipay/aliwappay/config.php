<?php

return [
   // 默认输出类型
   'default_return_type'    => 'html',

   'bird_db' => [
        // 数据库类型
        'type'            => 'mysql',
        // 服务器地址
        'hostname'        => '127.0.0.1',
        // 数据库名
        'database'        => 'weixitest',
        // 用户名
        'username'        => 'root',
        // 密码
        'password'        => '123456',
        // 端口
        'hostport'        => '',
        // 连接dsn
        'dsn'             => '',
        // 数据库连接参数
        'params'          => [],
        // 数据库编码默认采用utf8
        'charset'         => 'utf8',
   ],

  'aliwapPay' => [
    //应用ID
    'app_id'                    => '20180********81',
    //签名方式
    'sign_type'                 => 'RSA2',// RSA  RSA2

    // ！！！注意：如果是文件方式，文件中只保留字符串，不要留下 -----BEGIN PUBLIC KEY----- 这种标记
    // 可以填写文件路径，或者密钥字符串  当前字符串是 rsa2 的支付宝公钥(开放平台获取)
    'alipay_public_key'            => '-----****你的支付宝公钥****------'
    // ！！！注意：如果是文件方式，文件中只保留字符串，不要留下 -----BEGIN RSA PRIVATE KEY----- 这种标记
    'merchant_private_key'           => '-----------**你的支付宝私钥**----------'
    // 异步通知地址
    'notify_url' => 'https://***.***.com/aliwapnotify',

    //编码格式
    'charset' => 'UTF-8',

    //支付宝网关
    'gatewayUrl' => 'https://openapi.alipay.com/gateway.do',

    //同步跳转
    'return_url' => ''
  ]
  
];
