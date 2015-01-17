<?php
/**
 * 淘宝客活动类
 * @author KaiFu <aixinqing@gmail.com>
 */
class TbkactivityModel extends BpfModel
{
  /**
   * 插入淘宝客活动对象
   * @param arrary $tbkActivities 商家对象
   */
  public function insertTaobaokeActivities($tbkActivities)
  {
  
    $mysqlModel = $this->getModel('mysql');
    try {
      $result = $mysqlModel->inserts('taobaoke_activities', $tbkActivities, true);
      return $result->insertId();
    } catch (BpfException $e) {
      exit();
      return false;
    }
  } 

   /**
   * 获取全部活动标题
   * 
   * @return 标题数组
   */
  public function getTbkActivities()
  {
  
    $mysqlModel = $this->getModel('mysql');
    return $mysqlModel->getSqlBuilder()
        ->select('title')
        ->from('taobaoke_activities')
        ->query()
        ->all();
  }

  /**
   * 删除活动
   * @param $eventId 阿里妈妈活动id
   * @return 标题数组
   */
  public function deleteTbkActivities($eventId)
  {
  
    $mysqlModel = $this->getModel('mysql');
       //删除product
    $result = $mysqlModel->delete('taobaoke_activitiese',array(
      'eventid' => $eventId,
    ));
    $affected = $result->affected();
    return $affected;

  }
}
