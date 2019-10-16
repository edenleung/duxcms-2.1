<?php

namespace app\base\hook;

use framework\base\Config;
use framework\base\Hook;

class AppHook
{
    public $startTime = 0;

    public function appBegin()
    {
        // 多语言
        $config = include CONFIG_PATH.'lang.php';
        Config::set('LANG', $config);
        if ($config['LANG_OPEN']) {
            $file = CONFIG_PATH.'performance.php';
            $data = load_config($file);
            $rewrite = $data['REWRITE_ON'] ? true : false;
            if ($rewrite) {
                // 添加多语言首页伪静态规则
                $rewrite = [];
                $rewrite_rule = config('REWRITE_RULE');

                foreach ($rewrite_rule as $key=>$item) {
                    $rewrite['<lang>/'.$key] = $item;
                }

                foreach (config('LANG.LANG_LIST') as $key => $item) {
                    $rewrite[$key] = 'home/index/index';
                }

                config('REWRITE_RULE', $rewrite);
            }

            // 检查是否开启多语言
            if (!defined('ADMIN_STATUS')) {
                Hook::listen('CheckLang');
            } else {
                Hook::listen('CheckAdminLang');
            }
        }

        $this->startTime = microtime(true);
    }

    public function appEnd()
    {
        //echo microtime(true) - $this->startTime ;
    }

    public function appError($e)
    {
        if (404 == $e->getCode()) {
            $action = 'error404';
        } else {
            $action = 'error';
        }
        obj('app\base\controller\ErrorController')->$action($e);
    }

    public function routeParseUrl($rewriteRule, $rewriteOn)
    {
    }

    public function actionBefore($obj, $action)
    {
    }

    public function actionAfter($obj, $action)
    {
    }
}
