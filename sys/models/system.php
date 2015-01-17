<?php
/**
 * 系统类
 * @author Satan <zhangxinhe91@qq.com>
 */
class SystemModel extends BpfModel
{
  /**
   * 插入报表对象集合
   * @param arrary $conditions 条件数组
   * @return bool 成功 true, 失败返回 false
   */
  public function insertStatistics($conditions = null)
  {
    if (empty($conditions['time'])) {
      return false;
    }
    $userModel = $this->getModel('user');
    $curTime = $conditions['time'];
    $Date = date('Ymd', $curTime);
    $Yesterday = date('Y-m-d' , strtotime('-1 day'));
    $curDate = strtotime($Yesterday); // 昨天凌晨时间戳
    $befDate = $curDate + 86399; // 昨天晚上12点时间戳
    // 获取用户,积分，集分宝总数
    $conditions = array(
      'select' => 'COUNT(0) user_count, SUM(`scores`) scores_count, SUM(`jf`) jf_count',
    );
    $info = $this->_getUsers($conditions);
    // 获取商品评论总数
    $conditions = array(
      'select' => 'COUNT(0) comment_count',
    );
    $info->comment_count = $this->_getCommentsCount($conditions);
    // 获取签到量
    $info->checkins_count = $userModel->getcheckinsCount(array('date' => $Date));
    // 获取登陆量
    $conditions = array(
      'where' => '`last_login_time` BETWEEN ' . $curDate . ' AND ' . $befDate,
      'select' => 'COUNT(0) login_count',
    );
    $info->login = $this->_getUsers($conditions);
    // 获取注册用户总数
    $conditions = array(
      'where' => '`register_time` BETWEEN ' . $curDate . ' AND ' . $befDate,
      'select' => 'COUNT(0) reg_count',
    );
    $info->register = $this->_getUsers($conditions);
    // 获取PV和UV
    // $info->analytics = $this->getPiwikData(date('Y-m-d', $curTime));
    // 获取缓存点击量
    $cacheModel = $this->getModel('cache');
    $item_clicks = $cacheModel->get('item_clicks');

    //数据拼装
    $set = array(
      'date' => $Date,
      'users_count' => isset($info->user_count) ? $info->user_count : 0,
      'users_incre' => isset($info->register->reg_count) ? $info->register->reg_count : 0,
      'login_count' => isset($info->login->login_count) ? $info->login->login_count : 0,
      'checkins_count' => isset($info->checkins_count) ? $info->checkins_count : 0,
      'comments_count' => isset($info->comment_count) ? $info->comment_count : 0,
      'scores_amount' => isset($info->scores_count) ? $info->scores_count : 0,
      'jf_amount' => isset($info->jf_count) ? $info->jf_count : 0,
      'item_clicks' => isset($item_clicks) ? $item_clicks : 0,
    );
    // if (isset($info->analytics) && $info->analytics) {
    //   $set['page_views'] = $info->analytics->today->nb_visits;
    //   $set['unique_vistors'] = $info->analytics->today->nb_uniq_visitors;
    // }
    // 获取前天数据
    $conditions = array(
      'start' => date('Ymd', $curTime - 86400),
      'end' => date('Ymd', $curTime - 86400),
    );
    $dashboard = $this->getStatistic($conditions);
    if (isset($dashboard) && $dashboard) { //计算差值
      $set['scores_incre'] = max(($info->scores_count - $dashboard->scores_amount), 0);
      $set['jf_incre'] = max(($info->jf_count - $dashboard->jf_amount), 0);
    }
    try {
      // 入库汇总数据
      $this->_insertStatistic($set);
      $cacheModel->delete('item_clicks'); //清空缓存点击量
      return true;
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
  * 获取报表对象
  * @param array $conditions 查询条件数组
  * @return object/bool 报表对象, 失败返回false
  */
  public function getStatistic($conditions = null)
  {
    if (empty($conditions['start']) && empty($conditions['end'])) {
      $userModel = $this->getModel('user');
      $curDate = strtotime(date('Y-m-d')); // 当天凌晨时间戳
      $befDate = $curDate + 86399; // 当天晚上12点时间戳
      $Date = date('Ymd'); // 当天时间YMD格式
      // 获取用户,积分，集分宝总数
      $conditions = array(
        'select' => 'COUNT(0) user_count, SUM(`scores`) scores_count, SUM(`jf`) jf_count',
      );
      $info = $this->_getUsers($conditions);
      // 获取商品评论总数
      $conditions = array(
        'select' => 'COUNT(0) comment_count',
      );
      $info->comment_count = $this->_getCommentsCount($conditions);
      // 获取当天签到量
      $info->checkins_count = $userModel->getcheckinsCount(array('date' => $Date));
      // 获取当天登陆量
      $conditions = array(
        'where' => '`last_login_time` BETWEEN ' . $curDate . ' AND ' . $befDate,
        'select' => 'COUNT(0) login_count',
      );
      $info->login = $this->_getUsers($conditions);
      // 获取注册用户总数
      $conditions = array(
        'where' => '`register_time` BETWEEN ' . $curDate . ' AND ' . $befDate,
        'select' => 'COUNT(0) reg_count',
      );
      $info->register = $this->_getUsers($conditions);
      // 获取PV和UV
      // $info->analytics = $this->getPiwikData(date('Y-m-d'));
      // 获取缓存点击量
      $cacheModel = $this->getModel('cache');
      $info->item_clicks = $cacheModel->get('item_clicks');
      // 获取前天数据
      $conditions = array(
        'start' => date('Ymd', REQUEST_TIME - 86400),
        'end' => date('Ymd', REQUEST_TIME - 86400),
      );
      // 获取当天积分增量和集分宝增量
      $dashboard = $this->getStatistic($conditions);
      if (isset($dashboard) && $dashboard) { //计算差值
        $info->scores_incre = max(($info->scores_count - $dashboard->scores_amount), 0);
        $info->jf_incre = max(($info->jf_count - $dashboard->jf_amount), 0);
      }
      $info->date = $Date;
      return $info;
    } else {
      $mysqlModel = $this->getModel('mysql');
      try {
        $result = $mysqlModel->query('SELECT * FROM `statistic` WHERE `date`
          BETWEEN ' . $mysqlModel->escape($conditions['start']) . ' AND ' . $mysqlModel->escape($conditions['end']));
        if ($conditions['start'] === $conditions['end']) {
          $result = $result->row();
        } else {
          $result = $result->all();
        }
        return $result;
      } catch ( BpfException $e ) {
        return false;
      }
    }
  }

  /**
   * 获取站外统计数据
   * @return array
   */
  public function getPiwikData($today = null)
  {
    if (empty($today)) {
      $today = date('Y-m-d', REQUEST_TIME);
    }
    $yesterday = date('Y-m-d', mktime(0, 0, 0) - 1);
    $url = $this->serviceUrl . '/piwik?' . http_build_query(array(
      'today' => $today,
      'yesterday' => $yesterday,
    ));
    $result = parent::get($url);
    return $result && is_object($result) && isset($result->result) ? $result->result : false;
  }

  /**
   * 插入报表对象
   * @param arrary $statistic 报表数据
   * @return bool 成功 true, 失败返回 false
   */
  private function _insertStatistic($statistic)
  {
    if (!isset($statistic['date'])) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $set = array(
      'date' => trim($statistic['date']),
      'users_count' => isset($statistic['users_count']) ? trim($statistic['users_count']) : 0,
      'users_incre' => isset($statistic['users_incre']) ? trim($statistic['users_incre']) : 0,
      'login_count' => isset($statistic['login_count']) ? trim($statistic['login_count']) : 0,
      'checkins_count' => isset($statistic['checkins_count']) ? trim($statistic['checkins_count']) : 0,
      'comments_count' => isset($statistic['comments_count']) ? trim($statistic['comments_count']) : 0,
      'page_views' => isset($statistic['page_views']) ? trim($statistic['page_views']) : 0,
      'unique_vistors' => isset($statistic['unique_vistors']) ? trim($statistic['unique_vistors']) : 0,
      'item_clicks' => isset($statistic['item_clicks']) ? trim($statistic['item_clicks']) : 0,
      'scores_amount' => isset($statistic['scores_amount']) ? trim($statistic['scores_amount']) : 0,
      'scores_incre' => isset($statistic['scores_incre']) ? trim($statistic['scores_incre']) : 0,
      'jf_amount' => isset($statistic['jf_amount']) ? trim($statistic['jf_amount']) : 0,
      'jf_incre' => isset($statistic['jf_incre']) ? trim($statistic['jf_incre']) : 0,
      'created' => REQUEST_TIME,
    );
    try {
      $mysqlModel->insert('statistic', $set);
      return true;
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
   * 获取用户信息
   * @param array $conditions 用户查询条件数组
   * @return array 用户数组
   */
  private function _getUsers($conditions = null)
  {
    $mysqlModel = $this->getModel('mysql');
    if (empty($conditions['select'])) {
      $conditions['select'] = '*';
    }
    if (empty($conditions['where'])) {
      $conditions['where'] = '1=1';
    }
    $result = $mysqlModel->query('SELECT ' . $mysqlModel->escape($conditions['select']) . ' FROM `users`
        WHERE ' . $mysqlModel->escape($conditions['where']));
    try {
      $result = $result->row();
      return $result;
    } catch ( BpfException $e ) {
      return false;
    }
  }

  /**
   * 获取商品信息
   * @param array $conditions 商品查询条件数组
   * @return array 商品数组
   */
  private function _getProducts($conditions = null)
  {
    $mysqlModel = $this->getModel('mysql');
    if (empty($conditions['select'])) {
      $conditions['select'] = '*';
    }
    if (empty($conditions['where'])) {
      $conditions['where'] = '1=1';
    }
    $result = $mysqlModel->query('SELECT ' . $mysqlModel->escape($conditions['select']) . ' FROM `products`
        WHERE `status` = "1" AND ' . $mysqlModel->escape($conditions['where']));
    try {
      $result = $result->row();
      return $result;
    } catch ( BpfException $e ) {
      return false;
    }
  }

  /**
   * 获取评论信息
   * @param array $conditions 评论查询条件数组
   * @return array 评论数组
   */
  private function _getCommentsCount($conditions = null)
  {
    $mysqlModel = $this->getModel('mysql');
    if (empty($conditions['select'])) {
      $conditions['select'] = '*';
    }
    if (empty($conditions['where'])) {
      $conditions['where'] = '1=1';
    }
    $result = $mysqlModel->query('SELECT ' . $mysqlModel->escape($conditions['select']) . ' FROM `comments`
        WHERE `status` = "1" AND `reply_cid` = "0" AND ' . $mysqlModel->escape($conditions['where']));
    try {
      $result = $result->field();
      return $result;
    } catch ( BpfException $e ) {
      return false;
    }
  }
}
