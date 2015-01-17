<?php
class UserController extends BpfController
{
  // load 顶部登陆
  public function loginInfoAction()
  {
    $view = $this->getView();
    $view->display('user/login_info.phtml');
  }

  // 退出登录
  public function logoutAction()
  {
    global $user;
    session_destroy();
    gotoUrl('');
  }

  // 普通页面登陆
  public function loginAction()
  {
    if (isLogin()) {
      gotoUrl('');
    }
    $userModel = $this->getModel('user');
    $view = $this->getView();
    if (!$this->isPost()) { // 记录前一个页面
      $_SESSION['referer'] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
    }
    if ($this->isPost()) {
      if ($_POST['username'] == '' && $_POST['password'] == '') {
        $view->assign('loginMsg', '用户名或密码不能为空');
      } else if ($userResult = $userModel->authenticate($_POST['username'], $_POST['password'])) {
        global $user;
        $user = $userResult;
        $userModel->loginLog($user);
        $user->permissions =  $userModel->getUserPermissions($user->uid);
        $user->roles = $userModel->getUserRoles($user->uid);
        if ($user->roles) {
          $merchantRoleId = BpfConfig::get('merchant.rid');
          foreach ($user->roles as $row) {
            if ($row->rid == 1) {
              $user->admin = true;
              $user->adminTime = REQUEST_TIME;
            } else if ($row->rid == $merchantRoleId) {
              $user->merchant = true;
              break;
            }
          }
        }
        $url = url('', true); // 网站路径
        $referer = $_SESSION['referer']; // 网站前一个页面
        if (isset($referer) && strstr($referer, $url)) {
          unset($_SESSION['referer']);
          gotoUrl($referer);
        } else {
          gotoUrl('');
        }
      } else {
        $view->assign('loginMsg', '用户名或密码输入错误');
      }
    }
    $view->assign('username', isset($_POST['username']) ? $_POST['username'] : '');
    $view->assign('login', true);
    $view->display('user/login.phtml');
  }

  // 普通页面注册
  public function registerAction()
  {
    if (isLogin()) {
      gotoUrl('');
    }
    $view = $this->getView();
    if ($this->isPost()) {
      $captchaModel = $this->getModel('captcha');
      if (!$captchaModel->checkCode('register_code', $_POST['captcha'])) {
        $view->assign('captchError', '验证码无效');
      } else if (empty($_POST['password']) || empty($_POST['username']) || empty($_POST['repassword'])) {
        $view->assign('msg', '参数错误');
      } else if (preg_match('/\w{0,16}/', $_POST['username']) && preg_match('/\w{6,20}/', $_POST['password']) && (empty($_POST['email']) || preg_match("/\w+@(\w|\d)+\.\w{2,3}/i", $_POST['email']))) {
        $userModel = $this->getModel('user');
        if ($userModel->getUserByUserName($_POST['username'])) {
          $view->assign('userError', '该用户名已被注册');
        } else {
          $set = array(
            'username' => $_POST['username'],
            'password' => $_POST['password'],
            'email' => isset($_POST['email'])? $_POST['email']: null,
          );
          $uid = $userModel->insertUser($set);
          if ($uid) {
            global $user;
            $user = $userModel->getUser($uid);
            $userModel->loginLog($user);
            $user->permissions =  $userModel->getUserPermissions($user->uid);
            $user->roles = $userModel->getUserRoles($user->uid);
            if ($user->roles) {
              foreach ($user->roles as $row) {
                if ($row->rid == 1) {
                  $user->admin = true;
                  $user->adminTime = REQUEST_TIME;
                  break;
                }
              }
            }
            gotoUrl('');
          } else {
            $view->assign('msg', '注册不成功');
          }
        }
      } else {
        $view->assign('msg', '参数格式错误');
      }
    }
    $view->assign('username', isset($_POST['username'])? $_POST['username']: '');
    $view->assign('email', isset($_POST['email'])? $_POST['email']: '');
    $this->addJs('js/plugins/passwordcheck/passwdcheck.js');
    $view->display('user/login.phtml');
  }

  // load 登录
  public function loginLoadAction()
  {
    if (isLogin()) {
      gotoUrl('');
    }
    $userModel = $this->getModel('user');
    $view = $this->getView();
    if ($this->isPost()) {
      if ($_POST['username'] == '' && $_POST['password'] == '') {
        return json_encode(array('msg' => -1));
      } else if ($userResult = $userModel->authenticate($_POST['username'], $_POST['password'])) {
        global $user;
        $user = $userResult;
        $userModel->loginLog($user);
        $user->permissions =  $userModel->getUserPermissions($user->uid);
        $user->roles = $userModel->getUserRoles($user->uid);
        if ($user->roles) {
          $merchantRoleId = BpfConfig::get('merchant.rid');
          foreach ($user->roles as $row) {
            if ($row->rid == 1) {
              $user->admin = true;
              $user->adminTime = REQUEST_TIME;
            } else if ($row->rid == $merchantRoleId) {
              $user->merchant = true;
              break;
            }
          }
        }
        return json_encode(array('msg' => 1));
      } else {
        return json_encode(array('msg' => -1));
      }
    }
    $view->assign('username', isset($_POST['username']) ? $_POST['username'] : '');
    $view->assign('login', true);
    $view->display('user/login_load.phtml');
  }

  // load 注册
  public function registerLoadAction()
  {
    if (isLogin()) {
      gotoUrl('');
    }
    $view = $this->getView();
    if ($this->isPost()) {
      $captchaModel = $this->getModel('captcha');
      if (!$captchaModel->checkCode('register_code', $_POST['captcha'])) {
        return json_encode(array('msg' => -2));
      } else if (empty($_POST['password']) || empty($_POST['username']) || empty($_POST['repassword'])) {
        return json_encode(array('msg' => -1));
      } else if (preg_match('/\w{0,16}/', $_POST['username']) && preg_match('/\w{6,20}/', $_POST['password']) && (empty($_POST['email']) || preg_match("/\w+@(\w|\d)+\.\w{2,3}/i", $_POST['email']))) {
        $userModel = $this->getModel('user');
        if ($userModel->getUserByUserName($_POST['username'])) {
          return json_encode(array('msg' => -3));
        } else {
          $set = array(
            'username' => $_POST['username'],
            'password' => $_POST['password'],
            'email' => isset($_POST['email'])? $_POST['email']: null,
          );
          $uid = $userModel->insertUser($set);
          if ($uid) {
            global $user;
            $user = $userModel->getUser($uid);
            $userModel->loginLog($user);
            $user->permissions =  $userModel->getUserPermissions($user->uid);
            $user->roles = $userModel->getUserRoles($user->uid);
            if ($user->roles) {
              foreach ($user->roles as $row) {
                if ($row->rid == 1) {
                  $user->admin = true;
                  $user->adminTime = REQUEST_TIME;
                  break;
                }
              }
            }
            return json_encode(array('msg' => 1));
          } else {
            return json_encode(array('msg' => -1));
          }
        }
      } else {
        return json_encode(array('msg' => -1));
      }
    }
    $view->assign('username', isset($_POST['username'])? $_POST['username']: '');
    $view->assign('email', isset($_POST['email'])? $_POST['email']: '');
    $this->addJs('js/plugins/passwordcheck/passwdcheck.js');
    $view->display('user/login_load.phtml');
  }

  //注册验证码
  public function captchaAction()
  {
    $captchaModel = $this->getModel('captcha');
    $key = 'register_code';
    $captchaModel->buildCode($key);
    $captchaModel->display($key, 140, 32);
  }

  // 忘记密码
  public function forgotAction()
  {
    if (isLogin()) {
      gotoUrl('');
    }
    $view = $this->getView();
    $configModel = $this->getModel('config');
    $step = 'step1';
    $msg = '';
    if ($this->isPost()) {
      if (!empty($_POST['username'])) {
        $userModel = $this->getModel('user');
        $user = $userModel->getUserByUserName($_POST['username']);
        if (isset($user->email)) {
          $mailModel = $this->getModel('mail');
          $code = randomString(32);
          $userModel->updateUser($user->uid, array('code' =>  $code));
          $emailmod = $configModel->get('password', array());
          //替换修改密码的连接{change_password_url}
          //说明：富文本编辑器中a标签会自动加上http://代码的，所以为了避免重复，要连http也要删除掉。
          $econtent = str_replace("http://{change_password_url}",
            url('user/changePassword', true).'?code=' . $code, $emailmod['content']);
          //如果页面上非链接的地方加上了http://的时候就不用，否则就要替换
          // $econtent = str_replace("{change_password_url}",
          //   url('user/changePassword', true).'?code=' . $code, $econtent);
          //域名链接{domain_url}
          $econtent = str_replace("http://{domain_url}",
            url('', true), $econtent);
          $mailModel->send($user->email, $emailmod['title'], $econtent);
          $view->assign('username', $user->username);
          $view->assign('email', $user->email);
          $step = 'step2';
        } else {
         $msg = '该用户未绑定邮箱';
        }
      } else {
        $msg = '用户名不能为空';
      }
    }
    $view->assign('step', $step);
    $view->assign('msg', $msg);
    $view->display('user/forget.phtml');
  }

  //忘记密码后的修改页面
  public function changePasswordAction()
  {
    if (isLogin()) {
      gotoUrl('');
    }
    $msg = '';
    $view = $this->getView();
    if($this->isPost()) {
      if (empty($_POST['code']) || empty($_POST['username']) || empty($_POST['password']) || $_POST['password'] !==  $_POST['repassword']) {
        $msg = '参数出错，请正确填写表单';
      } else {
        $userModel = $this->getModel('user');
        $user = $userModel->getUserByUserName($_POST['username']);

        if ($user) {
          if ($user->code === $_POST['code']) {
            $userModel->updateUserPassword($user->uid, $_POST['password']);
            $view->assign('success', true);
          } else {
            $msg = 'code不匹配，请重新发送邮件获取';
          }
        } else {
          $msg = 'code已经失效，请重新发送邮件获取';
        }
      }
    }
    $view->assign('msg', $msg);
    $view->assign('code', isset($_GET['code'])? $_GET['code']: '');
    $this->addJs('js/plugins/passwordcheck/passwdcheck.js');
    $view->display('user/change_password.phtml');
  }

  // 邮件QQ登陆
  public function emailLoginAction()
  {
    if (isLogin()) {
      gotoUrl('user/checkin?code=' . randomString(32) . '&status=1');
    } else {
      $configModel = $this->getModel('config');
      $client_id =  $configModel->get('open.appid', '');
      $redirect_uri = urlencode(url('user/emailLoginCallback', true));
      $scope = 'get_user_info,add_share,check_page_fans,add_t,add_topic,add_idol';
      $url = 'https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id=' . $client_id;
      $url = $url . '&redirect_uri=' . $redirect_uri . '&scope=' . $scope;
      gotoUrl($url);
    }
  }

  // 邮件QQ登陆回调
  public function emailLoginCallbackAction()
  {
    if (!empty($_GET['code'])) {
      $userModel = $this->getModel('user');
      $qqToken = $userModel->getQqToken($_GET['code']);
      if ($qqToken) {
        $openId = $userModel->getQqOpenID($qqToken);
        $qqUser = $userModel->getQqUser($openId, $qqToken);
        //qq用户登录
        if (!empty($qqUser) && $qqUser->status == 1) {
          global $user;
          $user = $qqUser;
          //保存用户qq
          if (!empty($_GET['qq']) && is_numeric($_GET['qq'])) {
            $qq = intval($_GET['qq'] ,10);
            $userModel->updateUser($user->uid, array(
              'qq' => $qq,
              'email' => $qq . '@qq.com',
            ));
          }
          $userModel->loginLog($user);
          $user->permissions =  $userModel->getUserPermissions($user->uid);
          $user->roles = $userModel->getUserRoles($user->uid);
          if ($user->roles) {
            foreach ($user->roles as $row) {
              if ($row->rid == 1) {
                $user->admin = true;
                $user->adminTime = REQUEST_TIME;
                break;
              }
            }
          }
          gotoUrl('user/checkin?code=' . randomString(32) . '&status=1');
        } else {
          gotoUrl('/user/qqLogin');
        }
      } else {
        gotoUrl('/user/emailLogin');
      }
    } else {
      gotoUrl('/user/emailLogin');
    }
  }

  // 邮件QQ积分领取
  public function jfreceiveAction()
  {
    if ($this->isPost()) {
      if (isLogin()) {
        $userModel = $this->getModel('user');
        $users = $userModel->getUser($GLOBALS['user']->uid);
        $userData = unserialize($users->data); // 反序列化
        $jf = rand(10,12);
        if (isset($users) && empty($userData['email_receive']) && $userModel->updateUser($users->uid, array('data' => serialize(array('email_receive' => 1)))) && $userModel->updateUserScores($users->uid, $jf)) {
          // 积分记录
          $userModel->insertScoresLog($users->uid, 'email', $jf , '邮件礼包，赚取 ' . $jf . ' 个积分');
          $userCount = $userModel->getUsersScoresLogsCount('email');
          return json_encode(array(
            'result' => $jf,
            'count' => ($userCount + 15472),
          ));
        } else {
          return json_encode(array(
            'result' => -1,
          ));
        }
      } else {
        return json_encode(array(
          'result' => -1,
        ));
      }
    }
  }

  //qq互联
  public function qqLoginAction()
  {
    $configModel = $this->getModel('config');
    $client_id =  $configModel->get('open.appid', '');
    $redirect_uri = urlencode(url('user/qqLoginCallback', true));
    $scope = 'get_user_info,add_share,check_page_fans,add_t,add_topic,add_idol';
    $url = 'https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id=' . $client_id;
    $url = $url . '&redirect_uri=' . $redirect_uri . '&scope=' . $scope;
    gotoUrl($url);
  }

  //qq互联
  public function qqLoginCallbackAction()
  {
    global $user;
    if (!empty($_GET['code'])) {
      $userModel = $this->getModel('user');
      $qqToken = $userModel->getQqToken($_GET['code']);
      if ($qqToken) {
        $openId = $userModel->getQqOpenID($qqToken);
        //进行用户qq绑定授权
        if (isLogin() && isset($openId) && empty($user->openId)) {
          $result = $userModel->checkOpenId($openId);
          if (empty($result)) {
            $userModel->updateUser($user->uid, array(
              'qq_token' => $qqToken ,
              'openid' => $openId,
            ));
            $user->openid = $openId;
            $user->qq_token = $qq_token;
          } else {
            $_SESSION['qqbindMsg'] = '该QQ已被绑定';
          }
          gotoUrl('/user/center');
        }

        $qqUser = $userModel->getQqUser($openId, $qqToken);
        //qq用户登录
        if (!empty($qqUser) && $qqUser->status == 1) {
          global $user;
          $user = $qqUser;
          //保存用户qq
          if (!empty($_GET['qq']) && is_numeric($_GET['qq'])) {
            $qq = intval($_GET['qq'] ,10);
            $userModel->updateUser($user->uid, array(
              'qq' => $qq,
              'email' => $qq . '@qq.com',
            ));
          }
          $userModel->loginLog($user);
          $user->permissions =  $userModel->getUserPermissions($user->uid);
          $user->roles = $userModel->getUserRoles($user->uid);
          if ($user->roles) {
            foreach ($user->roles as $row) {
              if ($row->rid == 1) {
                $user->admin = true;
                $user->adminTime = REQUEST_TIME;
                break;
              }
            }
          }

          // 返回上一个页面
          $url = url('', true); // 网站路径
          if (isset($_SESSION['referer']) && strstr($_SESSION['referer'], $url)) {
            $url = $_SESSION['referer'];
            unset($_SESSION['referer']);
            gotoUrl($url);
          } else {
            gotoUrl('?code=' . $_GET['code']);
          }
        } else {
          gotoUrl('/user/qqLogin');
        }
      } else {
        gotoUrl('/user/qqLogin');
      }
    } else {
      gotoUrl('/user/qqLogin');
    }
  }

  //账号安全
  public function safeAction()
  {
    global $user;
    if (!isLogin()) {
      gotoUrl('user/login');
    }
    $view = $this->getView();
    if ($this->isPost()) {
      if (!empty($_POST['username']) && !empty($_POST['uid']) && is_numeric($_POST['uid'])) {
          $userModel = $this->getModel('user');
          $username = $_POST['username'];
          if (!$userModel->getUserByUserName($username)) {
            $set = array();
            $set['username_can_change'] = 0;
            $set['username'] = $username;
            if (!empty($_POST['email'])) {
              $set['email'] = $_POST['email'];
            }
            if (!empty($_POST['alipay'])) {
              $set['alipay'] = $_POST['alipay'];
            }
            if (!empty($_POST['npassword']) || !empty($_POST['rnpassword'])) {
              if (!empty($_POST['rnpassword']) && !empty($_POST['npassword']) && $_POST['rnpassword'] === $_POST['npassword']) {
                $userModel->updateUserPassword($user->uid, $_POST['rnpassword']);
              } else {
                setMessage('新密码两次不相同', 'error');
                gotoUrl('user/safe');
              }
            } else {
              setMessage('密码不能为空', 'error');
              gotoUrl('user/safe');
            }
            $userModel->updateUser($_POST['uid'], $set);
            $u = $userModel->getUser($user->uid);
            foreach ($u as $key => $value) {
              $user->$key = $value;
            }
            setMessage('设置账号成功', 'error');
          } else {
            setMessage('该用户名已经存在喔！！', 'error');
          }
      } else if (empty($_POST['opassword'])) {
        setMessage('请输入当前密码进行确认', 'error');
      } else {
        $userModel = $this->getModel('user');
        if ($userResult = $userModel->authenticate($user->username, $_POST['opassword'])){
          $set = array();
          if (!empty($_POST['email'])) {
            $set['email'] = $_POST['email'];
          }
          if (!empty($_POST['alipay'])) {
            $set['alipay'] = $_POST['alipay'];
          }
          if (!empty($_POST['npassword']) || !empty($_POST['rnpassword'])) {
            if (!empty($_POST['rnpassword']) && !empty($_POST['npassword']) && $_POST['rnpassword'] === $_POST['npassword']) {
              $userModel->updateUserPassword($user->uid, $_POST['rnpassword']);
            } else {
              setMessage('新密码两次不相同', 'error');
              gotoUrl('user/safe');
            }
          }
          if (!empty($set)) {
            $userModel->updateUser($user->uid, $set);
            $u = $userModel->getUser($user->uid);
            foreach ($u as $key => $value) {
              $user->$key = $value;
            }
            setMessage('修改成功');
          }
        } else {
          setMessage('当前密码错误！！', 'error');
        }
      }
      gotoUrl('user/safe');
    }
    $view->assign(array(
      'user' => $user,
      'now' => REQUEST_TIME,
    ));
    $this->addCss('css/users.css');
    $this->addJs('js/plugins/passwordcheck/passwdcheck.js');
    $view->display('user/safe.phtml');
  }

  //个人中心
  public function centerAction()
  {
    global $user;
    if (!isLogin()) {
      gotoUrl('/user/login');
    }
    $userModel = $this->getModel('user');
    $user->like = $userModel->getUserLikesCount($user->uid);
    $view = $this->getView();
    $view->assign(array(
      'user' => $user,
    ));
    $qqbindMsg = isset($_SESSION['qqbindMsg'])? $_SESSION['qqbindMsg']: null;
    unset($_SESSION['qqbindMsg']);
    $view->assign('qqbindMsg', $qqbindMsg);
    $this->addCss('css/users.css');
    $this->addJs('js/plugins/select2/select2.min.js');
    $this->addJs('js/plugins/fileupload/jquery.ui.widget.js');
    $this->addJs('js/plugins/fileupload/jquery.fileupload.js');
    $this->addCss('css/plugins.css');
    $view->display('user/user_center.phtml');
  }

  //上传用户头像
  public function uploadAvatarAction()
  {
    $set = array('msg' => '0' );
    $avatar = $_FILES['avatar_path'];
    if (isset($_FILES['avatar_path']) && is_array($_FILES['avatar_path'])) {
      $fileModel = $this->getModel('file');
      $fileName = date('Ymd/', REQUEST_TIME) . $_FILES['avatar_path']['name'];
      $fileContent = file_get_contents($_FILES['avatar_path']['tmp_name']);
      $file = $fileModel->write('avatar', $fileName, $fileContent);
      if (isset($file) && $file) {
        $set['file_id'] = $file->file_id;
        $set['file_path'] = $file->file_path;
        $set['msg'] = '1';
      }
    }
    return json_encode($set);
  }

  //更新用户
  public function updateAction()
  {
    if (isLogin()) {
      global $user;
      $set = array();
      if (isset($_POST['emotion'])) {
        $set['emotion'] = $_POST['emotion'];
      }
      if (isset($_POST['nickname'])) {
        $set['nickname'] = $_POST['nickname'];
        if (empty($_POST['nickname'])) {
          $set['nickname'] = $user->username;
        }
      }
      if (isset($_POST['sex'])) {
        $set['sex'] = $_POST['sex'];
      }
      if (isset($_POST['qq'])) {
        $set['qq'] = $_POST['qq'];
      }
      if (isset($_POST['weibo'])) {
        $set['weibo'] = $_POST['weibo'];
      }
      if (isset($_POST['city'])) {
        $set['city'] = $_POST['city'];
      }
      if (!empty($_POST['year']) && is_numeric($_POST['year'])) {
        $set['birthday'] = $_POST['year'];
        if(!empty($_POST['month']) && is_numeric($_POST['month'])) {
          $set['birthday'] .= sprintf('%02d', $_POST['month']);
        }
      }
      $userModel = $this->getModel('user');
      // 保存用户头像
      if (isset($_POST['avatar_id'])) {
        if($userModel->setUserAvatar($user->uid, $_POST['avatar_id'])) {
          $u = $userModel->getUser($user->uid);
          foreach ($u as $key => $value) {
            $user->$key = $value;
          }
        }
      }
      if (!empty($set)) {
        $userModel->updateUser($user->uid, $set);
        $u = $userModel->getUser($user->uid);
        foreach ($u as $key => $value) {
          $user->$key = $value;
        }
      }
    }
    gotoUrl('user/center');
  }

  // 用户首页
  public function infoAction($userId = null)
  {
    if (!preg_match('/^([a-f0-9]{8})\.html$/', $userId, $matches)) {
      return BPF_NOT_FOUND;
    }
    $userId = intval(hexdec($matches[1])) - 33445567;
    $userModel = $this->getModel('user');
    if ($userId <= 0 || !$user = $userModel->getUser($userId)) {
      return BPF_NOT_FOUND;
    }
    $user->like = $userModel->getUserLikesCount($user->uid);
    $view = $this->getView();
    $view->assign('user', $user);
    $this->addCss('css/users.css');
    $this->addJs('js/plugins/select2/select2.min.js');
    $this->addCss('css/plugins.css');
    $view->display('user/info.phtml');
  }

  // 签到
  public function checkinAction()
  {
    $userModel = $this->getModel('user');
    $productModel = $this->getModel('product');
    $configModel = $this->getModel('config');
    $user = $this->getUser();
    $userId = $user->uid;
    $today = date('Ymd', REQUEST_TIME);
    $checkinCode = substr(md5($today . $userId), 10, 6);
    if ($this->isPost()) {
      // 执行签到
      if (isset($_POST['code']) && $_POST['code'] == $checkinCode) {
        $result = $userModel->checkin($userId, $today);
        return json_encode(array(
          'result' => $result,
        ));
      } else {
        return json_encode(array(
          'result' => -1,
        ));
      }
    } else {
      // 签到页面
      if (isset($_GET['m'])) {
        $year = intval(substr($_GET['m'], 0, 4));
        $month = intval(substr($_GET['m'], 4));
        if ($year < 2014 || $year > date('Y', REQUEST_TIME)) {
          $year = date('Y', REQUEST_TIME);
          $month = date('n', REQUEST_TIME);
        } else if ($month < 1 || $month > 12) {
          $month = date('n', REQUEST_TIME);
        }
        $day = 1;
        $date = mktime(0, 0, 0, $month, 1, $year);
        $hasNext = date('Yn', REQUEST_TIME) != $year . $month;
      }
      if (!isset($_GET['m']) || !$hasNext || $date > REQUEST_TIME) {
        $year = date('Y', REQUEST_TIME);
        $month = date('n', REQUEST_TIME);
        $day = date('j', REQUEST_TIME);
        $date = REQUEST_TIME;
        $hasNext = false;
      }
      $firstDayWeek = date('w', mktime(0, 0, 0, $month, 1, $year));
      $lastDay = date('t', $date);
      $checkins = $userModel->getCheckins($userId, sprintf('%s%02d', $year, $month));

      if (isset($checkins[$today])) { //计算奖励日
        $reward = intval($configModel->get('scores.checkin_cycle', 7)) - ($checkins[$today]->running % intval($configModel->get('scores.checkin_cycle', 7)));
      }
      // $hotPros = $productModel->getProducts(array('orderby' => 'views DESC','where'=> array('status' => 1)), 1, 4);
      //类目
      $cates = $productModel->getHomeCategories();
      $view = $this->getView();
      $view->assign('cates', $cates);
      $view->assign(array(
        'year' => $year,
        'month' => $month,
        'today' => $today,
        'hasNext' => $hasNext,
        'nextMonth' => $month == 12 ? array($year + 1, 1) : array($year, $month + 1),
        'prevMonth' => $month == 1 ? array($year - 1, 12) : array($year, $month - 1),
        'day' => $hasNext ? 32 : $day,
        'firstDayWeek' => $firstDayWeek,
        'lastDay' => $lastDay,
        'checkins' => $checkins,
        'reward' => isset($reward) ? $reward : 7,
        'userCheckins' => $userModel->getUserCheckinsByDate(null, 24),
        'userCheckinRunning' => $userModel->getCheckinRunning($userId) + (isset($checkins[date($today)]) ? 1 : 0),
        // 'hotPros' => $hotPros,
        'checkinCode' => $checkinCode,
      ));
      $view->display('user/checkin.phtml');
    }
  }

  //用户收藏产品列表
  public function likesAction($userId = null)
  {
    if (!empty($userId)) {
      if (!preg_match('/^([a-f0-9]{8})\.html$/', $userId, $matches)) {
        return BPF_NOT_FOUND;
      }
      $userId = intval(hexdec($matches[1])) - 33445567;
      $userModel = $this->getModel('user');
      if ($userId <= 0 || !$user = $userModel->getUser($userId)) {
        return BPF_NOT_FOUND;
      }
      $id = $user->uid;
      $other = $user; // TA的喜欢
    } else if (isLogin() && $userId === null) {
      $id = $GLOBALS['user']->uid;
      $other = null;
    } else {
      gotoUrl('user/login');
    }

    $userModel = $this->getModel('user');
    $productModel = $this->getModel('product');
    $likes = $userModel->getUserLikes($id);//获取用户的收藏
    foreach ($likes as $like) {
      $like->users_like_count = $productModel->getProductLikesCount($like->pid);
      $like->zhe = $like->list_price != 0 ? (round($like->sell_price / $like->list_price, 2) * 10) : 0;
      $pid[] = $like->pid;
    }
    $productModel = $this->getModel('product');
    $tags = $productModel->getProductsTags(isset($pid) ? $pid:'');
    $count = $userModel->getUserLikesCount($id);
    $view = $this->getView();
    $view->assign(array(
      'other' => $other,
      'count' => $count,
      'products' => isset($likes) ? $likes:'',
      'tags' =>$tags,
    ));
    $this->addCss('css/users.css');
    $view->display('user/likes.phtml');
  }

  //用户添加收藏
  public function addlikesAction()
  {
    if (!isLogin()) {
      return 'flase';
    };
    if (isset($_POST['pid']) && $this->isPost()) {
      $productIds = $_POST['pid'];
      $uid = $GLOBALS['user']->uid;
      $userModel = $this->getModel('user');
      $userModel->doLikes($uid, array($productIds));
    } else {
      return BPF_NOT_FOUND;
    }
  }

  //用户删除收藏
  public function deletelikesAction()
  {
    if (!isLogin()) {
      return 'flase';
    }
    if (isset($_POST['pid']) && $this->isPost()) {
      $productIds = $_POST['pid'];
      $uid = $GLOBALS['user']->uid;
      $userModel = $this->getModel('user');
      $userModel->unLikes($uid, array($productIds));
    } else {
      return BPF_NOT_FOUND;
    }
  }

  //用户积分
  public function scoreAction($userId = null)
  {
    if (!empty($userId)) {
      if (!preg_match('/^([a-f0-9]{8})\.html$/', $userId, $matches)) {
        return BPF_NOT_FOUND;
      }
      $userId = intval(hexdec($matches[1])) - 33445567;
      $userModel = $this->getModel('user');
      if ($userId <= 0 || !$user = $userModel->getUser($userId)) {
        return BPF_NOT_FOUND;
      }
      $user = $user;
      $other = $user; // TA的积分
    } else if (isLogin() && $userId === null) {
      global $user;
      $other = null;
    } else {
      gotoUrl('user/login');
    }

    $userModel = $this->getModel('user');
    foreach ($userModel->getUser($user->uid) as $key => $value) {
      $user->$key = $value;
    }
    $configModel = $this->getModel('config');
    $rate = $configModel->get('scores.2jf', 1);
    $view = $this->getView();
    $view->assign('other', $other);
    $view->assign('rate', $rate);
    $view->assign('now', REQUEST_TIME);
    $this->addCss('css/users.css');
    $view->display('user/score.phtml');
  }

  //用户积分兑换
  public function score2jfAction()
  {
    $result = array();
    $result['code'] = '-1';
    $result['msg'] = '参数错误';
    if (!isLogin()) {
      $result['msg'] = '未登录';
      return json_encode($result);
    }
    global $user;
    if($this->isPost()) {
      if(!empty($_POST['much']) && is_numeric($_POST['much'])){
        $much = intval($_POST['much']);
        if ($much > $user->scores){
         $result['msg'] = '兑换的数目有点多喔！！';
        } else {
          $userModel = $this->getModel('user');
          $configModel = $this->getModel('config');
          $rate = $configModel->get('scores.2jf', 1);
          $jf = $much * intval($rate);
          $userModel->updateUserScores($user->uid, -$much);
          $userModel->updateUserJfs($user->uid, $jf);
          $userModel->insertScoresLog($user->uid, 'scores2jf', -$much, '集分宝兑换成功，消耗 ' . $much . ' 个积分');
          $u = $userModel->getUser($user->uid);
          foreach ($u as $key => $value) {
            $user->$key = $value;
          }
          $result['code'] = '-2';
          $result['msg'] = '兑换成功！！';
        }
      }
    }
    return json_encode($result);
  }

  //用户积分日志
  public function scoreLogsAction($page = 1, $userId = null)
  {
    $userModel = $this->getModel('user');
    if (!empty($userId)) {
      if ($userId <= 0 || !$user = $userModel->getUser($userId)) {
        return BPF_NOT_FOUND;
      }
    }
    $view = $this->getView();
    $rows = 10;
    if (empty($userId)) {
      $userId = $GLOBALS['user']->uid;
    }
    $scoresLogs = $userModel->getScoresLogs($userId, $page, $rows);
    $total = $userModel->getScoresLogsCount($userId);

    $view->assign('scoresLogs', empty($scoresLogs) ? array() : $scoresLogs);
    $view->assign('page', $page);
    $view->assign('rows', $rows);
    $view->assign('total', empty($total) ? 0 : $total);
    $view->display('user/scorelogs.phtml');
  }

  // 用户中心_我的浏览轨迹
  public function trajectoryAction($userId = null)
  {
    $userModel = $this->getModel('user');
    $productModel = $this->getModel('product');
    $view = $this->getView();
    $size = 3;
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;

    if (!empty($userId)) {
      if (!preg_match('/^([a-f0-9]{8})\.html$/', $userId, $matches)) {
        return BPF_NOT_FOUND;
      }
      $view->assign('userId',$userId);
      $userId = intval(hexdec($matches[1])) - 33445567;
      $userModel = $this->getModel('user');
      if ($userId <= 0 || !$user = $userModel->getUser($userId)) {
        return BPF_NOT_FOUND;
      }
      $other = $user; // TA的浏览
      $userId = $user->uid;
      $trajectorys = $userModel->getUserTrajectorys($userId,5,$page,$size);//查询用户浏览轨迹
       //遍历用户浏览轨迹
      foreach($trajectorys as $value){ 
        $value->date =  $value->_id->year.'-' .$value->_id->month .'-'. $value->_id->day;
        foreach ($value->items as $va) {
          $va->products = $productModel->getProduct($va->pid);
          unset($va->products->body);
          $va->likes = $productModel->getProductLikesCount($va->pid);//循环获取产品用户收藏总数
        }
      } 
    } else if (isLogin() && $userId === null) { //我的浏览
      global $user;
      $other = null;
      $userId = $user->uid;
      $trajectorys = $userModel->getUserTrajectorys($userId,5,$page,$size);//查询用户浏览轨迹
      foreach($trajectorys as $value){ //遍历我的浏览轨迹
        $value->date =  $value->_id->year.'-' .$value->_id->month .'-'. $value->_id->day;
        foreach ($value->items as $va) {
          $va->products = $productModel->getProduct($va->pid);
          unset($va->products->body);
          $va->likes = $productModel->getProductLikesCount($va->pid);//循环获取产品用户收藏总数
          // var_dump($value->items);exit;
        }
      } 
    } else {
      gotoUrl('user/login');
    }
    $count = $userModel->getUserTrajectorysCount($userId);

    $view->assign('trajectorys', $trajectorys);
    $view->assign('other', $other);
    $view->assign('page', $page);
    $view->assign('count', $count);
    $view->assign('size', $size);
    $this->addCss('css/users.css');
    $view->display('user/user_trajectory.phtml');
  }

  // 用户中心_我的分享
  public function shareAction($userId = null)
  {
    $userModel = $this->getModel('user');
    $productModel = $this->getModel('product');
    if (!empty($userId)) {
      if (!preg_match('/^([a-f0-9]{8})\.html$/', $userId, $matches)) {
        return BPF_NOT_FOUND;
      }
      $userId = intval(hexdec($matches[1])) - 33445567;
      $userModel = $this->getModel('user');
      if ($userId <= 0 || !$user = $userModel->getUser($userId)) {
        return BPF_NOT_FOUND;
      }
      $other = $user; // TA的购买
    } else if (isLogin() && $userId === null) {
      global $user;
      $other = null;
    } else {
      gotoUrl('user/login');
    }
    // 临时处理方法
    $shareList = array();
    $conditions = array(
      'uid' => $user->uid,
      'groupby' => 'pid',
    );
    for ($i = 0; $i < 7; $i++) { 
      $date = date('Ymd', REQUEST_TIME - (86400 * $i));
      $shareList[$i]['date'] = date('Y-m-d', REQUEST_TIME - (86400 * $i));
      $conditions['date'] = $date;
      $shareList[$i]['share'] = $userModel->getUserShares($conditions);
      if (!empty($shareList[$i]['share'])) {
        foreach ($shareList[$i]['share'] as $key => $value) {
          $value->product = $productModel->getProduct($value->pid);
        }
      }
    }
    $view = $this->getView();
    $this->addCss('css/users.css');
    $view->assign('other', $other);
    $view->assign('shareList', $shareList);
    $view->display('user/user_share.phtml');
  }

  public function feedbackAction()
  {
    if ($this->isPost()) {
      $type = isset($_POST['type']) ? $_POST['type'] : null;
      $opinion = isset($_POST['opinion']) ? $_POST['opinion'] : null;
      $contact = isset($_POST['contact']) ? $_POST['contact'] : null;
      if (isset($type) && $opinion && $contact) {
        $set = array(
          'type' => $type,
          'opinion' => $opinion,
          'contact' => $contact,
        );
        $userModel = $this->getModel('user');
        $insertId = $userModel->insertFeedback($set);
        if ($insertId) {
          return json_encode(array('result' => 1));
        } else {
          return json_encode(array('result' => -1));
        }
      } else {
        return BPF_NOT_FOUND;
      } 
    }
    $view = $this->getView();
    $view->display('user/feedback.phtml');
  }

  //触发邮件接口
  public function userEmailTasksAction()
  {
    $userModel = $this->getModel('user');
    $productModel = $this->getModel('product');
    $mailModel = $this->getModel('mail');
    $startTime = strtotime("-5 minute");
    $endTime = strtotime("+5 minute");
    $taskUsers = $userModel->getUserEmailTasks($startTime,$endTime);//查询用户点击数据
    foreach ($taskUsers as $key => $value) {
      $value = $value->items[0];
      # code...获取商品 发邮件
      $categoryId = $productModel->getProduct($value->pid)->cid;
      $limit = 12;  // 每页商品数
      //该分类的子分类
      $childCates = $productModel->getCategories($categoryId, 1);
      $childCatesCid = array();
      $childCatesCid[] = $categoryId;
      foreach ($childCates as $key => $value) {
        $childCatesCid[] = $value->cid;
      }
      // 排序规则
      $sort = '`weight` DESC,`sellcount` DESC,`clicks` DESC';
      $conditions = array(
        'orderby' => $sort,
        'where' =>  array(
          'cid IN' => $childCatesCid,
          'status' => 1,
        ),
      );
      $products = $productModel->getProducts($conditions, 1, $limit);
    }
   return true;
  }
}
