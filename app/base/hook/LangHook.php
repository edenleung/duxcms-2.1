<?php
namespace app\base\hook;

class LangHook
{
    /**
     * 默认配置
     *
     * @var array
     */
    protected $config = [
        'LANG_OPEN' => 0,
        'LANG_DEFAULT' => 'en-us',
        'LANG_AUTO_DETEC' => 1,
        'LANG_LIST' => [
            'en-us' => [
                'label' => '英文',
            ],
            'zh-cn' => [
                'label' => '中文-简体'
            ]
        ]
    ];

    protected $cookie_name = 'APP_LANG';

    /**
     * 保存cookie日期
     *
     * @var integer
     */
    protected $save_day = 24;

    public function __construct()
    {
        $c_config = include CONFIG_PATH . 'lang.php';
        $this->config = array_merge($this->config, $c_config);
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

            $lang = request('get.l') ?: session('APP_LANG');

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
        if ($this->config['LANG_OPEN']) {
            define('LANG_OPEN', true);
            define('LANG_CONFIG', serialize($this->config));
            $defaultLang = $this->getDefaultLang();

            $s = request('get.s');
            if (empty($s)) {
                if ($l = cookie($this->cookie_name)) 
                {
                    header('location:' . '/' .$l, true, $code);
                    exit();

                }
                header('location:' . '/' .$defaultLang, true, $code);
                exit();
            }

            list($temp, $lang) = explode('/', $s);
            $langs = array_keys($this->config['LANG_LIST']);
            if ($lang && !in_array($lang, $langs)) {
                header('location:' . '/' .$defaultLang, true, $code);
                exit();
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
        if (cookie($this->cookie_name)) {
            return cookie($this->cookie_name);
        } elseif ($this->config['LANG_AUTO_DETEC']) {
            // 自动侦测语言
            if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
                preg_match('/^([a-z\d\-]+)/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);
                return strtolower($matches[1]);
            }
        }

        return $this->config['LANG_DEFAULT'];
    }
}
