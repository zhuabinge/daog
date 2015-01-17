<?php
class MerchantActivitiesController extends BpfController
{
  // 活动管理-商品管理
  public function productsAction($page = 1)
  {
    if (!isLogin()) {
      gotoUrl('user/login');
    }
    if (!isMerchant()) {
      // 认证成功商家
      gotoUrl('merchant/user/shop_verify');
    }
    // 清除垃圾
    if(isset($_SESSION['renovate'])) {
      unset($_SESSION['renovate']);
    }
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
    $rows = 15; //每页数量
    //通过商家mid获取他的活动商品数据
    $view = $this->getView();
    $merchantModel = $this->getModel('merchant');
    $productModel = $this->getModel('product');
    $userModel = $this->getModel('user');
    $channelModel = $this->getModel('channel');
    $user = $GLOBALS['user'];
    $merchant = $merchantModel->getMerchant(array('uid' => $user->uid));
    // 店铺无效情况下。
    if (isset($merchant) && $merchant->status != 1) {
      gotoUrl('merchant/user/shop_verify');
    }
    $conditions = array(
      'where' => array(
        'mid' => $merchant->mid,
      ),
    );
    if (isset($_GET['status']) && is_numeric($_GET['status'])) {
      $conditions['where']['status'] = $_GET['status'];
    }
    $aProducts = $productModel->getProducts($conditions, $page, $rows);
    $aCount = $productModel->getProductsCount($conditions);
    foreach ($aProducts as $product) {
      if ($product->editor_uid > 0) {
        $product->nickname = $userModel->getUserInfo($product->editor_uid)->nickname;
      }
      $product->channels = $productModel->getProductChannels($product->pid);
      if (isset($product->channels)) {
        $product->aid = key($product->channels);
        $product->apid = reset($product->channels);
      }
      $product->category = $productModel->getCategory($product->cid);
    }
    $allCount = array();
    $conditions['where']['status'] = 0;
    $allCount[] = $productModel->getProductsCount($conditions);
    $conditions['where']['status'] = 1;
    $allCount[] = $productModel->getProductsCount($conditions);
    $conditions['where']['status'] = 2;
    $allCount[] = $productModel->getProductsCount($conditions);
    $conditions['where']['status'] = 3;
    $allCount[] = $productModel->getProductsCount($conditions);
    $conditions['where']['status'] = 4;
    $allCount[] = $productModel->getProductsCount($conditions);
    $conditions['where']['status'] = 5;
    $allCount[] = $productModel->getProductsCount($conditions);
    unset($conditions['where']['status']);
    $allCount[] = $productModel->getProductsCount($conditions);
    $view->assign(array(
      'allCount' => $allCount,
      'page' => $page,
      'rows' => $rows,
      'count' => $aCount,
      'merchant' => $merchant,
      'activities_products' => $aProducts,
    ));
    $view = $this->getView();
    $view->display('merchant/activities_products.phtml');
  }

  // 活动管理-订单管理
  public function orderAction()
  {
    if (!isLogin()) {
      gotoUrl('user/login');
    }
    if (!isMerchant()) {
      // 认证成功商家
      gotoUrl('merchant/user/shop_verify');
    }
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
    $rows = 15; //每页数量
    global $user;
    $view = $this->getView();
    $merchantModel = $this->getModel('merchant');
    $productModel = $this->getModel('product');
    $merchant = $merchantModel->getMerchant(array('uid' => $user->uid));
    $conditions = array(
      'merchantMid' => $merchant->mid,
    );
    if (isset($_GET['status']) && is_numeric($_GET['status'])) {
      $conditions['orderStatus'] = $_GET['status'];
    }
    $orders = $merchantModel->getOrders($conditions, $page, $rows);
    $count = $merchantModel->getOrdersCount($conditions);
    $view->assign(array(
      'page' => $page,
      'rows' => $rows,
      'count' => $count,
      'merchant' => $merchant,
      'orders' => $orders,
      'status' => isset($_GET['status']) ? $_GET['status'] : null,
    ));
    $view->display('merchant/activities_order.phtml');
  }

  // 活动申请 添加商品
  public function applyAction($aid = 0)
  {
    if (!isLogin()) {
      gotoUrl('user/login');
    }
    $view = $this->getView();

    $merchantModel = $this->getModel('merchant');
    $channelModel = $this->getModel('channel');
    $captchaModel = $this->getModel('captcha');
    $productModel = $this->getModel('product');
    $cacheModel = $this->getModel('cache');
    $user = $GLOBALS['user'];
    // 检测频道是否存在
    $channel = $channelModel->getChannel($aid);
    if (!$channel) {
      return BPF_NOT_FOUND;
    }
    $merchant = $merchantModel->getMerchant(array('uid' => $user->uid));
    // 店铺无效情况下。
    if (isset($merchant) && $merchant->status != 1) {
      gotoUrl('merchant/user/shop_verify');
    }
    $step = '1';
    $view->assign('step', 1);

    if($this->isPost())
    {
      if (!isMerchant()) {
        // 未认证成功商家
        gotoUrl('merchant/user/shop_verify');
      }
      // 成功之后刷新跳回活动页面
      if (isset($_SESSION['renovate'])) {
        unset($_SESSION['renovate']);
        gotoUrl('merchant/activities/products');
      }
      // 没有地址跳转到第二步
      if (!isset($_POST['url'])) {
        $step = '2';
      }
      if ($step == '1' && !empty($_POST['url'])) {
        $siteUrl = $_POST['url'];
        $item_id = '';
        if (preg_match('#id=(\d+).*#', $siteUrl, $match))
        {
        $item_id = $match[1];
        }
        if (!intval($item_id)) {
          $view->assign('step', 1);
          $view->assign('msg', '链接地址有错误，请重新输入！');
          $view->display('merchant/activities_apply.phtml');
          exit();
        }
        if (intval($item_id) && $itemAuth = $productModel->checkMallPid($item_id)) {
          $view->assign('step', 1);
          $view->assign('msg', '该商品已经参加过活动！');
          $view->display('merchant/activities_apply.phtml');
          exit();
        }
        // 检测缓存是否存在
        if (empty($cacheModel->get($item_id))) {
          $postdata = 'url='. urlencode($siteUrl) .'&token=kladkiewj4389jdsadf923cvmsdksa';
          $ch = curl_init();
          curl_setopt($ch,CURLOPT_URL, BpfConfig::get('java.url') . 'shop_get.do?');
          curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"POST");
          curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
          curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
          curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
          curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
          curl_setopt($ch,CURLOPT_AUTOREFERER,1);
          curl_setopt($ch,CURLOPT_POSTFIELDS,$postdata);
          curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
          $html = curl_exec($ch);
          $html = iconv("utf-8", "utf-8//IGNORE",$html);
          $json = json_decode($html);
          curl_close($ch);
        } else {
          $json = json_decode($cacheModel->get($item_id));
        }
        if($json->finish == 0) {
          $step = '1';
          $view->assign('msg', '数据获取失败，请检查链接是否正确。');
        } else if ($json->finish == 1) {
          if($json->shopId != $merchant->shop_sid) {
            $view->assign('msg', '该商品不是你店铺商品。');
            $step = '1';
          } else {
            $step = '2';
            $view->assign('json', $json);
            // 缓存采集好数据
            $cacheModel->set($item_id, json_encode($json), 6000);
            $_SESSION['p_data'] = isset($json) ? $json : '';
            $_SESSION['siteUrl'] = $siteUrl;
          }
        }
      } else if ($step == '2') {
        $p_data = $_SESSION['p_data'];
        $setProducts = array(
          'title' => isset($_POST['title']) ? $_POST['title'] : $p_data->title,
          'mall_pid' => isset($p_data->mallId) ? $p_data->mallId : '',
          'list_price' => isset($_POST['list_price']) ? $_POST['list_price'] : $p_data->sellPrice,
          'sellcount' => isset($p_data->sellCount) ? $p_data->sellCount : 0,
          'feature' => isset($_POST['feature']) ? $_POST['feature'] : $p_data->feature,
          'cid' => isset($_POST['cid']) ? $_POST['cid'] : $p_data->cateId,
          'sell_price' => isset($_POST['sell_price']) ? $_POST['sell_price'] : $p_data->promoPrice,
          'body' => $p_data->desc,
          'url' => isset($p_data->url) ? $p_data->url : $_SESSION['siteUrl'],
          'data' => isset($p_data->comment) ? $p_data->comment : '',
          'expiration' => isset($expiration) ? $expiration : 0,
          'buyerscore' => isset($p_data->buyerScore) ? $p_data->buyerScore : 0, // 用户评分
          'wantcount' => isset($p_data->wantCount) ? $p_data->wantCount : 0, // 想买人数
          'ratepercent' => isset($p_data->ratepercent) ? $p_data->ratepercent : 0, // 佣金比例
          'commission' => isset($p_data->commission) ? $p_data->commission : 0, // 佣金
          'totalnum' => isset($p_data->totalNum) ? $p_data->totalNum : 0, // 30天推广量
          'totalfeemoney' => isset($p_data->totalfeemoney) ? $p_data->totalfeemoney : 0, // 30天支出佣金
          'stock' => isset($_POST['stock']) ? $_POST['stock'] : '',
          'free' => isset($_POST['free']) ? $_POST['free'] : '',
          'delivery' => isset($_POST['delivery']) ? $_POST['delivery'] : '',
          'weight' => '1',
          'status' => 0,
          'mid' => $merchant->mid,
        );
        $setMerchant = array(
          'contact_wangwang' => isset($_POST['contact_wangwang']) ? $_POST['contact_wangwang'] : '',
          'seller' => isset($p_data->seller) ? $p_data->seller : '',
          'qq' => isset($_POST['qq']) ? $_POST['qq'] : '',
          'send_score' => isset($p_data->sendScore) ? $p_data->sendScore : '',
          'server_score' => isset($p_data->serverScore) ? $p_data->serverScore : '',
          'match_score' => isset($p_data->matchScore) ? $p_data->matchScore : '',
          'level' => isset($p_data->level) ? $p_data->level : '',
        );
        if ($captchaModel->checkCode('merchant_apply_code', $_POST['code'])) {
          $p = $productModel->checkMallPid($p_data->mallId);
          if (isset($_POST['file_images_id']) && $_POST['file_images_id'] != '' && isset($_POST['file_images2_id']) && $_POST['file_images2_id'] != '') {
            $setProducts['files'] = array($_POST['file_images_id'], $_POST['file_images2_id']);
          } else if (isset($_POST['file_images_id']) && $_POST['file_images_id'] != '') {
            $setProducts['files'] = array($_POST['file_images_id']);
          } else if (isset($_POST['file_images2_id']) && $_POST['file_images2_id'] != '') {
            $setProducts['files'] = array($_POST['file_images2_id']);
          }
          // 检查商品是否存在。
          if(!$p) {
            $pids = $productModel->insertProduct($setProducts);
            $channelModel->insertChannelProducts($aid, array($pids));
          } else {
            $productModel->updateProduct($p->pid, $setProducts);
            $pids = $p->pid;
            $channelModel->insertChannelProducts($aid, array($pids));
          }
          // 更新商家基本信息
          $mids = $merchantModel->updateMerchant($merchant->mid, $setMerchant);
          if ($mids && $pids) {
            $_SESSION['renovate'] = '1';
            unset($_SESSION['siteUrl']);
            $step = '3';
            unset($_SESSION['p_data']); // 用完抛弃
          } else {
            $json = (object)$setProducts;
            $merchant = (object)$setMerchant;
            $view->assign('json', $json);
            $view->assign('merchant', $merchant);
            $view->assign('msg', '非法操作，请重试');
          }
        } else {
          $json = array_merge($setProducts, $setMerchant);
          $json = (object)$json;
          if (isset($_SESSION['p_data']->images) && isset($_SESSION['p_data']->image_path)) {
            $json->images = $_SESSION['p_data']->images;
            $json->image_path = $_SESSION['p_data']->image_path;
          }
          $json->shopname = isset($json->shop_name) ? $json->shop_name : $_SESSION['p_data']->shopname;
          $json->mallId = $json->mall_pid;
          $json->matchScore = $json->match_score;
          $json->serverScore = $json->server_score;
          $json->sendScore = $json->send_score;
          $json->sellPrice = $json->list_price;
          $json->sellCount = $json->sellcount;
          $json->feature = $json->feature;
          $json->promoPrice = $json->sell_price;
          $json->desc = $json->body;
          $json->seller = $json->seller;
          $view->assign('json', $json);
          $merchant = (object)$setMerchant;
          $view->assign('merchant', $merchant);
          $view->assign('msg', '验证码错误');
        }
      }
    } else {
      // 编辑页面
      if(!empty($_GET['mall_id']) && !empty($_GET['apid']))
      {
        $aProduct = $productModel->getProduct($_GET['apid']);
        // 检测商品是否属于该商家
        if (!$aProduct || $merchant->mid != $aProduct->mid) {
          return BPF_NOT_FOUND;
        }
        $p = $productModel->checkMallPid($_GET['mall_id']);
        if($p) {
          // 图片ID
          if ($pImages = $productModel->getProductImages($p->pid)) {
            $p->files = array();
            foreach ($pImages as $key => $value) {
              array_push($p->files, $key);
            }
          }
          $step = '2';
          $json = array_merge((array)$p, (array)$merchant);
          $json = (object)$json;
          if ($pI = $productModel->getProductImages($p->pid)) {
            $json->images = $pI;
          }
          $json->image_path = $json->image_path;
          $json->shopname = $json->shop_name;
          $json->mallId = $json->mall_pid;
          $json->matchScore = $json->match_score;
          $json->serverScore = $json->server_score;
          $json->sendScore = $json->send_score;
          $json->sellPrice = $json->list_price;
          $json->sellCount = $json->sellcount;
          $json->shopId = $json->shop_sid;
          $json->feature = $json->feature;
          $json->promoPrice = $json->sell_price;
          $json->desc = $json->body;
          $json->seller = $json->seller;
          $view->assign('json', $json);
          $_SESSION['p_data'] = isset($json) ? $json : '';
        } else {
          return BPF_NOT_FOUND;
        }
      }
    }
    $cateList = $productModel->getHomeCategories();
    $conditions = array(
      'where' => array(
        'status' => 1,
        'deadline >' => REQUEST_TIME,
      ),
    );
    $conditions = array(
      'where' => array(
        'status' => 1,
      ),
      'orderby' => '`weight` DESC',
    );
    $channelsList = $channelModel->getChannels($conditions);
    $view->assign(array(
        'merchant' => $merchant,
        'cateList' => $cateList,
        'channelsList' => $channelsList,
        'step' => $step,
        'aid' => $aid,
    ));
    $this->addJs('js/plugins/fileupload/jquery.ui.widget.js');
    $this->addJs('js/plugins/fileupload/jquery.fileupload.js');
    $view->display('merchant/activities_apply.phtml');
  }

  // 上传图片
  public function uploadImagesAction()
  {
    if (!isLogin()) {
      return BPF_NOT_FOUND;
    }
    $set = array('msg' => '0');
    if (isset($_FILES['file_images']) && is_array($_FILES['file_images'])) {
      if ($_FILES['file_images']['size'] >= '300000') {
        return json_encode($set);
      }
      $type = $_FILES['file_images']['type'];
      $files = $_FILES['file_images'];
      $set['content'] = 'img-box1';
    } else if (isset($_FILES['file_images2']) && is_array($_FILES['file_images2'])) {
      if ($_FILES['file_images2']['size'] >= '300000') {
        return json_encode($set);
      }
      $type = $_FILES['file_images2']['type'];
      $files = $_FILES['file_images2'];
      $set['content'] = 'img-box2';
    } else {
      return json_encode($set);
    }
    if (isset($files) && is_array($files) && $type == 'image/jpeg' || $type == 'image/gif') {
      $fileModel = $this->getModel('file');
      $fileName = date('Ymd/', REQUEST_TIME) . $files['name'];
      $fileContent = file_get_contents($files['tmp_name']);
      $file = $fileModel->write('pro_img', $fileName, $fileContent);
      if (isset($file) && $file) {
        $set['file_id'] = $file->file_id;
        $set['file_path'] = $file->file_path;
        $set['msg'] = '1';
      }
    } else {
      return json_encode($set);
    }
    return json_encode($set);
  }

  // 验证码
  public function captchaAction()
  {
    $captchaModel = $this->getModel('captcha');
    $key = 'merchant_apply_code';
    $captchaModel->buildCode($key);
    $captchaModel->display($key, 140, 32);
  }
}
