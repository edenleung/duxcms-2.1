<?php

namespace app\admin\model;

use app\base\model\BaseModel;

/**
 * 用户操作.
 */
class AdminUserModel extends BaseModel
{
    //完成
    protected $_auto = [
        ['username', 'htmlspecialchars', 3, 'function'], //用户名
        ['nicename', 'htmlspecialchars', 3, 'function'], //昵称
        ['email', 'htmlspecialchars', 3, 'function'], //邮箱
        ['password', 'md5', 1, 'function'], //新增时密码
        ['password', '', 2, 'ignore'], //编辑时密码
        ['status', 'intval', 3, 'function'], //状态
        ['reg_time', 'time', 1, 'function'], //注册时间
        ];
    //验证
    protected $_validate = [
        ['group_id', 'number', '用户组未选择', 1, 'regex', 3],
        ['username', '1,20', '用户名称只能为1~20个字符', 1, 'length', 3],
        ['username', '', '已存在相同的用户名', 1, 'unique', 3],
        ['email', 'email', '邮箱地址输入不正确', 1, 'regex', 3],
        ['email', '', '已存在相同的邮箱', 1, 'unique', 3],
        ['password', '4,250', '请输入最少4位密码', 1, 'length', 1],
    ];

    /**
     * 获取列表.
     *
     * @return array 列表
     */
    public function loadList($where = [], $limit = 0)
    {
        $data = $this->table('admin_user as A')
                ->join('{pre}admin_group as B ON A.group_id = B.group_id')
                ->field('A.*,B.name as group_name')
                ->where($where)
                ->limit($limit)
                ->select();

        return $data;
    }

    /**
     * 获取数量.
     *
     * @return int 数量
     */
    public function countList($where = [])
    {
        return $this->table('admin_user as A')
                ->join('{pre}admin_group as B ON A.group_id = B.group_id')
                ->where($where)
                ->count();
    }

    /**
     * 获取信息.
     *
     * @param int $userId ID
     *
     * @return array 信息
     */
    public function getInfo($userId = 1)
    {
        $map = [];
        $map['user_id'] = $userId;

        return $this->getWhereInfo($map);
    }

    /**
     * 获取信息.
     *
     * @param array $where 条件
     *
     * @return array 信息
     */
    public function getWhereInfo($where)
    {
        return $this->table('admin_user as A')
                ->join('{pre}admin_group as B ON A.group_id = B.group_id')
                ->field('A.*,B.status as group_status,B.name as group_name,B.base_purview,B.menu_purview')
                ->where($where)
                ->find();
    }

    /**
     * 更新信息.
     *
     * @param string $type 更新类型
     *
     * @return bool 更新状态
     */
    public function saveData($type = 'add')
    {
        $data = $this->create();
        if (!$data) {
            return false;
        }
        if ($type == 'add') {
            return $this->add();
        }
        if ($type == 'edit') {
            if (empty($data['user_id'])) {
                return false;
            }
            if (!empty($data['password'])) { //密码非空，处理密码加密
                $data['password'] = md5($data['password']);
            }
            $status = $this->save($data);
            if ($status === false) {
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * 更新权限.
     *
     * @param string $type 更新类型
     *
     * @return bool 更新状态
     */
    public function savePurviewData()
    {
        $this->_auto = [];
        $this->create();
        $this->menu_purview = serialize($this->menu_purview);
        $this->base_purview = serialize($this->base_purview);
        $status = $this->save();
        if ($status === false) {
            return false;
        }

        return true;
    }

    /**
     * 删除信息.
     *
     * @param int $userId ID
     *
     * @return bool 删除状态
     */
    public function delData($userId)
    {
        $map = [];
        $map['user_id'] = $userId;

        return $this->where($map)->delete();
    }

    /**
     * 登录用户.
     *
     * @param int $userId ID
     *
     * @return bool 登录状态
     */
    public function setLogin($userId)
    {
        // 更新登录信息
        $data = [
            'user_id'         => $userId,
            'last_login_time' => NOW_TIME,
            'last_login_ip'   => \framework\ext\Util::getIp(),
        ];
        $this->save($data);

        //设置cookie
        $auth = [
            'user_id' => $userId,
        ];
        session('admin_user', $auth);
        session('admin_user_sign', data_auth_sign($auth));

        //写入系统记录
        api('Admin', 'AdminLog', 'addLog', '登录系统');

        return true;
    }

    /**
     * 注销当前用户.
     *
     * @return void
     */
    public function logout()
    {
        session('admin_user', null);
        session('admin_user_sign', null);
    }
}
