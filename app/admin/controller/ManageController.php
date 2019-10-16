<?php

namespace app\admin\controller;

/**
 * 网站管理.
 */
class ManageController extends AdminController
{
    /**
     * 当前模块参数.
     */
    protected function _infoModule()
    {
        return [
            'info'  => [
                'name'        => '网站管理',
                'description' => '管理网站基本功能',
                ],
            'menu' => [
                    [
                        'name' => '缓存管理',
                        'url'  => url('cache'),
                        'icon' => 'exclamation-circle',
                    ],
                ],
            ];
    }

    /**
     * 站点设置.
     */
    public function cache()
    {
        if (!IS_POST) {
            $breadCrumb = ['缓存管理'=>url()];
            $this->assign('breadCrumb', $breadCrumb);
            $this->assign('list', target('Manage')->getCacheList());
            $this->adminDisplay();
        } else {
            $key = request('post.data');
            if (empty($key)) {
                $this->error('没有获取到清除动作！');
            }
            if (target('Manage')->delCache($key)) {
                $this->success('缓存清空成功！');
            } else {
                $this->error('缓存清空失败！');
            }
        }
    }
}
