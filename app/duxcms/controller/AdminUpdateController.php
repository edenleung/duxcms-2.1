<?php
namespace app\duxcms\controller;

use app\admin\controller\AdminController;
use GuzzleHttp\Client;

require BASE_PATH.'/vendor/autoload.php';

/**
 * 更新管理
 */

class AdminUpdateController extends AdminController
{
    /**
     * 最新版本发布地址
     *
     * @var string
     */
    public $domain = 'https://raw.githubusercontent.com/xiaodit/duxcms-update/master';

    /**
     * 当前模块参数
     */
    protected function _infoModule()
    {
        return array(
            'info'  => array(
                'name' => '更新管理',
                'description' => '升级当前网站到最新版',
                )
            );
    }
    /**
     * 列表
     */
    public function index()
    {
        $breadCrumb = array('更新管理'=>url());
        $this->assign('breadCrumb', $breadCrumb);
        $this->adminDisplay();
    }

    /**
     * 获取最新版本
     */
    public function getVer()
    {
        $verTime = config('DUX_TIME');
        if (empty($verTime)) {
            $this->error('没有发现版本号！');
        }

        $url = $this->domain.'/ver.json';
        $info = \framework\ext\Http::doGet($url);
        $info = json_decode($info, true);

        if (empty($info)) {
            $this->error('无法获取版本信息，请稍后再试！');
        }

        if ($verTime == $info['data']['version']) {
            $this->error('已经是最新版本了！');
        }

        if ($info['status']) {
            $this->success($info['data']);
        } else {
            $this->error($info['data']);
        }
    }

    /**
     * 下载更新
     */
    public function dowload()
    {
        $url = request('post.url');
        $fileName = end(explode('/', $url));
        if (!$fileName) {
            $this->error($fileName.'没有发现更新地址，请稍后重试！');
        }
        $updateDir = DATA_PATH.'update/';
        if (!file_exists($updateDir)) {
            if (!mkdir($updateDir)) {
                $this->error('抱歉，无法为您创建更新文件夹，请手动创建目录【'.$updateDir.'】');
            }
        }

        //开始下载文件
        $fileName = $updateDir.$fileName;

        $client = new Client([
            'timeout' => 0
        ]);

        $response = $client->get($url);
        if (!file_put_contents($fileName, $response->getBody())) {
            $this->error('无法保存更新文件请检查目录【'.$updateDir.'】是否有写入权限！');
        };

        $flag = $this->backup();
        if (false === $flag) {
            $this->error('备份站点文件失败!');
        };

        $this->success("备份整站成功({$flag})，文件下载成功，正在执行解压操作！");
    }

    /**
     * 解压文件
     */
    public function unzip()
    {
        $url = request('post.url');
        $version = request('post.version');

        $fileName = end(explode('/', $url));

        $updateDir = DATA_PATH.'update/';
        $file = $updateDir.$fileName;
        if (!is_file($file)) {
            $this->error('没有发现更新文件，请稍后重试！');
        }

        $dir = $updateDir.'tmp_'.$version;
        $zip = new \ZipArchive;
        $res = $zip->open($file);
        if ($res === true) {
            $zip->extractTo($dir);
            $zip->close();
            $this->success("文件解压成功，等待更新操作！");
        } else {
            $this->error('解压文件失败请检查目录【'.$dir.'】是否有写入权限！');
        }
    }

    /**
     * 更新文件
     */
    public function upfile()
    {
        $version = request('post.version');
        $updateDir = DATA_PATH.'update/';
        $dir = $updateDir.'tmp_'.$version;

        // 不用覆盖文件与文件夹
        $diss = [
            $dir.'/data/config/lang',
            $dir.'/app/install',
            $dir.'/data/config/admin.php',
            $dir.'/data/config/db.php',
            $dir.'/data/config/development.php',
            $dir.'/data/config/global.php',
            $dir.'/data/config/lang.php',
            $dir.'/data/config/performance.php',
            $dir.'/data/config/push.php',
            $dir.'/data/config/tongji.php',
            $dir.'/data/config/upload.php'
        ];

        if (!copy_dir($dir, ROOT_PATH, $diss)) {
            $this->error('无法复制更新文件，请检查网站是否有写入权限！');
        }

        //更新SQL文件
        $file = ROOT_PATH.'update/'.config('DUX_TIME').'.sql';
        if (is_file($file)) {
            $sqlData = \framework\ext\Install::mysql($file, 'dux_', $data['DB_PREFIX']);
            //开始导入数据库
            foreach ($sqlData as $sql) {
                if (false === target('Update')->execute($sql)) {
                    $this->error($sql.'...失败！');
                }
            }
        }
        //清理更新文件
        del_dir(DATA_PATH.'update');
        $this->success('更新成功，稍后为您刷新！');
    }

    /**
     * 查询授权
     */
    public function Authorize()
    {
        $url = 'http://www.duxcms.com/index.php?r=service/Authorize/index';
        $info = \framework\ext\Http::doGet($url, 30);
        echo $info;
    }

    /**
     * 备份整站
     *
     * @return void
     */
    protected function backup()
    {
        $zip = new \ZipArchive();
        $fileName = date('Ymd').'-'.time().'.zip';
        
        if ($zip->open(ROOT_PATH.'data/backup/'.$fileName, \ZIPARCHIVE::CREATE) !== true) {
            return false;
        }

        $this->makeZip(ROOT_PATH, $zip);
        $zip->close();

        return 'data/backup/'.$fileName;
    }

    private function makeZip($path, $zip)
    {
        $handler = opendir($path);

        while (($filename = readdir($handler)) !== false) {
            if ($filename != "." && $filename != "..") {
                // 默认只备份以下三个文件夹
                $save = [
                    'app',
                    'themes',
                    'framework'
                ];
        
                $t = explode('/', $path.'/'.$filename)[6];
                if (in_array($t, $save)) {
                    if (is_dir($path.'/'.$filename)) {
                        $this->makeZip($path.'/'.$filename, $zip);
                    } else {
                        $zip->addFile($path.'/'.$filename);
                    }
                }
            }
        }
    }
}
