<?php

namespace app\duxcms\controller;

use app\admin\controller\AdminController;

/**
 * 站点统计
 */
class AdminStatisticsController extends AdminController
{
    /**
     * 当前模块参数.
     */
    protected function _infoModule()
    {
        return [
            'info'  => [
                'name'        => '站点统计',
                'description' => '网站综合统计信息',
                ],
            'menu' => [
                    [
                        'name' => '访问统计',
                        'url'  => url('index'),
                        'icon' => 'list',
                    ],
                    [
                        'name' => '蜘蛛统计',
                        'url'  => url('spider'),
                        'icon' => 'list',
                    ],
                ],
            ];
    }

    /**
     * 访问统计
     */
    public function index()
    {
        $breadCrumb = ['访问统计'=>url()];
        $this->assign('breadCrumb', $breadCrumb);
        $this->assign('jsonArray1', target('TotalVisitor')->getJson(7, 'day', 'm-d'));
        $this->assign('jsonArray2', target('TotalVisitor')->getJson(30, 'day', 'm-d'));
        $this->assign('jsonArray3', target('TotalVisitor')->getJson(12, 'month', 'Y-m'));
        $this->adminDisplay();
    }

    /**
     * 蜘蛛统计
     */
    public function spider()
    {
        $breadCrumb = ['蜘蛛统计'=>url()];
        $this->assign('breadCrumb', $breadCrumb);
        $this->assign('jsonArray1', target('TotalSpider')->getJson(7, 'day', 'm-d'));
        $this->assign('jsonArray2', target('TotalSpider')->getJson(30, 'day', 'm-d'));
        $this->assign('jsonArray3', target('TotalSpider')->getJson(12, 'month', 'Y-m'));
        $this->adminDisplay();
    }
}
