<?php

namespace app\admin\service;

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
            'Setting' => [
                'name' => '系统设置',
                'auth' => [
                    'site'        => '站点设置',
                    'mobile'      => '手机设置',
                    'tpl'         => '模板设置',
                    'performance' => '性能设置',
                    'upload'      => '上传设置',
                ],
            ],
            'Manage' => [
                'name' => '系统管理',
                'auth' => [
                    'cache' => '缓存管理',
                ],
            ],
            'Functions' => [
                'name' => '应用管理',
                'auth' => [
                    'index'     => '浏览',
                    'state'     => '状态更改',
                    'install'   => '安装',
                    'uninstall' => '卸载',
                ],
            ],
            'AdminBackup' => [
                'name' => '备份管理',
                'auth' => [
                    'index'  => '浏览',
                    'add'    => '添加',
                    'import' => '导入',
                    'del'    => '删除',
                ],
            ],
            'AdminLog' => [
                'name' => '系统日志',
                'auth' => [
                    'index' => '浏览',
                ],
            ],
            'AdminUser' => [
                'name' => '用户管理',
                'auth' => [
                    'index' => '列表',
                    'add'   => '添加',
                    'edit'  => '编辑',
                    'del'   => '删除',
                ],
            ],
            'AdminUserGroup' => [
                'name' => '用户组管理',
                'auth' => [
                    'index' => '列表',
                    'add'   => '添加',
                    'edit'  => '编辑',
                    'del'   => '删除',
                ],
            ],
        ];
    }
}
