<?php
/**
 * 集分宝
 * @author Hao <sixihaoyue@gmail.com>
 */
//倒入aop接口
require SYSPATH . '/models/jifenbaoAop/AopClient.php';
require SYSPATH . '/models/jifenbaoAop/AlipayPointBalanceGetRequest.php';
require SYSPATH . '/models/jifenbaoAop/AlipayPointBudgetGetRequest.php';
require SYSPATH . '/models/jifenbaoAop/AlipayPointOrderAddRequest.php';
require SYSPATH . '/models/jifenbaoAop/AlipayPointOrderGetRequest.php';
require SYSPATH . '/models/jifenbaoAop/AlipaySystemOauthTokenRequest.php';

class JiFenBaoModel extends BpfModel
{
  private $_client;
  public function __init()
  {
    //初始化client
    $this->_client = new AopClient;
    $this->_client->appId = '2014101900014023';
    $this->_client->rsaPrivateKeyFilePath = dirname(__FILE__)  . '/jifenbaoAop/rsa_private_key_new.pem';
  }

  /**
   * 获取授权码
   * @param string $code 回调code
   * @return string oauthToken 授权码
   */
  public function getOauthToken($code = null) {
    if (empty($code)) {
      return '';
    }
    $requset = new AlipaySystemOauthTokenRequest();
    $requset->setCode($code);
    $requset->setGrantType('authorization_code');
    $result = $this->_client->execute($requset);
    if (isset($result->alipay_system_oauth_token_response)) {
      return $result->alipay_system_oauth_token_response->access_token;
    }
    return '';
  }

  /**
   * 发放集分宝
   * @param string $user  发放集分宝的用户对象
   * @return string oauthToken 授权码
   */
  public function faFang($user, $oauthToken = null)
  {
    if (empty($oauthToken)) {
      return false;
    }
    $jf = intval($user->jf);
    if ($jf < 1) {
      return false;
    }
    $merchantOrderNo = date('YmdHis', REQUEST_TIME) . sprintf('%010d', $user->uid);
    $requset = new AlipayPointOrderAddRequest();
    //支付宝账号
    $requset->setUserSymbol($user->alipay);
    //账号类型，这里写死为支付宝类型
    $requset->setUserSymbolType('ALIPAY_LOGON_ID');
    //向用户展示集分宝发放备注， 这里写死
    $requset->setMemo('[天天逛逛集分宝发放]');
    //发放集分宝的数量
    $requset->setPointCount($jf);
    //发放集分宝时间 写死当前时间
    $requset->setOrderTime(date('Y-m-d H:i:s', REQUEST_TIME));
    //isv提供的发放号订单号，由数字和组成，最大长度为32 时间＋uid
    $requset->setMerchantOrderNo($merchantOrderNo);
    $resp = $this->_client->execute($requset, $oauthToken);
     $set = array(
      'oid' => $merchantOrderNo,
      'uid' => $user->uid,
      'date' => date('Ymd', REQUEST_TIME),
      'alipay' => $user->alipay,
      'jf' => $jf,
      'created' => REQUEST_TIME,
    );
    if (isset($resp->alipay_point_order_add_response)) {
      //支付成功
      //进行订单的记录
      $mysqlModel = $this->getModel('mysql');
      $set['status'] = 1;
      $set['alipay_order_no'] = $resp->alipay_point_order_add_response->alipay_order_no;
      $set['paid'] = time();
      $mysqlModel->insert('users_jf_orders', $set);
      //清除该用户的jf
      $this->_clearUserJf($user->uid);
      return true;
    } else {
      //支付错误
      //进行订单的记录
      $mysqlModel = $this->getModel('mysql');
      $set['status'] = 0;
      $set['data'] = json_encode($resp);
      $mysqlModel->insert('users_jf_orders', $set);
      return false;
    }
  }

  /**
  * 查询账户余额
  * @param string $oauthToken
  */
  public function chaXunYuE($oauthToken = null)
  {
    if (empty($oauthToken)) {
      return 0;
    }
    $requset = new AlipayPointBudgetGetRequest();
    $resp = $this->_client->execute($requset, $oauthToken);
    if (isset($resp->alipay_point_budget_get_response)) {
      return $resp->alipay_point_budget_get_response->budget_amount;
    }
    return 0;
  }

  /**
  * 查询订单
  * @param string $alipay  支付宝账户
  * @param string $oid  isv订单id
  * @param string $user  发放集分宝的用户对象
  * @param string $oauthToken
  */
  public function chaXunZhiFuBaoDingDan($alipay = null, $oid = null, $oauthToken = null)
  {
    if (empty($alipay) || empty($oid) || empty($oauthToken) ) {
      return false;
    }
    $requset = new AlipayPointOrderGetRequest();
    $requset->setMerchantOrderNo($oid);
    $requset->setUserSymbol($alipay);
    $requset->setUserSymbolType('ALIPAY_LOGON_ID');
    $resp = $this->_client->execute($requset, $oauthToken);
    if (isset($resp->alipay_point_order_get_response)) {
      return $resp->alipay_point_order_get_response;
    } else {
      return $resp->error_response;
    }
  }

  /**
   * 获取要发放的集分宝名单
   * @param
   * @return array
   */
  public function getFaFangList()
  {
    return $mysqlModel = $this->getModel('mysql')
        ->query('SELECT * FROM `users` ' .
          'WHERE `jf` > 0 ' .
          'AND `status` = 1 '.
          'AND (`alipay` <> "" AND `alipay` IS NOT NULL)')
        ->all();
  }

  /**
   * 获取要发放的集分宝的总数
   * @return int
   */
  public function getFaFangSum()
  {
    return $mysqlModel = $this->getModel('mysql')
        ->query('SELECT SUM(`jf`) FROM `users` ' .
          'WHERE `jf` > 0 ' .
          'AND `status` = 1 '.
          'AND (`alipay` <> "" AND `alipay` IS NOT NULL)')
        ->field();
  }

  /**
   * 将用户的jf字段置0
   * @param userId
   */
  private function _clearUserJf($userId)
  {
    $this->getModel('user')->updateUser($userId, array(
      'jf' => 0,
    ));
  }

}
