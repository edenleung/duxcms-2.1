<?php

namespace app\api\controller;

use app\article\service\LabelService as ArticleService;
use app\duxcms\service\LabelService as DuxService;

/**
 * Api调用
 * 
 */
class IndexController extends Response
{
    const APPS = ['DuxCms', 'Article'];
    const LABELS = ['contentList', 'categoryList', 'tagsList', 'formList'];

    protected $app;
    protected $label;

    protected $data = [];

    protected $config = [];

    protected $request;

    public function __construct()
    {
        $file = CONFIG_PATH.'api.php';
        $this->config = load_config($file);

        if (!$this->config['OPEN']) {
            return $this->error('此站未开启API调用功能');
        }

        $this->request = request('get.');
        
        if (!$this->checkSign()) {
            return $this->error('签名错误');
        }

        if ((time() - $this->request['timestamp']) > 5) {
            return $this->error('签名已过期');
        }
        
        $this->checkParams();
    }

    protected function checkParams()
    {
        $data = $this->request;
        if (empty($data['app']) || empty($data['label'])) {
            return $this->error('参数必须');
        }

        if (!in_array($data['app'], self::APPS) || !in_array($data['label'], self::LABELS)) {
            return $this->error('无效参数');
        }

        $this->app = $data['app'];
        $this->label = $data['label'];
        $this->data = $data;
    }

    /**
     * 效验签名
     *
     */
    protected function checkSign()
    {
        $data = $this->request;
        if (empty($data['signature']) || empty($data['timestamp']) || empty($data['nonce'])) {
            return $this->error('效验参数必须');
        }

        $signature = $data['signature'];

        unset($data['signature']);
        $data['token'] = $this->config['TOKEN'];
        $tmpArr = array_values($data);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
       
        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

    public function index()
    {
        try {
            if ($this->app == 'DuxCms') {
                $service = new DuxService;
            } else {
                $service = new ArticleService;
            }

            $result = call_user_func(array($service, $this->label), $this->data);
            return $this->success($result);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
        
    }
}
