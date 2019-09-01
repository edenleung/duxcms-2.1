<?php
namespace app\admin\controller;

use app\admin\controller\AdminController;

/**
 * 登录页面
 */
class LoginController extends AdminController
{

    /**
     * 登录页面
     */
    public function index()
    {
        if (!IS_POST) {
            if (config('loginBackgroudImage')) {
                echo '
                    <style>
                        body {
                            background-image: url(https://open.saintic.com/api/bingPic) !important;
                            background-size: cover!important;
                        }
                        #loginBg {
                            position: fixed;
                            width: 100%;
                            height: 100%;
                            transition: all 3s cubic-bezier(0.175, 0.885, 0.32, 0.9) 0s;
                            background: rgb(0,153,204)
                        }
                    </style>
                    <script>
                        window.onload = function(){
                            document.getElementById("loginBg").style.background = "rgba(0,0,0, 0.5)"
                        }
                    </script>
                ';
            }
            $this->display();
        } else {
            $userName = request('post.username');
            $passWord = request('post.password');
            if (empty($userName)||empty($passWord)) {
                $this->error('用户名或密码未填写！');
            }
            //查询用户
            $map = array();
            $map['username'] = $userName;
            $userInfo = target('AdminUser')->getWhereInfo($map);
            if (empty($userInfo)) {
                $this->error('登录用户不能存在！');
            }
            if (!$userInfo['status']||!$userInfo['group_status']) {
                $this->error('该用户已被禁止登录！');
            }
            if ($userInfo['password']<>md5($passWord)) {
                $this->error('您输入的密码不正确！');
            }
            $model = target('AdminUser');
            if ($model->setLogin($userInfo['user_id'])) {
                $this->redirect(url('Index/index'));
            } else {
                $this->error($model->getError());
            }
        }
    }
    /**
     * 退出登录
     */
    public function logout()
    {
        target('AdminUser')->logout();
        session('[destroy]');
        $this->success('退出系统成功！', url('index'));
    }
}
