<?php

namespace app\duxcms\model;

use app\base\model\BaseModel;

/**
 * 标签表操作.
 */
class TagsModel extends BaseModel
{
    //完成
    protected $_auto = [
        ['click', 'intval', 3, 'function'],
        ['quote', 'intval', 3, 'function'],
        ];

    /**
     * 获取列表.
     *
     * @return array 列表
     */
    public function loadList($where, $limit, $order = 'tag_id DESC')
    {
        $pageList = $this->where($where)->limit($limit)->order($order)->select();
        //处理数据类型
        $list = [];
        if (!empty($pageList)) {
            $i = 0;
            foreach ($pageList as $key=>$value) {
                $list[$key] = $value;
                $params = [
                    'name' => $value['name'],
                ];
                if (\defined('LANG_OPEN')) {
                    $params['lang'] = APP_LANG;
                }
                $list[$key]['url'] = url('duxcms/TagsContent/index', $params);
                $list[$key]['i'] = $i++;
            }
        }

        return $list;
    }

    /**
     * 获取统计
     *
     * @return int 数量
     */
    public function countList($where = [])
    {
        return  $this->where($where)->count();
    }

    /**
     * 获取信息.
     *
     * @param int $tagId ID
     *
     * @return array 信息
     */
    public function getInfo($tagId)
    {
        $map = [];
        $map['tag_id'] = $tagId;

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
        return $this->where($where)->find();
    }

    /**
     * 更新信息.
     *
     * @param string $type 更新类型
     * @param array  $data 更新数据
     *
     * @return bool 更新状态
     */
    public function saveData($type, $data)
    {
        if (!$data) {
            return false;
        }
        if ($type == 'add') {
            return $this->add($data);
        }
        if ($type == 'edit') {
            if (empty($data['tag_id'])) {
                return false;
            }
            $where = [];
            $where['tag_id'] = $data['tag_id'];
            $status = $this->where($where)->save($data);
            if ($status === false) {
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * 删除信息.
     *
     * @param int $tagId ID
     *
     * @return bool 删除状态
     */
    public function delData($tagId)
    {
        $map = [];
        $map['tag_id'] = $tagId;
        target('duxcms/TagsHas')->delData($map);

        return $this->where($map)->delete();
    }
}
