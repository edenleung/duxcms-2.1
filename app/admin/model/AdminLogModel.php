<?php

namespace app\admin\model;

use app\base\model\BaseModel;

/**
 * 操作记录.
 */
class AdminLogModel extends BaseModel
{
    //完成
    protected $_auto = [
        ['time', 'time', 1, 'function'],
        ['ip', 'get_client_ip', 1, 'function'],
        ['app', APP_NAME, 1, 'string'],
        ['user_id', 'get_admin_id', 1, 'function'],
        ];

    /**
     * 获取列表.
     *
     * @return array 列表
     */
    public function loadList($where = [], $limit = 0)
    {
        $data = $this->table('admin_log as A')
                ->join('{pre}admin_user as B ON A.user_id = B.user_id')
                ->field('A.*,B.username')
                ->where($where)
                ->limit($limit)
                ->order('A.log_id desc')
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
        return $this->table('admin_log as A')
                ->join('{pre}admin_user as B ON A.user_id = B.user_id')
                ->where($where)
                ->count();
    }

    /**
     * 添加信息.
     *
     * @param string $log 增加数据
     *
     * @return bool 更新状态
     */
    public function addData($log)
    {
        $data = [];
        $data['content'] = $log;
        if (empty($data)) {
            return false;
        }
        //只保留500条数据
        $count = $this->countList();
        if ($count > 500) {
            $this->order('log_id asc')->limit('1')->delete();
        }
        //增加记录
        $data = $this->create($data);

        return $this->add($data);
    }
}
