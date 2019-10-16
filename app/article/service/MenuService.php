<?php

namespace app\article\service;

/**
 * 后台菜单接口.
 */
class MenuService
{
    /**
     * 获取菜单结构.
     */
    public function getAdminMenu()
    {
        return [
            'Content' => [
                'menu' => [
                    [
                        'name'  => '文章管理',
                        'icon'  => 'reorder',
                        'url'   => url('article/AdminContent/index'),
                        'order' => 1,
                    ],
                ],
            ],
        ];
    }
}
