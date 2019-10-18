<?php

namespace app\page\service;

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
                'name' => '单页栏目管理',
                'auth' => [
                    'add'  => '添加',
                    'edit' => '编辑',
                    'del'  => '删除',
                ],
            ],
            'AdminSetting' => [
                'name' => '单页模块设置',
                'auth' => [
                    'index' => '设置',
                ],
            ],
        ];
    }
}
