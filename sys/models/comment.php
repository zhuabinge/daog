<?php
/**
 * 评论类
 * @author Hao <sixihaoyue@gmail.com>
 */
class CommentModel extends BpfModel
{
  /**
  * 插入评论对象
  * @param arrary $comment 评论对象
  * @return int/bool 新标签ID, 失败返回 false
  */
  public function insertComment($comment)
  {
    if (!isset($comment['body']) || !isset($comment['pid']) || !isset($comment['uid'])) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $set = array(
      'body' => trim($comment['body']),
      'pid' => isset($comment['pid']) ? $comment['pid'] : null,
      'uid' => isset($comment['uid']) ? $comment['uid'] : null,
      'status' => isset($comment['status']) ? $comment['status'] : null,
      'ip' =>  ipAddress(),
      'created' => REQUEST_TIME,
      'updated' => REQUEST_TIME,
    );
    //如果是管理员回复，则将被回复的评论进行处理
    if(isset($comment['reply_cid'])) {
      $set['reply_cid'] = $comment['reply_cid'];
      $this->_addReplyCount($comment['reply_cid']);
    }

    try {
      $result = $mysqlModel->insert('comments', $set);
      return $result->insertId();
    } catch (BpfException $e) {
      return false;
    }
  }

  private function _addReplyCount($commentId)
  {
    $mysqlModel = $this->getModel('mysql');
     $set = array(
      'reply_count' => array(
        'escape' => false,
        'value' => 'reply_count + 1',
      ),
      'last_reply_time' => REQUEST_TIME,
      'updated' => REQUEST_TIME,
    );
    $mysqlModel->update('comments', $set, array(
      'cid' => $commentId,
    ));
  }

  /**
  * 获取评论回复
  * @param array $commentIds 评论对象id集合
  * @return array 评论回复集合
  */
  public function getReplies($commentIds)
  {
    $mysqlModel = $this->getModel('mysql');
    $result = $mysqlModel->getSqlBuilder()
        ->from('comments')
        ->where('reply_cid IN', $commentIds)
        ->query()
        ->all();
    $replys = array();
    foreach ($result as $row) {
      if(!isset($replys[$row->reply_cid])) {
        $replys[$row->reply_cid] = array();
      }
      $replys[$row->reply_cid][] = $row;
    }
    return $replys;
  }

  /**
  * 更新评论对象（最多二级评论）可以管理管理员评论
  * @param int $commentId 评论对象id
  * @return int/bool 影响行数, 失败返回 false
  */
  public function updateComment($commentId, $op)
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
      return $mysqlModel->update('comments',
        array(
          'status' => $status,
          'updated' => REQUEST_TIME,
        ),
        array(
          'cid ' => $commentId,
        ))->affected();
    } catch (BpfException $e) {
      return false;
    }
  }



  /**
  * 量审批／删除评论对象(只考虑了二级回复，不考虑多级) 不可以删除管理员评论
  * @param array $commentIds 评论对象ids数组
  * @param string $op 要操作的类型
  * @return bool 失败返回 false， 成功返回true
  */
  public function updateComments($commentIds, $op)
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
      $mysqlModel->update('comments',
        array(
          'status' => $status,
          'updated' => REQUEST_TIME,
        ),
        array(
          'cid IN' => $commentIds,
          'reply_cid' => '0',
        ));
      return  true;
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
  * 获取评论
  * @param int $page 评论id
  * @return object 评论对象
  */
  public function getComment($commentId)
  {
    $mysqlModel = $this->getModel('mysql');
    $comment = $mysqlModel->getSqlBuilder()
        ->from('comments')
        ->where('cid', $commentId)
        ->query()
        ->row();
    if( $comment && $comment->reply_count > 0 ) {
      $comment->replys = $mysqlModel->getSqlBuilder()
          ->from('comments')
          ->where('reply_cid', $commentId)
          ->query()
          ->all();
    }
    return $comment;
  }

  /**
  * 删除评论
  * @param int $commentId 评论id
  * @return int 影响行数
  */
  public function deleteComment($commentId)
  {
    $mysqlModel = $this->getModel('mysql');
    $cid = $mysqlModel->escape($commentId);
    return $mysqlModel->query('DELETE FROM `comments` WHERE `cid` = ' . $cid . ' OR `reply_cid` = ' . $cid)
        ->affected();
  }

  /**
  * 批量删除评论
  * @param array $commentIds 评论id集合
  * @return int 影响行数
  */
  public function deleteComments($commentIds)
  {
    $mysqlModel = $this->getModel('mysql');
    $cids = $mysqlModel->escape('(' . implode(', ', $commentIds) . ')');
    return $mysqlModel->query('DELETE FROM `comments` WHERE `cid` IN ' . $cids . ' OR `reply_cid` IN ' . $cids)
        ->affected();
  }

  /**
  * 获取评论并进行分页
  * @param array $conditions 评论查询条件数组
  * @param int $limit 分页显示数 默认20
  * @param int $page 页码 默认1
  * @return array 评论数组
  */
  public function getComments($conditions = null, $page = null, $limit = 15)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder();
    $query->from('comments')
        ->where('reply_cid', '0');
    if (isset($conditions['search'])) {
      $query->where('body LIKE', '%' . $mysqlModel->escape($conditions['search']) . '%');
    }
    if (isset($conditions['uid'])) {
      $query->where('uid', $conditions['uid']);
    }
    if (isset($conditions['pid'])) {
      $query->where('pid', $conditions['pid']);
    }
    if (isset($conditions['status'])) {
      $query->where('status', $conditions['status']);
    }
    if (isset($conditions['orderby']) && $conditions['orderby'] === 'last_reply_time' ) {
      $query->orderby('last_reply_time DESC');
    } else {
      $query->orderby('created DESC');
    }
    if (isset($page)) {
      $query->limitPage($limit, $page);
    }
    return $query->query()->all();
  }

  public function getCommentsCount($conditions = null)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder();
    $query->select('COUNT(0)')
        ->from('comments')
        ->where('reply_cid', '0');
    if (isset($conditions['search'])) {
      $query->where('body LIKE', '%' . $mysqlModel->escape($conditions['search']) . '%');
    }
    if (isset($conditions['uid'])) {
      $query->where('uid', $conditions['uid']);
    }
    if (!empty($conditions['today'])) {
      $query->where('created >=', mktime(0, 0, 0));
    }
    if (isset($conditions['pid'])) {
      $query->where('pid', $conditions['pid']);
    }
    return $query->query()->field();
  }
}
