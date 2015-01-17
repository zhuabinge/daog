<?php
/**
 * 标签类
 * @author Hao <sixihaoyue@gmail.com>
 */
class TagModel extends BpfModel
{

  /**
   * 插入标签对象
   * @param arrary $tag 标签对象
   * @return int/bool 新标签ID, 失败返回 false
   */
  public function insertTag($tag)
  {
    if (!isset($tag['title'])) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $set = array(
      'title' => trim($tag['title']),
      'status' => isset($tag['status']) ? $tag['status'] : null,
      'created' => REQUEST_TIME,
      'updated' => REQUEST_TIME,
    );
    try {
      $result = $mysqlModel->insert('tags', $set);
      return $result->insertId();
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
   * 批量插入标签
   * @param arrary $tags 标签数组
   * @return bool/arrary 插入标签ID数组, 失败返回 false
   */
  public function saveTags($tags)
  {
    $mysqlModel = $this->getModel('mysql');
    $sets = array();
    foreach ($tags as $tag) {
      $sets[] = array(
        'title' => trim($tag),
        'created' => REQUEST_TIME,
        'updated' => REQUEST_TIME,
      );
    }
    $result = $mysqlModel->insert('tags', $sets, true);
    if($result) {
      return $mysqlModel->getSqlBuilder()
          ->select('tid, title')
          ->from('tags')
          ->where('title IN', $tags)
          ->query()
          ->columnWithKey('tid', 'title');
    }else {
      return false;
    }
  }

  /**
   * 更新标签对象
   * @param int $tagId 标签对象
   * @param arrary $tag 标签对象
   * @return int/bool 新标签ID, 失败返回 false
   */
  public function updateTag($tagId, $tag = null)
  {
    if (!isset($tagId)) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $set = array(
      'updated' => REQUEST_TIME,
    );
    if (isset($tag['title'])) {
      $set['title'] = trim($tag['title']);
    }
    if (isset($tag['status'])) {
      $set['status'] = $tag['status'];
    }
    try {
      $result = $mysqlModel->update('tags', $set, array(
        'tid' => $tagId,
      ));
      return $result->affected();
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
   * 批量修改标签状态
   * @param array $tagIds 标签对象
   * @param string $op 操作类型
   * @return bool
   */
  public function updateTags($tagIds, $op)
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
      $mysqlModel->update('tags',
        array(
          'status' => $status,
          'updated' => REQUEST_TIME,
        ),
        array(
          'tid IN' => $tagIds,
        ));
      return  true;
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
   * 删除标签对象
   * @param int $tagId 标签对象
   * @return int/bool 影响行数, 失败返回 false
   */
  public function deleteTag($tagId)
  {
    if (!isset($tagId)) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    //删除product
    $result = $mysqlModel->delete('tags',array(
      'tid' => $tagId,
    ));
    $affected = $result->affected();
    if ($affected) {
      //删除商品与标签的关系
      $mysqlModel->delete('products_tags',array(
        'tid' => $tagId,
      ));
    }
    return $affected;
  }

  /**
   * 获取标签对象
   * @param int $tagId 标签ID
   * @return object 标签对象
   */
  public function getTag($tagId)
  {
    if (!isset($tagId)) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $tag = $mysqlModel->query('SELECT * FROM `tags` WHERE `tid` = "' . $mysqlModel->escape($tagId) . '"')->row();
    if ($tag) {
      $tag->link = urlTag($tagId);
    }
    return $tag;
  }

  /**
   * 获取所有标签并进行分页
   * @param array $conditions 标签查询条件数组
   * @param int $limit 分页显示数 默认20
   * @param int $page 页码 默认1
   * @return array 标签数组
   */
  public function getTags($conditions = null, $page = 1, $limit = 15)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder()
        ->from('tags');
    if (isset($conditions['search'])) {
      $query->where('tags.title LIKE', '%' . $mysqlModel->escape($conditions['search']) . '%');
    }
    $result = $query->orderby('created DESC')
        ->limitPage($limit, $page)
        ->query()
        ->all();
    foreach ($result as $tag) {
      $tag->link = urlTag($tag);
    }
    return $result;
  }

   /**
   * 获取所有标签总数
   * @param array $conditions 标签查询条件数组
   * @return int/bool 符合商品总数, 查询条件出错返回false
   */
  public function getTagsCount($conditions = null)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder()
        ->select('COUNT(0)')
        ->from('tags');
    if (isset($conditions['search'])) {
      $query->where('tags.title LIKE', '%' . $mysqlModel->escape($conditions['search']) . '%');
    }
    return $query->query()->field();
  }

  /**
  * 批量设置标签商品
  * @param int $tagId 标签Id
  * @param arrry $productIds 商品id集合
  * @return bool 失败返回 false,成功返回true
  */
  public function insertTagProducts($tagId, $productIds)
  {
    $mysqlModel = $this->getModel('mysql');
    $set = array();
    foreach ($productIds as $pid) {
      array_push($set, array(
        'pid' => $pid,
        'tid' => $tagId,
      ));
    }
    try {
      $mysqlModel->insert('products_tags', $set)->insertId();
      return true;
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
  *获取所有有效的标签
  * @return array 返回标签值与标签名字的键值对
  */
  public function getTagsMap()
  {
    return $this->getModel('mysql')
        ->getSqlBuilder()
        ->select('tid, title')
        ->from('tags')
        ->where('status', '1')
        ->query()
        ->columnWithKey('tid', 'title');
  }

  /**
  * 批量删除标签商品
  * @param int $tagId 标签Id
  * @param arrry $productIds 商品id集合
  * @return bool 失败返回 false,成功返回true
  */
  public function deleteTagProducts($tagId, $productIds)
  {
    $mysqlModel = $this->getModel('mysql');
    try {
      $mysqlModel->delete('products_tags', array(
        'pid IN' => $productIds,
        'tid' => $tagId,
      ));
      return true;
    } catch (BpfException $e) {
      return false;
    }
  }

}
