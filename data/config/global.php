<?php

$dir = dirname(__FILE__);
$files = ['performance.php', 'rewrite.php', 'upload.php', 'ver.php'];
$db = include $dir.'/db.php';
$config = [
    //默认模块
    'DEFAULT_APP'        => 'home',
    'DEFAULT_CONTROLLER' => 'Index',
    'DEFAULT_ACTION'     => 'index',
    'ERROR_URL'          => '', //出错跳转地址
    'URL_BASE'           => '', //设置网址域名
    //模板设置
    'TPL'=> [
        'TPL_DEPR' => '/',
    ],
    //数据库
    'DB'=> [
        'default' => $db,
    ],
    'CACHE' => [
        'default' => [
            'CACHE_TYPE' => 'FileCache',
            'CACHE_PATH' => ROOT_PATH.'data/cache/',
            'GROUP'      => 'db',
            'HASH_DEEP'  => 0,
        ],
    ],
    // 登录页面 每日一图 背景图
    'loginBackgroudImage' => true,
];
foreach ($files as $value) {
    $array = include $dir.'/'.$value;
    $config = array_merge($config, $array);
}
if (defined('ADMIN_STATUS')) {
    $admin = include $dir.'/admin.php';
    $config = array_merge($config, $admin);
}

return $config;
