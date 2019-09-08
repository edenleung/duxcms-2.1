<?php

namespace app\api\controller;

use app\article\service\LabelService;

/**
 * Api调用
 * 
 */
class IndexController extends Response
{
    const APPS = ['DuxCms'];
    const LABELS = ['contentList', 'categoryList'];

    protected $app;
    protected $label;

    protected $data = [];

    public function __construct()
    {
        $data = request('get.');
        if (empty($data['app']) || empty($data['label'])) {
            return $this->error('参数异常');
        }

        if (!in_array($data['app'], self::APPS) || !in_array($data['label'], self::LABELS)) {
            return $this->error('无效参数');
        }

        $this->app = $data['app'];
        $this->label = $data['label'];
        $this->data = $data;
    }

    public function index()
    {
        $service = new LabelService;
        $result = call_user_func(array($service, $this->label), $this->data);
        return $this->success($result);
    }
}