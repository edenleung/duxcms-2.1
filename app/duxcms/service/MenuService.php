<?php

namespace app\duxcms\service;

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
        //获取表单列表
        $formList = target('duxcms/FieldsetForm')->loadList();
        $formMenu = [];
        if (!empty($formList)) {
            foreach ($formList as $key => $value) {
                $formMenu[] = [
                    'name'  => $value['name'],
                    'url'   => url('duxcms/AdminFormData/index', ['fieldset_id'=>$value['fieldset_id']]),
                    'order' => $key,
                    ];
            }
        }
        //返回菜单
        return [
            'Content' => [
                'name'  => '内容',
                'icon'  => 'bars',
                'order' => 1,
                'menu'  => [
                    [
                        'name'  => '栏目管理',
                        'icon'  => 'sitemap',
                        'url'   => url('duxcms/AdminCategory/index'),
                        'order' => 0,
                    ],
                ],
            ],
            'Form' => [
                'name'  => '表单',
                'icon'  => 'file-text-o',
                'order' => 2,
                'menu'  => $formMenu,
            ],
            'index' => [
                'menu' => [
                    [
                        'name'  => '站点统计',
                        'icon'  => 'tachometer',
                        'url'   => url('duxcms/AdminStatistics/index'),
                        'order' => 1,
                    ],
                    [
                        'name'  => '安全中心',
                        'icon'  => 'shield',
                        'url'   => url('duxcms/AdminSafe/index'),
                        'order' => 2,
                    ],
                    [
                        'name'  => '系统更新',
                        'icon'  => 'bug',
                        'url'   => url('duxcms/AdminUpdate/index'),
                        'order' => 3,
                    ],
                    [
                        'name'  => 'sitemap生成',
                        'icon'  => 'file-code-o',
                        'url'   => url('duxcms/Sitemap/sitemap'),
                        'order' => 4,
                    ],
                ],
            ],
            'Function' => [
                'name'  => '功能',
                'icon'  => 'wrench',
                'order' => 3,
                'menu'  => [
                    [
                        'name'  => '碎片管理',
                        'icon'  => 'leaf',
                        'url'   => url('duxcms/AdminFragment/index'),
                        'order' => 1,
                    ],
                    [
                        'name'  => '推荐位管理',
                        'icon'  => 'crosshairs',
                        'url'   => url('duxcms/AdminPosition/index'),
                        'order' => 2,
                    ],
                    [
                        'name'  => '扩展模型管理',
                        'icon'  => 'puzzle-piece',
                        'url'   => url('duxcms/AdminExpand/index'),
                        'order' => 3,
                    ],
                    [
                        'name'  => '表单管理',
                        'icon'  => 'file-text-o',
                        'url'   => url('duxcms/AdminForm/index'),
                        'order' => 4,
                    ],
                    [
                        'name'  => 'TAG管理',
                        'icon'  => 'tags',
                        'url'   => url('duxcms/AdminTags/index'),
                        'order' => 5,
                    ],
                ],
            ],
        ];
    }
}
