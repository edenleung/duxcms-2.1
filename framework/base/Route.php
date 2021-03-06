<?php

namespace framework\base;

class Route
{
    protected static $rewriteRule = [];
    protected static $rewriteOn = false;

    public static function parseUrl($rewriteRule, $rewriteOn = false)
    {
        self::$rewriteRule = $rewriteRule;
        self::$rewriteOn = $rewriteOn;
        if (self::$rewriteOn && !empty(self::$rewriteRule)) {
            if (($pos = strpos($_SERVER['REQUEST_URI'], '?')) !== false) {
                parse_str(substr($_SERVER['REQUEST_URI'], $pos + 1), $_GET);
            }
            foreach (self::$rewriteRule as $rule => $mapper) {
                $rule = ltrim($rule, './\\');
                if (false === stripos($rule, 'http://')) {
                    $rule = $_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\').'/'.$rule;
                }
                $rule = '/'.str_ireplace(['\\\\', 'http://', '-', '/', '<', '>', '.'], ['', '', '\-', '\/', '(?<', ">[a-z0-9_\-\/%]+)", '\.'], $rule).'/i';
                if (preg_match($rule, $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], $matches)) {
                    foreach ($matches as $matchkey => $matchval) {
                        if (('app' === $matchkey)) {
                            $mapper = str_ireplace('<app>', $matchval, $mapper);
                        } elseif ('c' === $matchkey) {
                            $mapper = str_ireplace('<c>', $matchval, $mapper);
                        } elseif ('a' === $matchkey) {
                            $mapper = str_ireplace('<a>', $matchval, $mapper);
                        } else {
                            if (!is_int($matchkey)) {
                                $_GET[$matchkey] = $matchval;
                            }
                        }
                    }
                    $_REQUEST['r'] = $mapper;
                    break;
                }
            }
        }

        $routeArr = isset($_REQUEST['r']) ? explode('/', $_REQUEST['r']) : [];
        if (!defined('ADMIN_STATUS') && $_SERVER['REQUEST_URI'] !== '/' && false === stripos($_SERVER['REQUEST_URI'], '?')) {
            $app_name = empty($routeArr[0]) ? Config::get('DEFAULT_APP') : $routeArr[0];
            $controller_name = empty($routeArr[1]) ? Config::get('DEFAULT_CONTROLLER') : $routeArr[1];
            $action_name = empty($routeArr[2]) ? 'error404' : $routeArr[2];
        } else {
            $app_name = empty($routeArr[0]) ? Config::get('DEFAULT_APP') : $routeArr[0];
            $controller_name = empty($routeArr[1]) ? Config::get('DEFAULT_CONTROLLER') : $routeArr[1];
            $action_name = empty($routeArr[2]) ? Config::get('DEFAULT_ACTION') : $routeArr[2];
        }

        $_REQUEST['r'] = $app_name.'/'.$controller_name.'/'.$action_name;

        if (!defined('APP_NAME')) {
            define('APP_NAME', strtolower($app_name));
        }
        if (!defined('CONTROLLER_NAME')) {
            define('CONTROLLER_NAME', $controller_name);
        }
        if (!defined('ACTION_NAME')) {
            define('ACTION_NAME', $action_name);
        }
    }

    public static function url($route = null, $params = [])
    {
        $app = APP_NAME;
        $controller = CONTROLLER_NAME;
        $action = ACTION_NAME;
        if ($route) {
            $route = explode('/', $route, 3);
            $routeNum = count($route);
            switch ($routeNum) {
                case 1:
                    $action = $route[0];
                    break;
                case 2:
                    $controller = $route[0];
                    $action = $route[1];
                    break;
                case 3:
                    $app = $route[0];
                    $controller = $route[1];
                    $action = $route[2];
                    break;
            }
        }
        $route = $app.'/'.$controller.'/'.$action;
        // 多语言
        if (defined('LANG_OPEN') && !isset($params['lang'])) {
            if ($route != 'home/Index/index') {
                $params['lang'] = APP_LANG;
            }
        }
        $paramStr = empty($params) ? '' : '&'.http_build_query($params);
        $url = $_SERVER['SCRIPT_NAME'].'?r='.$route.$paramStr;

        if (self::$rewriteOn && !empty(self::$rewriteRule)) {
            static $urlArray = [];
            if (!isset($urlArray[$url])) {
                foreach (self::$rewriteRule as $rule => $mapper) {
                    $mapper = '/'.str_ireplace(['/', '<app>', '<c>', '<a>'], ['\/', '(?<app>\w+)', '(?<c>\w+)', '(?<a>\w+)'], $mapper).'/i';
                    if (preg_match($mapper, $route, $matches)) {
                        list($app, $controller, $action) = explode('/', $route);
                        $urlArray[$url] = str_ireplace(['<app>', '<c>', '<a>'], [$app, $controller, $action], $rule);
                        if (!empty($params)) {
                            $_args = [];
                            foreach ($params as $argkey => $arg) {
                                $count = 0;
                                $urlArray[$url] = str_ireplace('<'.$argkey.'>', $arg, $urlArray[$url], $count);
                                if (!$count) {
                                    $_args[$argkey] = $arg;
                                }
                            }

                            if (!empty($_args)) {
                                $urlArray[$url] = preg_replace('/<\w+>/', '', $urlArray[$url]).'?'.http_build_query($_args);
                            }
                        }

                        $protocol = is_https() ? 'https' : 'http';
                        if (false === stripos($urlArray[$url], $protocol)) {
                            $urlArray[$url] = $protocol.'://'.$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['SCRIPT_NAME']), './\\').'/'.ltrim($urlArray[$url], './\\');
                        }

                        $rule = str_ireplace(['<app>', '<c>', '<a>'], '', $rule);
                        if (count($params) == preg_match_all('/<\w+>/is', $rule, $_match)) {
                            return $urlArray[$url];
                        }
                    }
                }

                return isset($urlArray[$url]) ? $urlArray[$url] : $url;
            }

            return $urlArray[$url];
        }

        return $url;
    }
}
