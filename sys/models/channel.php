<?php
/**
 * 频道类
 * @author Hao <sixihaoyue@gmail.com>
 */
class ChannelModel extends BpfModel
{

  /**
   * 检查频道seo路径是否存在
   * @param string $path 频道seo路径
   * @return int/bool 存在返回频道id, 不存在 false
   */
  public function checkChannelPathIsExists($path)
  {
    $mysqlModel = $this->getModel('mysql');
    $result = $mysqlModel->query('SELECT `cid` FROM `channels` WHERE `seo_path` = "' . $mysqlModel->escape($path) . '"')->field();
    return $result;
  }

  /**
   * 插入频道对象
   * @param arrary $channel 频道对象
   * @return int/bool 新频道ID, 失败返回 false
   */
  public function insertChannel($channel)
  {
    if (!isset($channel['title'])) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $set = array(
      'title' => trim($channel['title']),
      'status' => isset($channel['status']) ? $channel['status'] : null,
      'show_on_home' => isset($channel['show_on_home']) ? $channel['show_on_home'] : null,
      'seo_keyword' => isset($channel['seo_keyword']) ? trim($channel['seo_keyword']) : null,
      'seo_description' => isset($channel['seo_description']) ? trim($channel['seo_description']) : null,
      'rules' => isset($channel['rules']) ? trim($channel['rules']) : '',
      'template' => isset($channel['template']) ? trim($channel['template']) : null,
      'weight' => isset($channel['weight']) ? trim($channel['weight']) : null,
      'created' => REQUEST_TIME,
      'updated' => REQUEST_TIME,
    );
    if (isset($channel['seo_path']) && trim($channel['seo_path']) !== '') {
      $set['seo_path'] = trim($channel['seo_path']);
    }
    try {
      $result = $mysqlModel->insert('channels', $set);
      return $result->insertId();
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
   * 更新频道对象
   * @param int $channelId 频道对象
   * @param arrary $channel 频道对象
   * @return int/bool 新频道ID, 失败返回 false
   */
  public function updateChannel($channelId, $channel = null)
  {
    if (!isset($channelId)) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $set = array(
      'updated' => REQUEST_TIME,
    );
    if (isset($channel['title'])) {
      $set['title'] = trim($channel['title']);
    }
    if (isset($channel['status'])) {
      $set['status'] = $channel['status'];
    }
    if (isset($channel['show_on_home'])) {
      $set['show_on_home'] = $channel['show_on_home'];
    }
    if (isset($channel['seo_path']) && !empty($channel['seo_path'])) {
      $set['seo_path'] = trim($channel['seo_path']);
    }
    if (isset($channel['seo_keyword'])) {
      $set['seo_keyword'] = trim($channel['seo_keyword']);
    }
    if (isset($channel['seo_description'])) {
      $set['seo_description'] = trim($channel['seo_description']);
    }
    if (isset($channel['template'])) {
      $set['template'] = trim($channel['template']);
    }
    if (isset($channel['weight'])) {
      $set['weight'] = trim($channel['weight']);
    }
    if (isset($channel['rules'])) {
      $set['rules'] = trim($channel['rules']);
    }
    if (isset($channel['expired'])) {
      $set['expired'] = trim($channel['expired']);
    }
    if (isset($channel['scheduling'])) {
      $set['scheduling'] = trim($channel['scheduling']);
    }
    try {
      $result = $mysqlModel->update('channels', $set, array(
        'cid' => $channelId,
      ));
      return $result->affected();
    } catch (BpfException $e) {
      var_dump($e);exit();
      return false;
    }
  }

  /**
   * 批量修改频道状态
   * @param array $channelIds 频道对象
   * @param string $op 操作类型
   * @return bool
   */
  public function updateChannels($channelIds, $op)
  {
    $status ;
    if( $op === 'delect' ){
      $status = '0';
    } else if ( $op === 'pass' ) {
      $status = '1';
    }
    if (!isset($status)) {
      return false;
    }
     $mysqlModel = $this->getModel('mysql');
    try{
      $mysqlModel->update('channels',
        array(
          'status' => $status,
          'updated' => REQUEST_TIME,
        ),
        array(
          'cid IN' => $channelIds,
        ));
      return  true;
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
   * 删除频道对象
   * @param int $channelId 频道对象
   * @return int/bool 影响行数, 失败返回 false
   */
  public function deleteChannel($channelId)
  {
    if (!isset($channelId)) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    //删除product
    $result = $mysqlModel->delete('channels',array(
      'cid' => $channelId,
    ));
    $affected = $result->affected();
    if ($affected) {
      //删除商品与频道的关系
      $mysqlModel->delete('products_channels',array(
        'cid' => $channelId,
      ));
    }
    return $affected;
  }

  /**
   * 获取频道对象
   * @param int $channelId 频道ID
   * @return object 频道对象
   */
  public function getChannel($channelId)
  {
    if (!isset($channelId)) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $channel = $mysqlModel->query('SELECT * FROM `channels` WHERE `cid` = "' . $mysqlModel->escape($channelId) . '"')->row();
    if ($channel) {
      $channel->link = urlChannel($channel);
      if ($channel->rules != '') {
        $channel->rules = json_decode($channel->rules, true);
      }
    }
    return $channel;
  }


  /**
   * 获取所有频道并进行分页
   * @param array $conditions 频道查询条件数组
   * @param int $limit 分页显示数 默认20
   * @param int $page 页码 默认1
   * @return array 频道数组
   */
  public function getChannels($conditions = null, $page = 1, $limit = 15)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder()
        ->from('channels');
    if (isset($conditions['search'])) {
      $query->where('channels.title LIKE', '%' . $mysqlModel->escape($conditions['search']) . '%');
    }
    if (isset($conditions['where'])) {
      foreach ($conditions['where'] as $k => $value ) {
        $query->where('channels.' . trim($k), $value);
      }
    }
    if (isset($conditions['orderby'])) {
      $query->orderby(trim($mysqlModel->escape($conditions['orderby'])));
    } else {
      $query->orderby('created DESC');
    }
    $result = $query->limitPage($limit, $page)->query()->all();
    foreach ($result as $channel) {
      $channel->link = urlChannel($channel);
    }
    return $result;
  }


  /**
   * 获取所有频道总数
   * @param array $conditions 频道查询条件数组
   * @return int 频道总数
   */
  public function getChannelsCount($conditions = null)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder()
        ->select('COUNT(0)')
        ->from('channels');
    if (isset($conditions['search'])) {
      $query->where('channels.title LIKE', '%' . $mysqlModel->escape($conditions['search']) . '%');
    }
    return $query->query()->field();
  }

  /**
  * 批量设置频道商品
  * @param int $channelId 频道Id
  * @param arrry $productIds 商品id集合
  * @return bool 失败返回 false,成功返回true
  */
  public function insertChannelProducts($channelId, $productIds)
  {
    $mysqlModel = $this->getModel('mysql');
    $set = array();
    foreach ($productIds as $pid) {
      array_push($set, array(
        'pid' => $pid,
        'cid' => $channelId,
      ));
    }
    try {
      $mysqlModel->insert('products_channels', $set)->insertId();
      return true;
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
  * 检查商品是否在频道中
  * @param int $channelId 频道Id
  * @param arrry $productIds 商品id集合
  * @return
  */
  public function checkProducts($channelId, $productIds)
  {
    return $this->getModel('mysql')
        ->getSqlBuilder()
        ->from('products_channels')
        ->where('cid', $channelId)
        ->where('pid IN', $productIds)
        ->query()
        ->column('pid');
  }

  /**
  * 批量删除频道商品
  * @param int $channelId 频道Id
  * @param arrry $productIds 商品id集合
  * @return bool 失败返回 false,成功返回true
  */
  public function deleteChannelProducts($channelId, $productIds)
  {
    $mysqlModel = $this->getModel('mysql');
    try {
      $mysqlModel->delete('products_channels', array(
        'pid IN' => $productIds,
        'cid' => $channelId,
      ));
      return true;
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
  *获取所有有效的频道
  * @return array 返回标签值与标签名字的键值对
  */
  public function getChannelMap()
  {
    return $this->getModel('mysql')
        ->getSqlBuilder()
        ->select('cid, title')
        ->from('channels')
        ->where('status', '1')
        ->query()
        ->columnWithKey('cid', 'title');
  }

  /**
   * 获取匹配的频道
   * @param object $product 商品
   * @return array 频道ID列表
   */
  public function getMatchChannels($product)
  {
    static $channels = null;
    if (!isset($channels)) {
      $mysqlModel = $this->getModel('mysql');
      $channels = array();
      $list = $mysqlModel->query('SELECT `cid`, `rules` FROM `channels` WHERE `rules` <> ""')->all();
      foreach ($list as $channel) {
        if ($rules = json_decode($channel->rules, true)) {
          $channels[$channel->cid] = $rules;
        }
      }
    }
    if (!is_object($product) || !isset($product->title) || !isset($product->feature) || !isset($product->sell_price)) {
      return array();
    }
    $matches = array();
    foreach ($channels as $cid => $rules) {
      if (isset($rules['title']) && false !== (mb_strpos($product->title, $rules['title']))) {
        $matches[] = $cid;
        continue;
      } else if (isset($rules['feature']) && false !== (mb_strpos($product->feature, $rules['feature']))) {
        $matches[] = $cid;
        continue;
      } else if (isset($rules['price1']) && isset($rules['price1']['op']) && isset($rules['price1']['value']) &&
          isset($rules['price2']) && isset($rules['price2']['op']) && isset($rules['price2']['value'])) {
        if ($this->_checkMatchPrice($product->sell_price, $rules['price1']) &&
            $this->_checkMatchPrice($product->sell_price, $rules['price2'])) {
          $matches[] = $cid;
        }
      } else if (isset($rules['price1']) && isset($rules['price1']['op']) && isset($rules['price1']['value'])) {
        if ($this->_checkMatchPrice($product->sell_price, $rules['price1'])) {
          $matches[] = $cid;
        }
      } else if (isset($rules['price2']) && isset($rules['price2']['op']) && isset($rules['price2']['value'])) {
        if ($this->_checkMatchPrice($product->sell_price, $rules['price2'])) {
          $matches[] = $cid;
        }
      }
    }
    return $matches;
  }

  private function _checkMatchPrice($price, $priceRule)
  {
    switch ($priceRule['op']) {
      case '>':
        if ($price > $priceRule['value']) {
          return true;
        }
        break;
      case '<':
        if ($price < $priceRule['value']) {
          return true;
        }
        break;
      case '>=':
        if ($price >= $priceRule['value']) {
          return true;
        }
        break;
      case '<=':
        if ($price <= $priceRule['value']) {
          return true;
        }
        break;
      case '=':
        if ($price = $priceRule['value']) {
          return true;
        }
        break;
    }
    return false;
  }
}
