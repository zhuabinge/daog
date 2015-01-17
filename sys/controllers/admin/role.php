<?php
class AdminRoleController extends BpfController
{
  public static function __permissions()
  {
    return array(
      'name' => '角色管理',
      'permissions' => array(
        'role-view' => '查询角色',
        'role-edit' => '编辑角色',
        'role-delete' =>'删除角色',
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
    $rows = 15;//默认一页显示1条记录
    if (isset($_GET['page'])) {
      $page = $_GET['page'];
    } else {
      $page = 1;
    }
    $userModel = $this->getModel('user');
    $count = $userModel->getRolesCount();
    $roleslist = $userModel->getRoles(null, $page, $rows);
    $view = $this->getView();
    $view->assign('roleslist', isset($roleslist) ? $roleslist : '');
    $view->assign('page', isset($page) ? $page : '');
    $view->assign('rows', isset($rows) ? $rows : '');
    $view->assign('count', isset($count) ? $count : '');
    $view->display('admin/user/role.phtml');
  }

  public function deleteAction()
  {
    $userModel = $this->getModel('user');
    if (isset($_GET['id']) && trim($_GET['id'])!='') {
      $deRst = $userModel->deleteRole(trim($_GET['id']));
      if ($deRst) {
        setMessage('删除成功', 'success');
      } else {
        setMessage('删除失败','error');
      }
    }
    gotoUrl('admin/role');//跳转到列表页
  }

  public function addAction()
  {
    $userModel = $this->getModel('user');
    if (isset($_POST['rolename']) && trim($_POST['rolename'])!='') {
      $roleId = $userModel->insertRole(array(
        'name' => trim($_POST['rolename'])
        ));
      if ($roleId) {
        $pmsre = $userModel->setRolePermissions($roleId, $_POST['perId']);
      } else {
        setMessage('角色保存失败','error');
        gotoUrl('admin/role/add');
      }
      if ($pmsre) {
        setMessage('保存成功', 'success');
        gotoUrl('admin/role/');
      } else {
        setMessage('保存失败', 'error');
        gotoUrl('admin/role/add');
      }
    }
    $allpermission = $userModel->getPermissions();
    $view = $this->getView();
    $view->assign('allpermission', isset($allpermission) ? $allpermission:'');//权限
    $view->assign('action', 'add');
    $this->addCss('css/plugins.css'); //包含所有插件CSS
    $view->display('admin/user/role_edit.phtml');
  }

  public function editAction()
  {
    $userModel = $this->getModel('user');
    if (isset($_POST['roleId']) && trim($_POST['roleId'])) {
      $re = $userModel->setRolePermissions($_POST['roleId'], array_values($_POST['perId']));
      $userModel->updateRole($_POST['roleId'], array('name' => $_POST['rolename'], ));
      setMessage('保存成功', 'success');
      gotoUrl('admin/role/');
    }
    if (isset($_GET['id']) && trim($_GET['id'])!='') {
      $roleGetId = trim($_GET['id']);
      if ($roleGetId==1) {//屏蔽管理员角色
        throw new BpfException();
      }
      $roleInfo = $userModel->getRole($roleGetId);//id to role info
      if ($roleInfo) {
        $rolePer = $userModel->getRolePermissions($roleGetId);
      } else {
        gotoUrl('admin/role/');
      }
    } else if ($_GET['id'] == null) {
      gotoUrl('admin/role/');
    }
    $allpermission = $userModel->getPermissions();
    $view = $this->getView();
    $view->assign('allpermission', isset($allpermission) ? $allpermission : '');
    $view->assign('action', 'edit');
    $view->assign('roleGetId', isset($roleGetId) ? $roleGetId : '');
    $view->assign('rolePer', isset($rolePer) ? $rolePer : '');
    $view->assign('roleInfo', isset($roleInfo) ? $roleInfo : '');
    $view->display('admin/user/role_edit.phtml');
  }

  public function permissionAction()
  {
    $userModel = $this->getModel('user');
    if ($this->isPost() && isset($_POST['permission'])) {
      $permissions = $_POST['permission'];
      foreach ($permissions as $rid => $pers) {
        $userModel->setRolePermissions($rid, array_keys($pers));
      }
      setMessage('保存成功', 'success');
      gotoUrl('admin/role/permission');
    }
    $rolesCount = $userModel->getRolesCount();
    $roles = $userModel->getRoles(null, null, $rolesCount);
    $rolesPer = $userModel->getRolesPermissions();
    $allpermission = $userModel->getPermissions();
    $view = $this->getView();
    $this->addCss('css/plugins.css'); //包含所有插件CSS
    $view->assign('roles', $roles);
    $view->assign('allpermission', $allpermission);
    $view->assign('rolesPer', $rolesPer);
    $view->display('admin/user/permission.phtml');
  }

  public function userAction()
  {
    $userModel = $this->getModel('user');
    if (isset($_POST['roleId']) && trim($_POST['roleId'])!='') {
      $ruRe = $userModel->setRoleUsers(trim($_POST['roleId']), $_POST['uid']);
      if ($ruRe) {
        setMessage('保存成功', 'success');
      } else {
        setMessage('保存失败', 'error');
      }
      gotoUrl('admin/role/');
    }
    if (isset($_GET['id']) && trim($_GET['id'])!='') {
      $roleGetId = trim($_GET['id']);
      if ($roleGetId==1) {//屏蔽管理员角色
        throw new BpfException();
      }
      if ($roleGetId) {
        $roleInfo = $userModel->getRole($roleGetId);
        $roleusers = $userModel->getRoleUsers($roleGetId);
      }
    } else {
      gotoUrl('admin/role/');
    }
    $usersInfo = $userModel->getUsers();
    $view = $this->getView();
    $view->assign('roleusers', $roleusers);
    $view->assign('usersInfo', isset($usersInfo) ? $usersInfo : '');
    $view->assign('roleGetId', isset($roleGetId) ? $roleGetId : '');
    $view->assign('roleInfo', isset($roleInfo) ? $roleInfo : '');//role info
    $view->display('admin/user/role_user.phtml');
  }
}