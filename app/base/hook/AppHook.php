<?php
namespace app\base\hook;

use framework\base\Hook;
use framework\base\Config;
use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;

require BASE_PATH . '/vendor/autoload.php';

class AppHook
{
    public $startTime = 0;

    public function appBegin()
    {
        // 注册异常接管
        $whoops = new Run();
        $whoops->prependHandler(new PrettyPageHandler);
        $whoops->register();

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
        // 多语言
        $config = include CONFIG_PATH . 'lang.php';
        Config::set('LANG', $config);
        if ($config['LANG_OPEN']) {

            if ($rewriteOn) {
                // 添加多语言首页伪静态规则
                $rewrite = [];
                $rewrite_rule = config('REWRITE_RULE');

                foreach($rewrite_rule as $key=>$item)
                {
                    $rewrite['<lang>/' . $key] = $item;
                }

                foreach(config('LANG.LANG_LIST') as $key => $item){
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
       
    }

    public function actionBefore($obj, $action)
    {
    }

    public function actionAfter($obj, $action)
    {
    }
}
