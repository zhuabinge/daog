<?php
class AdminLoginController extends BpfController
{
  public function __init()
  {
    // 管理员权限判断
    if (!isLogin() || !isAdmin()) {
      throw new Bpf404Exception();
    }
  }

  public function indexAction()
  {
    global $user;
    $view = $this->getView();
    $userModel = $this->getModel('user');

    if ($this->isPost()) {
      $captchaModel = $this->getModel('captcha');
      if (!$captchaModel->checkCode('admin_login_code', $_POST['code'])) {
        $view->assign('themeMessages', '验证码无效');
      } else if ($_POST['password'] == '') {
        $view->assign('themeMessages', '密码不能为空');
      } else if ($userModel->authenticate($user->username, $_POST['password'])) {
        //TODO判断rid == 1

        $user->adminTime = REQUEST_TIME;
        gotoUrl('admin');
      } else {
        $view->assign('themeMessages', '密码输入错误');
      }
    } else if (isAdminLogin()) {
      gotoUrl('admin');
    }

    $this->addCss('css/login.css');
    $view->assign('user', $user);
    $view->display('admin/login.phtml');
  }

  public function captchaAction()
  {
    $captchaModel = $this->getModel('captcha');
    $key = 'admin_login_code';
    $captchaModel->buildCode($key);
    $captchaModel->display($key, 140, 32);
  }
}
