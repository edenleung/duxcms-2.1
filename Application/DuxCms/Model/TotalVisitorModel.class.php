<?php
namespace DuxCms\Model;
use Think\Model;
/**
 * 访问统计表操作
 */
class TotalVisitorModel extends Model {

    /**
     * 获取列表
     * @return array 列表
     */
    public function loadList($where,$limit){
        return  $this->where($where)->limit($limit)->order('time desc')->select();
    }

    /**
     * 获取统计
     * @return int 数量
     */
    public function countList($where){
        return  $this->where($where)->count();
    }

    /**
     * 获取信息
     * @param int $fragmentId ID
     * @return array 信息
     */
    public function getInfo($fragmentId)
    {
        $map = array();
        $map['fragment_id'] = $fragmentId;
        return $this->getWhereInfo($map);
    }

    /**
     * 获取信息
     * @param array $where 条件
     * @return array 信息
     */
    public function getWhereInfo($where)
    {
        return $this->where($where)->find();
    }

    /**
     * 更新信息
     * @param string $type 更新类型
     * @return bool 更新状态
     */
    public function saveData($type = 'add'){
        $data = $this->create();
        if(!$data){
            return false;
        }
        if($type == 'add'){
            return $this->add();
        }
        if($type == 'edit'){
            if(empty($data['fragment_id'])){
                return false;
            }
            $status = $this->save();
            if($status === false){
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * 删除信息
     * @param int $fragmentId ID
     * @return bool 删除状态
     */
    public function delData($fragmentId)
    {
        $map = array();
        $map['fragment_id'] = $fragmentId;
        return $this->where($map)->delete();
    }

    /**
     * 生成时间数据
     * @param int $num 数量
     * @param int $type 类型
     * @return array 信息
     */
    public function getJson($num , $type = 'day', $date = 'Y-m-d'){
        $jsonArray = array();
        $jsonArray['labels'] = array();
        $datasets = $this->getChart();
        $datasets['label'] = '近期访问量';
        $timeArray = array();
        for ($i=0; $i < $num; $i++) {
            $timeNow = strtotime("-".$i." ".$type,strtotime(date('Y-m-d 0:0:0')));
            $jsonArray['labels'][] = date($date,$timeNow);
            $where = array();
            $where['time'] = array(array('EGT', $timeNow), array('LT', strtotime('+ 1 '.$type, $timeNow)),'and');
            $sum = $this->where($where)->sum('count');
            if($sum){
                $datasets['data'][] = $sum;
            }else{
                $datasets['data'][] = 0;
            }
        }
        $jsonArray['labels'] = array_reverse($jsonArray['labels']);
        $datasets['data'] = array_reverse($datasets['data']);
        $jsonArray['datasets'][] = $datasets;
        return json_encode($jsonArray);
        
    }

    /**
     * 图表样式
     * @param int $fragmentId ID
     * @return array 信息
     */
    public function getChart($type)
    {
        $style = array();
        switch ($type) {

            case 'grey':
                $style['fillColor'] = "rgba(220,220,220,0.2)";
                $style['strokeColor'] = "rgba(220,220,220,1)";
                $style['pointColor'] = "rgba(220,220,220,1)";
                $style['pointStrokeColor'] = "#fff";
                $style['pointHighlightFill'] = "#fff";
                $style['pointHighlightStroke'] = "rgba(220,220,220,1)";
                break;
            case 'green':
                $style['fillColor'] = "rgba(102,205,170,0.2)";
                $style['strokeColor'] = "rgba(102,205,170,1)";
                $style['pointColor'] = "rgba(102,205,170,1)";
                $style['pointStrokeColor'] = "#fff";
                $style['pointHighlightFill'] = "#fff";
                $style['pointHighlightStroke'] = "rgba(102,205,170,1)";
                break;
            case 'orange':
                $style['fillColor'] = "rgba(233,150,122,0.2)";
                $style['strokeColor'] = "rgba(233,150,122,1)";
                $style['pointColor'] = "rgba(233,150,122,1)";
                $style['pointStrokeColor'] = "#fff";
                $style['pointHighlightFill'] = "#fff";
                $style['pointHighlightStroke'] = "rgba(233,150,122,1)";
                break;
            case 'blue':            
            default:
                $style['fillColor'] = "rgba(151,187,205,0.2)";
                $style['strokeColor'] = "rgba(151,187,205,1)";
                $style['pointColor'] = "rgba(151,187,205,1)";
                $style['pointStrokeColor'] = "#fff";
                $style['pointHighlightFill'] = "#fff";
                $style['pointHighlightStroke'] = "rgba(151,187,205,1)";
                break;
        }
        return $style;
    }

}
