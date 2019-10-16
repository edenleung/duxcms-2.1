<?php

namespace app\duxcms\service;

/**
 * 权限接口.
 */
class PurviewService
{
    /**
     * 获取模块权限.
     */
    public function getAdminPurview()
    {
        return [
            'AdminExpand' => [
                'name' => '扩展模型',
                'auth' => [
                    'index' => '列表',
                    'add'   => '添加',
                    'edit'  => '编辑',
                    'del'   => '删除',
                ],
            ],
            'AdminExpandField' => [
                'name' => '扩展字段',
                'auth' => [
                    'index' => '列表',
                    'add'   => '添加',
                    'edit'  => '编辑',
                    'del'   => '删除',
                ],
            ],
            'AdminForm' => [
                'name' => '表单管理',
                'auth' => [
                    'index' => '列表',
                    'add'   => '添加',
                    'edit'  => '编辑',
                    'del'   => '删除',
                ],
            ],
            'AdminFormField' => [
                'name' => '表单字段',
                'auth' => [
                    'index' => '列表',
                    'add'   => '添加',
                    'edit'  => '编辑',
                    'del'   => '删除',
                ],
            ],
            'AdminFormData' => [
                'name' => '表单数据',
                'auth' => [
                    'index' => '列表',
                    'add'   => '添加',
                    'edit'  => '编辑',
                    'del'   => '删除',
                ],
            ],
            'AdminFragment' => [
                'name' => '网站碎片',
                'auth' => [
                    'index' => '列表',
                    'add'   => '添加',
                    'edit'  => '编辑',
                    'del'   => '删除',
                ],
            ],
            'AdminPosition' => [
                'name' => '推荐位管理',
                'auth' => [
                    'index' => '列表',
                    'add'   => '添加',
                    'edit'  => '编辑',
                    'del'   => '删除',
                ],
            ],
            'AdminStatistics' => [
                'name' => '站点统计',
                'auth' => [
                    'index'  => '访客统计',
                    'spider' => '蜘蛛统计',
                ],
            ],
            'AdminSafe' => [
                'name' => '安全检测',
                'auth' => [
                    'index' => '访客统计',
                ],
            ],
            'AdminTags' => [
                'name' => 'TAG管理',
                'auth' => [
                    'index'       => '列表',
                    'batchAction' => '删除',
                ],
            ],
            'AdminTags' => [
                'name' => '系统更新',
                'auth' => [
                    'index' => '更新管理',
                ],
            ],
        ];
    }
}
