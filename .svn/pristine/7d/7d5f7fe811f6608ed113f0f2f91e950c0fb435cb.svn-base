<?php
class AdminSystemController extends BpfController
{
  public static function __permissions()
  {
    return array(
      'name' => '系统设置',
      'permissions' => array(
        'score-edit' => '积分设置',
        'open-edit' => '开放平台信息设置',
        'content-edit' => '内容管理',
        'email-edit' => '邮件模板管理',
        'jifenbao' => '集分宝发放管理',
      ),
    );
  }

  public function __init()
  {
    // 管理员权限判断
    if (!isLogin() || !isAdmin()) {
      throw new Bpf404Exception();
    } else if (!isAdminLogin()) {
      gotoUrl('admin/login');
    }
    $GLOBALS['user']->adminTime = REQUEST_TIME;
  }

  // 基本信息设置
  public function indexAction()
  {
    gotoUrl('admin/system/score');
  }

  // 积分设置
  public function scoreAction()
  {
    $configModel = $this->getModel('config');
    if ($this->isPost()) {
      if (isset($_POST['login']) && is_numeric($_POST['login'])) {
        $configModel->set('scores.login', max(intval($_POST['login']), 0));
      }
      if (isset($_POST['comment']) && is_numeric($_POST['comment'])) {
        $configModel->set('scores.comment', max(intval($_POST['comment']), 0));
      }
      if (isset($_POST['comment_top']) && is_numeric($_POST['comment_top'])) {
        $configModel->set('scores.comment_top', max(intval($_POST['comment_top']), 0));
      }
      if (isset($_POST['checkin']) && is_numeric($_POST['checkin'])) {
        $configModel->set('scores.checkin', max(intval($_POST['checkin']), 0));
      }
      if (isset($_POST['checkin_running']) && is_numeric($_POST['checkin_running'])) {
        $configModel->set('scores.checkin_running', max(intval($_POST['checkin_running']), 0));
      }
      if (isset($_POST['checkin_top']) && is_numeric($_POST['checkin_top'])) {
        $configModel->set('scores.checkin_top', max(intval($_POST['checkin_top']), 0));
      }
      if (isset($_POST['checkin_cycle']) && is_numeric($_POST['checkin_cycle'])) {
        $configModel->set('scores.checkin_cycle', max(intval($_POST['checkin_cycle']), 0));
      }
      if (isset($_POST['checkin_cycle_reward']) && is_numeric($_POST['checkin_cycle_reward'])) {
        $configModel->set('scores.checkin_cycle_reward', max(intval($_POST['checkin_cycle_reward']), 0));
      }
      if (isset($_POST['score_2jf']) && is_numeric($_POST['score_2jf'])) {
        $configModel->set('scores.2jf', max(intval($_POST['score_2jf']), 1));
      }
      if (isset($_POST['score_share']) && is_numeric($_POST['score_share'])) {
        $configModel->set('scores.share', max(intval($_POST['score_share']), 1));
      }if (isset($_POST['score_share_top']) && is_numeric($_POST['score_share_top'])) {
        $configModel->set('scores.share_top', max(intval($_POST['score_share_top']), 1));
      }
      $configModel->save();
      setMessage('积分设置成功', 'success');
      setMessage('更新缓存中，修改设置将在 10 秒内生效', 'warning');
      gotoUrl('admin/system/score');
    } else {
      $view = $this->getView();
      $view->assign(array(
        'login' => intval($configModel->get('scores.login', 1)),
        'comment' => intval($configModel->get('scores.comment', 1)),
        'comment_top' => intval($configModel->get('scores.comment_top', 10)),
        'checkin' => intval($configModel->get('scores.checkin', 1)),
        'checkin_running' => intval($configModel->get('scores.checkin_running', 1)),
        'checkin_top' => intval($configModel->get('scores.checkin_top', 20)),
        'checkin_cycle' => intval($configModel->get('scores.checkin_cycle', 7)),
        'checkin_cycle_reward' => intval($configModel->get('scores.checkin_cycle_reward', 5)),
        'score_2jf' => intval($configModel->get('scores.2jf', 1)),
        'score_share' => intval($configModel->get('scores.share', 1)),
        'score_share_top' => intval($configModel->get('scores.share_top', 1)),
      ));
      $view->display('admin/system/score.phtml');
    }
  }

  // 开放平台信息设置
  public function openAction()
  {
    $configModel = $this->getModel('config');
    if ($this->isPost()) {
      if (isset($_POST['appid'])) {
        $configModel->set('open.appid', trim($_POST['appid']));
      }
      if (isset($_POST['appkey'])) {
        $configModel->set('open.appkey', trim($_POST['appkey']));
      }
      if (isset($_POST['baidu'])) {
        $configModel->set('open.baidu', trim($_POST['baidu']));
      }
      if (isset($_POST['wpaqq'])) {
        $configModel->set('open.wpaqq', trim($_POST['wpaqq']));
      }
      $configModel->save();
      setMessage('开放平台信息设置成功', 'success');
      setMessage('更新缓存中，修改设置将在 10 秒内生效', 'warning');
      gotoUrl('admin/system/open');
    } else {
      $view = $this->getView();
      $view->assign(array(
        'appid' => $configModel->get('open.appid', ''),
        'appkey' => $configModel->get('open.appkey', ''),
        'baidu' => $configModel->get('open.baidu', ''),
        'wpaqq' => $configModel->get('open.wpaqq', ''),
      ));
      $view->display('admin/system/open.phtml');
    }
  }

  // 商家规则内容管理
  public function merchantAction($action = null)
  {
    if (!isset($action)) {
      gotoUrl('admin/system/content');
    }
    $configModel = $this->getModel('config');
    if ($this->isPost()) {
      if (isset($_POST['content']) && isset($_POST['title'])) {
        $set = array(
          'title' => $_POST['title'],
          'content' => $_POST['content'],
        );
        $configModel->set($action, json_encode($set));
      }
      $configModel->save();
      setMessage('设置成功', 'success');
      setMessage('更新缓存中，修改设置将在 10 秒内生效', 'warning');
      gotoUrl('admin/system/content');
    } else {
      $view = $this->getView();
      $content = $configModel->get($action);
      $view->assign(array(
        'content' => json_decode($content),
      ));
      $view->display('admin/system/merchant.phtml');
    }
  }

  //内容管理列表
  public function contentAction()
  {
    $view = $this->getView();
    $view->display('admin/system/content.phtml');
  }

  public function pageAction($action = null)
  {
    if (!isset($action)) {
      gotoUrl('admin/system/content');
    }
    $configModel = $this->getModel('config');
    if ($this->isPost()) {
      if (isset($_POST['content'])) {
        $configModel->set($action, $_POST['content']);
      }
      $configModel->save();
      setMessage('设置成功', 'success');
      setMessage('更新缓存中，修改设置将在 10 秒内生效', 'warning');
      gotoUrl('admin/system/content');
    } else {
      $view = $this->getView();
      $view->assign(array(
        'content' => $configModel->get($action, ''),
        'action' => $action,
      ));
      $view->display('admin/system/content_edit.phtml');
    }
  }

  public function emailAction()
  {
    $view = $this->getView();
    $view->display('admin/system/email.phtml');
  }

  public function emailmodAction($action = null)
  {
    if (!isset($action)) {
      gotoUrl('admin/system/email');
    }
    $configModel = $this->getModel('config');
    if ($this->isPost()) {
      if (isset($_POST['content']) && isset($_POST['title'])) {
        $configModel->set($action, array('content' => $_POST['content'] ,'title' => $_POST['title'] ));
      }
      $configModel->save();
      setMessage('模板设置成功', 'success');
      setMessage('更新缓存中，修改设置将在 10 秒内生效', 'warning');
      gotoUrl('admin/system/email');
    } else {
      $view = $this->getView();
      $value = $configModel->get($action, array());
      $view->assign(array(
        'content' => isset($value['content']) ? $value['content'] : '',
        'title' => isset($value['title']) ? $value['title'] : '',
        'action' => $action,
      ));
      $view->display('admin/system/email_edit.phtml');
    }
  }

  //集分宝发放及设置
  public function jifenbaoAction()
  {
    $cacheModel = $this->getModel('cache');
    $jifenbaoModel = $this->getModel('jifenbao');
    $users = $jifenbaoModel->getFaFangList();
    $view = $this->getView();
    $view->assign(array(
      'users' => $users,
      'jfSum' => $jifenbaoModel->getFaFangSum(),
    ));
    $token = $cacheModel->get('zhifubao_token');
    if(isset($token)) {
      $view->assign('token', $token);
    }
    $view->display('admin/system/jifenbao.phtml');
  }

   //通过code获取token
  public function getTokenAction()
  {
    $jifenbaoModel = $this->getModel('jifenbao');
    $token = $jifenbaoModel->getOauthToken($_GET['code']);
    if (empty($token)) {
      return json_encode(array('msg' => 'code无效'));
    }
    $cacheModel = $this->getModel('cache');
    $cacheModel->set('zhifubao_token', $token, 86400*3);
    return json_encode(array('token' => $token));
  }
  //查询账户余额
  public function chaXunYuEAction()
  {
    $jifenbaoModel = $this->getModel('jifenbao');
    if (empty($_POST['token'])) {
      return json_encode(array('sum' => 0));
    }
    $sum = $jifenbaoModel->chaXunYuE($_POST['token']);
    return json_encode(array('sum' => $sum));
  }

  //发放集分宝
  public function faFangAction()
  {
    if (empty($_POST['token'])) {
      return json_encode(array('msg' => '参数错误'));
    }
    $jifenbaoModel = $this->getModel('jifenbao');
    $users = $jifenbaoModel->getFaFangList();
    foreach ($users as $user) {
      var_dump( $jifenbaoModel->faFang($user, $_POST['token']));
    }
  }

  // SEO 设置
  public function seoAction()
  {

  }

  // 邮件设置
  public function mailAction()
  {

  }

  // 日志查询
  public function logAction()
  {

  }
}
