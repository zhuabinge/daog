<?php
/**
 * 日志操作类
 * @author Satan <zhangxinhe91@qq.com>
 */
class LogModel extends BpfModel
{
  /**
   * 获取后台日志并进行分页
   * @param array $conditions 日志查询条件数组
   * @param int $limit 分页显示数 默认20
   * @param int $page 页码 默认1
   * @return array 日志数组
   */
  public function getAdminLogs($conditions = null, $page = 1, $limit = 15)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder()
        ->select('*')
        ->from('admin_logs');
    if (isset($conditions['where'])) {
      foreach ($conditions['where'] as $k => $value) {
        $query->where(trim($k), $mysqlModel->escape($value));
      }
    }
    if (isset($conditions['orderby'])) {
      $query->orderby(trim($mysqlModel->escape($conditions['orderby'])));
    } else {
      $query->orderby('created DESC');
    }
    try {
      $result = $query->limitPage($limit, $page)->query()->all();
      return $result;
    } catch (BpfException $e) {
      return array();
    }
  }

  /**
   * 获取后台日志总数
   * @param array $conditions 日志查询条件数组
   * @return int/bool 符合日志总数, 查询条件出错返回false
   */
  public function getAdminLogsCount($conditions = null)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder()
        ->select('COUNT(0)')
        ->from('admin_logs');
    if (isset($conditions['where'])) {
      foreach ($conditions['where'] as $k => $value ) {
        $query->where(trim($k), $mysqlModel->escape($value));
      }
    }
    try {
      $result = $query->query()->field();
      return $result;
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
   * 插入操作日志记录
   * @param arrary $logs 日志数据
   * @return bool 成功 true, 失败返回 false
   */
  public function insertAdminLog($adminlog)
  {
    if (!isset($adminlog['uid'])) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $set = array(
      'uid' => trim($adminlog['uid']),
      'op' => isset($adminlog['op']) ? trim($adminlog['op']) : '',
      'body' => isset($adminlog['body']) ? trim($adminlog['body']) : '',
      'data' => isset($adminlog['data']) ? trim($adminlog['data']) : '',
      'url' => isset($adminlog['url']) ? trim($adminlog['url']) : '',
      'ip' => ipAddress(),
      'created' => REQUEST_TIME,
    );
    try {
      $mysqlModel->insert('admin_logs', $set);
      return true;
    } catch (BpfException $e) {
      return false;
    }
  }
}