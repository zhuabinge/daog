<?php
class AdminUserController extends BpfController
{
  public static function __permissions()
  {
    return array(
      'name' => '用户管理',
      'permissions' => array(
        'user-view' => '查询用户',
        'role-show-view' => '查询权限',
        'user-edit' => '编辑用户',
        'user-delete' => '删除用户',
        'feedback-view' => '查询反馈',
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

  public function indexAction()
  {
    $rows = 15;
    $userModel = $this->getModel('user');
    $total = $userModel->getUsersCount();//get the user sum
    if (isset($_GET['page'])) {
      $page = $_GET['page'];
    } else {
      $page = 1;
    }
    if(isset($_GET['sort'])) {
      $sort = $_GET['sort'];
    } else {
      $sort = "opttime";
    }
    //get all the user info
    if ($sort == "opttime") {
      $result = $userModel->getUsers(null, $page, $rows);
    } else {
      $result = $userModel->getUsers(array('orderby' =>'username' ), $page, $rows);
    }
    $roleslist = $userModel->getRoles(); // 角色列表
    if (!empty($_GET['filter'])) {
      $rid = $_GET['filter'];
      $total = $userModel->getRoleUsersCount($rid); // 符合角色的用户总数
      $result = $userModel->getRoleUsers($rid, null, $page, $rows); // 符合角色的用户列表
    }
    $view = $this->getView();
    $this->addJs('js/plugins/select2/select2.min.js'); //下拉框插件
    $view->assign('usersInfo', $result);
    $view->assign('roleslist', $roleslist);
    $view->assign('curPage', $page);
    $view->assign('option', $sort);
    $view->assign('total', $total);
    $view->assign('rows', $rows);
    $view->assign('curPage', $page);
    $view->assign('seakey', null);
    $view->assign('actionname', 'index');
    $view->display('admin/user/user.phtml');
  }

  public function searchAction()
  {
    $pagesize = 15;
    if (isset($_GET['keyword']) && $_GET['keyword']!='') {
      $keyword = $_GET['keyword'];
    } else {
      gotoUrl('admin/user');
    }
    if (isset($_GET['page'])) {
      $page = $_GET['page'];
    } else {
      $page = 1;
    }
    if (isset($_GET['sort'])) {
      $sort = $_GET['sort'];
    } else {
      $sort = "opttime";
    }
    $condition = array('search' =>$keyword);
    $userModel = $this->getModel('user');
    $total = $userModel->getUsersCount($condition);//get the user sum
    //get all the user info
    if($sort === "opttime") {
      $result = $userModel->getUsers($condition, $page, $pagesize);
    } else {
      $result = $userModel->getUsers(array('orderby' =>'username','search' =>$keyword), $page, $pagesize);
    }
    $view=$this->getView();
    $view->assign('usersInfo', $result);
    $view->assign('option', $sort);
    $view->assign('total', $total);
    $view->assign('rows', $pagesize);
    $view->assign('curPage', $page);
    $view->assign('actionname','search');
    $view->assign('seakey', $keyword);
    $view->display('admin/user/user.phtml');
  }

  public function addAction()
  {
    $userModel = $this->getModel('user');
    $view = $this->getView();
    if (isset($_POST['username']) && trim($_POST['username']) != '' && isset($_POST['nickname']) && trim($_POST['nickname']) != '' &&
      isset($_POST['email']) && trim($_POST['email']) != '' && isset($_POST['password']) && trim($_POST['password']) != '' &&
      isset($_POST['conf_password']) && trim($_POST['conf_password']) != ''&& isset($_POST['status']) &&
      isset($_POST['roles'])) {
      //判断post参数
      if ($userModel->getUserByUserName(trim($_POST['username']))) {
        setMessage('用户已经存在!', 'warining');
        gotoUrl('admin/user/add');
      }
      if (strcmp($_POST['password'],$_POST['conf_password'])!=0) {
        setMessage('两次输入密码不一致', 'warining' );
        gotoUrl('admin/user/add');
      }
      $postData = array(
        'username' => trim($_POST['username']),
        'nickname' => trim($_POST['nickname']),
        'email' => trim($_POST['email']),
        'password' => trim($_POST['password']),
        'status' => trim($_POST['status']),
      );
      //插入数据库
      $rolesData = $_POST['roles'];
      $userrst = $userModel->insertUser($postData);
      if ($userrst) {
        $rolesResult = $userModel->setUserRoles($userrst, array_values($rolesData));
        if ($rolesResult) {
          setMessage('保存成功!', 'success');
          gotoUrl('admin/user/');
        }else {
          setMessage('保存失败!', 'error');
        }
      }
    }
    $view->assign('roles', $userModel->getRoles());//roles
    $view->display('admin/user/user_add.phtml');
  }

  public function editAction()
  {
    if (!isset($_GET['uid'])) {
      gotoUrl('admin/user');
    }
    $userId = trim($_GET['uid']);
    $userModel = $this->getModel('user');
    $user = $userModel->getUser($userId);
    if (!$user) {
      setMessage('没有此用户','error');
      gotoUrl('admin/user/');
    }
    $userRoles = $userModel->getUserRoles($userId);
    $userRoles = getAssocArray($userRoles, 'rid');
    //post修改用户状态以及权限
    if ($this->isPost()) {
      $rolesPost = $_POST['roles'];
      $statusPost = $_POST['status'];
      $rolesRe = $userModel->setUserRoles($userId, array_values($rolesPost));
      $statusRe=$userModel->updateUser($userId, array(
        'status' => $statusPost,
      ));
      if ($rolesRe && $statusRe){
        setMessage('保存成功', 'success');
        gotoUrl('admin/user/');
      } else {
        setMessage('保存失败', 'error');
        gotoUrl('admin/user/edit?uid=' . $userId);
      }
    }
    $view = $this->getView();
    $view->assign('roles', $userModel->getRoles());
    $view->assign('userRoles', $userRoles);
    $view->assign('user', $user);
    $view->display('admin/user/user_edit.phtml');
  }

  public function commentAction()
  {
    $rows=15;
    $commentModel = $this->getModel('comment');
    $proModel = $this->getModel('product');
    $userModel = $this->getModel('user');
    if (isset($_GET['page'])) {
      $page = $_GET['page'];
    } else {
      $page = 1;
    }
    if(isset($_GET['sort'])) {
      $sort = $_GET['sort'];
    } else {
      $sort = "comTime";
    }
    if ($sort == "comTime") {
      $comments = $commentModel->getComments(array('uid' => $GLOBALS['user']->uid), $page, $rows);
    } else {
      $comments = $commentModel->getComments(array('orderby' =>'last_reply_time','uid' => $GLOBALS['user']->uid), $page, $rows);
    }
    ;
    // var_dump($comments);exit();
    if (!empty($comments)) {
      foreach ($comments as $comment) {
        $pid[] = $comment->pid;
        $uid[] = $comment->uid;
        $cids[] = $comment->cid;
      }
    }
    if (!empty($pid) && !empty($uid) && !empty($cids)) {
      $products = $proModel->getProductsByIds($pid);
      $critics = $userModel->getUsersByIds($uid);
    }
    $total = $commentModel->getCommentsCount();
    $view = $this->getView();
    $view->assign('critics', isset($critics) ? $critics : '');
    $view->assign('products', isset($products) ? $products : '');
    $view->assign('comments', $comments);
    $view->assign('option', $sort);
    $view->assign('total', $total);
    $view->assign('rows', $rows);
    $view->assign('page', $page);
    $view->assign('seakey', null);
    $view->assign('actionname', 'index');
    $view->assign('replyInfo', isset($replyInfo) ? $replyInfo : '');
    $view->display('admin/comment/comment.phtml');
  }

  public function logAction()
  {
    $pagesize = 10;
    if (isset($_GET['page'])) {
      $page = $_GET['page'];
    } else {
      $page = 1;
    }
    $userModel = $this->getModel('user');
    $view = $this->getView();
    $uid = $GLOBALS['user']->uid;
    $userInfo = $userModel->getUserInfo($uid);
    // $view->assign('uid', $uid);
    $view->assign('userInfo', $userInfo);
    $view->display('admin/user/log.phtml');
  }

  public function batchAction()
  {
    $batchtid = $_POST['uid'];
    if ($this->isPost()) {
      if (empty($batchtid) || !is_array($batchtid)) {
        setMessage('操作失败，请重新选择', 'error');
        gotoUrl('admin/user/');
      } else if (is_array($batchtid) && $_POST['status'] != -1) {
        $userModel = $this->getModel('user');
        if ($_POST['status'] == 1 || $_POST['status'] == 0) {//status
          $status = ($_POST['status'] == 1) ? 'pass' : 'delect';
          $userModel->updateUsers($batchtid, $status);
        }
        setMessage('批量操作成功', 'success');
        gotoUrl('admin/user/');
      }
    } else {
      throw new BpfException();
    }
  }

  // 无设置头像用户自动设置头像
  public function setAvatarAction()
  {
    $view = $this->getView();
    $userModel = $this->getModel('user');
    $condition = array(
      'avatar_file_path' => true,
    );
    // $count = $userModel->getUsersCount($condition);
    $userList = $userModel->getUsers($condition, 1, 1);
    $avatarRand = rand(1, 26);
    $avatarPath = DOCROOT . '/static/default/images/avatar/' . $avatarRand . '.jpg';
    if ($this->isPost()) {
      if (isset($userList) && $userList) {
        $fileModel = $this->getModel('file');
        $fileName = date('Ymd/', REQUEST_TIME) . $avatarRand . '.jpg';
        $fileContent = file_get_contents($avatarPath);
        $file = $fileModel->write('avatar', $fileName, $fileContent);
        $status = '失败';
        if (isset($file) && $file) {
          foreach ($userList as $key => $value) {
            if ($userModel->setUserAvatar($value->uid, $file->file_id)) {
              $status = '成功';
            };
            return json_encode(array(
              'uid' => $value->uid,
              'nickname' => $value->nickname,
              'status' => $status,
            ));
          }
        }
      }
    }
    $view->assign('count', 1000);
    $view->display('admin/user/set_avatar.phtml');
  }

  //反馈意见
  public function feedbackAction()
  {
    global $user;
    $userModel = $this->getModel('user');
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
    $rows = 15;

    $conditions = array();
    if (isset($_GET['type'])) {
      $conditions['type'] = $_GET['type'];
    }
    if (!empty($_GET['opinion'])) {
      $conditions['opinion'] = $_GET['opinion'];
    }
    $feedbackList = $userModel->getFeedbacks($conditions, $page, $rows);
    $count = $userModel->getFeedbacksCount($conditions);
    $view = $this->getView();
    $view->assign(array(
      'count' => $count,
      'page' => $page,
      'rows' => $rows,
      'feedbackList' => $feedbackList,
    ));
    $this->addJs('js/plugins/select2/select2.min.js'); //下拉框插件
    $view->display('admin/user/feedback.phtml');
  }


   //删除意见反馈
  public function feedbackDeleteAction()
  {
    $userModel = $this->getModel('user');
    if(isset($_GET['id'])) {
      $fid = trim($_GET['id']);
    }
    $del = $userModel->deleteFeedback($fid);
    if($del) {
      setMessage('删除成功', 'success');
    } else{
      setMessage('删除失败','error');
    }
    gotoUrl('admin/user/feedback');
    
  }

  //用户行为日志
  public function usersScoresLogsAction()
  {
    $userModel = $this->getModel('user');
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
    $rows = 15;
    
    $conditions = null;
    if(!empty($_GET['op'])) {
      $conditions = $_GET['op'];
    }
    $usersScoresLogsList = $userModel->getUsersScoresLogs($conditions, $page, $rows);
    $count = $userModel->getUsersScoresLogsCount($conditions);
    $view = $this->getView();
    $view->assign(array(
      'count' => $count,
      'page' => $page,
      'rows' => $rows,
      'usersScoresLogsList' => $usersScoresLogsList,
    ));
   
    $this->addJs('js/plugins/select2/select2.min.js'); //下拉框插件
    $view->display('admin/user/user_scoreslogs.phtml');
  }

  //日志查询
  public function adminLogsAction()
  {
    $logModel = $this->getModel('log');
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
    $rows = 15;

    $conditions = array();
    if(!empty($_GET['op'])) {
      $conditions['where']['op'] = $_GET['op'];
    }
    $adminLogsList = $logModel->getAdminlogs($conditions,$page,$rows);
    $count = $logModel->getAdminLogsCount($conditions);
    $view = $this->getView();
    $view->assign(array(
      'count' => $count,
      'page' => $page,
      'rows' => $rows,
      'adminLogsList' => $adminLogsList,
    ));

    $this->addJs('js/plugins/select2/select2.min.js'); //下拉框插件
    $view->display('admin/user/admin_Logs.phtml');
  }

  // 清除所有缓存
  public function clearAllCacheAction()
  { 
    $view = $this->getView();
    $view->clearAllCache();
    setMessage('全部缓存清空完成！', 'success');
    gotoUrl('admin/product');
  }
}