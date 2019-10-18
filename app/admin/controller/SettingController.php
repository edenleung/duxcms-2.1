<?php

namespace app\admin\controller;

/**
 * 网站设置.
 */
class SettingController extends AdminController
{
    /**
     * 当前模块参数.
     */
    protected function _infoModule()
    {
        return [
            'info'  => [
                'name'        => '网站设置',
                'description' => '设置网站整体功能',
                ],
            'menu' => [
                    [
                        'name' => '站点信息',
                        'url'  => url('Setting/site'),
                        'icon' => 'exclamation-circle',
                    ],
                    [
                        'name' => '手机设置',
                        'url'  => url('Setting/mobile'),
                        'icon' => 'mobile',
                    ],
                    [
                        'name' => '多语言设置',
                        'url'  => url('Setting/lang'),
                        'icon' => 'dashboard',
                    ],
                    [
                        'name' => '模板设置',
                        'url'  => url('Setting/tpl'),
                        'icon' => 'eye',
                    ],
                    [
                        'name' => '性能设置',
                        'url'  => url('Setting/performance'),
                        'icon' => 'dashboard',
                    ],
                    [
                        'name' => '上传设置',
                        'url'  => url('Setting/upload'),
                        'icon' => 'upload',
                    ],
                    [
                        'name' => '百度链接提交',
                        'url'  => url('Setting/push'),
                        'icon' => 'upload',
                    ],
                    [
                        'name' => 'API设置',
                        'url'  => url('Setting/api'),
                        'icon' => 'upload',
                    ],
                ],
        ];
    }

    /**
     * 站点设置.
     */
    public function site()
    {
        if (!IS_POST) {
            $breadCrumb = ['站点信息'=>url()];
            $this->assign('breadCrumb', $breadCrumb);
            if (defined('LANG_OPEN')) {
                $file = CONFIG_PATH.'/lang/'.APP_LANG.'.php';
                $config = load_config($file);
            } else {
                $config = target('Config')->getInfo();
            }
            $this->assign('info', $config);
            $this->adminDisplay();
        } else {
            if (defined('LANG_OPEN')) {
                $file = CONFIG_PATH.'/lang/'.APP_LANG.'.php';

                if (save_config($file, $_POST)) {
                    $this->success('站点配置成功！');
                } else {
                    $this->error('站点配置失败');
                }
            } else {
                if (target('Config')->saveData()) {
                    $this->success('站点配置成功！');
                } else {
                    $this->error('站点配置失败');
                }
            }
        }
    }

    /**
     * 手机设置.
     */
    public function mobile()
    {
        if (!IS_POST) {
            $breadCrumb = ['模板设置'=>url()];
            $this->assign('breadCrumb', $breadCrumb);
            $this->assign('themesList', target('Config')->themesList());
            $this->assign('tplList', target('Config')->tplList());
            $this->assign('info', target('Config')->getInfo());
            $this->adminDisplay();
        } else {
            if (target('Config')->saveData()) {
                $this->success('模板配置成功！');
            } else {
                $this->error('模板配置失败');
            }
        }
    }

    /**
     * 模板设置.
     */
    public function tpl()
    {
        if (!IS_POST) {
            $breadCrumb = ['模板设置'=>url()];
            $this->assign('breadCrumb', $breadCrumb);
            $this->assign('themesList', target('Config')->themesList());
            $this->assign('tplList', target('Config')->tplList());
            $this->assign('info', target('Config')->getInfo());
            $this->adminDisplay();
        } else {
            if (target('Config')->saveData()) {
                $this->success('模板配置成功！');
            } else {
                $this->error('模板配置失败');
            }
        }
    }

    /**
     * 性能设置.
     */
    public function performance()
    {
        $file = CONFIG_PATH.'performance.php';
        if (!IS_POST) {
            $breadCrumb = ['性能设置'=>url()];
            $this->assign('breadCrumb', $breadCrumb);
            $this->assign('info', load_config($file));
            $this->adminDisplay();
        } else {
            if (save_config($file, $_POST)) {
                $this->success('性能配置成功！');
            } else {
                $this->error('性能配置失败');
            }
        }
    }

    /**
     * 上传设置.
     */
    public function upload()
    {
        $file = CONFIG_PATH.'upload.php';
        if (!IS_POST) {
            $breadCrumb = ['上传设置'=>url()];
            $this->assign('breadCrumb', $breadCrumb);
            $this->assign('info', load_config($file));
            $this->adminDisplay();
        } else {
            if (save_config($file, $_POST)) {
                $this->success('上传配置成功！');
            } else {
                $this->error('上传配置失败');
            }
        }
    }

    /**
     * 多语言配置.
     *
     * @return void
     */
    public function lang()
    {
        $file = CONFIG_PATH.'lang.php';
        if (!IS_POST) {
            $this->assign('info', load_config($file));
            $this->adminDisplay();
        } else {
            if (save_config($file, $_POST)) {
                if (0 == request('post.LANG_OPEN')) {
                    cookie('APP_LANG', null);
                }
                $this->success('上传配置成功！');
            } else {
                $this->error('上传配置失败');
            }
        }
    }

    /**
     * 百度推送
     *
     * @return void
     */
    public function push()
    {
        $file = CONFIG_PATH.'push.php';
        if (!IS_POST) {
            $this->assign('info', load_config($file));
            $this->adminDisplay();
        } else {
            if (save_config($file, $_POST)) {
                $this->success('上传配置成功！');
            } else {
                $this->error('上传配置失败');
            }
        }
    }

    /**
     * API 设置.
     *
     * @return void
     */
    public function api()
    {
        $file = CONFIG_PATH.'api.php';
        if (!IS_POST) {
            $this->assign('info', load_config($file));
            $this->adminDisplay();
        } else {
            if ($_POST['OPEN'] && empty($_POST['TOKEN'])) {
                $this->error('请生成token再保存！');
            }
            if (save_config($file, $_POST)) {
                $this->success('上传配置成功！');
            } else {
                $this->error('上传配置失败');
            }
        }
    }
}
