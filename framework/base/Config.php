<?php

namespace framework\base;

class Config
{
    protected static $config = [];

    public static function init($basePath = '')
    {
        self::$config = [
            'ENV'      => 'development',
            'DEBUG'    => true,
            'LOG_ON'   => false,
            'LOG_PATH' => $basePath.'data/log/',
            'TIMEZONE' => 'PRC',

            'REWRITE_ON'   => false,
            'REWRITE_RULE' => [
            ],

            'DEFAULT_APP'        => 'main',
            'DEFAULT_CONTROLLER' => 'Default',
            'DEFAULT_ACTION'     => 'index',

            'DB'=> [
                'default'=> [
                        'DB_TYPE'    => 'MysqlPdo',
                        'DB_HOST'    => 'localhost',
                        'DB_USER'    => 'root',
                        'DB_PWD'     => '',
                        'DB_PORT'    => 3306,
                        'DB_NAME'    => 'cp',
                        'DB_CHARSET' => 'utf8',
                        'DB_PREFIX'  => '',
                        'DB_CACHE'   => 'DB_CACHE',
                        'DB_SLAVE'   => [],
                    ],
            ],

            'TPL'=> [
                'TPL_PATH'   => $basePath,
                'TPL_SUFFIX' => '.html',
                'TPL_CACHE'  => 'TPL_CACHE',
                'TPL_DEPR'   => '_',
            ],

            'CACHE'=> [
                'TPL_CACHE' => [
                    'CACHE_TYPE' => 'FileCache',
                    'CACHE_PATH' => $basePath.'data/cache/',
                    'GROUP'      => 'tpl',
                    'HASH_DEEP'  => 0,
                ],

                'DB_CACHE' => [
                    'CACHE_TYPE' => 'FileCache',
                    'CACHE_PATH' => $basePath.'data/cache/',
                    'GROUP'      => 'db',
                    'HASH_DEEP'  => 2,
                ],
            ],

            'STORAGE'=> [
                'default'=> ['STORAGE_TYPE'=>'File'],
            ],

            'LANG' => [],
        ];
    }

    public static function loadConfig($file)
    {
        if (!file_exists($file)) {
            throw new \Exception("Config file '{$file}' not found", 500);
        }
        $config = require $file;
        foreach ($config as $k=>$v) {
            if (is_array($v)) {
                if (!isset(self::$config[$k])) {
                    self::$config[$k] = [];
                }
                self::$config[$k] = array_merge((array) self::$config[$k], $config[$k]);
            } else {
                self::$config[$k] = $v;
            }
        }
    }

    public static function get($key = null)
    {
        if (empty($key)) {
            return self::$config;
        }
        $arr = explode('.', $key);
        switch (count($arr)) {
            case 1:
                if (isset(self::$config[$arr[0]])) {
                    return self::$config[$arr[0]];
                }
                break;
            case 2:
                if (isset(self::$config[$arr[0]][$arr[1]])) {
                    return self::$config[$arr[0]][$arr[1]];
                }
                break;
            case 3:
                if (isset(self::$config[$arr[0]][$arr[1]][$arr[2]])) {
                    return self::$config[$arr[0]][$arr[1]][$arr[2]];
                }
                break;
            default: break;
        }
    }

    public static function set($key, $value)
    {
        $arr = explode('.', $key);
        switch (count($arr)) {
            case 1:
                self::$config[$arr[0]] = $value;
                break;
            case 2:
                self::$config[$arr[0]][$arr[1]] = $value;
                break;
            case 3:
                self::$config[$arr[0]][$arr[1]][$arr[2]] = $value;
                break;
            default: return false;
        }

        return true;
    }
}
