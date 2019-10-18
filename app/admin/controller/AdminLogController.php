<?php

namespace app\admin\controller;

/**
 * 后台操作记录.
 */
class AdminLogController extends AdminController
{
    /**
     * 当前模块参数.
     */
    protected function _infoModule()
    {
        return [
            'info'  => [
                'name'        => '安全记录',
                'description' => '查询网站操作记录',
                ],
            'menu' => [
                    [
                        'name' => '记录列表',
                        'url'  => url('index'),
                        'icon' => 'list',
                    ],
                ],
            ];
    }

    /**
     * 列表.
     */
    public function index()
    {
        //筛选条件
        $where = [];
        $keyword = request('request.keyword', '');
        if (!empty($keyword)) {
            $where['B.username'] = $keyword;
        }
        //URL参数
        $pageMaps = [];
        $pageMaps['keyword'] = $keyword;
        //查询数据
        $list = target('AdminLog')->page(20)->loadList($where, $limit);
        $this->pager = target('AdminLog')->pager;
        //位置导航
        $breadCrumb = ['安全记录'=>url()];
        //模板传值
        $this->assign('breadCrumb', $breadCrumb);
        $this->assign('list', $list);
        $this->assign('page', $this->getPageShow($pageMaps));
        $this->assign('keyword', $keyword);
        $this->adminDisplay();
    }
}
