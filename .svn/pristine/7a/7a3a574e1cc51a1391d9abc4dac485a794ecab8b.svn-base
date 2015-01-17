<?php
class AdminAdController extends BpfController
{
  public static function __permissions()
  {
    return array(
      'name' => '广告管理',
      'permissions' => array(
        'ad-view' => '查询广告',
        'ad-edit' => '编辑广告',
        'ad-delete' => '删除广告',
        'ader-view' => '查询广告主',
        'ader-edit' => '编辑广告主',
        'ader-delete' => '删除广告主',
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

  public function indexAction($page = 1)
  {
    if (!access('ad-view')) {
      throw new BpfException();
    }
    $rows = 15; //每页数量
    $view = $this->getView();
    $adModel = $this->getModel('ad');
    $userModel = $this->getModel('user');
    // 过滤条件
    $conditions = array(
      'orderby' => '`status` DESC,`updated` DESC',
    );
    $filter = array();
    if (!empty($_GET['sid'])) {
      $conditions['socket_id'] = $_GET['sid'];
    }
    if (!empty($_GET['stime'])) {
      $conditions['date'] = $_GET['stime'];
    } else {
      $conditions['date'] = date('Ymd', REQUEST_TIME);
    }
    // 排序规则
    if (!empty($_GET['sortv'])) {
      $_GET['sortv'] == 1 ? array_push($filter, '`views` DESC') : array_push($filter, '`views` ASC');
    }
    if (!empty($_GET['sortc'])) {
      $_GET['sortc'] == 1 ? array_push($filter, '`cvp` DESC') : array_push($filter, '`cvp` ASC');
    }
    if (!empty($_GET['uptime'])) {
      $_GET['uptime'] == 1 ? array_push($filter, '`updated` DESC') : array_push($filter, '`updated` ASC');
    }
    if (!empty($_GET['ctime'])) {
      $_GET['ctime'] == 1 ? array_push($filter, '`created` DESC') : array_push($filter, '`created` ASC');
    }
    if (!empty($_GET['uv'])) {
      $_GET['uv'] == 1 ? array_push($filter, '`tclicks` DESC') : array_push($filter, '`tclicks` ASC');
    }
    if (count($filter) > 0) {
      $conditions['orderby'] = implode(',', $filter);
    }
    
    if (!access('ader-view') || !access('ader-edit')) {
      $adsOwner = $adModel->getAdsOwner(array('uid' => $GLOBALS['user']->uid)); //所属广告主
      $oIds = array();
      foreach ($adsOwner as $key => $value) {
        $oIds[] = $value->oid;
      }
      $conditions['oid'] = $oIds;
    }
    // 过滤广告主试用
    if (!empty($_GET['oid']) && $_GET['oid'] > 0) {
      $conditions['oid'] = array($_GET['oid']);
    }
    if (isset($_GET['all']) && $_GET['all'] == 2) {
      unset($conditions['oid']);
    }
    // 指定用户
    if (isset($_GET['uid']) && $_GET['uid'] > 0) {
      $adsOwner = $adModel->getAdsOwner(array('uid' => $_GET['uid'])); //所属广告主
      $oIds = array();
      foreach ($adsOwner as $key => $value) {
        $oIds[] = $value->oid;
      }
      $conditions['oid'] = $oIds;
    }
    // 所有广告主
    $adsOwnerList = $adModel->getAdsOwners();
    // 所有广告列表
    $adsCount = $adModel->getAdsCount($conditions);
    $adsList = $adModel->getAds($conditions, $page, $rows);
    foreach ($adsList as $key => $value) {
      $owner = $adModel->getAdsOwner(array('oid' => $value->oid)); //所属广告主
      $user = array();
      foreach ($owner as $key => $uuu) {
        $user[] = $userModel->getUserInfo($uuu->uid);
      }
      $userNickname = array();
      foreach ($user as $key => $uu) {
        $userNickname[] = $uu->nickname;
      }
      $value->nickname = implode(',', $userNickname);
    }
    $adsSockets = $adModel->getSockets(); // 获取所有广告位
    $totalPage = ($adsCount / $rows);
    if ($adsCount % $rows > 0 ) {
      $totalPage++;
    }
    if ($page != 1 && $page > $totalPage) { //溢出处理
      $sid = isset($_GET['sid']) ? '?sid=' . $_GET['sid'] : '';
      gotoUrl('admin/ad/index/1' . $sid);
    }
    $this->addJs('js/plugins/select2/select2.min.js'); //下拉框插件
    $view->assign('page', is_numeric($page) ? $page : 1);
    $view->assign('rows', $rows);
    $view->assign('count', $adsCount);
    $view->assign('adsList', $adsList);
    $view->assign('adsOwnerList', $adsOwnerList);
    $view->assign('adsSockets', $adsSockets);
    $view->display('admin/ad/ad.phtml');
  }

  public function viewAction($aid = 0)
  {
    if (!access('ad-view')) {
      throw new BpfException();
    }
    $view = $this->getView();
    $adModel = $this->getModel('ad');
    $ads = $adModel->getAd($aid);
    if (!$ads) {
      throw new BpfException();
    }
    // 时间检测
    if (empty($_GET['time'])) {
      $date = date('Ymd', REQUEST_TIME);
      $yesdate = date('Ymd', REQUEST_TIME - 86400);
    } else {
      $date = date('Ymd', strtotime($_GET['time']));
      $yesdate = date('Ymd', strtotime($_GET['time']) - 86400);
    }
    $adsReport = $adModel->getAdsReport($aid);
    $yesterday = $adModel->getAdsReport($aid, $yesdate);
    // 当天
    $infodash = array(
      'tclicks' => array(),
      'views' => array(),
      'clicks' => array(),
    );
    $strtotime = strtotime($date) / 60;
    for ($i = 0; $i < 1440; $i++) { 
      $minute = ($strtotime + $i);
      if (isset($adsReport[$i]->minute) && trim($adsReport[$i]->minute) == $minute) {
        $infodash['tclicks'][] = $adsReport[$i]->clicks_tcounter;
        $infodash['views'][] = $adsReport[$i]->counter;
        $infodash['clicks'][] = $adsReport[$i]->clicks_counter;
      } else {
        $infodash['tclicks'][] = 0;
        $infodash['views'][] = 0;
        $infodash['clicks'][] = 0;
      }
    }
    $infodash['tclicks'] = implode(',', $infodash['tclicks']);
    $infodash['views'] = implode(',', $infodash['views']);
    $infodash['clicks'] = implode(',', $infodash['clicks']);
    // 昨天
    $yestInfodash = array(
      'tclicks' => array(),
      'views' => array(),
      'clicks' => array(),
    );
    $yesStrtotime = strtotime($yesdate) / 60;
    for ($i = 0; $i < 1440; $i++) { 
      $minute = ($yesStrtotime + $i);
      if (isset($yesterday[$i]->minute) && trim($yesterday[$i]->minute) == $minute) {
        $yestInfodash['tclicks'][] = $yesterday[$i]->clicks_tcounter;
        $yestInfodash['views'][] = $yesterday[$i]->counter;
        $yestInfodash['clicks'][] = $yesterday[$i]->clicks_counter;
      } else {
        $yestInfodash['tclicks'][] = 0;
        $yestInfodash['views'][] = 0;
        $yestInfodash['clicks'][] = 0;
      }
    }
    $yestInfodash['tclicks'] = implode(',', $yestInfodash['tclicks']);
    $yestInfodash['views'] = implode(',', $yestInfodash['views']);
    $yestInfodash['clicks'] = implode(',', $yestInfodash['clicks']);
    $view->assign('ads', $ads);
    $view->assign('strtotime', $strtotime);
    $view->assign('yestInfodash', $yestInfodash);
    $view->assign('infodash', $infodash);
    $this->addJs('js/plugins/highcharts/highcharts.js');
    $view->display('admin/ad/ad_view.phtml');
  }

  public function editAction($aid = 0)
  {
    if (!access('ad-edit')) {
      throw new BpfException();
    }
    $view = $this->getView();
    $adModel = $this->getModel('ad');
    $fileModel = $this->getModel('file');
    $adsSockets = $adModel->getSockets();
    foreach ($adsSockets as &$value) {
      $value = plain($value);
    }
    // 所属广告主
    $adsOwner = $adModel->getAdsOwner(array('uid' => $GLOBALS['user']->uid));

    if ($aid === 0) {
      if ($this->isPost()) {
        $set = array(
          'type' => $_POST['type'],
          'name' => $_POST['name'],
          'url' => $_POST['url'],
          'status' => $_POST['status'],
          'code' => $_POST['code'],
        );
        if (isset($adsOwner) && !empty($_POST['oid'])) {
          $set['oid'] = $_POST['oid'];
        }
        if (isset($_FILES['image_path']) && $_FILES['image_path']['tmp_name'] != '')
        { //图片处理
          $fileName = date('Ymd/', REQUEST_TIME) . $_FILES['image_path']['name'];
          $fileContent = file_get_contents($_FILES['image_path']['tmp_name']);
          $file = $fileModel->write('banner_img', $fileName, $fileContent);
          if (isset($file) && $file) {
            $set['file_id'] = $file->file_id;
          }
        }
        $aids = $adModel->insertAd($set);
        if ($aids) {
          if (isset($_POST['sid']) && $_POST['sid']) {
            $sid = explode(',', $_POST['sid']);
            $adModel->setAdSockets($aids, $sid);
          }
          setMessage('广告添加成功', 'success');
          gotoUrl('admin/ad');
        } else {
          setMessage('广告添加失败', 'success');
          gotoUrl('admin/ad/edit');
        }
      }
    } else if (is_numeric($aid)) {
      //修改广告
      $ads = $adModel->getAd($aid);
      if (!$ads) {
        throw new BpfException();
      }
      $socket_id = $adModel->getAdSockets($aid);
      if ($this->isPost()) {
        $set = array(
          'type' => $_POST['type'],
          'name' => $_POST['name'],
          'url' => $_POST['url'],
          'status' => $_POST['status'],
          'code' => $_POST['code'],
        );
        if (isset($adsOwner) && !empty($_POST['oid'])) {
          $set['oid'] = $_POST['oid'];
        }
        if (isset($_FILES['image_path']) && $_FILES['image_path']['tmp_name'] != '')
        { //图片处理
          $fileName = date('Ymd/', REQUEST_TIME) . $_FILES['image_path']['name'];
          $fileContent = file_get_contents($_FILES['image_path']['tmp_name']);
          $file = $fileModel->write('banner_img', $fileName, $fileContent);
          if (isset($file) && $file) {
            $set['file_id'] = $file->file_id;
          }
        }
        $aids = $adModel->updataAd($aid, $set);
        if ($aids) {
          if (isset($_POST['sid']) && $_POST['sid']) {
            // CURL清空缓存
            $uri = "http://pos.ttgg.com/pppp.php";
            // 参数数组
            $data = array(
              'type' => $_POST['sid'],
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $uri);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $return = curl_exec($ch);
            curl_close($ch);
            if (empty($return)) {
              setMessage('站外广告数据同步失败', 'error');
            } else {
              setMessage('站外广告数据同步成功', 'success');
            }
            // 修改广告位
            $sid = explode(',', $_POST['sid']);
            $adModel->setAdSockets($aid, $sid);
          }
          setMessage('广告修改成功', 'success');
          gotoUrl('admin/ad');
        } else {
          setMessage('广告修改失败', 'success');
          gotoUrl('admin/ad/edit');
        }
      }
      $view->assign('socketTags', is_array($socket_id) ? implode(',', $socket_id) : '');
      $view->assign('socket_id', $socket_id);
      $view->assign('ads', $ads);
    } else {
      throw new BpfException();
    }
    $view->assign('adsOwner', $adsOwner);
    $this->addJs('js/plugins/select2/select2.min.js'); //下拉框插件
    $view->assign('adsSockets', is_array($adsSockets) ? implode('","',  $adsSockets) : '');
    $view->display('admin/ad/ad_edit.phtml');
  }

  public function removeAction($aid)
  {
    if (!access('ad-delete')) {
      throw new BpfException();
    }
    if (isset($aid) && is_numeric($aid)) {
      $adModel = $this->getModel('ad');
      $aids = $adModel->deleteAd($aid);
      if ($aids) {
        setMessage('删除操作成功', 'success');
      } else {
        setMessage('删除操作失败', 'error');
      }
      gotoUrl('admin/ad');
    } else {
      throw new BpfException();
    }
  }

  // 批量操作
  public function batchAction()
  {
    if (!access('ad-delete')) {
      throw new BpfException();
    }
    $batchaid = $_POST['aid'];
    if ($this->isPost()) {
      if (empty($batchaid) || !is_array($batchaid)) {
        setMessage('操作失败，请重新选择', 'error');
        gotoUrl('admin/ad');
      } else if (is_array($batchaid) && $_POST['status'] != -1) {
        $adModel = $this->getModel('ad');
        if ($_POST['status'] == 1 || $_POST['status'] == 0) {
          $status = ($_POST['status'] == 1) ? 'pass' : 'delect';
          // 临时使用更新站外广告
          foreach ($batchaid as $value) {
            $ad = $adModel->getAdSockets($value);
            if (isset($ad[0])) {
              // CURL清空缓存
              $uri = "http://pos.ttgg.com/pppp.php";
              // 参数数组
              $data = array(
                'type' => $ad[0],
              );
              $ch = curl_init();
              curl_setopt($ch, CURLOPT_URL, $uri);
              curl_setopt($ch, CURLOPT_POST, 1);
              curl_setopt($ch, CURLOPT_TIMEOUT, 5);
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
              curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
              $return = curl_exec($ch);
              curl_close($ch);
              if (empty($return)) {
                setMessage('站外广告数据同步失败', 'error');
              } else {
                setMessage('站外广告数据同步成功', 'success');
              }
            }
          }
          $adModel->updateAds($batchaid, $status);
        } else if ($_POST['status'] == 2) {
          $adModel->deleteAds($batchaid);
        }
        $page = '';
        if (is_numeric($_POST['page'])) {
          $page = '/index/' . $_POST['page'];
        }
        setMessage('批量操作成功', 'success');
        gotoUrl('admin/ad' . $page);
      }
    } else {
      throw new BpfException();
    }
  }

  // 广告主
  public function aderAction()
  {
    if (!access('ader-view')) {
      throw new BpfException();
    }
    $rows = 15;
    $page = 1;
    if (isset($_GET['page']) && is_numeric($_GET['page'])) {
      $page = $_GET['page'];
    }
    $adModel = $this->getModel('ad');
    $adsCount = $adModel->getAdsOwnersCount();
    $adsList = $adModel->getAdsOwners(null, 1, $adsCount);

    $view = $this->getView();
    $view->assign(array(
      'adsList' => $adsList,
      'rows' => $rows,
      'page' => $page,
      'count' => $adsCount,
    ));
    $view->display('admin/ad/ader.phtml');
  }

  public function aderEditAction($oId = 0)
  {
    if (!access('ader-edit')) {
      throw new BpfException();
    }
    $adModel = $this->getModel('ad');
    $userModel = $this->getModel('user');
    // 符合角色的用户列表
    $total = $userModel->getRoleUsersCount(1);
    $userList = $userModel->getRoleUsers(1, null, 1, $total);
    // 增加广告主
    if (empty($oId)) {
      if ($this->isPost()) {
        $set = array(
          'name' => empty($_POST['name']) ? null : $_POST['name'],
          'uids' => empty($_POST['uids']) ? null : $_POST['uids'],
        );
        if ($adModel->insertAdsOwners($set)) {
          setMessage('广告主添加成功', 'success');
          gotoUrl('admin/ad/ader');
        } else {
          setMessage('广告主添加失败', 'error');
          gotoUrl('admin/ad/aderEdit');
        }
      }
    } elseif (is_numeric($oId)) {
      // 修改广告主
      $adsList = $adModel->getAdsOwner(array('oid' => $oId));
      if (!empty($adsList)) {
        $adsUid = array(); // 初始化
        $adsName = $adsList[0]->name; // 广告主
        foreach ($adsList as $key => $value) {
          $adsUid[] = $value->uid; // 所有用户ID
        }

        if ($this->isPost()) {
          $set = array(
            'name' => empty($_POST['name']) ? null : $_POST['name'],
          );
          if (is_array($_POST['uids'])) {
            $oIds = $adModel->updataAdsOwner($oId, $set);
            if ($oIds) {
              $adModel->setAdsOwners($oId, $_POST['uids']);
              setMessage('广告主修改成功', 'success');
              gotoUrl('admin/ad/ader');
            } else {
              setMessage('广告主修改失败', 'error');
              gotoUrl('admin/ad/aderEdit/' . $oId);
            };
          }
        }
      } else {
        throw new BpfException();
      }
    }
    $view = $this->getView();
    $view->assign(array(
      'oId' => is_numeric($oId) ? $oId : 0,
      'userList' => $userList,
      'adsUid' => empty($adsUid) ? array() : $adsUid,
      'adsName' => empty($adsName) ? array() : $adsName,
    ));
    $this->addJs('js/plugins/select2/select2.min.js'); //下拉框插件
    $view->display('admin/ad/ader_edit.phtml');
  }

  public function aderDelAction($oid)
  {
    if (!access('ader-delete')) {
      throw new BpfException();
    }
    if (isset($oid) && is_numeric($oid)) {
      $adModel = $this->getModel('ad');
      $oids = $adModel->deleteAdsOwners($oid);
      if ($oids) {
        setMessage('删除操作成功', 'success');
      } else {
        setMessage('删除操作失败', 'error');
      }
      gotoUrl('admin/ad/ader');
    } else {
      throw new BpfException();
    }
  }
}
