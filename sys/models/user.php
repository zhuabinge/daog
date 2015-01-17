<?php
/**
 * 用户类
 * @author Hao <sixihaoyue@gmail.com>
 */
class UserModel extends BpfModel
{
  private function _getPasswordHash($password)
  {
    return md5($password);
  }

  /**
   * 验证用户名密码
   * @param string $username 用户名
   * @param string $password 密码
   * @return object/bool 用户对象, 失败返回 false
   */
  public function authenticate($username, $password)
  {
    $mysqlModel = $this->getModel('mysql');
    $uid = $mysqlModel->getSqlBuilder()
        ->select('uid')
        ->from('users')
        ->where('username', $username)
        ->where('password', $this->_getPasswordHash($password))
        ->where('status', 1)
        ->query()
        ->field();
    if ($uid) {
      return $this->getUser($uid);
    } else {
      return false;
    }
  }

   /**
   * 新增一个用户
   * @param array $user 用户数组
   * @param string $type 用户类型
   * @param bool $ignore 处理重复键值
   * @return int/bool 新用户ID, 失败返回 false
   */
  public function insertUser($user, $type = 'user', $ignore = false)
  {
    if (!isset($user['username']) || !isset($user['password'])) {
      if ($type !== 'qqUser') {
       return false;
      }
    }
    $mysqlModel = $this->getModel('mysql');
    $set = array(
      'created' => REQUEST_TIME,
      'updated' => REQUEST_TIME,
    );
    $set['openid'] = isset($user['openid']) ? trim($user['openid']) : null;
    $set['alipay'] = isset($user['alipay']) ? trim($user['alipay']) : null;
    $set['username'] = isset($user['username']) ? trim($user['username']) : null;
    $set['password'] = isset($user['password']) ? $this->_getPasswordHash($user['password']) : null;
    $set['email'] = isset($user['email']) ? trim($user['email']) : '';
    $set['nickname'] = isset($user['nickname']) ? trim($user['nickname']) : $set['username'];
    $set['register_time'] = REQUEST_TIME;
    $set['register_ip'] = ipAddress();
    $set['qq_token'] = isset($user['qq_token']) ? trim($user['qq_token']) : null;
    $set['username_can_change'] = isset($user['username_can_change']) ? $user['username_can_change'] : null;
    $set['birthday'] = isset($user['birthday']) ? $user['birthday'] : null;
    $set['city'] = isset($user['city']) ? $user['city'] : null;
    $set['sex'] = isset($user['sex']) ? $user['sex'] : null;
    $set['emotion'] = isset($user['emotion']) ? $user['emotion'] : null;
    $set['qq'] = isset($user['qq']) ? $user['qq'] : null;
    $set['jf'] = isset($user['jf']) ? $user['jf'] : null;
    $set['weibo'] = isset($user['weibo']) ? $user['weibo'] : null;
    $set['scores'] = isset($user['scores']) ? $user['scores'] : null;
    try {
      $result = $mysqlModel->insert('users', $set, $ignore);
      $uid = $result->insertId();
      if (isset($user['avatar_file_id'])) {
        $this->setUserAvatar($uid, $user['avatar_file_id']);
      }
      return $uid;
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
  * 设置用户头像
  * @param int $userId 用户id
  * @param int $fileId 文件id
  * @return int/bool 影响行数, 失败返回 false
  */
  public function setUserAvatar($userId, $fileId)
  {
    $fileModel = $this->getModel('file');
    $mysqlModel = $this->getModel('mysql');
    $file = $fileModel->getFile('avatar', $fileId);
    if ($file) {
      $ordFile = $mysqlModel->query('SELECT `avatar_file_id` FROM `users` WHERE `uid` = ' . $mysqlModel->escape($userId))->row()->avatar_file_id;
      //旧头像存在
      if ($ordFile && $ordFile > 0 && $fileId != $ordFile) {
        $fileModel->delete('avatar', $ordFile);
      }
      $set = array(
        'avatar_file_id' => $fileId,
        'avatar_file_path' => $file->file_path,
      );
      $mysqlModel->update('users', $set, array(
        'uid' => $userId,
      ));
      return true;
    }
    return false;
  }

  /**
  * 获取用户数据
  * @param int $userId 用户id
  * @return object/bool 用户信息, 失败返回 false
  */
  public function getUserInfo($userId)
  {
    if (!isset($userId)) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    return $mysqlModel->query('SELECT * FROM `users` WHERE `uid` = ' . $mysqlModel->escape($userId))->row();
  }

   /**
   * 更新用户
   * @param int $userId 用户ID
   * @param array 用户数组
   * @return int/bool 影响行数, 失败返回 false
   */
  public function updateUser($userId, $user = null)
  {
    if (!isset($userId)) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $set = array(
      'updated' => REQUEST_TIME,
    );
    if (isset($user['email'])) {
      $set['email'] = $user['email'];
    }
    if (isset($user['username'])) {
      $set['username'] = $user['username'];
    }
    if (isset($user['nickname'])) {
      $set['nickname'] = $user['nickname'];
    }
    if (isset($user['alipay'])) {
      $set['alipay'] = $user['alipay'];
    }
    if (isset($user['openid'])) {
      $set['openid'] = $user['openid'];
    }
    if (isset($user['code'])) {
      $set['code'] = $user['code'];
    }
    if (isset($user['status'])) {
      $set['status'] = $user['status'];
    }
    if (isset($user['qq_token'])) {
      $set['qq_token'] = $user['qq_token'];
    }
    if (isset($user['username_can_change'])) {
      $set['username_can_change'] = $user['username_can_change'];
    }
    if (isset($user['birthday'])) {
      $set['birthday'] = $user['birthday'];
    }
    if (isset($user['emotion'])) {
      $set['emotion'] = $user['emotion'];
    }
    if (isset($user['qq'])) {
      $set['qq'] = $user['qq'];
    }
    if (isset($user['jf'])) {
      $set['jf'] = $user['jf'];
    }
    if (isset($user['weibo'])) {
      $set['weibo'] = $user['weibo'];
    }
    if (isset($user['scores'])) {
      $set['scores'] = $user['scores'];
    }
    if (isset($user['city'])) {
      $set['city'] = $user['city'];
    }
    if (isset($user['sex'])) {
      $set['sex'] = $user['sex'];
    }
    if (isset($user['data'])) {
      $set['data'] = $user['data'];
    }
    try {
      $result = $mysqlModel->update('users', $set, array(
        'uid' => $userId,
      ));
      if (isset($user['avatar_file_id'])) {
        $this->setUserAvatar($uid, $user['avatar_file_id']);
      }
      return $result->affected();
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
   * 批量更新用户状态
   * @param array $userIds 用户ID
   * @param string $op delect/pass
   * @return bool 影响行数, 失败返回 false
   */
  public function updateUsers($userIds, $op)
  {
    if($op === 'delect') {
      $status = '0';
    } else if ($op === 'pass') {
      $status = '1';
    }
    if (!isset($status)) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    try{
      $mysqlModel->update('users',
        array(
          'status' => $status,
          'updated' => REQUEST_TIME,
        ),
        array(
          'uid IN' => $userIds,
        )
      );
      return  true;
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
   * 更新用户密码
   * @param int $userId 用户ID
   * @param string $password 新密码
   * @return int/bool 影响行数, 失败返回 false
   */
  public function updateUserPassword($userId, $password)
  {
    $mysqlModel = $this->getModel('mysql');
    try {
      $set = array(
        'code' => '',
        'password' => $this->_getPasswordHash($password),
        'updated' => REQUEST_TIME,
      );
      $result = $mysqlModel->update('users', $set, array('uid' => $userId));
      return $result->affected();
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
   * 设置登录日志
   * @param array $user 用户数组
   * @return int/bool 影响行数, 失败返回 false
   */
  public function loginLog($user)
  {
    $userId = $user->uid;
    if ($user->last_login_time < mktime(0, 0, 0)) {
      // 当天第一次登录，增加积分
      $configModel = $this->getModel('config');
      $scores = intval($configModel->get('scores.login', 1));
      if ($this->updateUserScores($userId, $scores)) {
        $this->insertScoresLog($userId, 'login', $scores, sprintf('登录成功，赚取 %d 个积分', $scores));
      }
    }
    $mysqlModel = $this->getModel('mysql');
    try {
      $set = array(
        'login_counter' => array(
          'escape' => false,
          'value' => 'login_counter + 1',
          ),
        'last_login_time' => REQUEST_TIME,
        'last_login_ip' => ipAddress(),
      );
      $result = $mysqlModel->update('users', $set, array('uid' => $userId));
      return $result->affected();
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
   * 跟据用户id获取用户
   * @param strng $username 用户名
   * @return object/bool 用户对象, 失败返回false
   */
  public function getUser($userId = null)
  {
    if(!isset($userId)){
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $user = $mysqlModel->getSqlBuilder()
        ->from('users')
        ->where('uid', $userId)
        ->query()
        ->row();
    if ($user) {
      unset($user->password);
      $user->link = urlUser($user);
    }
    return $user;
  }

  /**
   * 跟据用户名获取用户
   * @param strng $username 用户名
   * @return object/bool 用户对象
   */
  public function getUserByUserName($username)
  {
    if(!isset($username)){
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $uid = $mysqlModel->query('SELECT `uid` FROM `users` WHERE `username` = "' . $mysqlModel->escape($username) . '"')->field();
    if ($uid) {
      return $this->getUser($uid);
    }
    return false;
  }

  /**
   * 获取用户总数
   * @param array $conditions 用户查询条件数组
   * @return int 用户总数
   */
  public function getUsersCount($conditions = null)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder();
    $query->select('COUNT(0)')
        ->from('users');
    if (isset($conditions['search'])) {
      $query->where('users.username LIKE', '%' . $mysqlModel->escape($conditions['search']) . '%');
    }
    if (isset($conditions['avatar_file_path'])) {
      $query->where('users.avatar_file_path', '');
    }
    $result = $query->query()->field();
    return $result;
  }

  /**
   * 获取用户并进行分页
   * @param array $conditions 用户查询条件数组
   * @param int $limit 分页显示数 默认20
   * @param int $page 页码 默认1
   * @return array 用户数组
   */
  public function getUsers($conditions = null, $page = null, $limit = 15)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder();
    $query->from('users');
    if (isset($conditions['search'])) {
      $query->where('users.username LIKE', '%' . $mysqlModel->escape($conditions['search']) . '%');
    }
    if (isset($conditions['avatar_file_path'])) {
      $query->where('users.avatar_file_path', '');
    }
    if (isset($conditions['orderby']) && $conditions['orderby'] === 'username') {
      $query->orderby('username');
    } else {
      $query->orderby('created DESC');
    }
    if(isset($page)){
      $query->limitPage($limit, $page);
    }
    $result = $query->query()->all();
    if ($result) {
      foreach ($result as $user) {
        unset($user->password);
        $user->link = urlUser($user);
      }
    }
    return $result;
  }

  /**
   * 通过用户ID数组 获取uid排序用户数组
   * @param array $userIds 用户ID数组
   * @return array 用户数组
   */
  public function getUsersByIds($userIds)
  {
    $result = $this->getModel('mysql')
        ->getSqlBuilder()
        ->from('users')
        ->where('users.uid IN', $userIds)
        ->query()
        ->all();
    if($result){
      return getAssocArray($result, 'uid');
    } else {
      return array();
    }
  }


  /**
   * 获取用户所有角色
   * @param int $userId 用户id
   * @return array/bool 用户角色数组
   */
  public function getUserRoles($userId)
  {
    if (!isset($userId)) {
      return array();
    }
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder();
    return $query->select('*')
        ->from('users_roles')
        ->join('roles','users_roles.rid = roles.rid')
        ->where('uid', $userId)
        ->query()
        ->all();
  }

  /**
   * 批量设置用户的角色
   * @param int $userId 用户id
   * @param array $roles 角色id数组
   * @return bool 失败返回 false,成功返回true
   */
  public function setUserRoles($userId, $roles)
  {
    if (!isset($userId)) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $set = array();
    foreach ($roles as $rid) {
      $set[] = array(
        'uid' => $userId,
        'rid' => $rid,
      );
    }
    try {
      $mysqlModel->delete('users_roles', array('uid' => $userId));
      $mysqlModel->insert('users_roles', $set);
      return true;
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
   * 获取用户所有权限
   * @param int $userId 用户id
   * @return false/array 用户权限集合
   */
  public function getUserPermissions($userId)
  {
    if (!isset($userId)) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder();
    return $query->select('permission')
        ->distinct()
        ->from('users_roles ur')
        ->join('roles_permissions up','ur.rid = up.rid')
        ->where('ur.uid', $userId)
        ->query()
        ->column();
  }

  /**
   * 新增角色
   * @param array $role 角色数组
   * @return int/bool 新角色ID, 失败返回 false
   */
  public function insertRole($role)
  {
    if (!isset($role['name'])) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $set = array(
      'name' => trim($role['name']),
      'description' => isset($role['status']) ? $role['description'] : '',
      'created' => REQUEST_TIME,
      'updated' => REQUEST_TIME,
    );
    try {
      $result = $mysqlModel->insert('roles', $set);
      return $result->insertId();
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
   * 更新角色
   * @param int $roleId 角色ID
   * @param array 角色数组
   * @return int/bool 影响行数, 失败返回 false
   */
  public function updateRole($roleId, $role = null)
  {
    if (!isset($roleId)) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $set = array(
      'updated' => REQUEST_TIME,
    );
    if (isset($role['name'])) {
      $set['name'] = trim($role['name']);
    }
    if (isset($role['description'])) {
      $set['description'] = trim($role['description']);
    }
    try {
      $result = $mysqlModel->update('roles', $set, array(
        'rid' => $roleId,
      ));
      return $result->affected();
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
   * 删除角色
   * @param int $roleId 角色ID
   * @return int/bool 影响行数, 失败返回 false
   */
  public function deleteRole($roleId)
  {
    if (!isset($roleId) || $roleId == 1) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $result = $mysqlModel->delete('roles', array(
      'rid' => $roleId,
    ));
    $affected = $result->affected();
    if ($affected) {
      //删除用户与角色的关系
      $mysqlModel->delete('users_roles', array(
        'rid' => $roleId,
      ));
      //删除角色与权限的关系
      $mysqlModel->delete('roles_permissions', array(
        'rid' => $roleId,
      ));
    }
    return $affected;
  }

  /**
   * 跟据ID获取角色
   * @param int $roleId 用户名
   * @return object/bool 用户对象, 失败返回false
   */
  public function getRole($roleId)
  {
    if(!isset($roleId)){
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder();
    return $query->select('*')
        ->from('roles')
        ->where('rid', $roleId)
        ->query()
        ->row();
  }

  /**
   * 获取角色并进行分页
   * @param array $conditions 角色查询条件数组
   * @param int $limit 分页显示数 默认20
   * @param int $page 页码 默认1
   * @return array 角色数组
   */
  public function getRoles($conditions = null, $page = 1, $limit = 15)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder();
    $query->from('roles');
    if (isset($conditions['search'])) {
      $query->where('roles.name LIKE', '%' . $mysqlModel->escape($conditions['search']) . '%');
    }
    if (isset($conditions['orderby']) && $conditions['orderby'] === 'name') {
      $query->orderby('name');
    } else {
      $query->orderby('created DESC');
    }
    return $query->limitPage($limit, $page)
        ->query()
        ->all();
  }

  /**
   * 获取角色总数
   * @param array $conditions 角色查询条件数组
   * @return int 角色总数
   */
  public function getRolesCount($conditions = null)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder();
    $query->select('COUNT(0)')->from('roles');
    if (isset($conditions['search'])) {
      $query->where('roles.name LIKE', '%' . $mysqlModel->escape($conditions['search']) . '%');
    }
    $result = $query->query()->field();
    return $result;
  }

  /**
   * 获取角色符合条件的所有用户的总数
   * @param int $roleId 角色id
   * @param array $conditions 用户查询条件数组
   * @return int/bool 符合条件的用户的总数
   */
  public function getRoleUsersCount($roleId, $conditions = null)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder();
    $query->select('COUNT(0)')
        ->from('users_roles')
        ->join('users','users_roles.uid = users.uid')
        ->where('rid', $roleId);
    if (isset($conditions['search'])) {
      $query->where('users.username LIKE', '%' . $mysqlModel->escape($conditions['search']) . '%');
    }
    return $query->query()->field();
  }

  /**
   * 获取角色所有用户 提供分组
   * @param int $roleId 角色id
   * @param array $conditions 用户查询条件数组
   * @param int $page 分页页码
   * @param int $limit 分页展示数
   * @return false/array 角色用户数组
   */
  public function getRoleUsers($roleId, $conditions = null, $page = null, $limit = 15)
  {
    if (!isset($roleId)) {
      return array();
    }
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder();
    $query->select('*')
        ->from('users_roles')
        ->join('users','users_roles.uid = users.uid')
        ->where('rid', $roleId);
    if (isset($conditions['search'])) {
      $query->where('users.username LIKE', '%' . $mysqlModel->escape($conditions['search']) . '%');
    }
    if (isset($page)) {
      $query->limitPage($limit, $page);
    }
    $result = $query->query()->all();
    if ($result) {
      foreach ($result as $row) {
        unset($row->password);
        $row->link = urlUser($row);
      }
    }
    return $result;
  }

  /**
   * 批量设置角色的用户
   * @param int $roleId 角色id
   * @param array $users 用户id数组
   * @return bool 失败返回 false,成功返回true
   */
  public function setRoleUsers($roleId, $users)
  {
    if (!isset($roleId)) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $set = array();
    foreach ($users as $uid) {
      array_push($set, array(
        'uid' => $uid,
        'rid' => $roleId,
      ));
    }
    try {
      $mysqlModel->delete('users_roles', array('rid' => $roleId));
      $mysqlModel->insert('users_roles', $set);
      return true;
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
   * 获取角色所有权限
   * @param int $roleId 角色id
   * @return false/array 角色权限集合
   */
  public function getRolePermissions($roleId)
  {
    if (!isset($roleId)) {
      return array();
    }
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder();
    return $query->select('permission')
        ->distinct()
        ->from('roles_permissions')
        ->where('rid', $roleId)
        ->query()
        ->all();
  }

  /**
   * 获取所有角色所有权限
   * @return false/array 角色权限集合
   */
  public function getRolesPermissions()
  {
    $result = $this->getModel('mysql')
        ->query('SELECT * FROM `roles_permissions`')
        ->all();
    $rolesPermissions = array();
    foreach ($result as $row) {
      if (!isset($rolesPermissions[$row->rid])) {
        $rolesPermissions[$row->rid] = array();
      }
      $rolesPermissions[$row->rid][] = $row->permission;
    }
    return $rolesPermissions;
  }

  /**
   * 批量设置角色的权限
   * @param int $roleId 角色id
   * @param array $permissions 权限数组
   * @return bool 失败返回 false,成功返回true
   */
  public function setRolePermissions($roleId, $permissions)
  {
    if (!isset($roleId)) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $set = array();
    foreach ($permissions as $permission) {
      array_push($set, array(
        'permission' => $permission,
        'rid' => $roleId,
        ));
    }
    try {
      $mysqlModel->delete('roles_permissions', array('rid' => $roleId));
      $mysqlModel->insert('roles_permissions', $set);
      return true;
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
   * 获取权限列表
   * return array
   */
  public function getPermissions()
  {
    return $this->_getPermissionsDir(SYSPATH . '/controllers');
  }

  private function _getPermissionsDir($dir, $folder = null, $reset = false)
  {
    static $list = array();
    if ($reset) {
      $list = array();
    }
    if ($dh = opendir($dir)) {
      while(false !== ($file = readdir($dh))) {
        if ($file[0] == '.') {
          continue;
        }
        if (is_dir($dir . '/' . $file)) {
          $this->_getPermissionsDir($dir . '/' . $file, $file);
        } else {
          include_once $dir . '/' . $file;
          $controllerClass = (isset($folder) ? ucfirst($folder) : '') .  ucfirst(basename($file, '.php')) . 'Controller';
          if (class_exists($controllerClass, false) && method_exists($controllerClass, '__permissions')) {
            $permissions = call_user_func(array($controllerClass, '__permissions'));
            if (is_array($permissions) && isset($permissions['name']) && isset($permissions['permissions']) && is_array($permissions['permissions'])) {
              $controllerKey = $permissions['name'];
              if (!isset($list[$controllerKey])) {
                $list[$controllerKey] = array();
              }
              $list[$controllerKey] = array_merge($list[$controllerKey], $permissions['permissions']);
            }
          }
        }
      }
      closedir($dh);
    }
    return $list;
  }

  public function getQqToken($code)
  {
    if (empty($code)) {
      return false;
    }
    $configModel = $this->getModel('config');
    $client_id =  $configModel->get('open.appid', '');
    $client_secret = $configModel->get('open.appkey', '');
    $redirect_uri = urlencode(url('user/qqLoginCallback', true));
    $url = 'https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&client_id=' . $client_id;
    $url = $url . '&client_secret=' . $client_secret . '&code=' . $code . '&redirect_uri=' . $redirect_uri;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $response = curl_exec($ch);
    curl_close($ch);
    preg_match('/access_token=([^&]+)/', $response, $result);
    if (isset($result[1])) {
      return $result[1];
    } else {
      return false;
    }
  }

  public function getQqOpenID($token)
  {
    if (empty($token)) {
      return false;
    }
    $url = 'https://graph.qq.com/oauth2.0/me?access_token=' . $token;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $response = curl_exec($ch);
    curl_close($ch);
    preg_match('/"openid":"([^"]+)/', $response, $result);
    if (isset($result[1])) {
      return $result[1];
    } else {
      return false;
    }
  }

  /**
   *  检查openid 是否存在
   * return object
   */
  public function checkOpenId($openid)
  {
    $mysqlModel = $this->getModel('mysql');
    return $mysqlModel->query('SELECT * FROM users WHERE `openid` = "' . $mysqlModel->escape($openid) . '"')
        ->row();
  }

  /**
   * 获取qq登录用户
   * return object
   */
  public function getQqUser($openid, $token = null)
  {
    $mysqlModel = $this->getModel('mysql');
    $user = $this->checkOpenId($openid);
    if ($user) {
      if (isset($token)) {
        $user->qq_token =  $token;
        $this->updateUser($user->uid, array('qq_token' => $token));
      }
      $user->link = urlUser($user);
      return $user;
    } else if (!isset($token)) {
      return false;
    }
    $configModel = $this->getModel('config');
    $client_id =  $configModel->get('open.appid', '');
    $url = 'https://graph.qq.com/user/get_user_info?access_token=' . $token . '&oauth_consumer_key=' . $client_id . '&openid=' . $openid;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $response = curl_exec($ch);
    curl_close($ch);
    $userInfo = json_decode($response);
    if (empty($userInfo)) {
      return false;
    } else {
      $fileModel = $this->getModel('file');
      //获取图片
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $userInfo->figureurl_qq_2);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      $fileContent = curl_exec($ch);
      curl_close($ch);
      $fileName = date('Ymd/', REQUEST_TIME) . $openid . '.png';
      $file = $fileModel->write('avatar', $fileName, $fileContent);
      $sex = 0;
      if ($userInfo->gender == '女') {
        $sex = 2;
      } else if ($userInfo->gender == '男') {
        $sex = 1;
      }
      $set = array(
        'nickname' => isset($userInfo->nickname) ?  $userInfo->nickname : '',
        'username' => $this->_getPasswordHash($openid),
        'openid' => $openid,
        'qq_token' => $token,
        'username_can_change' => 1,
        'avatar_file_id' => $file->file_id,
        'city' => isset($userInfo->city) ? $userInfo->city : '',
        'sex' => $sex,
        'birthday' => isset($userInfo->year) ? $userInfo->year : '',
      );
      $uid = $this->insertUser($set, 'qqUser', true);
      return $this->getUser($uid);
    }
  }


  /**
   * 获取用户收藏数
   * return int 用户收藏数
   */
  public function getUserLikesCount($uid)
  {
    $mysqlModel = $this->getModel('mysql');
    return $mysqlModel->query('SELECT COUNT(0) FROM `users_likes` INNER JOIN `products` ON users_likes.pid = products.pid WHERE users_likes.uid = "' . $mysqlModel->escape($uid) . '"')
        ->field();
  }

  /**
   * 获取用户收藏的商品
   * @param int $uid 用户id
   * @param int $page 分页数
   * @param int $limit 每页显示数
   * @return array 商品数组
   */
  public function getUserLikes($uid, $page = null, $limit = 15)
  {
    if (!isset($uid)) {
      return array();
    }
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder()
        ->select(' products.* ,users_likes.created as love_time')
        ->from('users_likes')
        ->join('products', 'products.pid = users_likes.pid')
        ->where('users_likes.uid', $uid)
        ->where('products.status', 1)
        ->orderby('products.created DESC');
    if (isset($page)) {
      $query->limitPage($limit, $page);
    }
    $result = $query->query()
        ->all();
    foreach ($result as $product) {
      $product->link = urlProduct($product);
    }
    return $result;
  }



  /**
   * 用户收藏行为商品
   * @param int $uid 用户id
   * @param array $productIds 商品数组
   * @return bool
   */
  public function doLikes($userId, $productIds)
  {
    if (!isset($userId) || empty($productIds) || !is_array($productIds) ) {
      return false;
    }
    $sets = array();
    foreach ($productIds as $pid) {
      $sets[] = array(
        'uid' => $userId,
        'pid' => $pid,
        'created' => REQUEST_TIME,
      );
    }
    try {
      $this->getModel('mysql')
          ->insert('users_likes', $sets, true);
        return true;
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
   * 取消用户收藏
   * @param int $uid 用户id
   * @param array $productIds 商品数组
   * @return bool
   */
  public function unLikes($userId, $productIds)
  {
    if (!isset($userId) || empty($productIds) || !is_array($productIds) ) {
      return false;
    }
    $sets = array();
    try {
      $this->getModel('mysql')
          ->delete('users_likes', array(
            'uid' => $userId,
            'pid IN' => $productIds,
          ));
        return true;
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
   * 检查用户是否收藏该一系列商品
   * @param int $uid 用户id
   * @param array $productIds 要检查的商品数组
   * @return bool/array  用户收藏的商品
   */
  public function checkLikes($userId, $productIds)
  {
    if (!isset($userId) || empty($productIds) || !is_array($productIds) ) {
      return array();
    }
    return $this->getModel('mysql')
        ->getSqlBuilder()
        ->from('users_likes')
        ->where('uid', $userId)
        ->where('pid IN', $productIds)
        ->query()
        ->column('pid');
  }

  /**
   * 获取签到总数
   * @param array $conditions 签到查询条件数组
   * @return int 签到总数
   */
  public function getcheckinsCount($conditions = null)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder();
    $query->select('COUNT(0)')
        ->from('users_checkins');
    if (isset($conditions['date'])) {
      $query->where('date', $mysqlModel->escape($conditions['date']));
    }
    $result = $query->query()->field();
    return $result;
  }

  /**
   * 签到
   * @param int $userId 用户id
   * @param string $date 日期, 格式为 YYYYmmdd
   * @return bool
   */
  public function checkin($userId, $date)
  {
    if (!$userId) {
      return 0;
    }
    $configModel = $this->getModel('config');
    $running = intval($this->getCheckinRunning($userId));
    $scores = min(intval($configModel->get('scores.checkin', 1)) +
        intval($configModel->get('scores.checkin_running', 1)) *
        ($running % intval($configModel->get('scores.checkin_cycle', 7))), // 达标周期天数奖励重置
        intval($configModel->get('scores.checkin_top', 20)));
    // 检测签到积分周期天数奖励
    if (($running + 1) % intval($configModel->get('scores.checkin_cycle', 7)) === 0) {
      $scores = $scores + intval($configModel->get('scores.checkin_cycle_reward', 5));
    }
    $mysqlModel = $this->getModel('mysql');
    $result = $mysqlModel->insert('users_checkins', array(
      'uid' => $userId,
      'date' => $date,
      'ranking' => array(
        'escape' => false,
        'value' => '(SELECT * FROM (SELECT COUNT(0) + 1 FROM `users_checkins` WHERE `date` = "' .
            $mysqlModel->escape($date) . '") t)'
      ),
      'running' => $running + 1,
      'scores' => $scores,
      'created' => REQUEST_TIME,
    ), true);
    if ($result->affected()) {
      if ($this->updateUserScores($userId, $scores)) {
        $this->insertScoresLog($userId, 'checkin', $scores, sprintf('签到成功，赚取 %d 个积分', $scores));
      }
      return array(
        'ranking' => $this->getCheckinRanking($userId, $date),
        'r' => $running + 1,
        's' => $scores,
      );
    }
    return 0;
  }

  /**
   * 获取签到列表
   * @param int $userId 用户id
   * @param string $month 月份, 格式为 YYYYmm
   * @return array
   */
  public function getCheckins($userId, $month)
  {
    if (!$userId) {
      return array();
    }
    $mysqlModel = $this->getModel('mysql');
    return $mysqlModel->getSqlBuilder()
        ->from('users_checkins')
        ->where('uid', $userId)
        ->where('date LIKE', $mysqlModel->escape($month) . '__')
        ->query()
        ->allWithKey('date');
  }

  public function getUserCheckinsByDate($date = null, $limit = 10)
  {
    if (!isset($date)) {
      $date = date('Ymd', REQUEST_TIME);
    }
    $limit = max(intval($limit), 1);
    $mysqlModel = $this->getModel('mysql');
    $result = $mysqlModel->getSqlBuilder()
        ->select('uc.*, uc.scores checkin_scores, u.*')
        ->from('users_checkins uc')
        ->join('users u', 'u.uid = uc.uid')
        ->where('uc.date', $date)
        ->orderby('uc.created DESC')
        ->limit($limit)
        ->query()
        ->all();
    foreach ($result as $row) {
      unset($row->password);
      $row->link = urlUser($row);
    }
    return $result;
  }

  /**
   * 获取签到信息
   * @param int $userId 用户id
   * @param string $date 日期, 格式为 YYYYmmdd
   * @return object
   */
  public function getCheckin($userId, $date = null)
  {
    if (!$userId) {
      return false;
    }
    if (!isset($date)) {
      $date = date('Ymd', REQUEST_TIME);
    }
    $mysqlModel = $this->getModel('mysql');
    return $mysqlModel->query('SELECT * FROM `users_checkins` WHERE `uid` = ' .
        intval($userId) . ' AND `date` = "' . $mysqlModel->escape($date) . '"')->row();
  }

  /**
   * 获取签到排名
   * @param int $userId 用户id
   * @param string $date 日期, 格式为 YYYYmmdd
   * @return int
   */
  public function getCheckinRanking($userId, $date = null)
  {
    $checkin = $this->getCheckin($userId, $date);
    return $checkin ? $checkin->ranking : 0;
  }

  /**
   * 获取连续签到天数
   * @param int $userId 用户id
   * @return int
   */
  public function getCheckinRunning($userId)
  {
    $yesterday = date('Ymd', REQUEST_TIME - 86400);
    $checkin = $this->getCheckin($userId, $yesterday);
    return $checkin ? $checkin->running : 0;
  }

  /**
   * 修改用户集分宝
   * @param int $userId 用户id
   * @param int $jf 集分宝增量
   * @return bool
   */
  public function updateUserJfs($userId, $jf)
  {
    $jf = intval($jf);
    if (!$jf) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $result = $mysqlModel->update('users', array(
      'jf' => array(
        'escape' => false,
        'value' => '`jf` + ' . intval($jf),
      ),
      'jf_amount' => array(
        'escape' => false,
        'value' => '`jf_amount` + ' . intval($jf),
      ),
    ), array('uid' => $userId));
    return $result->affected();
  }

  /**
   * 修改用户积分
   * @param int $userId 用户id
   * @param int $scores 积分增量
   * @return bool
   */
  public function updateUserScores($userId, $scores)
  {
    $scores = intval($scores);
    if (!$scores) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $result = $mysqlModel->update('users', array(
      'scores' => array(
        'escape' => false,
        'value' => '`scores` + ' . intval($scores),
      ),
    ), array('uid' => $userId));
    return $result->affected();
  }

  public function insertScoresLog($userId, $op, $scores, $body, $data = null)
  {
    $scores = intval($scores);
    if (!$scores) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $result = $mysqlModel->insert('users_scores_log', array(
      'uid' => $userId,
      'op' => $op,
      'body' => $body,
      'data' => isset($data) ? serialize($data) : '',
      'scores' => $scores,
      'ip' => ipAddress(),
      'created' => REQUEST_TIME,
    ));
    return true;
  }

   /**
   * 获取用户积分日志
   * @param int $conditions 查询条件
   * @param int $page
   * @param int $limit
   * @return array
   */
  public function getUsersScoresLogs($conditions = null, $page = 1, $limit = 15)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder()
        ->from('users_scores_log');
    if (isset($conditions)) {
      $query->where('op', $mysqlModel->escape($conditions));
    }
    $query->orderby('created DESC');
    if (isset($page)) {
      $query->limitPage($limit, $page);
    }
    return $query->query()->all();
  }

  /**
   * 获取用户积分日志的总数
   * @param int $conditions 查询条件
   * @return array
   */
  public function getUsersScoresLogsCount($conditions = null)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder();
    $query->select('COUNT(0)')
        ->from('users_scores_log');
    if (isset($conditions)) {
      $query->where('op', $mysqlModel->escape($conditions));
    }
    $result = $query->query()->field();
    return $result;
  }

   /**
   * 获取用户积分日志
   * @param int $userId 用户id
   * @param int $page
   * @param int $limit
   * @return array
   */
  public function getScoresLogs($userId, $page = null, $limit = 15)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder()
        ->from('users_scores_log')
        ->where('uid', $userId)
        ->orderby('created DESC');
    if (isset($page)) {
      $query->limitPage($limit, $page);
    }
    return $query->query()->all();
  }

  /**
   * 获取用户积分日志的总数
   * @param int $userId 用户id
   * @return array
   */
  public function getScoresLogsCount($userId)
  {
    $mysqlModel = $this->getModel('mysql');
    return $mysqlModel->query('SELECT COUNT(0) FROM `users_scores_log` WHERE `uid` = ' .
        intval($userId))->field();
  }

  /**
   * 新增一个用户分享
   * @param array $user 用户数组
   * @return int/bool  insertId, 失败返回 false
   */
  public function insertUserShares($user)
  {
    if (!isset($user['uid']) || !isset($user['pid'])) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $set = array(
      'uid' => isset($user['uid']) ? trim($user['uid']) : 0,
      'pid' => isset($user['pid']) ? trim($user['pid']) : 0,
      'created' => REQUEST_TIME,
      'date' => date('Ymd', REQUEST_TIME),
      'tid' => isset($user['tid']) ? trim($user['tid']) : '',
    );
    try {
      $insertId = $mysqlModel->insert('users_shares', $set)->insertId();
      return $insertId;
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
   * 获取用户分享并进行分页
   * @param array $conditions 分享查询条件数组
   * @param int $limit 分页显示数 默认20
   * @param int $page 页码 默认1
   * @return array 用户数组
   */
  public function getUserShares($conditions = null, $page = null, $limit = 15)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder();
    $query->from('users_shares')
          ->orderby('created DESC');
    if (isset($conditions['uid'])) {
      $query->where('uid', $mysqlModel->escape($conditions['uid']));
    }
    if (isset($conditions['date'])) {
      $query->where('date', $mysqlModel->escape($conditions['date']));
    }
    if (isset($conditions['groupby'])) {
      $query->groupby($conditions['groupby']);
    }
    if(isset($page)){
      $query->limitPage($limit, $page);
    }
    $result = $query->query()->all();
    return $result;
  }

  /**
   * 获取用户分享的总数
   * @param $conditions 查询条件
   * @return array
   */
  public function getUserSharesCount($conditions = null)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder();
    $query->select('COUNT(0)')
        ->from('users_shares');
    if (isset($conditions['uid'])) {
      $query->where('uid', $mysqlModel->escape($conditions['uid']));
    }
    if (isset($conditions['date'])) {
      $query->where('date', $mysqlModel->escape($conditions['date']));
    }
    $result = $query->query()->field();
    return $result;
  }

   /**
   * 新增一个用户轨迹
   * @param array $user 用户数组
   * @return  成功 object, 失败返回 false
   */
  public function insertUserTrajectory($trajectory)
  {
    if (!is_numeric($trajectory['uid']) || !is_numeric($trajectory['pid']) || !isset($trajectory['op']) ) {
      return false;
    }
    $url = $this->serviceUrl . '/insertTrajectory';
    $params = array(
      'uid' => $trajectory['uid'],
      'pid' => $trajectory['pid'],
      'op'  => $trajectory['op'],
    );
    $result = $this->post($url, $params);
    return !empty($result->result);
  }

  /**
   * 查询用户轨迹的轨迹
   * @param int $uid 用户id
   * @param int $itemsize 每天最后纪录轨迹的条数
   * @param int $page 页码
   * @param int $size 每页展示数
   * @return  成功 object, 失败返回 false
   */
  public function getUserTrajectorys($uid, $itemsize, $page = 1, $size = 7)
  {
    if (!is_numeric($uid) || !is_numeric($itemsize)) {
      return false;
    }
    $url = $this->serviceUrl . '/getTrajectorys';
    $params = array(
      'uid' => $uid,
      'itemsize' => $itemsize,
      'page' => $page - 1,
      'size'  => $size,
    );
    $result = $this->post($url, $params);
    return $result;
  }

  /**
   * 查询用户轨迹的轨迹总天数
   * @param int $uid 用户id
   * @return  成功 int , 失败返回 false
   */
  public function getUserTrajectorysCount($uid)
  {
    if (!is_numeric($uid)) {
      return false;
    }
    $url = $this->serviceUrl . '/getTrajectorysCount';
    $params = array(
      'uid' => $uid,
    );
    $result = $this->post($url, $params)->count;
    return $result;
  }


  /**
   * 获取全部奖品
   *
   */
  public function getPrizes($page = null, $limit = 15)
  {

    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder()
        ->from('prizes')
        ->where('count >','0')
        ->orderby('weight ASC');
      $query->limitPage($limit, $page);
    return $query->query()->all();
  }

  /**
   * 修改奖品库存
   * @param int $prizeId 用户pzid
   * @param int $scores 库存增量
   * @return bool
   */
  public function updatePrizeCount($prizeId, $count)
  {
    $count = intval($count);
    if (!$count) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $result = $mysqlModel->update('prizes', array(
      'count' => array(
        'escape' => false,
        'value' => '`count` + ' . intval($count),
      ),
    ), array('pzid' => $prizeId));
    return $result->affected();
  }

  /**
   * 插入中奖纪录
   *
   */
  public function insertPrizeLogs($log)
  {
    if (!isset($log['uid']) || !isset($log['pzid'])) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $set = array(
      'uid' => isset($log['uid']) ? trim($log['uid']) : 0,
      'pzid' => isset($log['pzid']) ? trim($log['pzid']) : 0,
      'created' => REQUEST_TIME,
      'date' => date('Ymd', REQUEST_TIME),
      'body' => isset($log['body']) ? trim($log['body']) : '',
    );
    try {
      $insertId = $mysqlModel->insert('prize_logs', $set)->insertId();
      return $insertId;
    } catch (BpfException $e) {
      return false;
    }
  }

   /**
   * 获取抽奖用户并进行分页
   *
   */
  public function getPrizeLogs($conditions = null, $page = 1, $limit = 30)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder()
        ->select('prize_logs.*')
        ->distinct()
        ->from('prize_logs');
    if (isset($conditions['where'])) {
      foreach($conditions['where'] as $k => $value ) {
      $query->where(trim($k), $value);
      }
    }
    if (isset($conditions['orderby'])) {
      $query->orderby(trim($conditions['orderby']));
    } else {
      $query->orderby('created DESC');
    }
    try {
      $result = $query->limitPage($limit, $page)->query()->all();
      return $result;
    } catch ( BpfException $e ) {
      return array();
    }
  }

  /**
   * 获取抽奖用户总数
   * @param array $conditions 中奖查询条件数组
   * @return int/bool 符合中奖总数, 查询条件出错返回false
   */
  public function getPrizeLogsCount($conditions)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder()
        ->select('COUNT(0)')
        ->distinct()
        ->from('prize_logs');
    if (isset($conditions['where'])) {
      foreach ($conditions['where'] as $k => $value ) {
        $query->where(trim($k), $value);
      }
    }
    try {
      $result = $query->query()->field();
      return $result;
    } catch ( BpfException $e ) {
      return false;
    }
  }

  /**
   * 获取当天抽奖信息
   * @param int $userId 用户id
   * @param string $date 日期, 格式为 YYYYmmdd
   * @return object
   */
  public function getUserPriceLog($userId, $date = null)
  {
    if (!$userId) {
      return false;
    }
    if (!isset($date)) {
      $date = date('Ymd', REQUEST_TIME);
    }
    $mysqlModel = $this->getModel('mysql');
    return $mysqlModel->query('SELECT * FROM `prize_logs` WHERE `uid` = ' .
        intval($userId) . ' AND `date` = "' . $mysqlModel->escape($date) . '"')->row();
  }

  /**
  * 新增一个用户反馈
  * @param array $feedback 用户反馈数组
  * @return int/bool 用户反馈ID, 失败返回 false
  */
  public function insertFeedback($feedback)
  {
    if (!isset($feedback['opinion']) || !isset($feedback['contact'])) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $set = array(
      'type' => isset($feedback['type']) ? trim($feedback['type']) : 1,
      'opinion' => trim($feedback['opinion']),
      'contact' => trim($feedback['contact']),
      'created' => REQUEST_TIME,
    );
    try {
      $insertId = $mysqlModel->insert('feedbacks', $set)->insertId();
      return $insertId;
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
   * 获取用户反馈总数
   * @param array $conditions 用户反馈查询条件数组
   * @return int 用户反馈总数
   */
  public function getFeedbacksCount($conditions = null)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder();
    $query->select('COUNT(0)')
        ->from('feedbacks');
    if (isset($conditions['opinion'])) {
      $query->where('opinion LIKE', '%' . $mysqlModel->escape($conditions['opinion']) . '%');
    }
    if (isset($conditions['type'])) {
      $query->where('type', $conditions['type']);
    }
    $result = $query->query()->field();
    return $result;
  }

  /**
   * 获取用户反馈并进行分页
   * @param array $conditions 用户反馈查询条件数组
   * @param int $limit 分页显示数 默认20
   * @param int $page 页码 默认1
   * @return array 用户反馈数组
   */
  public function getFeedbacks($conditions = null, $page = null, $limit = 15)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder();
    $query->from('feedbacks');
    if (isset($conditions['opinion'])) {
      $query->where('opinion LIKE', '%' . $mysqlModel->escape($conditions['opinion']) . '%');
    }
    if (isset($conditions['type'])) {
      $query->where('type', $conditions['type']);
    }
    $query->orderby('created DESC');
    if(isset($page)) {
      $query->limitPage($limit, $page);
    }
    $result = $query->query()->all();
    return $result;
  }


  /**
  *删除反馈对象
  *@param int $feedbackId 反馈对象
  *@return int/bool 影响行数，失败返回 false
  */
  public function deleteFeedback($feedbackId)
  {
    if(!isset($feedbackId)) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $result = $mysqlModel->delete('feedbacks',array('fid' => $feedbackId));
    $affected = $result->affected();
    return $affected;
  }

  /**
   * 查询用户邮件任务
   */
  public function getUserEmailTasks($startTime, $endTime)
  {
    if (!isset($startTime) || !isset($endTime)) {
      return false;
    }
    $params = array(
      'gt' => $startTime,
      'lte' => $endTime,
    );
    $url = $this->serviceUrl . '/getBehavior?' . http_build_query($params);
    $result = $this->get($url)->result;
    return $result;
  }

  /**
   * 添加用户点击数据
   * @return  成功 object, 失败返回 false
   */
  public function insertUserClicks($userClick)
  {
    if (!is_numeric($userClick['uid']) || !is_numeric($userClick['pid'])) {
      return false;
    }
    $url = $this->serviceUrl . '/insertBehavior';
    $params = array(
      'uid' => $userClick['uid'],
      'pid' => $userClick['pid'],
      'created'  => $userClick['created'],
      'date'  => $userClick['date'],
    );
    $result = $this->post($url, $params, false);
    return $result->result;
  }
}
