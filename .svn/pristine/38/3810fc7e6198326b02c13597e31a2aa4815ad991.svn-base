<?php
/**
 * 广告类
 * @author Hao <sixihaoyue@gmail.com>
 */
class AdModel extends BpfModel
{
  /**
  * 新增一个广告
  * @param array $ad 广告类
  * @return int/bool 新广告ID, 失败返回 false
  */
  public function insertAd($ad)
  {
    if (!isset($ad['name'])) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $set = array(
      'name' => trim($ad['name']),
      'created' => REQUEST_TIME,
      'updated' => REQUEST_TIME,
    );
    $set['code'] = isset($ad['code']) ? trim($ad['code']) : null;
    $set['type'] = isset($ad['type']) ? $ad['type'] : null;
    $set['url'] = isset($ad['url']) ? trim($ad['url']) : null;
    $set['status'] = isset($ad['status']) ? $ad['status'] : null;
    $set['oid'] = isset($ad['oid']) ? trim($ad['oid']) : null;
    try {
      $aid = $mysqlModel->insert('ads', $set)->insertId();
      if(isset($ad['file_id'])) {
        $this->setAdImage($aid, $ad['file_id']);
      }
      return $aid;
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
  * 设置广告图片
  * @param int $adId 广告id
  * @param int $fileId 文件id
  * @return bool 成功返回true, 失败返回 false
  */
  public function setAdImage($adId, $fileId)
  {
    $mysqlModel = $this->getModel('mysql');
    $fileModel = $this->getModel('file');
    $file = $fileModel->getFile('banner_img', $fileId);
    if ($file) {
      $oFileId = $mysqlModel->query('SELECT `file_id` FROM `ads` WHERE `aid` = ' . $mysqlModel->escape($adId))
          ->row()->file_id;
      //如果旧广告图存在并且与新广告图相同，则删除旧广告图
      if ($oFileId && $oFileId != $fileId) {
          $fileModel->delete('banner_img', $oFileId);
      }
      try {
        $set = array(
          'file_id' =>  $file->file_id,
          'image_path' => $file->file_path,
        );
        $mysqlModel->update('ads',$set, array('aid' => $adId));
        return true;
      } catch (BpfException $e) {
        return false;
      }
    }
    return false;
  }

  /**
  * 更新广告
  * @param int $aid 广告ID
  * @param array 广告更新内容
  * @return int/bool 影响行数, 失败返回 false
  */
  public function updataAd($adId, $ad = null)
  {
    if(!isset($adId)) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $set = array(
      'updated' => REQUEST_TIME,
    );
    if (isset($ad['name'])) {
      $set['name'] = trim($ad['name']);
    }
    if (isset($ad['type'])) {
      $set['type'] = $ad['type'];
    }
    if (isset($ad['url'])) {
      $set['url'] = trim($ad['url']);
    }
    if (isset($ad['image_path'])) {
      $set['image_path'] = trim($ad['image_path']);
    }
    if (isset($ad['code'])) {
      $set['code'] = trim($ad['code']);
    }
    if (isset($ad['status'])) {
      $set['status'] = $ad['status'];
    }
    if (isset($ad['oid'])) {
      $set['oid'] = $ad['oid'];
    }
    try {
      $result = $mysqlModel->update('ads', $set, array(
        'aid' => $adId,
      ));
      if (isset($ad['file_id'])){
        $this->setAdImage($adId, $ad['file_id']);
      }
      return $result->affected();
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
  * 批量禁用／启用广告
  * @param array $aids 广告ID数组
  * @param array 更新广告操作
  * @return int/bool 返回受影响行数, 失败返回 false
  */
  public function updateAds($adIds, $op)
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
    try {
      return $mysqlModel->update('ads',
        array(
          'status' => $status,
          'updated' => REQUEST_TIME,
        ),
        array(
          'aid IN' => $adIds,
        ))->affected();
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
  * 删除广告
  * @param int $adId 广告id
  * @return int/bool 影响行数, 失败返回 false
  */
  public function deleteAd($adId)
  {
    $mysqlModel = $this->getModel('mysql');
    $oFileId = $mysqlModel->query('SELECT `file_id` FROM `ads` WHERE `aid` = ' . $mysqlModel->escape($adId))
        ->row();
    //如果该广告包含图片，则删除图片
    if ($oFileId) {
      $this->getModel('file')
          ->delete('banner_img', $oFileId->file_id);
    }
    try {
      $affected = $mysqlModel->delete('ads',array('aid' => $adId))->affected();
      if ($affected) {
        //删除广告关联数据
        $mysqlModel->delete('ads_sockets', array(
          'aid' => $adId,
        ));
        $mysqlModel->delete('ads_clicks', array(
          'aid' => $adId,
        ));
        $mysqlModel->delete('ads_views', array(
          'aid' => $adId,
        ));
      }
      return $affected;
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
  * 批量删除广告
  * @param array $adIds 广告id集合
  * @return int/bool 影响行数, 失败返回 false
  */
  public function deleteAds($adIds)
  {
    $mysqlModel = $this->getModel('mysql');
    $fileModel = $this->getModel('file');
    //删除广告图片
    $flieIds = $mysqlModel->getSqlBuilder()
        ->select('file_id')
        ->from('ads')
        ->where('aid IN' , $adIds)
        ->query()
        ->column();
    $fileModel->delete('banner_img', $flieIds);
    //删除广告数据
    $result = $mysqlModel->delete('ads', array(
        'aid IN' => $adIds,
      ));
    $affected = $result->affected();
    if ($affected) {
      //删除广告关联数据
      $mysqlModel->delete('ads_sockets', array(
        'aid IN' => $adIds,
      ));
      $mysqlModel->delete('ads_clicks', array(
        'aid IN' => $adIds,
      ));
      $mysqlModel->delete('ads_views', array(
        'aid IN' => $adIds,
      ));
    }
    return $affected;
  }

  /**
  * 获取广告
  * @param int $adId 广告id
  * @return object/boot
  */
  public function getAd($adId)
  {
    $mysqlModel = $this->getModel('mysql');
    $aid = $mysqlModel->escape($adId);
    return $mysqlModel->query('SELECT * FROM `ads` WHERE `aid` = ' . $aid)->row();
  }

  /**
  * 获取广告按创建时间列出所有广告，可按广告位过滤，提供分页；
  * @param array $conditions 用户查询条件数组
  * @param int $limit 分页显示数 默认20
  * @param int $page 页码 默认1
  * @return array 广告数组
  */
  public function getAds($conditions = null, $page = 1, $limit = 15)
  {
    $mysqlModel = $this->getModel('mysql');
    $socketSql = ''; $oIds = '';
    if (isset($conditions['socket_id'])) {
      $socketSql = 'INNER JOIN ads_sockets ads ON ads.aid = ad.aid AND ads.socket_id = "' . $mysqlModel->escape($conditions['socket_id']) . '"';
    }
    if (!isset($conditions['date'])) {
      $date = date('Ymd', REQUEST_TIME);
    } else {
      $date = $mysqlModel->escape($conditions['date']);
    }
    if (isset($conditions['oid'])) {
      $oIds = 'WHERE ad.oid IN (' . implode(',', $conditions['oid']) . ')';
    }
    if (isset($conditions['orderby'])) {
      $orderby = ' ORDER BY ' . $mysqlModel->escape($conditions['orderby']);
    } else {
      $orderby = ' ORDER BY ad.`created` DESC ';
    }
    $result = $mysqlModel->query('SELECT ad.*, IFNULL(adc.`tcounter`, 0) tclicks, IFNULL(adv.`counter`, 0) views, IFNULL(adc.`counter`, 0) clicks, IF(IFNULL(adv.`counter`, 0) = 0, 0, IFNULL(adc.`counter`, 0) / adv.`counter`) cvp FROM `ads` ad 
        ' . $socketSql . ' LEFT JOIN (SELECT `aid`, `date`, SUM(`counter`) counter FROM `ads_views` GROUP BY aid, `date` HAVING `date` = "' . $date . '") adv ON ad.aid = adv.aid
        LEFT JOIN (SELECT `aid`, `date`, COUNT(0) counter,COUNT(DISTINCT tid) tcounter FROM `ads_clicks` GROUP BY aid, `date` HAVING `date` = "' . $date . '") adc ON ad.`aid` = adc.`aid` 
        ' . $oIds . $orderby .' LIMIT ' . $limit * ($page - 1) . ',' . $limit)->all();
    return $result;
  }

  /**
   * 获取广告总数
   * @param array $conditions 广告查询条件数组
   * @return int 广告总数
   */
  public function getAdsCount($conditions)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder();
    $query->select('COUNT(0)')->from('ads');
    if (isset($conditions['socket_id'])) {
      $query->join('ads_sockets', 'ads_sockets.aid = ads.aid')
          ->where('ads_sockets.socket_id', $conditions['socket_id']);
    }
    if (isset($conditions['oid'])) {
      $query->where('ads.oid IN', $conditions['oid']);
    }
    return $query->query()->field();
  }


   /**
  * 通过socketId获取广告
  * @param int $adId 广告位id
  * @return object／bool 成功返回true, 失败返回 false
  */
  public function getAdBySocketId($socketId)
  {
    if (!isset($socketId)) {
      return false;
    }
    $ad = $this->getModel('mysql')
        ->getSqlBuilder()
        ->from('ads')
        ->join('ads_sockets', 'ads.aid = ads_sockets.aid')
        ->where('ads_sockets.socket_id', $socketId)
        ->where('status', 1)
        ->orderby('RAND()')
        ->limit(1)
        ->query()
        ->row();
    if ($ad) {
      $ad->link = urlAd($ad, $socketId);
    }
    return $ad;
  }

  /**
  * 设置广告的广告位
  * @param int $adId 广告id
  * @param array $socketIds 广告位id集合
  * @return bool 成功返回true, 失败返回 false
  */
  public function setAdSockets($adId ,$socketIds)
  {
    if (!isset($adId)) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $set = array();
    foreach ($socketIds as $socketId) {
      $set[] = array(
        'aid' => $adId,
        'socket_id' => $socketId,
      );
    }
    try {
      $mysqlModel->delete('ads_sockets', array('aid' => $adId));
      $mysqlModel->insert('ads_sockets', $set);
      return true;
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
  * 获取广告的广告位
  * @param int $adId 广告id
  * @param array $socketIds 广告位id集合
  * @return bool 成功返回true, 失败返回 false
  */
  public function getAdSockets($adId)
  {
    if (!isset($adId)) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder();
    return $query->select('socket_id')
        ->from('ads_sockets')
        ->where('aid', $adId)
        ->query()
        ->column();
  }

  /**
  * 获取广告的广告位
  * @param int $adId 广告id
  * @param int $socketId 广告位id
  * @return bool 成功返回true, 失败返回 false
  */
  public function clickAd($adId, $socketId, $tId)
  {
    if (!isset($adId) || !isset($socketId)) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    try {
      $mysqlModel->insert('ads_clicks', array(
        'aid' => $adId,
        'socket_id' => $socketId,
        'created' => REQUEST_TIME,
        'date' => date('Ymd', REQUEST_TIME),
        'minute' => floor(REQUEST_TIME / 60),
        'tid' => isset($tId) ? $tId : '',
      ));
      return true;
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
   * 获取广告点击总数 --- 临时使用///
   * @param int $adId 广告id
   * @param int $socketId 广告位id
   * @param array $conditions 广告时间条件
   * @return int 广告点击总数, 失败返回 false
   */
  public function getClickAdCount($adId, $socketId, $conditions = null)
  {
    if (!isset($adId) || !isset($socketId)) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder();
    if (!isset($conditions)) {
      $query->select('COUNT(0)')->from('ads_clicks')
          ->where('aid', $adId)
          ->where('socket_id', $socketId);
      try {
        return $query->query()->field();
      } catch (BpfException $e) {
        return false;
      }
    } else {
      $start = strtotime($conditions);
      $end = $start + 86399;
      try {
        return $mysqlModel->query('SELECT COUNT(0) FROM `ads_clicks` WHERE `aid` = "' . $adId . '" AND `socket_id` = "' 
          . $socketId . '" AND `created` BETWEEN ' . $start . ' AND ' . $end)->field();
      } catch (BpfException $e) {
        return false;
      }
    }
  }

  /**
  * 获取所有广告位
  * @return array Socket集合
  */
  public function getSockets()
  {
    return $mysqlModel = $this->getModel('mysql')
        ->query('SELECT DISTINCT `socket_id` FROM `ads_sockets`')
        ->column();
  }

  /**
  * 更新广告主
  * @param int $oId 广告主ID
  * @param array 广告更新内容
  * @return int/bool 影响行数, 失败返回 false
  */
  public function updataAdsOwner($oId, $ad = null)
  {
    if(!isset($oId)) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $set = array(
      'updated' => REQUEST_TIME,
    );
    if (isset($ad['name'])) {
      $set['name'] = trim($ad['name']);
    }
    try {
      $result = $mysqlModel->update('ads_owners', $set, array(
        'oid' => $oId,
      ));
      return $result->affected();
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
  * 获取所有广告主
  * @param int $oId 广告主id
  * @return object/boot
  */
  public function getAdsOwner($conditions)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder();
    $query->from('ads_owners')
          ->join('users_owners', 'ads_owners.oid = users_owners.oid');
    if (isset($conditions['oid'])) {
      $query->where('ads_owners.oid', $conditions['oid']);
    }
    if (isset($conditions['uid'])) {
      $query->where('users_owners.uid', $conditions['uid']);
    }
    return $query->query()->all();
  }

  /**
   * 获取广告主总数
   * @param array $conditions 广告查询条件数组
   * @return int 广告主总数
   */
  public function getAdsOwnersCount($conditions = null)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder();
    $query->select('COUNT(0)')->from('ads_owners');
    return $query->query()->field();
  }
  
  /**
  * 按更新时间列出所有广告主
  * @param array $conditions 用户查询条件数组
  * @param int $limit 分页显示数 默认20
  * @param int $page 页码 默认1
  * @return array 广告数组
  */
  public function getAdsOwners($conditions = null, $page = 1, $limit = 15)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder()
        ->from('ads_owners');
    if (isset($conditions['orderby'])) {
      $query->orderby(trim($conditions['orderby']));
    } else {
      $query->orderby('updated DESC');
    }
    return $query->limitPage($limit, $page)->query()->all();
  }

  /**
  * 新增一个广告主
  * @param array $ad 广告主类
  * @return int/bool 新广告ID, 失败返回 false
  */
  public function insertAdsOwners($ad)
  {
    if (!isset($ad['name'])) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $set = array(
      'name' => trim($ad['name']),
      'updated' => REQUEST_TIME,
    );
    try {
      $insertId = $mysqlModel->insert('ads_owners', $set)->insertId();
      if (is_array($ad['uids'])) {
        $this->setAdsOwners($insertId, $ad['uids']);
      }
      return true;
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
  * 设置广告主的用户
  * @param int $oId 广告主id
  * @param array $uIds 用户id集合
  * @return bool 成功返回true, 失败返回 false
  */
  public function setAdsOwners($oId ,$uIds)
  {
    if (!isset($oId)) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $set = array();
    foreach ($uIds as $uId) {
      $set[] = array(
        'oid' => $oId,
        'uid' => $uId,
      );
    }
    try {
      $mysqlModel->delete('users_owners', array('oid' => $oId));
      $mysqlModel->insert('users_owners', $set);
      return true;
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
  * 删除广告主
  * @param int $oId 广告主id
  * @return int/bool 影响行数, 失败返回 false
  */
  public function deleteAdsOwners($oId)
  {
    $mysqlModel = $this->getModel('mysql');
    try {
      $affected = $mysqlModel->delete('ads_owners', array('oid' => $oId))->affected();
      if ($affected) {
        //删除广告主关联数据
        $mysqlModel->delete('users_owners', array(
          'oid' => $oId,
        ));
      }
      return $affected;
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
  * 获取广告统计报表
  * @param array $aid 广告ID
  * @return array 广告数组
  */
  public function getAdsReport($aId = null, $date = null)
  {
    $mysqlModel = $this->getModel('mysql');
    if (!isset($aId)) {
      return false;
    }
    if (empty($date)) {
      $date = date('Ymd', REQUEST_TIME);
    }
    $result = $mysqlModel->query('SELECT views.*, IFNULL(clicks.`counter`, 0) clicks_counter, IFNULL(clicks.`tcounter`, 0) clicks_tcounter FROM 
        (SELECT aid, `minute`, SUM(`counter`) counter FROM `ads_views` WHERE `aid` = ' . $mysqlModel->escape($aId) . ' AND `date` = "' . $date . '" GROUP BY `minute`) views
        LEFT JOIN (SELECT `aid`, `minute`, COUNT(0) counter, COUNT(DISTINCT tid) tcounter FROM `ads_clicks` WHERE `aid` = ' . $mysqlModel->escape($aId) .' AND `date` = "' . $date . '" GROUP BY `minute`) clicks
        ON views.aid = clicks.aid AND views.minute = clicks.minute')->all();
    return $result;
  }
}
