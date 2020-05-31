<?php

namespace app\base\hook;

class LangHook
{
    protected $cookie_name = 'APP_LANG';

    /**
     * 保存cookie日期
     *
     * @var int
     */
    protected $save_day = 24;

    public function __construct()
    {
        $this->config = config('LANG');
    }

    /**
     * 后台.
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
     * 前台.
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

            $requestLang = $this->getRequestLang();

            if ($requestLang) {
                $lang = $requestLang;
            } elseif (cookie($this->cookie_name)) {
                $lang = cookie($this->cookie_name);
            } else {
                $lang = $defaultLang;
            }

            $langs = array_keys($this->config['LANG_LIST']);
            if ($lang && !in_array($lang, $langs)) {
                define('APP_NAME', 'home');
                define('APP_LANG', $defaultLang);

                throw new \Exception('404页面不存在！', 404);
            }
            define('APP_LANG', $lang);
            $this->setCookie($lang);
        }
    }

    protected function getRequestLang()
    {
        $requestLang = '';
        if ($lang = $_GET['lang']) {
            $requestLang = $lang;
        } else {
            if (config('REWRITE_ON')) {
                $lang = $_GET['lang'];
                $r = $_SERVER['REQUEST_URI'];
                list($temp, $lang) = explode('/', $r);
    
                if (!empty($lang)) {
                    $requestLang = $lang;
                }
            }
        }

        return $requestLang;
    }

    /**
     * 默认语言重定向.
     *
     * @param string $url
     *
     * @return void
     */
    protected function redirect($url)
    {
        header('location:'.$url, true);
        exit();
    }

    /**
     * 保存cookie.
     *
     * @param [type] $lang
     *
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
                if (in_array(strtolower($matches[1]), $this->config['LANG_LIST'])) {
                    return strtolower($matches[1]);
                }
            }
        }

        return $this->config['LANG_DEFAULT'];
    }
}
