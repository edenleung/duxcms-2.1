<?php
namespace app\base\hook;

class LangHook
{
    protected $cookie_name = 'APP_LANG';

    /**
     * 保存cookie日期
     *
     * @var integer
     */
    protected $save_day = 24;

    public function __construct()
    {
        $this->config = config('LANG');
    }

    /**
     * 后台
     *
     * @return void
     */
    public function CheckAdminLang()
    {
        // 开启多语言
        if ($this->config['LANG_OPEN']) {
            define('LANG_OPEN', true);
            define('LANG_CONFIG', serialize($this->config));
            $defaultLang = $this->getDefaultLang();

            $lang = request('get.l') ? request('get.l') : session('APP_LANG');

            if (empty($lang)) {
                $lang = $defaultLang;
            } else {
                $langs = array_keys($this->config['LANG_LIST']);
                if (in_array($lang, $langs)) {
                    session('APP_LANG', $lang);
                }
            }

            define('APP_LANG', $lang);
        }
    }

    /**
     * 前台
     *
     * @return void
     */
    public function CheckLang()
    {
        // 开启多语言
        if ($this->config['LANG_OPEN'] && !defined('API_STATUS')) {
            define('LANG_OPEN', true);
            define('LANG_CONFIG', serialize($this->config));
            $defaultLang = $this->getDefaultLang();
            $s = config('REWRITE_ON') ? request('get.s') : request('get.lang', $defaultLang);
            if (empty($s)) {
                define('APP_LANG', $defaultLang);
                $url = config('REWRITE_ON') ? '/' .$defaultLang : url('home/index/index');
                header('location:' . $url, true, $code);
                exit();
            } else {
                if (config('REWRITE_ON') ) {
                    list($temp, $lang) = explode('/', $s);
                } else {
                    $lang = $s;
                }
            }

            $langs = array_keys($this->config['LANG_LIST']);
            if ($lang && !in_array($lang, $langs)) {
                define('APP_NAME', 'home');
                define('APP_LANG', $defaultLang);
                throw new \Exception("404页面不存在！", 404);
            }
            define('APP_LANG', $lang);
            $this->setCookie($lang);
        }
    }

    /**
     * 保存cookie
     *
     * @param [type] $lang
     * @return void
     */
    public function setCookie($lang)
    {
        cookie($this->cookie_name, $lang, $this->save_day * 24);
    }

    /**
     * 获取默认语言
     *
     * @return void
     */
    protected function getDefaultLang()
    {
        if ($this->config['LANG_AUTO_DETEC']) {
            // 自动侦测语言
            if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
                preg_match('/^([a-z\d\-]+)/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);
                return strtolower($matches[1]);
            }
        }

        return $this->config['LANG_DEFAULT'];
    }
}
