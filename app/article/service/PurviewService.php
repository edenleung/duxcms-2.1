<?php

namespace app\article\service;

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
            'AdminCategory' => [
                'name' => '文章栏目管理',
                'auth' => [
                    'add'  => '添加',
                    'edit' => '编辑',
                    'del'  => '删除',
                ],
            ],
            'AdminContent' => [
                'name' => '文章管理',
                'auth' => [
                    'index'       => '列表',
                    'add'         => '添加',
                    'edit'        => '编辑',
                    'del'         => '删除',
                    'batchAction' => '批量操作',
                ],
            ],
            'AdminSetting' => [
                'name' => '文章模块设置',
                'auth' => [
                    'index' => '设置',
                ],
            ],
        ];
    }
}
