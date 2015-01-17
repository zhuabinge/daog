<?php
/**
 * 商家类
 * @author KaiFu <aixinqing@gmail.com>
 */
class MerchantModel extends BpfModel
{
  /**
   * 插入商家对象
   * @param arrary $merchant 商家对象
   * @return int/bool 新商家ID, 失败返回 false
   */
  public function insertMerchant($merchant)
  {
    if (!isset($merchant['uid'])) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $set = array(
      'uid' => isset($merchant['uid']) ? trim($merchant['uid']) : '',
      'seller' => isset($merchant['seller']) ? trim($merchant['seller']) : '',
      'shop_sid' => isset($merchant['shop_sid']) ? trim($merchant['shop_sid']) : '',
      'shop_url' => isset($merchant['shop_url']) ? trim($merchant['shop_url']) : '',
      'margin' => isset($merchant['margin']) ? trim($merchant['margin']) : '',
      'deposit' => isset($merchant['deposit']) ? trim($merchant['deposit']) : '',
      'service_fee' => isset($merchant['service_fee']) ? trim($merchant['service_fee']) : '',
      'send_score' => isset($merchant['send_score']) ? trim($merchant['send_score']) : '',
      'server_score' => isset($merchant['server_score']) ? trim($merchant['server_score']) : '',
      'match_score' => isset($merchant['match_score']) ? trim($merchant['match_score']) : '',
      'level' => isset($merchant['level']) ? trim($merchant['level']) : '',
      'shop_name' => isset($merchant['shop_name']) ? trim($merchant['shop_name']) : '',
      'telphone' => isset($merchant['telphone']) ? trim($merchant['telphone']) : '',
      'contact_wangwang' => isset($merchant['contact_wangwang']) ? trim($merchant['contact_wangwang']) : '',
      'email' => isset($merchant['email']) ? trim($merchant['email']) : '',
      'qq' => isset($merchant['qq']) ? trim($merchant['qq']) : '',
      'merchant_type' => isset($merchant['merchant_type']) ? trim($merchant['merchant_type']) : '',
      'category' => isset($merchant['category']) ? trim($merchant['category']) : '',
      'status' => isset($merchant['status']) ? trim($merchant['status']) : 0,
      'created' => REQUEST_TIME,
      'updated' => REQUEST_TIME,
    );
    try {
      $result = $mysqlModel->insert('merchants', $set);
      return $result->insertId();
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
   * 更新商家对象
   * @param int $mId 商家对象
   * @param arrary $merchant 商家对象
   * @return int/bool 新商家ID, 失败返回 false
   */
  public function updateMerchant($mId, $merchant = null)
  {
    if (!isset($mId)) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $set = array(
      'updated' => REQUEST_TIME,
    );
    if (isset($merchant['seller'])) {
      $set['seller'] = trim($merchant['seller']);
    }
    if (isset($merchant['status'])) {
      $set['status'] = $merchant['status'];
    }
    if (isset($merchant['shop_url'])) {
      $set['shop_url'] = $merchant['shop_url'];
    }
    if (isset($merchant['margin'])) {
      $set['margin'] = $merchant['margin'];
    }
    if (isset($merchant['deposit'])) {
      $set['deposit'] = $merchant['deposit'];
    }
    if (isset($merchant['service_fee'])) {
      $set['service_fee'] = $merchant['service_fee'];
    }
    if (isset($merchant['send_score'])) {
      $set['send_score'] = $merchant['send_score'];
    }
    if (isset($merchant['server_score'])) {
      $set['server_score'] = $merchant['server_score'];
    }
    if (isset($merchant['match_score'])) {
      $set['match_score'] = $merchant['match_score'];
    }
    if (isset($merchant['level'])) {
      $set['level'] = $merchant['level'];
    }
    if (isset($merchant['shop_name'])) {
      $set['shop_name'] = $merchant['shop_name'];
    }
    if (isset($merchant['shop_sid'])) {
      $set['shop_sid'] = $merchant['shop_sid'];
    }
    if (isset($merchant['telphone'])) {
      $set['telphone'] = $merchant['telphone'];
    }
    if (isset($merchant['contact_wangwang'])) {
      $set['contact_wangwang'] = $merchant['contact_wangwang'];
    }
    if (isset($merchant['email'])) {
      $set['email'] = $merchant['email'];
    }
    if (isset($merchant['qq'])) {
      $set['qq'] = $merchant['qq'];
    }
    if (isset($merchant['merchant_type'])) {
      $set['merchant_type'] = $merchant['merchant_type'];
    }
    if (isset($merchant['category'])) {
      $set['category'] = $merchant['category'];
    }
    if (isset($merchant['pay_password'])) {
      $set['pay_password'] = md5($merchant['pay_password']);
    }
    if (isset($merchant['contacts'])) {
      $set['contacts'] = $merchant['contacts'];
    }
    try {
      $result = $mysqlModel->update('merchants', $set, array(
        'mid' => $mId,
      ));
      return $result->affected();
    } catch (BpfException $e) {
      print_r($e);
      return false;
    }
  }

  /**
  * 获取商家
  * @param arrary $merchant 商家条件
  * @return object/boot
  */
  public function getMerchant($merchant = null)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder()
        ->from('merchants');
    if (isset($merchant['mid'])) {
      $query->where('mid', $merchant['mid']);
    }
    if (isset($merchant['uid'])) {
      $query->where('uid', $merchant['uid']);
    }
    if (isset($merchant['shop_url'])) {
      $query->where('shop_url', $merchant['shop_url']);
    }
    if (isset($merchant['telphone'])) {
      $query->where('telphone', $merchant['telphone']);
    }
    return $query->query()->row();
  }

  /**
  * 获取商家列表按创建时间列出所有，可按条件过滤，提供分页；
  * @param array $conditions 用户查询条件数组
  * @param int $limit 分页显示数 默认15
  * @param int $page 页码 默认1
  * @return array 商家数组
  */
  public function getMerchants($conditions = null, $page = 1, $limit = 15)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder()
        ->from('merchants');
    if (isset($conditions['search'])) {
      $query->where('merchants.seller LIKE', '%' . $mysqlModel->escape($conditions['search']) . '%');
    }
    if (isset($conditions['where'])) {
      foreach ($conditions['where'] as $k => $value) {
        $query->where('merchants.' . trim($k), $value);
      }
    }
    $result = $query->orderby('created DESC')
        ->limitPage($limit, $page)
        ->query()
        ->all();
    return $result;
  }

  /**
   * 获取商家列表总数
   * @param array $conditions 商家查询条件数组
   * @return int 商家总数
   */
  public function getMerchantsCount($conditions = null)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder()
        ->select('COUNT(0)')
        ->from('merchants');
    if (isset($conditions['search'])) {
      $query->where('merchants.seller LIKE', '%' . $mysqlModel->escape($conditions['search']) . '%');
    }
    if (isset($conditions['where'])) {
      foreach ($conditions['where'] as $k => $value) {
        $query->where('merchants.' . trim($k), $value);
      }
    }
    return $query->query()->field();
  }

  /**
   * 插入活动对象
   * @param arrary $merchant 商家对象
   * @return int/bool 新商家ID, 失败返回 false
   */
  public function insertActivitie($activitie)
  {
    if (empty($activitie['cid']) || empty($activitie['deadline'])) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $set = array(
      'cid' => trim($activitie['cid']),
      'deadline' => trim($activitie['deadline']),
      'content' => isset($activitie['content']) ? trim($activitie['content']) : '',
      'title' => isset($activitie['title']) ? trim($activitie['title']) : '',
      'status' => isset($activitie['status']) ? trim($activitie['status']) : 0,
      'created' => REQUEST_TIME,
      'updated' => REQUEST_TIME,
    );
    try {
      $result = $mysqlModel->insert('activities', $set);
      return $result->insertId();
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
  * 更新活动对象
  * @param int $aid 活动id
  * @param arrary $activitie 要变更的活动内容
   * @return int/bool
  */
  public function updateActivitie($aid, $activitie)
  {
    if (!isset($aid)) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $set = array(
      'updated' => REQUEST_TIME,
    );
    if (isset($activitie['content'])) {
      $set['content'] = $activitie['content'];
    }
    if (isset($activitie['title'])) {
      $set['title'] = $activitie['title'];
    }
    if (isset($activitie['deadline'])) {
      $set['deadline'] = $activitie['deadline'];
    }
    if (isset($activitie['cid'])) {
      $set['cid'] = $activitie['cid'];
    }
    if (isset($activitie['status'])) {
      $set['status'] = $activitie['status'];
    }
    try {
      $result = $mysqlModel->update('activities', $set, array(
        'aid' => $aid,
      ));
      return $result->affected();
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
  * 根据活动id获取活动
  * @param int $aid 活动id
  * @return object/boot
  */
  public function getActivitie($aid)
  {
    if (empty($aid)) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    return $mysqlModel->getSqlBuilder()
        ->from('activities')
        ->where('aid', $aid)
        ->query()->row();
  }

  /**
  * 获取活动列表按创建时间列出所有，可按条件过滤，提供分页；
  * @param array $conditions 用户查询条件数组
  * @param int $limit 分页显示数 默认15
  * @param int $page 页码 默认1
  * @return array 活动数组
  */
  public function getActivities($conditions = null, $page = 1, $limit = 15)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder()
        ->from('activities');
    if (isset($conditions['search'])) {
      $query->where('activities.content LIKE', '%' . $mysqlModel->escape($conditions['search']) . '%');
    }
    if (isset($conditions['where'])) {
      foreach ($conditions['where'] as $k => $value) {
        $query->where('activities.' . trim($k), $value);
      }
    }
    $result = $query->orderby('created DESC')
        ->limitPage($limit, $page)
        ->query()
        ->all();
    return $result;
  }

  /**
   * 获取活动列表总数
   * @param array $conditions 商家查询条件数组
   * @return int 活动总数
   */
  public function getActivitiesCount($conditions = null)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder()
        ->select('COUNT(0)')
        ->from('activities');
    if (isset($conditions['search'])) {
      $query->where('activities.content LIKE', '%' . $mysqlModel->escape($conditions['search']) . '%');
    }
    if (isset($conditions['where'])) {
      foreach ($conditions['where'] as $k => $value) {
        $query->where('activities.' . trim($k), $value);
      }
    }
    return $query->query()->field();
  }

  /**
   * 添加活动商品
   * @param int $aid 活动Id
   * @param arrry $info 产品审核的信息集合
   * @return bool 失败返回 false,成功返回true
   */
  public function insertActivitieProduct($aid, $info)
  {
    if (empty($aid) || empty($info['pid']) || empty($info['mid'])){
      return false;
    }

    $mysqlModel = $this->getModel('mysql');
    $set = array(
      'aid' => trim($aid),
      'pid' => trim($info['pid']),
      'mid' => trim($info['mid']),
      'reply' => isset($info['reply']) ? trim($info['reply']) : null,
      'a_status' => isset($info['a_status']) ? trim($info['a_status']) : 0,
      'adress' => isset($info['adress']) ? trim($info['adress']) : null,
      'courier_num' => isset($info['courier_num']) ? trim($info['courier_num']) : null,
      'count' => isset($info['count']) ? trim($info['count']) : '',
      'sentdate' => isset($info['sentdate']) ? trim($info['count']) : null,
      'created' => REQUEST_TIME,
    );
    try {
      $insertId = $mysqlModel->insert('activities_products', $set)->insertId();
      return $insertId;
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
  * 根据活动产品id获取商品信息
  * @param int $apid 活动产品id
  * @return object/boot
  */
  public function getActivitieProduct($apid)
  {
    if (empty($apid)) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder()
        ->from('activities_products ap')
        ->join('products p', 'ap.pid = p.pid')
        ->where('ap.apid', $apid);
    return  $query->query()->row();
  }

  /**
   * 获取活动商品 (暂时只支持aid的查询)
   * @param int $aid 活动Id
   * @return arrary
   */
  public function getActivitieProducts($conditions = null, $page = 1, $limit = 15)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder()
    ->from('activities_products ap')
    ->join('products p', 'ap.pid = p.pid');
    if (isset($conditions['search'])) {
       $query->where('p.title LIKE', '%' . $mysqlModel->escape($conditions['search']) . '%');
    }
    if (isset($conditions['where'])) {
      foreach ($conditions['where'] as $k => $value) {
        $query->where('ap.' . trim($k), $value);
      }
    }
    if (isset($conditions['orderby'])) {
       $query ->orderby($conditions['orderby']);
    } else {
        $query ->orderby('ap.created DESC');
    }
    return  $query ->limitPage($limit, $page)
      ->query()
      ->all();
  }

  /**
   * 获取活动商品的数量
   * @param int $aid 活动Id
   * @return arrary
   */
  public function getActivitieProductsCount($conditions = null)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder()
      ->select('COUNT(0)')
      ->from('activities_products ap')
      ->join('products p', 'ap.pid = p.pid');
    if (isset($conditions['search'])) {
       $query->where('p.title LIKE', '%' . $mysqlModel->escape($conditions['search']) . '%');
    }
    if (isset($conditions['where'])) {
      foreach ($conditions['where'] as $k => $value) {
        $query->where('ap.' . trim($k), $value);
      }
    }
    return  $query->query()->field();
  }

  /**
  * 批量更新活动产品状态
  * @param int $aid 活动Id
  * @param int $pids 产品的Id
  * @param array $info 需要变更的信息
  * @return int
  */
  public function updateActivitieProducts($apids, $info)
  {
    if (empty($apids) || !is_array($apids)|| empty($info)) {
      return false;
    }

    $mysqlModel = $this->getModel('mysql');
    $set = array(
      'updated' => REQUEST_TIME,
    );
    if (isset($info['a_status'])) {
        $set['a_status'] = $info['a_status'];
    }
    if (isset($info['a_editor_uid'])) {
        $set['a_editor_uid'] = $info['a_editor_uid'];
    }
    try {
      $result = $mysqlModel->update('activities_products', $set, array(
        'apid IN' => $apids,
      ));
      return $result->affected();
    } catch (BpfException $e) {
    exit();
      return false;
    }
  }


  /**
  * 获取消费记录按创建时间列出所有，可按条件过滤，提供分页；
  * @param array $conditions 消费记录查询条件数组
  * @param int $limit 分页显示数 默认15
  * @param int $page 页码 默认1
  * @return array 消费记录数组
  */
  public function getCosts($conditions = null, $page = 1, $limit = 15)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder()
        ->from('costs');
    if (isset($conditions['where'])) {
      foreach ($conditions['where'] as $k => $value) {
        $query->where('costs.' . trim($k), $value);
      }
    }
    $result = $query->orderby('created DESC')
        ->limitPage($limit, $page)
        ->query()
        ->all();
    return $result;
  }

  /**
   * 获取消费记录总数
   * @param array $conditions 消费记录查询条件数组
   * @return int 消费记录总数
   */
  public function getCostsCount($conditions = null)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder()
        ->select('COUNT(0)')
        ->from('costs');
    if (isset($conditions['where'])) {
      foreach ($conditions['where'] as $k => $value) {
        $query->where('costs.' . trim($k), $value);
      }
    }
    return $query->query()->field();
  }

  /**
  *  新增一条消费记录
  * @param array $cost 消费类
  * @return int/bool 消费ID, 失败返回 false
  */
  public function insertCost($cost)
  {
    if (!isset($cost['mid'])) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $set = array(
      'pid' => isset($cost['pid']) ? trim($cost['pid']) : 0,
      'mid' => trim($cost['mid']),
      'content' => isset($cost['content']) ? trim($cost['content']) : '',
      'cost' => isset($cost['cost']) ? trim($cost['cost']) : 0,
      'type' => isset($cost['type']) ? trim($cost['type']) : '',
      'op' => isset($cost['op']) ? trim($cost['op']) : '',
      'order_id' => isset($cost['order_id']) ? trim($cost['order_id']) : date('Ymdhis', REQUEST_TIME),
      'created' => REQUEST_TIME,
    );
    try {
      $cid = $mysqlModel->insert('costs', $set)->insertId();
      return $cid;
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
  *  新增一个订单(暂时不做订单的删改)
  * @param array $order 订单类
  * @return int/bool 订单ID, 失败返回 false
  */
  public function insertOrder($order)
  {
    if (!isset($order['pid']) || !isset($order['mid'])) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $set = array(
      'pid' => trim($order['pid']),
      'mid' => trim($order['mid']),
      'buyer' => $order['buyer'],
      'created' => trim($order['created']),
      'data' => trim($order['data']),
    );
    $set['count'] = isset($order['count']) ? trim($order['count']) : null;
    $set['price'] = isset($order['price']) ? $order['price'] : null;
    try {
      $oid = $mysqlModel->insert('orders', $set)->insertId();
      return $oid;
    } catch (BpfException $e) {
      print_r($e);
      return false;
    }
  }

  /**
  * 获取订单 （目前只提供按照order的属性来查找）
  * @param arrary $conditions conditions
  * @return object/boot
  */
  public function getOrders($conditions = null, $page = 1, $limit = 15)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder()
        ->select('o.*, p.title, p.url, p.sell_price, p.image_path, m.seller, m.shop_name')
        ->from('orders o')
        ->join('products p', 'o.pid = p.pid')
        ->join('merchants m', 'm.mid = o.mid');
    if (isset($conditions['orderId'])) {
        $query->where('o.order_number' , $conditions['orderId']);
    }
    if (isset($conditions['orderStatus'])) {
        $query->where('o.status' , $conditions['orderStatus']);
    }
    if (isset($conditions['merchantName'])) {
        $query->where('m.seller LIKE', '%' . $mysqlModel->escape($conditions['merchantName']) . '%');
    }
    if (isset($conditions['merchantMid'])) {
        $query->where('m.mid' , $conditions['merchantMid']);
    }
    $result = $query->orderby('created DESC')
        ->limitPage($limit, $page)
        ->query()
        ->all();
    return $result;
  }
  /**
  * 获取单个订单 （目前只提供按照order的属性来查找）
  * @param arrary $conditions conditions
  * @return object/boot
  */
  public function getOrder($conditions = null)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder()
        ->select('o.*')
        ->from('orders o');

    if (isset($conditions['pid'])) {
        $query->where('o.pid' , $conditions['pid']);
    }
    if (isset($conditions['mid'])) {
        $query->where('o.mid', $conditions['mid']);
    }
    $result = $query->orderby('created DESC')
        ->query()
        ->row();
    return $result;
  }

  /**
  * 获取订单总数
  * @param arrary $conditions 查询条件
  * @return object/boot
  */
  public function getOrdersCount($conditions = null) {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder()
      ->select('COUNT(0)')
      ->from('orders o')
      ->join('products p', 'o.pid = p.pid')
      ->join('merchants m', 'm.mid = o.mid');
    if (isset($conditions['orderId'])) {
        $query->where('o.order_number' , $conditions['orderId']);
    }
    if (isset($conditions['orderStatus'])) {
        $query->where('o.status' , $conditions['orderStatus']);
    }
    if (isset($conditions['merchantName'])) {
        $query->where('m.seller LIKE', '%' . $mysqlModel->escape($conditions['merchantName']) . '%');
    }
    if (isset($conditions['merchantMid'])) {
        $query->where('m.mid' , $conditions['merchantMid']);
    }
    return $query->query()->field();
  }

  /**
  * 获取商家某个商品订单总数
  * @param arrary $conditions 查询条件
  * @return object/boot
  */
  public function getMerchantsProductsOrdersCount($conditions = null) 
  {
    if (!isset($conditions['mid']) || !isset($conditions['pid'])) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder()
      ->select('COUNT(0)')
      ->from('orders')
      ->where('mid' , $conditions['mid'])
      ->where('pid' , $conditions['pid']);
    return $query->query()->field();
  }

  /**
  *  新增一条发票记录
  * @param array $cost 发票类
  * @return int/bool 发票ID, 失败返回 false
  */
  public function insertInvoice($invoice)
  {
    if (!isset($invoice['mid'])) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $set = array(
      'content' => isset($invoice['content']) ? trim($invoice['content']) : '',
      'mid' => trim($invoice['mid']),
      'money' => isset($invoice['money']) ? trim($invoice['money']) : 0,
      'status' => isset($invoice['status']) ? trim($invoice['status']) : 0,
      'created' => REQUEST_TIME,
    );
    try {
      $iid = $mysqlModel->insert('invoices', $set)->insertId();
      return $iid;
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
  * 获取发票
  * @param arrary $conditions 查询条件
  * @return object/boot
  */
  public function getInvoices($conditions = null, $page = 1, $limit = 15)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder()
        ->select('i.*, m.seller')
        ->from('invoices i')
        ->join('merchants m', 'm.mid = i.mid');
    if (isset($conditions['merchantName'])) {
        $query->where('m.seller LIKE', '%' . $mysqlModel->escape($conditions['merchantName']) . '%');
    }
    if (isset($conditions['status'])) {
        $query->where('i.status' , $conditions['status']);
    }
    $result = $query->orderby('i.created DESC')
        ->limitPage($limit, $page)
        ->query()
        ->all();
    return $result;
  }

  /**
  * 获取发票
  * @param arrary $conditions 查询条件
  * @return object/boot
  */
  public function getInvoicesCount($conditions = null)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder()
          ->select('COUNT(0)')
        ->from('invoices i')
        ->join('merchants m', 'm.mid = i.mid');
    if (isset($conditions['merchantName'])) {
        $query->where('m.seller LIKE', '%' . $mysqlModel->escape($conditions['merchantName']) . '%');
    }
    if (isset($conditions['status'])) {
        $query->where('i.status' , $conditions['status']);
    }
    return $query->query()->field();
  }

  /**
  * 获取发票总数
  * @param arrary $conditions 查询条件
  * @return object/boot
  */
  public function getInvoicesSum($conditions = null) 
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder()
      ->select('SUM(`money`)')
      ->from('invoices');
    if (isset($conditions['status'])) {
        $query->where('status', $conditions['status']);
    }
    if (isset($conditions['mid'])) {
        $query->where('mid', $conditions['mid']);
    }
    return $query->query()->field();
  }

  /**
  * 给指定手机发送验证码
  * @param string $telphone 手机号码
  * @return string 2分钟有效验证码
  */
  public function sendVerificationCode($telphone = null)
  {
    if (!isset($telphone)) {
      return false;
    }
    //获取token
    $appid = "263477500000038555";
    $appsecret = "334eb256f86bd1954ee54975ed43b269";
    $tokenAPI = "https://oauth.api.189.cn/emp/oauth2/v3/access_token";
    $data = array (
     'app_id' => $appid,
     'app_secret' => $appsecret,
     'grant_type' => 'client_credentials',
    );
    $send = 'app_id=' . $appid . '&app_secret=' . $appsecret . '&grant_type=client_credentials';
    $access_token = $this->_curlPost($tokenAPI, $send);
    $access_token = json_decode($access_token, true)['access_token'];
    //获取token结束
    $timestamp = date('Y-m-d H:i:s', time());
    $url = 'http://api.189.cn/v2/dm/randcode/token?';
    $param2['app_id']= 'app_id=' . $appid;
    $param2['access_token'] = 'access_token=' . $access_token;
    $param2['timestamp'] = 'timestamp=' . $timestamp;
    ksort($param2);
    $plaintext = implode('&', $param2);
    $param2['sign'] = 'sign=' . rawurlencode(base64_encode(hash_hmac('sha1', $plaintext, $appsecret, $raw_output = True)));
    ksort($param2);
    $url .= implode('&', $param2);
    $result = $this->_curlGet($url);
    $resultArray = json_decode($result,true);
    $token = $resultArray['token'];
    //发送验证码
    $url = "http://api.189.cn/v2/dm/randcode/sendSms";
    $randcode = randomString(6, 10); //随机6位数字验证码
    $param['app_id'] = 'app_id=' . $appid;
    $param['access_token'] = 'access_token=' . $access_token;
    $param['timestamp'] = 'timestamp=' . $timestamp;
    $param['token'] = 'token=' . $token;
    $param['phone'] = 'phone=' . $telphone;
    $param['randcode'] = 'randcode=' . $randcode;
    ksort($param);
    $plaintext = implode('&', $param);
    $param['sign'] = 'sign=' . rawurlencode(base64_encode(hash_hmac('sha1', $plaintext, $appsecret, $raw_output = True)));
    ksort($param);
    $str = implode('&', $param);
    $this->_curlPost($url, $str);
    return $randcode;
  }

  /**
  * POST发送请求
  * @return array
  */
  private function _curlPost($url = '', $postdata = '')
  {
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch,CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
    curl_setopt($ch,CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch,CURLOPT_AUTOREFERER, 1);
    curl_setopt($ch,CURLOPT_POSTFIELDS, $postdata);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
    $data = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $data;
  }

  /**
  * GET发送请求
  * @return array
  */
  private function _curlGet($url = '', $options = array())
  {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    if (!empty($options)) {
      curl_setopt_array($ch, $options);
    }
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
  }

  /**
  *获取需要抓取订单数据的活动商品
  *
  */
  public function getActivitiesProducts($conditions = null) 
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder()
        ->select('ap.mid,ap.pid,p.url')
        ->from('activities_products ap')
        ->join('products p', 'p.pid = ap.pid');
    if (isset($conditions['status'])) {
        $query->where('ap.a_status', $conditions['status']);
    }

    $result = $query->orderby('ap.updated ASC')
        ->query()
        ->all();
    return $result;
  }
}
