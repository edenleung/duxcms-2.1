# DUXCMS 2.1
[![LICENSE](https://img.shields.io/badge/license-Anti%20996-blue.svg)](https://github.com/996icu/996.ICU/blob/master/LICENSE)
[![Build Status](https://travis-ci.org/xiaodit/duxcms-2.1.svg?branch=master)](https://travis-ci.org/xiaodit/duxcms-2.1)
[![Release](https://img.shields.io/github/v/release/xiaodit/duxcms-2.1.svg?style=flat)](https://github.com/xiaodit/duxcms-2.1/releases/latest)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/xiaodit/duxcms-2.1/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/xiaodit/duxcms-2.1/?branch=master)
[![HitCount](http://hits.dwyl.io/{username}/xiaodit/duxcms-21.svg)](http://hits.dwyl.io/{username}/xiaodit/duxcms-21)

[最新版本](https://github.com/xiaodit/duxcms-2.1/releases/latest)

默认没有前台模板的，请下载[官方仓库](https://gitee.com/duxcms/DuxCms-2.0/tree/master/themes/default)模板文件进行开发  

没安装Composer？ 请在最新版本链接下找`DuxCMS_Full.zip`下载

* `DuxCMS_Full.zip` 含composer包的完整程序 建议首次下载
* `DuxCMS-update.zip` 提供给在线更新的增量更新包

## 特征
* 支持php 5.6+ 7.4-
* [支持多语言](#多语言)
* [添加常用判断标签](#标签)
* [提供更稳定的分词服务](#分词功能)
* [提供文章推送百度收录](#百度推送)
* [提供更好的异常接管（Whoops）](https://packagist.org/packages/filp/whoops)
* [提供多条件筛选](#多条件筛选)
* [提供在线更新服务(增量更新)](#在线更新) 🚀
* 提供网站地图生成
* [提供api接口调用](#api-调用)

## 安装
* mysqli扩展必须安装
* pdo扩展可选安装
```sh
$ composer install
```

## 预览
[线上预览](http://duxcms.xiaodim.com)  
感谢 👉[Coding Page](http://coding.net)👈提供免费服务器支持

## 数据库驱动
* mysqli
* pdo

## 参考模板
[GITEE](https://gitee.com/duxcms/DuxCms-2.0/tree/master/themes/default)  
[开发文档](http://doc.duxcms.com/)

## 多语言

### 添加语种
[在此文件](https://github.com/xiaodit/duxcms-2.1/blob/master/data/config/lang.php#L7)添加其它语种

### 开启语言
1. 后台设置->多语言设置->开启
2. 选择默认语言

### 切换语言
#### 前台切换
```php
// 切换英文
http://www.domain.com/en-us

// 切换中文
http://www.domain.com/zh-cn
```
#### 前台获取语言配置
模板调用 `$lang_list` 获取列表

```php
<?php var_dump($lang_list);?>
```

具体用法
```html
<ul>
<!--foreach{$lang_list as $key=>$vo}-->
  <li><a href="/{$key}">{$vo.label}</a></li>
<!--{/foreach}-->
</ul>
```

## 百度推送
* 模板使用 `<!--#pushBaidu-->`
* 后台实时提交 系统设置->百度链接提交->开启


## 小功能
### 栏目页
* 添加分类文章统计（支持频道、列表） `$categoryInfo['article_count']`

## 助手函数
* `hasSub($cid)` 是否有下级分类
* `articleSumByCid(int $cid, $positionId = '', $isShow = true)` 获取分类文章统计
* `isOdd($index)` 是否为奇数 例子 `<!--if{isOdd($list['i'])}-->单数 <!--{else}-->偶数<!--{/if}-->`

## 标签
| 标签 | 说明| 参数 | 例子
| ----|----|----|----
| empty | 数组为空时| name | `{empty name="$list"}{/empty}`
| noempty | 数组不为空时 | name | `{noempty name="$list"}{/noempty}`
| defined | 常量已定义时 | name | `{defined name="APP_NAME"}{/defined}`
| nodefined | 常量未定义时 | name | `{nodefined name="APP_NAME"}{/nodefined}`
| isset | 变量定义时 | name | `{isset name="$test"}{/isset}`
| noset | 变量未定义时 | name | `{noset name="$test"}{/noset}`
| between | 变量存在某个区间时 | name, value | `{between name="$test" value="1,2"}{/between}`
| nobetween | 变量不存在某个区间时 | name, value | `{nobetween name="$test" value="1,2"}{/nobetween}`
| in | 变量存在数组时 | name, value | `{in name="$test" value="1,2"}{/in}`
| noin | 变量不存在数组时 | name, value | `{noin name="$test" value="1,2"}{/noin}`
| page | 单页信息| class 分类id, id 调用的变量名 | `{page class="$class_id" id="page"}{$page.content}{/page}`
| progress | 获取文章阅读进度 | container, parent, child, class | [详细说明](#文章阅读进度)

### 文章阅读进度
参数
* `container` 包着文章内容的根 （id, class） 例如 .back-to-top
* `parent` 包着百分比的根（id, class） 例如 .back-to-top
* `child` 包着百分比的标签 例如 span
* `class` 当页面浏览到文章内容内，加的类名 例如 on
```
  <div class="g-bd">
    文章内容
  </div>
  {progress container=".g-bd" parent=".back-to-top" child="span" class="on"}
    <div class="back-to-top" style="position: fixed;top:50">
      <span></span>
    </div>
  {/progress}
```
## 分词功能
由于http://keyword.discuz.com 出现403(应该关服务了)

使用[@梁斌penny](https://weibo.com/pennyliang)的分词服务, 使用此项目的'模板工'们，你们应该感谢梁厂长！

## 多条件筛选

### 后台
1. 后台创建扩展模型 
2. 添加【下拉菜单】类型的字段， 【字段配置】用逗号分隔 例如创建一个码数的字段 字段配置：m,xl,xxl

### 前台
`$duowei`数组  
`$v['selected']`当前属性是否已选择  
`$v['url']` 当前属性的url 相当于 使用当前属性进行筛选  
`$v['durl']` 当前属性的url 相当于 不使用当前属性进行筛选  
```
<!--foreach{$duowei as $vo}-->
  <ul>
    <li>
      <a href="#">{$vo.name}</a>
      {noempty name="$vo['child']"}
      <ul>
        <!--foreach{$vo['child'] as $v}-->
          <!--if{$v['selected']==true}-->
            <a href="{$v.durl}" class="on">
              {$v.name}
            </a>
          <!--{/if}-->

          <!--if{$v['selected']==false}-->
            <a href="{$v.url}">
              {$v.name}
            </a>
          <!--{/if}-->
        <!--{/foreach}--> 
      </ul>
      {/noempty}
    </li>
  </ul>
  <!--{/foreach}--> 
```

## 伪静态 规则
* 列表页 `urlname` 栏目URL
* 列表详情页 `class_urlname` 上级分类栏目URL `urltitle` 当前页URL
* 单页面 `urlname` 当前页URL  

|名称 | 规则 | 对应类| 例子|
|----|----|----|----|
|列表页|`<urlname>`|`article/Category/index`|`'<urlname>/index.html' => 'article/Category/index'`
|列表详情页|`<class_urlname>` `<urltitle>`|`article/Content/index`|`'<class_urlname>/<urltitle>.html' => 'article/Content/index'`
|单页面|`<urlname>`|`page/Category/index`|`'page/<urlname>.html' => 'page/Category/index'`

## api 调用

GET `/api.php`

**注意区分大小写**  

| 参数 | 说明(参数范围)| 是否必传 | 
| ----|----|----|
| signature |  签名 | 是 |
| timestamp |  时间戳 | 是 |
| nonce | 随机字符串| 是 |
| app |  ['DuxCms', 'Article']| 是 |
| label | ['contentList', 'categoryList', 'tagsList'] | 是 |
| ... | 其它查询参数，与普通模板标签一样 | 否 |

签名生成参考
```
$timestamp = time();
$nonce = rand(1, 9999);
$token = '';

$params = [
  $token,
  $timestamp,   // timestamp 参数值
  $nonce,   // nonce 参数值
  'DuxCms', // app 参数值
  'categoryList' // label 参数值
];

sort($params, SORT_STRING);
$tmpStr = implode( $params );
$signature = sha1( $tmpStr );
```

组装 query 参数
```php
$host = 'http://www.domain.com';
$params = [
    // 上面生成的签名
    'signature' => $signature,
    // 时间戳
    'timestamp' => $timestamp,
    // 随机字符串
    'nonce' => $nonce,
    // app
    'app' => 'Duxcms',
    // label
    'label' => 'contentList'
];

$url = $host ."?" .http_build_query($params);
```

## 在线更新
为了不影响已有的文件，作了增量更新包。  
更新服务默认下载releases的Duxcms-update.zip压缩包，进行程序升级。  
[更新服务](https://github.com/xiaodit/duxcms-update)  
[检测地址](https://raw.githubusercontent.com/xiaodit/duxcms-update/master/ver.json)

## 打赏
如果对你有帮助的，打赏下小弟吧。  
<img src="https://raw.githubusercontent.com/xiaodit/duxcms-update/master/WechatIMG4.jpeg" title="微信" width="300">
