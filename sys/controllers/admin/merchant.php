<?php
class AdminMerchantController extends BpfController
{
  public static function __permissions()
  {
    return array(
      'name' => '商家中心',
      'permissions' => array(
        'merchant-view' => '商家列表',
        'merchant-edit' => '商家编辑',
        'activities-view' => '活动列表',
        'activities-edit' => '活动编辑',
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
    // 商家列表
    if (!access('merchant-view')) {
      throw new BpfException();
    }
    $rows = 15;
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
    $merchantModel = $this->getModel('merchant');
    $productModel = $this->getModel('product');
    $conditions = array(
      'search' => empty($_GET['seller']) ? '' : $_GET['seller'],
    );
    $count = $merchantModel->getMerchantsCount($conditions);
    $merchantsList = $merchantModel->getMerchants($conditions, $page, $rows);
    foreach ($merchantsList as $key => $value) {
      $conditions = array(
        'where' => array(
          'mid' => $value->mid,
        ),
      );
      if ($status = $productModel->getProducts($conditions, 1, 1)) {
        $value->pStatus = $status[0]->status;
      } else {
        $value->pStatus = '-1';
      }
    }
    $view = $this->getView();
    $view->assign(array(
      'count' => $count,
      'page' => $page,
      'rows' => $rows,
      'merchantsList' => $merchantsList,
    ));
    $view->display('admin/merchant/merchant.phtml');
  }

  public function editAction($mid = 0)
  {
    if (!access('merchant-edit')) {
      throw new BpfException();
    }
    $view = $this->getView();
    $merchantModel = $this->getModel('merchant');
    $productModel = $this->getModel('product');
    if (isset($mid) && is_numeric($mid)) {
      // 修改商家
      $merchant = $merchantModel->getMerchant(array('mid' => $mid));
      if (!$merchant) {
        throw new BpfException();
      }
      if ($this->isPost()) {
        $set = array(
          // 'seller' => $_POST['seller'],
          // 'shop_url' => $_POST['shop_url'],
          // 'shop_name' => $_POST['shop_name'],
          'telphone' => $_POST['telphone'],
          'contact_wangwang' => $_POST['contact_wangwang'],
          'qq' => $_POST['qq'],
          'merchant_type' => $_POST['merchant_type'],
          'email' => $_POST['email'],
          'send_score' => $_POST['send_score'],
          'server_score' => $_POST['server_score'],
          'match_score' => $_POST['match_score'],
          'category' => $_POST['category'],
          'status' => $_POST['status'],
          'contacts' => $_POST['contacts'],
        );
        $mids = $merchantModel->updateMerchant($mid, $set);
        if ($mids) {
          setMessage('商家修改成功', 'success');
          gotoUrl('admin/merchant');
        } else {
          setMessage('商家修改失败', 'error');
          gotoUrl('admin/merchant/edit/' . $mid);
        }
      }
      $this->addJs('js/plugins/select2/select2.min.js'); //下拉框插件
      $view->assign('merchant', $merchant);
    } else {
      throw new BpfException();
    }
    $cateList = $productModel->getTopCategories();
    $view->assign(array(
      'cateList' => $cateList,
    ));
    $view->display('admin/merchant/merchant_edit.phtml');
  }

  public function ordersAction()
  {
    $merchantModel = $this->getModel('merchant');
    // 订单列表
    $rows = 15;
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;

    $conditions = array();
    if (!empty($_GET['orderId'])) {
      $conditions['orderId'] = $_GET['orderId'];
    }
    if (isset($_GET['orderStatus']) && ($_GET['orderStatus'] == 1 || $_GET['orderStatus'] == 0)) {
      $conditions['orderStatus'] = $_GET['orderStatus'];
    }
    if (!empty($_GET['merchantName'])) {
      $conditions['merchantName'] = $_GET['merchantName'];
    }
    $ordersList = $merchantModel->getOrders($conditions, $page, $rows);
    $count =  $merchantModel->getOrdersCount($conditions);
    $view = $this->getView();
    $view->assign(array(
      'count' => $count,
      'page' => $page,
      'rows' => $rows,
      'ordersList' => $ordersList,
    ));
    $this->addJs('js/plugins/select2/select2.min.js'); //下拉框插件
    $view->display('admin/merchant/orders.phtml');
  }

  public function invoicesAction()
  {
    $merchantModel = $this->getModel('merchant');
    // 发票列表
    $rows = 15;
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
    $conditions = array();
    if (!empty($_GET['invoiceStatus']) ) {
      if ($_GET['invoiceStatus'] == 1 || $_GET['invoiceStatus'] == 0) {
          $conditions['status'] = $_GET['invoiceStatus'];
      }
    }
    if (!empty($_GET['merchantName'])) {
      $conditions['merchantName'] = $_GET['merchantName'];
    }
    $invoicesList = $merchantModel->getInvoices($conditions, $page, $rows);
    $count =  $merchantModel->getInvoicesCount($conditions);
    $view = $this->getView();
    $view->assign(array(
      'count' => $count,
      'page' => $page,
      'rows' => $rows,
      'invoicesList' => $invoicesList,
    ));
    $this->addJs('js/plugins/select2/select2.min.js'); //下拉框插件
    $view->display('admin/merchant/invoices.phtml');
  }

  public function productAction()
  {
    $mid = isset($_GET['mid']) ? $_GET['mid'] : 0;
    if (empty($mid)) {
      throw new BpfException();
    }
    $rows = 15;
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
    $view = $this->getView();
    $productModel = $this->getModel('product');
    $merchantModel = $this->getModel('merchant');
    $userModel = $this->getModel('user');
    $conditions = array(
      'where' => array(
        'mid' => $mid,
      ),
    );
    $poductsList = $productModel->getProducts($conditions, $page, $rows);
    $count = $productModel->getProductsCount($conditions);
    foreach ($poductsList as $key => $value) {
      $conditions = array(
        'mid' => $value->mid,
        'pid' => $value->pid,
      );
      $value->ordersCount = $merchantModel->getMerchantsProductsOrdersCount($conditions);
      if (!empty($value->editor_uid)) {
        $value->nickname = $userModel->getUser($value->editor_uid)->nickname;
      }
    }

    $view->assign(array(
      'page' => $page,
      'rows' => $rows,
      'count' => $count,
      'poductsList' => $poductsList,
    ));
    $view->display('admin/merchant/merchant_product.phtml');
  }

  // public function activitiesAction()
  // {
  //   if (!access('activities-view')) {
  //     throw new BpfException();
  //   }
  //   // 活动列表
  //   $rows = 15;
  //   $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
  //   $merchantModel = $this->getModel('merchant');
  //   $channelModel = $this->getModel('channel');
  //   $conditions = array(
  //     'search' => empty($_GET['content']) ? '' : $_GET['content'],
  //   );
  //   $count = $merchantModel->getActivitiesCount($conditions);
  //   $activitiesList = $merchantModel->getActivities($conditions, $page, $rows);
  //   foreach ($activitiesList as $key => $value) {
  //     $value->channel = $channelModel->getChannel($value->cid);
  //   }
  //   $view = $this->getView();
  //   $view->assign(array(
  //     'count' => $count,
  //     'page' => $page,
  //     'rows' => $rows,
  //     'activitiesList' => $activitiesList,
  //   ));
  //   $view->display('admin/merchant/activities.phtml');
  // }

  // public function activities_editAction($aid = null)
  // {
  //   if (!access('activities-edit')) {
  //     throw new BpfException();
  //   }
  //   if (!is_numeric($aid)) {
  //     gotoUrl('/admin/merchant/activities');
  //   }
  //   if ($this->isPost()) {
  //     if ($aid != $_POST['aid']) {
  //       gotoUrl('/admin/merchant/activities');
  //     }
  //     $activitie = array();
  //     if (!empty($_POST['title'])) {
  //       $activitie['title'] = $_POST['title'];
  //     }
  //     if (!empty($_POST['content'])) {
  //       $activitie['content'] = $_POST['content'];
  //     }
  //     if (!empty($_POST['channel'])) {
  //       $activitie['cid'] = $_POST['channel'];
  //     }
  //     if (!empty($_POST['deadline'])) {
  //       $activitie['deadline'] = strtotime($_POST['deadline']);
  //     }
  //     if (is_numeric($_POST['status'])) {
  //       $activitie['status'] = $_POST['status'];
  //     }
  //     if (!empty($activitie)) {
  //       $merchantModel = $this->getModel('merchant');
  //       if ($aid == 0) {
  //         $result = $merchantModel->insertActivitie($activitie);
  //         if ($result === false) {
  //           setMessage('添加活动失败', 'error');
  //         } else {
  //           setMessage('添加活动成功', 'success');
  //           $aid = $result;
  //         }
  //       } else {
  //         $merchantModel->updateActivitie($aid, $activitie);
  //         setMessage('活动更新成功', 'success');
  //       }
  //       gotoUrl('/admin/merchant/activities');
  //     }
  //   } else {
  //     $merchantModel = $this->getModel('merchant');
  //     $view = $this->getView();
  //     if ($aid != 0) {
  //       $activitie = $merchantModel->getActivitie($aid);
  //       if (empty($activitie)) {
  //         gotoUrl('/admin/merchant/activities');
  //       }

  //       $view->assign(array(
  //         'activitie' => $activitie,
  //         'channelsList' => empty($channelsList) ? array() : $channelsList,
  //       ));
  //     }
  //     $channelModel = $this->getModel('channel');
  //     $channelsList = $channelModel->getChannels(array(
  //       'where' => array(
  //         'status' => 1,
  //       )
  //     ),1, 100);
  //     $view->assign(array(
  //       'channelsList' => empty($channelsList) ? array() : $channelsList,
  //       'aid' => $aid,
  //     ));
  //     $this->addJs('js/jquery-ui.js');
  //     $this->addJs('js/plugins/select2/select2.min.js'); //下拉框插件
  //     $view->display('admin/merchant/activitie_edit.phtml');
  //   }
  // }

  // public function activities_getProductsAction($aid = null)
  // {
  //   if(!is_numeric($aid)) {
  //     throw new BpfException();
  //   }
  //   $view = $this->getView();
  //   $rows = 15;
  //   if ($aid == 0) {
  //     $view->assign(array(
  //       'aid' => 0,
  //       'productlist' => array(),
  //       'page' => 1,
  //       'total' => 0,
  //       'rows' => $rows,
  //     ));
  //   } else {
  //     $page = empty($_GET['page']) ? 1 : $_GET['page'];
  //     $merchantModel = $this->getModel('merchant');
  //     $conditions = array(
  //       'where' => array(
  //        'aid' => $aid,
  //       ),
  //     );
  //     if (!empty($_GET['title'])) {
  //       $conditions['search'] = $_GET['title'];
  //     }
  //     $userModel = $this->getModel('user');
  //     $productModel = $this->getModel('product');
  //     $productlist =  $merchantModel->getActivitieProducts($conditions, $page, $rows);
  //     $total =  $merchantModel->getActivitieProductsCount($conditions);
  //     foreach ($productlist as $key => $sa) {
  //       $sa->user = $userModel->getUser($sa->a_editor_uid);
  //       $sa->cate = $productModel->getCategory($sa->cid);
  //       $sa->activitie = $merchantModel ->getActivitie($sa->aid);
  //     }
  //     $view->assign(array(
  //       'aid' => $aid,
  //       'productlist' => $productlist,
  //       'page' => $page ? $page : 1,
  //       'total' => $total,
  //       'rows' => $rows,
  //     ));
  //   }
  //   $view->display('admin/merchant/activitie_product.phtml');
  // }
  //   //修改状态
  //  public function activities_setProductsAction($aid = null)
  // {
  //   if (empty($_POST['status']) || empty($_POST['apid'])) {
  //     return json_encode(array(
  //      'msg' => '',
  //     ));
  //   }
  //   $apids = $_POST['apid'];

  //   $info = array(
  //     'a_status' =>  $_POST['status'],
  //     'a_editor_uid' => $_POST['a_editor'],
  //   );
  //   $merchantModel = $this->getModel('merchant');
  //   $result = $merchantModel->updateActivitieProducts($apids, $info);
  //   if ($result === false) {
  //     return json_encode(array(
  //      'msg' => '操作失败',
  //     ));
  //   }else{
  //   if($_POST['status'] == 3){
  //     foreach ($apids as $key => $id) {
  //     $a = $merchantModel->getActivitieProduct($id);
  //     unset($a->title);
  //     unset($a->adress);
  //     unset($a->body);
  //     unset($a->image);
  //     unset($a->feature);
  //     $ap[] = $a;

  //     }
  //     $list = new stdClass();
  //     $list->apList = $ap;
  //     $postdata = 'product_json='. urlencode(json_encode($list)).'&token=kladkiewj4389jdsadf923cvmsdksa';
  //     $ch = curl_init();
  //     curl_setopt($ch,CURLOPT_URL,BpfConfig::get('java.url') . 'shop_order.do?');
  //     curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"POST");
  //     curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
  //     curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
  //     curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
  //     curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
  //     curl_setopt($ch,CURLOPT_AUTOREFERER,1);
  //     curl_setopt($ch,CURLOPT_POSTFIELDS,$postdata);
  //     curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
  //     curl_exec($ch);
  //     curl_close($ch);
  //   }
  //     return json_encode(array(
  //    'msg' => '操作成功',
  //   ));
  //   }

  // }

  // public function productsAction($page = 1)
  // {
  //   $rows = 15; //每页数量
  //   $view = $this->getView();
  //   $merchantModel = $this->getModel('merchant');
  //   $userModel = $this->getModel('user');
  //   $productModel = $this->getModel('product');
  //   $conditions = array( //过滤条件
  //     'where' => array(),
  //   );
  //   if (!empty($_GET['title'])) {
  //     $conditions['search'] = $_GET['title'];
  //   }
  //   if (!empty($_GET['aid'])) {
  //     $conditions['where']['aid']= $_GET['aid'];
  //   }
  //   $count = $merchantModel->getActivitieProductsCount($conditions);
  //   $productlist = $merchantModel->getActivitieProducts($conditions, $page, $rows);
  //   foreach ($productlist as $key => $sa) {
  //     $sa->user = $userModel->getUser($sa->a_editor_uid);
  //     $sa->cate = $productModel->getCategory($sa->cid);
  //     $sa->activitie = $merchantModel->getActivitie($sa->aid);
  //     $sa->merchant = $merchantModel->getMerchant($sa->mid);
  //   }
  //   $activities = $merchantModel->getActivities(null , 1, 1000);
  //   $this->addJs('js/plugins/select2/select2.min.js'); //下拉框插件
  //   $view->assign('activities', $activities);
  //   $view->assign('page', $page);
  //   $view->assign('rows', $rows);
  //   $view->assign('count', $count);
  //   $view->assign('productlist', $productlist);
  //   $view->display('admin/merchant/product.phtml');
  // }
}
