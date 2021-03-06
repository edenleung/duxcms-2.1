<?php

/**
 * 判断是否有子栏目.
 */
function hasSub($cid)
{
    $sub = target('duxcms/Category')->getSubClassId($cid);
    if (empty($sub)) {
        return false;
    } else {
        return true;
    }
}

/**
 * 推送当前链接到百度收录.
 *
 * @return void
 */
function pushBaidu()
{
    $script = '(function(){var bp=document.createElement("script");var curProtocol=window.location.protocol.split(":")[0];if(curProtocol==="https"){bp.src="https://zz.bdstatic.com/linksubmit/push.js"}else{bp.src="http://push.zhanzhang.baidu.com/push.js"}var s=document.getElementsByTagName("script")[0];s.parentNode.insertBefore(bp,s)})();';
    echo '<script>'.$script.'</script>';
}

/**
 * 统计分类下的所有文章数.
 *
 * @param int    $cid        分类id
 * @param [type] $positionId 推荐位id
 * @param [type] $positionId 统计已发布的文章
 *
 * @return void
 */
function articleSumByCid($cid, $positionId = '', $isShow = true)
{
    $model = target('article/CategoryArticle');
    $categoryInfo = $model->getInfo($cid);

    // 频道页(统计频道页下所有分类)
    $classIds = '';
    if ($categoryInfo['type'] == 0) {
        $classIds = target('duxcms/Category')->getSubClassId($categoryInfo['class_id']);
    }

    if ($classIds) {
        $classIds = $classIds;
    } else {
        $classIds = $categoryInfo['class_id'];
    }

    $where = [];
    $where[] = 'C.class_id in ('.$classIds.')';

    if (!empty($positionId)) {
        $where[] = 'find_in_set('.$positionId.',position) ';
    }

    if ($isShow) {
        $where['A.status'] = 1;
    } else {
        $where['A.status'] = 0;
    }

    $data = target('article/ContentArticle')->loadList($where);

    return count($data);
}

/**
 * 判断是否奇数.
 *
 * @param number $index $list['i']
 *
 * @return bool
 */
function isOdd($index)
{
    $index++;
    if ($index % 2 == 0) {
        return false;
    }

    return true;
}

/**
 * 判断当前协议是否为HTTPS.
 *
 * @return bool
 */
function is_https()
{
    if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
        return true;
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
        return true;
    } elseif (!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
        return true;
    }

    return false;
}

/**
 * (实时)推送链接到百度.
 *
 * @param [type] $urls
 *
 * @return void
 */
function push($urls)
{
    $file = CONFIG_PATH.'push.php';
    $config = load_config($file);

    $token = $config['push_token'];
    $site = $_SERVER['HTTP_HOST'];
    $url = "http://data.zz.baidu.com/urls?site={$site}&token={$token}";

    if (is_array($urls)) {
        $urls = implode("\n", $urls);
    }

    $ch = curl_init();
    $options = [
        CURLOPT_URL            => $url,
        CURLOPT_POST           => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS     => $urls,
        CURLOPT_HTTPHEADER     => ['Content-Type: text/plain'],
    ];

    curl_setopt_array($ch, $options);
    $result = curl_exec($ch);

    return $result;
}

/**
 * 获取request请求方法.
 */
function request($str, $default = null, $function = null)
{
    $str = trim($str);
    list($method, $name) = explode('.', $str, 2);
    $method = strtoupper($method);
    switch ($method) {
        case 'POST':
            $type = $_POST;
            break;
        case 'SESSION':
            $type = $_SESSION;
            break;
        case 'REQUEST':
            $type = $_REQUEST;
            break;
        case 'COOKIE':
            $type = $_COOKIE;
            break;
        case 'GET':
        default:
            $type = $_GET;
            break;
    }
    if (empty($name)) {
        $request = filter_string($type);
    } else {
        if (!isset($type[$name])) {
            return;
        }
        if ($method == 'GET') {
            $request = urldecode($type[$name]);
        } else {
            $request = $type[$name];
        }

        $request = filter_string($request);
        //设置默认值
        if ($default) {
            if (empty($request)) {
                $request = $default;
            }
        }
        //设置处理函数
        if ($function) {
            $request = call_user_func($function, $request);
        }
    }

    return $request;
}
/**
 * 过滤数据.
 *
 * @param array $data 过滤数据
 */
function filter_string($data)
{
    if ($data === null) {
        return false;
    }
    if (is_array($data)) {
        foreach ($data as $k=>$v) {
            $data[$k] = filter_string($v);
        }

        return $data;
    } else {
        return SafeFilter($data);
    }
}

/**
 * 显示文章阅读进度.
 *
 * @param [type] $container 文章容器
 * @param [type] $parent    包着百分百的
 * @param string $child     进度div显示百分比的标签
 *
 * @return void
 */
function showArticleProgress($container, $parent, $child, $class)
{
    $script = 'function scrollFunc(){var THRESHOLD=50;var DOC="{CONTAINER}";var PARENT="{PARENT}";var CHILD="{CHILD}";var CLASS="{CLASS}";var parentElement=document.querySelector(PARENT);if(parentElement){var docHeight=document.querySelector(DOC).offsetHeight;var winHeight=window.innerHeight;var contentVisibilityHeight=docHeight>winHeight?docHeight-winHeight:document.body.scrollHeight-winHeight;var scrollPercentRounded=Math.round(100*window.scrollY/contentVisibilityHeight);scrollPercent=Math.min(scrollPercentRounded,100)+"%"}if(parentElement){window.scrollY>THRESHOLD?parentElement.classList.add(CLASS):parentElement.classList.remove(CLASS);parentElement.querySelector(CHILD).innerText=scrollPercent;parentElement.addEventListener("click",function(){document.body.scrollTop=0;document.documentElement.scrollTop=0})}}if(document.addEventListener){document.addEventListener("DOMMouseScroll",scrollFunc,false);document.addEventListener("DOMContentLoaded",scrollFunc,false)}window.onmousewheel=document.onmousewheel=scrollFunc;';
    $script = str_replace('{CONTAINER}', $container, $script);
    $script = str_replace('{PARENT}', $parent, $script);
    $script = str_replace('{CHILD}', $child, $script);
    $script = str_replace('{CLASS}', $class, $script);

    return '<script>'.$script.'</script>';
}

/**
 * 百度统计
 *
 * @return void
 */
function tongji()
{
    $file = CONFIG_PATH.'statistics.php';
    $file = load_config($file);
    if (!$file['tongji_open']) {
        return;
    }

    $type = $file['default'];
    $config = $file['store'][$type];

    if (!$config['token']) {
        return;
    }

    $scripts = [
        'baidu' => '<script type="text/javascript">var _hmt=_hmt||[];(function(){var hm=document.createElement("script");hm.src="https://hm.baidu.com/hm.js?{TOKEN}";var s=document.getElementsByTagName("script")[0];s.parentNode.insertBefore(hm,s)})();</script>',
        'cznn'  => '<script type="text/javascript" src="https://v1.cnzz.com/z_stat.php?id={TOKEN}&web_id={TOKEN}"></script>',
    ];

    $script = $scripts[$type];

    return str_replace('{TOKEN}', $config['token'], $script);
}

/**
 * String to Intval.
 *
 * @param [type] $string
 *
 * @return void
 */
function string2Intval($string)
{
    return intval($string);
}

/**
 * 生成筛选url.
 *
 * @param [type] $flag
 * @param [type] $fields
 * @param [type] $field
 * @param [type] $classId
 * @param [type] $id
 *
 * @return void
 */
function buildScreenUri($flag, $fields, $field, $categoryInfo, $id = '')
{
    $params = [];
    foreach ($fields as $item) {
        if ($field != $item) {
            $value = request('get.'.$item);
            $array = explode(',', $value);
            $array = array_map(string2Intval, $array);
            if (!in_array(0, $array)) {
                $params[$item] = $value;
            }
        }
    }

    $page = request('get.page', 0, 'intval');
    if ($page) {
        $params['page'] = $page;
    }

    if ($flag) {
        $value = request('get.'.$field);
        if (!$value) {
            $params[$field] = $id;
        } else {
            $array = explode(',', $value);
            $array = array_map(string2Intval, $array);
            array_push($array, $id);
            $params[$field] = implode(',', $array);
        }
    } else {
        $value = request('get.'.$field);
        if ($value) {
            $array = explode(',', $value);
            foreach ($array as $key=> $item) {
                if ($item == $id) {
                    unset($array[$key]);
                }
            }

            if (!empty($array)) {
                $params[$field] = implode(',', $array);
            }
        }
    }

    $paramer['class_id'] = $categoryInfo['class_id'];
    $dir = APP_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME;
    $paramer['urlname'] = $categoryInfo['urlname'];

    return match_url($dir, $paramer, $params);
}

/**
 * 数据签名认证
 *
 * @param array $data 被认证的数据
 *
 * @return string 签名
 */
function data_auth_sign($data)
{
    //数据类型检测
    if (!is_array($data)) {
        $data = (array) $data;
    }
    ksort($data); //排序
    $code = http_build_query($data); //url编码并生成query字符串
    $sign = sha1($code); //生成签名
    return $sign;
}
/**
 * 读取模块配置.
 *
 * @param string $file 调用文件
 *
 * @return array
 */
function load_config($file)
{
    $file = get_config_file($file);

    return require $file;
}

/**
 * 读取当前模块配置.
 *
 * @return array
 */
function current_config()
{
    return load_config('config');
}

/**
 * 保存模块配置.
 *
 * @param string $file 调用文件
 *
 * @return array
 */
function save_config($file, $config)
{
    if (empty($config) || !is_array($config)) {
        return [];
    }
    $file = get_config_file($file);
    //读取配置内容
    $conf = file_get_contents($file);
    //替换配置项
    foreach ($config as $key => $value) {
        if (is_string($value) && !in_array($value, ['true', 'false'])) {
            if (!is_numeric($value)) {
                $value = "'".$value."'"; //如果是字符串，加上单引号
            }
        }
        $conf = preg_replace("/'".$key."'\s*=\>\s*(.*?),/iU", "'".$key."'=>".$value.',', $conf);
    }
    //写入应用配置文件
    // if (!IS_WRITE) {
    //     return false;
    // } else {
    if (file_put_contents($file, $conf)) {
        return true;
    } else {
        return false;
    }

    return '';
    // }
}

/**
 * 字符串转方法数组.
 *
 * @param string $str
 *
 * @return array
 */
function get_method_array($str = '')
{
    $strArray = [];
    if (!empty($str)) {
        $strArray = explode('/', $str, 3);
    }
    $strCount = count($strArray);
    switch ($strCount) {
            case 1:
                $app = APP_NAME;
                $controller = CONTROLLER_NAME;
                $action = $strArray[0];
                break;
            case 2:
                $app = APP_NAME;
                $controller = $strArray[0];
                $action = $strArray[1];
                break;
            case 3:
                $app = $strArray[0];
                $controller = $strArray[1];
                $action = $strArray[2];
                break;
            default:
                $app = APP_NAME;
                $controller = CONTROLLER_NAME;
                $action = ACTION_NAME;
                break;
        }

    return [
            'app'        => strtolower($app),
            'controller' => $controller,
            'action'     => $action,
            ];
}

/**
 * 解析配置文件路径.
 *
 * @param string $file 文件路径或简写路径
 *
 * @return dir
 */
function get_config_file($file)
{
    $name = $file;
    if (!is_file($file)) {
        $str = explode('/', $file);
        $strCount = count($str);
        switch ($strCount) {
            case 1:
                $app = APP_NAME;
                $name = $str[0];
                break;
            case 2:
                $app = $str[0];
                $name = $str[1];
                break;
        }
        $app = strtolower($app);
        if (empty($app) && empty($file)) {
            throw new \Exception("Config '{$file}' not found'", 500);
        }
        $file = APP_PATH."{$app}/conf/{$name}.php";
        if (!file_exists($file)) {
            throw new \Exception("Config '{$file}' not found", 500);
        }
    }

    return $file;
}
/**
 * 自适应URL规则.
 *
 * @param string $str        URL路径
 * @param array  $params     自动解析参数
 * @param string $mustParams 必要参数
 *
 * @return url
 */
function match_url($str, $params = [], $mustParams = [])
{
    $newParams = [];
    $keyArray = array_keys($params);
    if (config('REWRITE_ON')) {
        //获取规则文件
        $config = config('REWRITE_RULE');
        $configArray = array_flip($config);
        $route = $configArray[$str];
        if ($route) {
            preg_match_all('/<(\w+)>/', $route, $matches);
            foreach ($matches[1] as $value) {
                if ($params[$value]) {
                    $newParams[$value] = $params[$value];
                }
            }
        } else {
            if (!empty($keyArray)) {
                $newParams[$keyArray[0]] = current($params);
            }
        }
    } else {
        if (!empty($keyArray)) {
            $newParams[$keyArray[0]] = current($params);
        }
    }
    $newParams = array_merge((array) $newParams, (array) $mustParams);
    $newParams = array_filter($newParams);

    return url($str, $newParams);
}

/**
 * 通用模块调用函数.
 *
 * @param string $str   调用路径
 * @param string $layer 调用层 默认model
 *
 * @return obj
 */
function target($str, $layer = 'model')
{
    static $_target = [];
    $str = explode('/', $str);
    $strCount = count($str);
    switch ($strCount) {
        case 1:
            $app = APP_NAME;
            $module = $str[0];
            break;
        case 2:
            $app = $str[0];
            $module = $str[1];
            break;
    }
    $app = strtolower($app);
    $name = $app.'/'.$layer.'/'.$module;
    if (isset($_target[$name])) {
        return $_target[$name];
    }
    $class = "\\app\\{$app}\\{$layer}\\{$module}".ucfirst($layer);
    if (!class_exists($class)) {
        throw new \Exception("Class '{$class}' not found'", 500);
    }
    $target = new $class();
    $_target[$name] = $target;

    return $target;
}

/**
 * 获取所有模块Service.
 *
 * @param string $name 指定service名
 *
 * @return array
 */
function get_all_service($name, $method, $vars = [])
{
    if (empty($name)) {
        return;
    }
    $apiPath = APP_PATH.'*/service/'.$name.'Service.php';
    $apiList = glob($apiPath);
    if (empty($apiList)) {
        return;
    }
    $appPathStr = strlen(APP_PATH);
    $method = 'get'.$method.$name;
    $data = [];
    foreach ($apiList as $value) {
        $path = substr($value, $appPathStr, -4);
        $path = str_replace('\\', '/', $path);
        $appName = explode('/', $path);
        $appName = $appName[0];
        $config = load_config($appName.'/config');
        if (!$config['APP_SYSTEM'] && (!$config['APP_STATE'] || !$config['APP_INSTALL'])) {
            continue;
        }
        $class = target($appName.'/'.$name, 'service');
        if (method_exists($class, $method)) {
            $data[$appName] = $class->$method($vars);
        }
    }

    return $data;
}

/**
 * 获取指定模块Service.
 *
 * @param string $name 指定service名
 *
 * @return Service
 */
function service($appName, $name, $method, $vars = [])
{
    $config = load_config($appName.'/config');
    if (!$config['APP_SYSTEM'] && (!$config['APP_STATE'] || !$config['APP_INSTALL'])) {
        return;
    }
    $class = target($appName.'/'.$name, 'service');
    if (method_exists($class, $method)) {
        return $class->$method($vars);
    }
}

/**
 * 调用指定模块的API.
 *
 * @param string $name 指定api名
 *
 * @return Api
 */
function api($appName, $name, $method, $vars = [])
{
    $config = load_config($appName.'/config');
    if (!$config['APP_SYSTEM'] && (!$config['APP_STATE'] || !$config['APP_INSTALL'])) {
        return;
    }
    $class = target($appName.'/'.$name, 'api');
    if (method_exists($class, $method)) {
        return $class->$method($vars);
    }
}

/**
 * 二维数组排序.
 *
 * @param array  $array 排序的数组
 * @param string $key   排序主键
 * @param string $type  排序类型 asc|desc
 * @param bool   $reset 是否返回原始主键
 *
 * @return array
 */
function array_order($array, $key, $type = 'asc', $reset = false)
{
    if (empty($array) || !is_array($array)) {
        return $array;
    }
    foreach ($array as $k => $v) {
        $keysvalue[$k] = $v[$key];
    }
    if ($type == 'asc') {
        asort($keysvalue);
    } else {
        arsort($keysvalue);
    }
    $i = 0;
    foreach ($keysvalue as $k => $v) {
        $i++;
        if ($reset) {
            $new_array[$k] = $array[$k];
        } else {
            $new_array[$i] = $array[$k];
        }
    }

    return $new_array;
}

/**
 * session获取.
 *
 * @param string $name
 * @param string $value
 *
 * @return string
 */
function session($name = '', $value = '')
{
    if (empty($name)) {
        return $_SESSION;
    }
    $sessionId = request('request.session_id');
    if (!empty($sessionId)) {
        session_id($sessionId);
    }
    if (!isset($_SESSION)) {
        session_start();
    }
    $pre = config('COOKIE_PREFIX');
    if ($value === '') {
        $session = $_SESSION[$pre.$name];
    } elseif (is_null($value)) {
        unset($_SESSION[$pre.$name]);
    } else {
        $session = $_SESSION[$pre.$name] = $value;
    }

    return $session;
}

/**
 * cookie获取.
 *
 * @param string $name
 * @param string $value
 * @param int    $time  小时时间
 *
 * @return string
 */
function cookie($name = '', $value = '', $time = 1)
{
    if (empty($name)) {
        return $_COOKIE;
    }
    $pre = config('COOKIE_PREFIX');
    if ($value === '') {
        $cookie = $_COOKIE[$pre.$name];
    } else {
        $cookie = setcookie($pre.$name, $value, time() + 3600 * $time, '/');
    }

    return $cookie;
}

/**
 * 默认数据.
 *
 * @param string $data
 * @param string $var
 *
 * @return string
 */
function default_data($data, $var)
{
    if (empty($data)) {
        return $var;
    } else {
        return $data;
    }
}

//图片裁剪
function cut_image($img, $width, $height, $type = 3)
{
    if (empty($width) && empty($height)) {
        return $img;
    }
    $imgDir = realpath(ROOT_PATH.$img);
    if (!is_file($imgDir)) {
        return $img;
    }
    $imgInfo = pathinfo($img);
    $newImg = $imgInfo['dirname'].'/cut_'.$width.'_'.$height.'_'.$imgInfo['basename'];
    $newImgDir = ROOT_PATH.$newImg;
    if (!is_file($newImgDir)) {
        $image = new \app\base\util\ThinkImage();
        $image->open($imgDir);
        $image->thumb($width, $height, $type)->save($newImgDir);
    }

    return $newImg;
}

/**
 * 获取文件或文件大小.
 *
 * @param string $directoty 路径
 *
 * @return int
 */
function dir_size($directoty)
{
    $dir_size = 0;
    if ($dir_handle = @opendir($directoty)) {
        while ($filename = readdir($dir_handle)) {
            $subFile = $directoty.DIRECTORY_SEPARATOR.$filename;
            if ($filename == '.' || $filename == '..') {
                continue;
            } elseif (is_dir($subFile)) {
                $dir_size += dir_size($subFile);
            } elseif (is_file($subFile)) {
                $dir_size += filesize($subFile);
            }
        }
        closedir($dir_handle);
    }

    return $dir_size;
}

/**
 * 复制目录.
 *
 * @param [type] $sourceDir
 * @param [type] $aimDir
 * @param array  $diss      忽略覆盖
 *
 * @return void
 */
function copy_dir($sourceDir, $aimDir, $diss = [])
{
    $succeed = true;
    if (!file_exists($aimDir)) {
        if (!mkdir($aimDir, 0777)) {
            return false;
        }
    }
    $objDir = opendir($sourceDir);
    while (false !== ($fileName = readdir($objDir))) {
        if (($fileName != '.') && ($fileName != '..')) {
            if (!in_array("$sourceDir/$fileName", $diss)) {
                if (!is_dir("$sourceDir/$fileName")) {
                    if (!copy("$sourceDir/$fileName", "$aimDir/$fileName")) {
                        $succeed = false;
                        break;
                    }
                } else {
                    copy_dir("$sourceDir/$fileName", "$aimDir/$fileName", $diss);
                }
            }
        }
    }
    closedir($objDir);

    return $succeed;
}

/**
 * 遍历删除目录和目录下所有文件.
 *
 * @param string $dir 路径
 *
 * @return bool
 */
function del_dir($dir)
{
    framework\ext\Util::delDir($dir);
}

/**
 * html代码输入.
 */
function html_in($str)
{
    $str = htmlspecialchars($str);
    if (!get_magic_quotes_gpc()) {
        $str = addslashes($str);
    }

    return $str;
}

/**
 * html代码输出.
 */
function html_out($str)
{
    if (function_exists('htmlspecialchars_decode')) {
        $str = htmlspecialchars_decode($str);
    } else {
        $str = html_entity_decode($str);
    }
    $str = stripslashes($str);

    return $str;
}

/**
 * 字符串截取.
 */
function len($str, $len = 0)
{
    if (!empty($len)) {
        return \framework\ext\Util::msubstr($str, 0, $len);
    } else {
        return $str;
    }
}

/**
 * 兼容老版U方法.
 */
function U($str = null, $var = null)
{
    return url($str, $var);
}

/**
 * 生成唯一数字.
 */
function unique_number()
{
    return date('Ymd').substr(implode(null, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
}

/**
 * 生成随机字符串.
 */
function random_str()
{
    $year_code = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
    $order_sn = $year_code[intval(date('Y')) - 2010].
    strtoupper(dechex(date('m'))).date('d').
    substr(time(), -5).substr(microtime(), 2, 5).sprintf('d', rand(0, 99));

    return $order_sn;
}

/**
 * 获取客户端IP.
 */
function get_client_ip()
{
    return \framework\ext\Util::getIp();
}

function get_admin_id()
{
    $user = session('admin_user');
    if (empty($user)) {
        return 0;
    } else {
        return session('admin_user_sign') == data_auth_sign($user) ? $user['user_id'] : 0;
    }
}

/**
 * 判断不为空.
 */
function is_empty($str)
{
    if (empty($str)) {
        return false;
    } else {
        return true;
    }
}

/**
 * 截取摘要
 */
function get_text_make($data, $cut = 0, $str = '...')
{
    $data = strip_tags($data);
    $pattern = '/&[a-zA-Z]+;/';
    $data = preg_replace($pattern, '', $data);
    if (!is_numeric($cut)) {
        return $data;
    }
    if ($cut > 0) {
        $data = mb_strimwidth($data, 0, $cut, $str);
    }

    return $data;
}

/**
 * xss过滤.
 *
 * @param [type] $value
 *
 * @return void
 */
function SafeFilter($value)
{
    include ROOT_PATH.'vendor/ezyang/htmlpurifier/extras/HTMLPurifierExtras.auto.php';
    $config = HTMLPurifier_Config::createDefault();
    $purifier = new HTMLPurifier($config);

    return $purifier->purify($value);
}

function tagList($data = [])
{
    return target('duxcms/Tags')->loadList([], 0);
}

function keyword_replace($str)
{
    $tags = tagList();
    $key_array = [];

    foreach ($tags as $tag) {
        $key_array[] = [$tag['name'], $tag['url']];
    }

    $array_first = $key_array;
    $array_last = [];
    foreach ($array_first as $key=>$value) {
        $array_last[$key] = ['TFXS00'.$key, $value[0], '<a target="_blank" href="'.$value[1].'" title="'.$value[0].'">'.$value[0].'</a>'];
    }
    $count = count($array_last);
    for ($i = 0; $i < $count; $i++) {
        for ($j = $count - 1; $j > $i; $j--) {
            //如果后一个元素长度大于前一个则调换位置
            if (strlen($array_last[$j][1]) > strlen($array_last[$j - 1][1])) {
                $tmp = $array_last[$j];
                $array_last[$j] = $array_last[$j - 1];
                $array_last[$j - 1] = $tmp;
            }
        }
    }
    $keys = $array_last;
    foreach ($keys as $nkeys) {
        $str = str_ireplace($nkeys[1], $nkeys[0], $str);
    }
    foreach ($keys as $nkeys) {
        $str = str_ireplace($nkeys[0], $nkeys[2], $str);
    }

    return $str;
}
