<?php
class MerchantUserController extends BpfController
{
  // 店铺认证
  public function shop_verifyAction()
  {
  	if (!isLogin()) {
 	    gotoUrl('user/login');
  	}
    $merchantModel = $this->getModel('merchant');
    $user = $GLOBALS['user'];
  	$view = $this->getView();
    $view->assign('step', 1);
    $merchant = $merchantModel->getMerchant(array('uid' => $user->uid));
    if (isMerchant()) {
      if (isset($merchant) && $merchant->status != 1) {
        $view->assign('msg', '店铺已经被冻结，请联系客服！');
      }
      // 认证成功商家
      $view->assign('step', 3);
    } else {
      $product = $this->getModel('product');
      $cateList = $product->getTopCategories();
      if(empty($merchant))
      {
        if($this->isPost())
        {
         switch ($_POST['step']){
          case '1':

          $merchant = $merchantModel->getMerchant(array('shop_url' => parse_url($_POST['url'], PHP_URL_HOST)));
          if(isset($_POST['url'])&&isset($merchant))
          {
            if(strstr($_POST['url'],'taobao.com')||strstr($_POST['url'],'tmall.com'))
            {
              header("Content-Type:text/html;charset=utf-8");
              $html = @file_get_contents(trim($_POST['url']));
              $html = iconv("gb2312", "utf-8//IGNORE",$html);
              $title = '没有成功获取到标题';
              if (preg_match('#<title>(.*)</title>#isU', $html, $match))
              {
                $title = $match[1];
                if(strstr($title,'ffWdc9A7'))
                {
                 if (preg_match('#\<a class="slogo-shopname".*\<strong\>(.*)\</strong\>\</a\>#isU', $html, $match))
                 {
                   $shopName = $match[1];
                 }
                 if (preg_match('#<div class="tb-shop-name">\s*<dl>\s*<dd>\s*<strong>\s*<a href="(.*)" title="(.*)"#isU', $html, $match))
                 {
                   $shopName = $match[2];
                   $shopUrl = $match[1];
                 }
                 if (preg_match('#shopid="(\d+)"#isU', $html, $match))
                 {
                   $shop_sid = $match[1];
                 }
                 if (preg_match('#shopUrl:"(.*)"#isU', $html, $match))
                 {
                   $shopUrl = $match[1];
                 }
                 if (preg_match('#掌.*柜：</label>\s*<div class="right">\s*<a.*>(.*)</a>#isU', $html, $match))
                 {
                   $seller = $match[1];
                 }
                 if (preg_match('#掌柜.*</dt.*\s*.*\s*<a.*>\s*(.*)\s*</a>#isU', $html, $match))
                 {
                   $seller = $match[1];
                 }
                 if(strstr($_POST['url'],'taobao.com'))
                 {
                   $merchantType = 'C';
                 }
                 else if (strstr($_POST['url'],'tmall.com')){
                   $merchantType = 'B';
                 }

                 $merchant = $merchantModel->getMerchant(array('shop_url' => $shopUrl, ));
                 if(!empty($merchant))
                 {
                  $view->assign(array(
                  'message' => '商店已绑定！',
                   ));
                 }
                 else
                 {
                 $view->assign('shop_name', $shopName);
                 $view->assign('merchant_type', $merchantType);
                 $view->assign('shop_url', $shopUrl);
                 $view->assign('seller', $seller);
                 $view->assign('shop_sid', $shop_sid);
                 $view->assign('step', 2);
                 }
               } else {
                  $view->assign(array(
                    'message' => '验证失败，请检查验证码是否正确！',
                  ));
                }
              }
            } else {
            $view->assign(array(
              'message' => '输入链接有错误，请重新输入！',
            ));
          }
          }
          else
          {
           $view->assign(array(
            'message' => '商店不存在或者已绑定！',
            ));
         }
         break;

         case '2':
         $category = $_POST['category'];
         $shopName = $_POST['shopName'];
         $merchantType = $_POST['merchantType'];
         $shopUrl = $_POST['shopUrl'];
         $seller = $_POST['seller'];
         $shop_sid = $_POST['shopId'];
         $uid = $user->uid;
         $merchant = array(
          'category' => $category,
          'shop_name' => $shopName,
          'merchant_type' => $merchantType,
          'shop_url' => $shopUrl,
          'shop_sid' => $shop_sid,
          'uid' => $uid,
          'seller' => $seller,
          'status' => 1,
          );
         if($merchantId = $merchantModel->insertMerchant($merchant))
         {
          $userModel = $this->getModel('user');
          $merchantRoleId = BpfConfig::get('merchant.rid');
          if ($userModel->setUserRoles($user->uid, array($merchantRoleId))) {
            $user->merchant = true;
          };

          $view->assign(array(
            'step' => '3',
            ));
        }
        break;

        default:
            # code...
        break;
         }
       }
      }
      $view->assign('cateList', $cateList);
    }

    $view->display('merchant/shop_verify.phtml');
  }

  // 修改密码
  public function passwordAction()
  {
    if (!isLogin()) {
      gotoUrl('user/login');
    }
    global $user;
    $view = $this->getView();
    $merchantModel = $this->getModel('merchant');
    $merchant = $merchantModel->getMerchant(array('uid' => $user->uid));
    // 店铺无效情况下。
    if (isset($merchant) && $merchant->status != 1) {
      gotoUrl('merchant/user/shop_verify');
    }
    if (!$merchant) {
      $view->assign('verify', 1);
    }
    $view->display('merchant/password.phtml');
  }

  // 修改登陆密码
  public function verify_login_passwordAction()
  {
    if (!isLogin()) {
      gotoUrl('user/login');
    }
    if ($this->isPost()) {

    }
    $view = $this->getView();
    $view->display('merchant/password_login.phtml');
  }

  // 修改支付密码
  public function verify_pay_passwordAction()
  {
    if (!isLogin()) {
      gotoUrl('user/login');
    }
    global $user;
    $msg = ''; // 提示
    $view = $this->getView();
    $merchantModel = $this->getModel('merchant');
    $merchant = $merchantModel->getMerchant(array('uid' => $user->uid));
    // 店铺无效情况下。
    if (isset($merchant) && $merchant->status != 1) {
      gotoUrl('merchant/user/shop_verify');
    }
    if (isset($merchant->pay_password)) {
      $view->assign('step', 1);
    }
    if ($this->isPost()) {
      if (isset($merchant) && $merchant) {
        $password = !empty($_POST['password']) ? $_POST['password'] : '';
        $payPassword = $_POST['pay_password'];
        $payPasswordConfirmation = $_POST['pay_password_confirmation'];
        if (isset($merchant->pay_password) && $merchant->pay_password != md5($password)) {
          $view->assign('msg', '原始密码错误，请重试');
          $view->display('merchant/password_pay.phtml');
          exit();
        }
        if (!empty($payPassword) && !empty($payPasswordConfirmation) && $payPassword === $payPasswordConfirmation) {
          if (isset($merchant)) {
            $set = array(
              'pay_password' => $payPassword,
            );
            $merchantModel->updateMerchant($merchant->mid, $set);
            $view->assign('step', 1);
            $msg = '密码设置成功'; // 提示
          } else {
            $msg = '操作错误，请重试'; // 提示
          }
        } else {
          $msg = '两次输入密码不一致，请重试'; // 提示
        }
      } else {
        $msg = '请先验证店铺信息在设置支付密码'; // 提示
      }
    }
    $view->assign('msg', $msg);
    $view->display('merchant/password_pay.phtml');
  }

  // 绑定手机
  public function binding_phoneAction()
  {
    if (!isLogin()) {
      gotoUrl('user/login');
    }
    // 清除垃圾
    if(isset($_SESSION['renovate'])) {
      unset($_SESSION['renovate']);
      gotoUrl('merchant/user/binding_phone');
    }
    global $user;
    $view = $this->getView();
    $view->assign('step', 1);
    $merchantModel = $this->getModel('merchant');
    $cacheModel = $this->getModel('cache');
    $merchant = $merchantModel->getMerchant(array('uid' => $user->uid));
    // 店铺无效情况下。
    if (isset($merchant) && $merchant->status != 1) {
      gotoUrl('merchant/user/shop_verify');
    }

    //判断是否认证成功，成功才绑定
    if (!$merchant) {
      $view->assign('verify', 1);
      $view->display('merchant/binding_phone.phtml');
      exit();
    }
    // 检测手机是否存在
    if (isset($merchant->telphone) && $merchant->telphone != '') {
      $view->assign('step', 3);
    }
    if(isset($_POST['change']) && $_POST['change'] != ''){
      $view->assign('step', 1);
    }

    if($this->isPost()) {
      if(isset($_POST['telphone']) && $_POST['telphone'] != '') {
        $merchant = $merchantModel->getMerchant(array('telphone' => $_POST['telphone']));
        if(!empty($merchant))
        {
          $view->assign('step', 1);
          $view->assign('msg', '该手机号码已被绑定');
        } else {
          if (empty($cacheModel->get('telphone'))) {
            $telphone = $_POST['telphone'];
            $randcode = $merchantModel->sendVerificationCode($telphone);
            // 缓存手机号码和验证码2分钟有效
            $cacheModel->set('telphone', $telphone, 120);
            $cacheModel->set('randcode', $randcode, 120);
            $view->assign('step', 2);
          } else {
            $view->assign('step', 2);
            $view->assign('msg', '验证码已经发送，请查看手机！');
          }
        }
      }

      if(isset($_POST['code'])) {
        $telphone = $cacheModel->get('telphone');
        $randcode = $cacheModel->get('randcode');
        if(!empty($randcode) && !empty($telphone) && $randcode == $_POST['code']) {
          $set = array(
            'telphone' => $telphone,
          );
          $merchantModel->updateMerchant($merchant->mid, $set);
          // 清空缓存
          $cacheModel->delete('telphone');
          $cacheModel->delete('randcode');
          $_SESSION['renovate'] = true; //防止重复刷新
          $view->assign('step', 3);
        } else {
          $view->assign('step', 2);
          $view->assign('msg', '验证失败，请检查验证码是否正确！');
        }
      }
    }
    $view->display('merchant/binding_phone.phtml');
  }

  // 绑定邮箱
  public function binding_emailAction()
  {
    if (!isLogin()) {
      gotoUrl('user/login');
    }
    // 清除垃圾
    if(isset($_SESSION['renovate'])) {
      unset($_SESSION['renovate']);
      gotoUrl('merchant/user/binding_email');
    }
    global $user;
    $view = $this->getView();
    $view->assign('step', 1);
    $merchantModel = $this->getModel('merchant');
    $merchant = $merchantModel->getMerchant(array('uid' => $user->uid));
    // 店铺无效情况下。
    if (isset($merchant) && $merchant->status != 1) {
      gotoUrl('merchant/user/shop_verify');
    }
    // 检测邮箱是否存在
    if (isset($merchant->email) && $merchant->email != '') {
      $view->assign('step', 3);
    }
    // 需要验证店铺才能绑定
    if (!$merchant) {
      $view->assign('verify', 1);
      $view->display('merchant/binding_email.phtml');
      exit();
    }
    if ($this->isPost()) {
      $email = isset($_POST['email']) ? $_POST['email'] : '';
      $emailCode = isset($_POST['code']) ? $_POST['code'] : '';
      $verifyStep = isset($_POST['step']) ? $_POST['step'] : 1;
      $mode = '/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/';
      if (isset($email) && !preg_match($mode, $email, $content) && $verifyStep == 1) {
        $view->assign('msg', '邮箱输入错误，重新输入');
        $view->assign('step', 1);
      } else if (isset($email) && $email != '' && $emailCode == '' && $merchant && $verifyStep == 1) {
        $mailModel = $this->getModel('mail');
        $user->code = randomString(32);
        $econtent = '亲爱的会员：<br>您申请绑定邮箱，请在复制验证码已便完成设置。<br>绑定邮箱验证码：' . $user->code . '<br>此为系统邮件，请勿回复 天天逛逛：www.ttgg.com';
        $user->yemail = $email;
        $mailModel->send($email, '天天逛逛邮箱验证', $econtent);
        $view->assign('step', 2);
      } else if (isset($emailCode) && $emailCode != '' && $verifyStep == 2) {
        if ($emailCode == $user->code) {
          if (isset($merchant) && isset($user->yemail)) {
            $set = array(
              'email' => $user->yemail,
            );
            $merchantModel->updateMerchant($merchant->mid, $set);
            $view->assign('step', 3);
            $_SESSION['renovate'] = true; //防止重复刷新
            $view->assign('msg', '邮箱设置成功');
          }
        } else {
          $view->assign('msg', '邮箱验证码错误，重新输入');
          $view->assign('step', 2);
        }
      } else {
        $view->assign('msg', '邮箱输入错误，重新输入');
      }
    }
    $view->display('merchant/binding_email.phtml');
  }

  // 资金管理
  public function fundsAction()
  {
  	if (!isLogin()) {
  	  gotoUrl('user/login');
  	}
    $user = $GLOBALS['user'];
  	$view = $this->getView();
  	$merchantModel = $this->getModel('merchant');
  	$merchant = $merchantModel->getMerchant(array('uid' => $user->uid));
    // 店铺无效情况下。
    if (isset($merchant) && $merchant->status != 1) {
      gotoUrl('merchant/user/shop_verify');
    }
    if (isset($merchant->mid)) {
      $conditions = array( //过滤条件
        'where' =>  array(
          'mid' => $merchant->mid,
        ),
      );
      $count = $merchantModel->getCostsCount($conditions);
      $costsList = $merchantModel->getCosts($conditions, 1, $count);
      $view->assign(array(
        'merchant' => $merchant,
        'costsList' => $costsList,
      ));
    }
    $view->display('merchant/funds.phtml');
  }

  // 资金充值
  public function funds_rechargeAction()
  {
    if (!isLogin()) {
      gotoUrl('user/login');
    }
    global $user;
    $merchantModel = $this->getModel('merchant');
    if (!$merchant = $merchantModel->getMerchant(array('uid' => $user->uid))) {
      gotoUrl('merchant/user/shop_verify');
    }
    // 店铺无效情况下。
    if (isset($merchant) && $merchant->status != 1) {
      gotoUrl('merchant/user/shop_verify');
    }
    $view = $this->getView();
    if ($this->isPost()) {
      if (!isset($_POST['WIDtotal_fee']) || intval($_POST['WIDtotal_fee']) < 1 || intval($_POST['WIDtotal_fee']) > 9999999) {
        $view->assign('msg', '输入金额有误，重新输入');
        $view->display('merchant/funds_recharge.phtml');
        exit();
      }
      require SYSPATH . '/models/alipaydirect/alipay.config.php';
      require SYSPATH . '/models/alipaydirect/alipay_submit.class.php';
      header("Content-Type:text/html; charset=utf-8");
      //构造要请求的参数数组，无需改动
      $parameter = array(
        "service" => "create_direct_pay_by_user",
        "partner" => trim($alipay_config['partner']),
        "payment_type"  => '1', //支付类型 必填，不能修改
        "notify_url"  => url('merchant/user/funds_notify/' . $merchant->mid, true), //服务器异步通知页面路径
        // "notify_url"  => 'http://test.ec61.com/merchant/user/funds_notify/' . $merchant->mid, //服务器异步通知页面路径
        "return_url"  => url('merchant/user/funds_return', true), //页面跳转同步通知页面路径
        "seller_email"  => 'ttgg_pay@163.com', //卖家支付宝帐户
        "out_trade_no"  => randomString(32), //商户网站订单系统中唯一订单号，必填
        "subject" => '天天逛逛商家充值', //订单名称
        "total_fee" => $_POST['WIDtotal_fee'], //必填 付款金额
        // "total_fee" => 0.01, //必填 付款金额 测试使用
        "body"  => '', //订单描述
        "show_url"  => '', //商品展示地址
        "anti_phishing_key" => '', //若要使用请调用类文件submit中的query_timestamp函数
        "exter_invoke_ip" => '', //客户端的IP地址
        "_input_charset"  => trim(strtolower($alipay_config['input_charset'])),
      );
      $alipaySubmitModel = new AlipaySubmit($alipay_config);
      $html_text = $alipaySubmitModel->buildRequestForm($parameter, "get", "正在跳转至支付宝请稍后...");
      echo $html_text;
    }
    $view->display('merchant/funds_recharge.phtml');
  }

  // 充值成功回调地址
  public function funds_notifyAction($mid = 0)
  {
    $merchantModel = $this->getModel('merchant');
    if (!$merchant = $merchantModel->getMerchant(array('mid' => $mid))) {
      return BPF_NOT_FOUND;
    }
    // 店铺无效情况下。
    if (isset($merchant) && $merchant->status != 1) {
      return BPF_NOT_FOUND;
    }
    if ($this->isPost() && isset($_POST['trade_no']) && isset($_POST['total_fee'])) {
      $set = array(
        'mid' => $mid,
        'content' => '账户余额充值',
        'cost' => $_POST['total_fee'],
        'type' => 1,
        'op' => 'funds_notify',
        'order_id' => trim($_POST['trade_no']),
      );
      $cId = $merchantModel->insertCost($set);
      $mIds = $merchantModel->updateMerchant($mid, array('deposit' => array('escape' => false, 'value' => 'deposit + ' . $_POST['total_fee'],)));
      if ($cId && $mIds) {
        return 'success';
      };
    } else {
      return BPF_NOT_FOUND;
    }
  }

  public function funds_returnAction()
  {
    if (!isLogin()) {
      gotoUrl('user/login');
    }
    global $user;
    $merchantModel = $this->getModel('merchant');
    if (!$merchantModel->getMerchant(array('uid' => $user->uid)) || $_GET['buyer_id'] != '2088602024463331') {
      return BPF_NOT_FOUND;
    }
    // 店铺无效情况下。
    if (isset($merchant) && $merchant->status != 1) {
      return BPF_NOT_FOUND;
    }
    $view = $this->getView();
    $view->assign('step', 1);
    $view->display('merchant/funds_recharge.phtml');
  }

  // 保证金缴纳
  public function funds_marginAction()
  {
    if (!isLogin()) {
      gotoUrl('user/login');
    }
    global $user;
    $merchantModel = $this->getModel('merchant');
    if (!$merchant = $merchantModel->getMerchant(array('uid' => $user->uid))) {
      gotoUrl('merchant/user/shop_verify');
    }
    // 店铺无效情况下。
    if (isset($merchant) && $merchant->status != 1) {
      gotoUrl('merchant/user/shop_verify');
    }
    $view = $this->getView();
    if ($this->isPost()) {
      if (!isset($_POST['WIDtotal_fee']) || intval($_POST['WIDtotal_fee']) < 1) {
        $view->assign('msg', '输入金额有误，重新输入');
      } else if (!isset($_POST['pay_password']) || md5($_POST['pay_password']) != $merchant->pay_password) {
        $view->assign('msg', '输入支付密码错误，重新输入');
      } else if (!($merchant->deposit >= $_POST['WIDtotal_fee'])) {
        $view->assign('msg', '账户余额不足，<a href="/merchant/user/funds_recharge" target="_black">点击充值</a>');
      } else if (md5($_POST['pay_password']) == $merchant->pay_password) {
        $fee = $_POST['WIDtotal_fee'];
        $set = array(
          'deposit' => array(
            'escape' => false,
            'value' => 'deposit - ' . $fee,
          ),
          'margin' => array(
            'escape' => false,
            'value' => 'margin + ' . $fee,
          ),
        );
        $merchantModel->updateMerchant($merchant->mid, $set);
        $set = array(
          'mid' => $merchant->mid,
          'content' => '商家保证金缴纳',
          'cost' => '-' . $fee,
          'type' => 1,
          'op' => 'funds_margin',
        );
        $merchantModel->insertCost($set);
        $set = array(
          'mid' => $merchant->mid,
          'content' => '商家保证金缴纳',
          'money' => $fee,
        );
        $merchantModel->insertInvoice($set);
        gotoUrl('merchant/user/funds');
      }
    }
    $view->display('merchant/funds_margin.phtml');
  }

  // 技术服务费缴纳
  public function funds_serviceAction()
  {
    if (!isLogin()) {
      gotoUrl('user/login');
    }
    global $user;
    $merchantModel = $this->getModel('merchant');
    if (!$merchant = $merchantModel->getMerchant(array('uid' => $user->uid))) {
      gotoUrl('merchant/user/shop_verify');
    }
    // 店铺无效情况下。
    if (isset($merchant) && $merchant->status != 1) {
      gotoUrl('merchant/user/shop_verify');
    }
    $view = $this->getView();
    if ($this->isPost()) {
      if (!isset($_POST['WIDtotal_fee']) || intval($_POST['WIDtotal_fee']) < 1) {
        $view->assign('msg', '输入金额有误，重新输入');
      } else if (!isset($_POST['pay_password']) || md5($_POST['pay_password']) != $merchant->pay_password) {
        $view->assign('msg', '输入支付密码错误，重新输入');
      } else if (!($merchant->deposit >= $_POST['WIDtotal_fee'])) {
        $view->assign('msg', '账户余额不足，<a href="/merchant/user/funds_recharge" target="_black">点击充值</a>');
      } else if (md5($_POST['pay_password']) == $merchant->pay_password) {
        $fee = $_POST['WIDtotal_fee'];
        $set = array(
          'deposit' => array(
            'escape' => false,
            'value' => 'deposit - ' . $fee,
          ),
          'service_fee' => array(
            'escape' => false,
            'value' => 'service_fee + ' . $fee,
          ),
        );
        $merchantModel->updateMerchant($merchant->mid, $set);
        $set = array(
          'mid' => $merchant->mid,
          'content' => '技术服务费缴纳',
          'cost' => '-' . $fee,
          'type' => 1,
          'op' => 'funds_service',
        );
        $merchantModel->insertCost($set);
        $set = array(
          'mid' => $merchant->mid,
          'content' => '技术服务费缴纳',
          'money' => $fee,
        );
        $merchantModel->insertInvoice($set);
        gotoUrl('merchant/user/funds');
      }
    }
    $view->display('merchant/funds_service.phtml');
  }

  // 我的发票
  public function invoicesAction()
  {
    if (!isLogin()) {
      gotoUrl('user/login');
    }
    global $user;
    $merchantModel = $this->getModel('merchant');
    if (!$merchant = $merchantModel->getMerchant(array('uid' => $user->uid))) {
      gotoUrl('merchant/user/shop_verify');
    }
    // 店铺无效情况下。
    if (isset($merchant) && $merchant->status != 1) {
      gotoUrl('merchant/user/shop_verify');
    }
    $conditions = array(
      'status' => 0,
      'mid' => $merchant->mid,
    );
    $iCount = $merchantModel->getInvoicesSum($conditions);
    // 已开发票金额
    $conditions['status'] = 1;
    $nCount = $merchantModel->getInvoicesSum($conditions);
    $view = $this->getView();
    $view->assign('iCount', $iCount);
    $view->assign('nCount', $nCount);
    $view->display('merchant/invoices.phtml');
  }
}
