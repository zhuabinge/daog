<?php
/**
 * 商品类
 * @author Hao <sixihaoyue@gmail.com>
 */
class ProductModel extends BpfModel
{
  /**
   * 检查商品类目seo路径是否存在
   * @param string $path 商品类目seo路径
   * @return int/bool 存在商品类目id, 不存在 false
   */
  public function checkCategoryPathIsExists($path)
  {
    $params = array(
      'path' => $path,
    );
    $url = $this->serviceUrl . '/checkCategoryPathIsExists?' . http_build_query($params);
    $result = $this->get($url)->result;
    return $result;
  }

  /**
   * 插入商品类目对象D
   * @param arrary $category 商品类目
   * @return int/bool 新商品类ID, 失败返回 false
   */
  public function insertCategory($category)
  {
    if (!isset($category['name'])) {
      return false;
    }
    $url = $this->serviceUrl . '/insertCategory';
    $set = array(
      'name' => trim($category['name']),
      'image_path' => isset($category['image_path']) ? trim($category['image_path']) : null,
      'status' => isset($category['status']) ? $category['status'] : null,
      'show_on_home' => isset($category['show_on_home']) ? $category['show_on_home'] : null,
      'sort_weight' => isset($category['sort_weight']) ? $category['sort_weight'] : null,
      'seo_keyword' => isset($category['seo_keyword']) ? trim($category['seo_keyword']) : null,
      'seo_description' => isset($category['seo_description']) ? trim($category['seo_description']) : null,
      'created' => REQUEST_TIME,
      'updated' => REQUEST_TIME,
    );
    if (isset($category['seo_path']) && trim($category['seo_path']) !== '') {
      $set['seo_path'] = trim($category['seo_path']);
    }
    if(isset($category['parent_cid']) && $category['parent_cid'] != 0) {
      $parentCategory = $this->getCategory($category['parent_cid']);
      if ($parentCategory && $parentCategory->parent_cid == 0) {
        $set['parent_cid'] = $category['parent_cid'] ;
      } else {
        return false;
      }
    }
    $categorieId = $this->post($url, $set, false)->result;
    if ($categorieId > 0 && isset($category['file_id'])) {
      $this->setCategorieImage($categorieId, $category['file_id']);
    }
    return $categorieId;
  }

  /**
   * 更新商品类目对象
   * @param int $cid 商品类目ID
   * @param arrary $category 要更新商品类目信息
   * @return int/bool 影响行数, 失败返回 false
   */
  public function updateCategory($categoryId, $category)
  {
    if (!isset($categoryId)) {
      return false;
    }
    $set = array(
      'categoryId' => $categoryId,
      'updated' => REQUEST_TIME,
    );
    if(isset($category['parent_cid']) ) {
      if ($category['parent_cid'] != 0) {
        $parentCategory = $this->getCategory($category['parent_cid']);
        if ($parentCategory && $parentCategory->parent_cid != 0) {
          return false;
        }
      }
      $set['parent_cid'] = $category['parent_cid'];
    }

    if (isset($category['name'])) {
      $set['name'] = trim($category['name']);
    }
    if (isset($category['status'])) {
      $set['status'] = $category['status'];
    }
    if (isset($category['show_on_home'])) {
      $set['show_on_home'] = $category['show_on_home'];
    }
    if (isset($category['sort_weight'])) {
      $set['sort_weight'] = $category['sort_weight'];
    }
    if (isset($category['seo_path'])) {
      $set['seo_path'] = trim($category['seo_path']);
    }
    if (isset($category['seo_keyword']) && trim($category['seo_keyword']) !== '') {
      $set['seo_keyword'] = trim($category['seo_keyword']);
    }
    if (isset($category['seo_description'])) {
      $set['seo_description'] = trim($category['seo_description']);
    }
    //更新商品类目图片
    if (isset($category['file_id'])){
      $this->setCategorieImage($categoryId, $category['file_id']);
    }
    $url = $this->serviceUrl . '/updateCategory';
    $affected = $this->put($url, $set)->result;
    return $affected;
  }

  /**
   * 删除商品类目对象 （如果有子类目会删除子类目，并将所有关联的产品的类型重置为0）
   * @param int $cid 商品类目ID
   * @return int/bool 影响行数, 失败返回 false
   */
  public function deleteCategory($categoryId)
  {
    $params = array(
      'categoryId' => $categoryId,
    );
    $url = $this->serviceUrl . '/deleteCategory?' . http_build_query($params);
    $affected = $this->del($url)->result;
    return $affected;
  }

  /**
  * 设置类目图片
  * @param int $categorieId 类目id
  * @param int $fileId 文件id
  * @return int/bool 影响行数, 失败返回 false
  */
  public function setCategorieImage($categorieId, $fileId)
  {
    $fileModel = $this->getModel('file');
    $mysqlModel = $this->getModel('mysql');
    $file = $fileModel->getFile('cate_img', $fileId);
    if ($file) {
      $ordFile = $mysqlModel->query('SELECT `file_id` FROM `categories` WHERE `cid` = ' . $mysqlModel->escape($categorieId))->row()->file_id;
      //旧类目图片存在
      if ($ordFile > 0 && $fileId != $ordFile) {
        $fileModel->delete('cate_img', $ordFile);
      }
      $set = array(
        'file_id' => $fileId,
        'image_path' => $file->file_path,
      );
      $mysqlModel->update('categories', $set, array(
        'cid' => $categorieId,
      ));
      return true;
    }
    return false;
  }

  /**
   * 更新商品类目顺序和父子关系
   * @param array $order 类目顺序和父子关系数组
   * @return bool
   * 数组格式:
   * {
   *   { id: n1 }
   *   { id: n2 }
   *   { id: n3, children: [
   *     { id: n4 }
   *     { id: n5 }
   *   ]}
   * }
   */
  public function updateCategoriesOrder($order)
  {
    $list = $this->_getCategoriesOrder($order);
    if ($list) {
      $set = array();
      $count = count($list);
      foreach ($list as $cid => $parentCid) {
        $set[] = array(
          'cid' => $cid,
          'parent_cid' => $parentCid,
          'sort_weight' => --$count,
        );
      }
      $mysqlModel = $this->getModel('mysql');
      $mysqlModel->insert('categories', $set, false, true);
    }
    return true;
  }

  private function _getCategoriesOrder($list, $parentCid = 0)
  {
    $rows = array();
    foreach ($list as $i => $row) {
      $rows[$row['id']] = $parentCid;
      if (isset($row['children'])) {
        // 如果包含子目录
        $children = $this->_getCategoriesOrder($row['children'], $row['id']);
        $rows += $children;
      }
    }
    return $rows;
  }

  /**
   * 获取商品类目对象
   * @param int $categoryId 商品类目ID
   * @return object 类目对象
   */
  public function getCategory($categoryId)
  {
    if (!isset($categoryId)) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $category = $mysqlModel->query('SELECT * FROM `categories` WHERE `cid` = "' . $mysqlModel->escape($categoryId) . '"')->row();
    if ($category) {
      $category->link = urlCategory($category);
    }
    return $category;
  }

  /**
   * 获取首页推荐类目
   * @return array 类目数组
   */
  public function getHomeCategories()
  {
    $result = $this->getModel('mysql')
        ->query('SELECT * FROM `categories` WHERE `show_on_home` = 1 AND `status` = 1 ORDER BY `sort_weight` DESC')
        ->all();
    foreach ($result as $category) {
      $category->link = urlCategory($category);
    }
    return $result;
  }

  /**
   * 获取所有一级类目
   * @return array 类目数组
   */
  public function getTopCategories()
  {
    $result = $this->getModel('mysql')
        ->query('SELECT * FROM `categories` WHERE `parent_cid` = 0 AND `status` = 1 ORDER BY `sort_weight` DESC')
        ->all();
    foreach ($result as $category) {
      $category->link = urlCategory($category);
    }
    return $result;
  }

  /**
   * 获取商品类目列表
   * @param int $parentCid 父节点类目ID, 默认返回所有类目
   * @return array 类目数组
   */
  public function getCategories($parentCid = null, $status = null)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder()
        ->from('categories')
        ->orderby('sort_weight DESC');
    if (isset($parentCid)) {
      // 按父节点查询
      $query->where('parent_cid', $parentCid);
    }
    if (isset($status) && is_numeric($status)) {
      $query->where('status', $status);
    }
    $result = $query->query()->all();
    foreach ($result as $category) {
      $category->link = urlCategory($category);
    }
    return $result;
  }

  /**
   * 获取商品类目树
   * @return array 类目树数组
   */
  public function getCategoryTree()
  {
    $tree = array();
    $categories = $this->getCategories();
    // 生成一级类目
    foreach ($categories as $key => $row) {
      if ($row->parent_cid == 0) {
        $row->children = array();
        $tree[$row->cid] = $row;
        unset($categories[$key]);
      }
    }
    // 生成二级类目
    foreach ($categories as $row) {
      if (isset($tree[$row->parent_cid])) {
        $tree[$row->parent_cid]->children[] = $row;
      }
    }
    return $tree;
  }

  /**
   * 插入商品对象
   * @param arrary $product 商品
   * @return int/bool 新商品ID, 失败返回 false
   */
  public function insertProduct($product)
  {
    if (!isset($product['title'])) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $set = array(
      'cid' => isset($product['cid']) ? $product['cid'] : null,
      'title' => trim($product['title']),
      'feature' => isset($product['feature']) ? trim($product['feature']) : null,
      'mall_pid' => isset($product['mall_pid']) ? trim($product['mall_pid']) : null,
      'body' => isset($product['body']) ? trim($product['body']) : null,
      'data' => isset($product['data']) ? trim($product['data']) : null,
      'list_price' => isset($product['list_price']) ? $product['list_price'] : null,
      'sell_price' => isset($product['sell_price']) ? $product['sell_price'] : null,
      'url' => isset($product['url']) ? trim($product['url']) : null,
      'status' => isset($product['status']) ? $product['status'] : null,
      'editor_uid' => isset($product['editor_uid']) ? $product['editor_uid'] : 0,
      'views' => isset($product['views']) ? $product['views'] : 0,
      'expired' => isset($product['expired']) ? $product['expired'] : 0,
      'scheduling' => isset($product['scheduling']) ? $product['scheduling'] : 0,
      'is_ads' => isset($product['is_ads']) ? $product['is_ads'] : 0,
      'ratepercent' => isset($product['ratepercent']) ? $product['ratepercent'] : 0,
      'commission' => isset($product['commission']) ? $product['commission'] : 0,
      'totalnum' => isset($product['totalnum']) ? $product['totalnum'] : 0,
      'totalfeemoney' => isset($product['totalfeemoney']) ? $product['totalfeemoney'] : 0,
      'sellcount' => isset($product['sellcount']) ? $product['sellcount'] : 0,
      'buyerscore' => isset($product['buyerscore']) ? $product['buyerscore'] : 0,
      'wantcount' => isset($product['wantcount']) ? $product['wantcount'] : 0,
      'stock' => isset($product['stock']) ? $product['stock'] : 0,
      'free' => isset($product['free']) ? $product['free'] : 0,
      'delivery' => isset($product['delivery']) ? $product['delivery'] : 0,
      'weight' => isset($product['weight']) ? $product['weight'] : 0,
      'history_price' => isset($product['history_price']) ? $product['history_price'] : 0,
      'sort' => isset($product['sort']) ? $product['sort'] : 0,
      'mid' => isset($product['mid']) ? $product['mid'] : 0,
      'created' => REQUEST_TIME,
      'updated' => REQUEST_TIME,
    );
    if (isset($product['files'][0])) {
      $file = $this->getModel('file')->getFile('pro_img', $product['files'][0]);
      if ($file ) {
        $set['image_path'] = trim($file->file_path);
      }
    }
    try {
      $insertId = $mysqlModel->insert('products', $set)->insertId();
      if ($insertId) {
        $this->setProductImages($insertId, $product['files']);
      }
      return $insertId ;
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
   * 更新商品对象
   * @param int $productId 商品id
   * @param arrary $product 商品
   * @return int/bool 影响行数, 失败返回 false
   */
  public function updateProduct($productId, $product = null, $updated = 0)
  {
    if (!isset($productId)) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $set = array(
      'updated' => REQUEST_TIME,
    );
    // 不更新时间
    if ($updated === 1) {
      unset($set['updated']);
    }
    if (isset($product['cid'])) {
      $set['cid'] = $product['cid'];
    }
    if (isset($product['title'])) {
      $set['title'] = trim($product['title']);
    }
    if (isset($product['feature'])) {
      $set['feature'] = trim($product['feature']);
    }
    if (isset($product['body'])) {
      $set['body'] = trim($product['body']);
    }
    if (isset($product['files'][0])) {
      $file = $this->getModel('file')->getFile('pro_img', $product['files'][0]);
      if ($file ) {
        $set['image_path'] = trim($file->file_path);
      }
    }
    if (isset($product['list_price'])) {
      $set['list_price'] = $product['list_price'];
    }
    if (isset($product['sell_price'])) {
      $set['sell_price'] = $product['sell_price'];
    }
    if (isset($product['url'])) {
      $set['url'] = trim($product['url']);
    }
    if (isset($product['status'])) {
      $set['status'] = $product['status'];
    }
    if (isset($product['editor_uid'])) {
      $set['editor_uid'] = $product['editor_uid'];
    }
    if (isset($product['views'])) {
      $set['views'] = $product['views'];
    }
    if (isset($product['data'])) {
      $set['data'] = $product['data'];
    }
    if (isset($product['scheduling'])) {
      $set['scheduling'] = $product['scheduling'];
    }
    if (isset($product['expired'])) {
      $set['expired'] = $product['expired'];
    }
    if (isset($product['is_ads'])) {
      $set['is_ads'] = $product['is_ads'];
    }
    if (isset($product['ratepercent'])) {
      $set['ratepercent'] = $product['ratepercent'];
    }
    if (isset($product['commission'])) {
      $set['commission'] = $product['commission'];
    }
    if (isset($product['totalnum'])) {
      $set['totalnum'] = $product['totalnum'];
    }
    if (isset($product['totalfeemoney'])) {
      $set['totalfeemoney'] = $product['totalfeemoney'];
    }
    if (isset($product['sellcount'])) {
      $set['sellcount'] = $product['sellcount'];
    }
    if (isset($product['buyerscore'])) {
      $set['buyerscore'] = $product['buyerscore'];
    }
    if (isset($product['wantcount'])) {
      $set['wantcount'] = $product['wantcount'];
    }
    if (isset($product['stock'])) {
      $set['stock'] = $product['stock'];
    }
    if (isset($product['free'])) {
      $set['free'] = $product['free'];
    }
    if (isset($product['delivery'])) {
      $set['delivery'] = $product['delivery'];
    }
    if (isset($product['weight'])) {
      $set['weight'] = $product['weight'];
    }
    if (isset($product['history_price'])) {
      $set['history_price'] = $product['history_price'];
    }
    if (isset($product['sort'])) {
      $set['sort'] = $product['sort'];
    }
    try {
      $result = $mysqlModel->update('products', $set, array(
        'pid' => $productId,
      ));
      if (isset($product['files'])) {
        $this->setProductImages($productId, $product['files']);
      }
      return $result->affected();
    } catch (BpfException $e) {

      return false;
    }
  }

  /**
   * 更新商品对象
   * @param int $productIds 商品id集合
   * @param arrary $product 更新的内容
   * @return int/bool 影响行数, 失败返回 false
   */
  public function updateProducts($productIds, $product = null)
  {
    $mysqlModel = $this->getModel('mysql');
    $set = array(
      'updated' => REQUEST_TIME,
    );
    if (isset($product['cid'])) {
      $set['cid'] = $product['cid'];
    }
    if (isset($product['status'])) {
      $set['status'] = $product['status'];
    }
    try {
      $result = $mysqlModel->update('products', $set, array(
        'pid IN' => $productIds,
      ));
      return $result->affected();
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
   * 删除商品对象
   * @param int $productId 商品id
   * @return int/bool 影响行数, 失败返回 false
   */
  public function deleteProduct($productId)
  {
    if (!isset($productId)) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    //删除product
    $result = $mysqlModel->delete('products',array(
      'pid' => $productId,
    ));
    $affected = $result->affected();
    if ($affected) {
      //删除products_images
      $this->setProductImages($productId, array());
      //删除products_channels
      $mysqlModel->delete('products_channels',array(
        'pid' => $productId,
      ));
      //删除comments
      $mysqlModel->delete('comments',array(
        'pid' => $productId,
      ));
      //删除products_tags
      $mysqlModel->delete('products_tags',array(
        'pid' => $productId,
      ));
      //删除与用户的收藏关系
      $mysqlModel->delete('users_likes',array(
        'pid' => $productId,
      ));
    }
    return $affected;
  }

  /**
  * 获取商品对象
  * @param int $productId 商品id
  * @return object/bool 商品对象, 失败返回false
  */
  public function getProduct($productId)
  {
    if (!isset($productId)) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');

    $product = $mysqlModel->getSqlBuilder()
        ->from('products')
        ->where('products.pid', $productId)
        ->query()
        ->row();
    if ($product) {
      $product->images = $this->getProductImages($product->pid);
      $product->link = urlProduct($product);
      $product->link_click = urlProduct($product, 'click');
    }
    return $product;
  }


  /**
   * 通过关键词获取商品并进行分页
   * @param string $key 商品查询关键词
   * @param int $limit 分页显示数 默认15
   * @param int $page 页码 默认1
   * @return array 商品数组
   */
  public function searchProducts($key, $page = 1, $limit = 15)
  {
    if (!isset($key)) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $result = $mysqlModel->query('SELECT * FROM products WHERE `status` = "1" AND (`title` LIKE "%' .
        $mysqlModel->escape($key) . '%" OR `feature` LIKE "%' . $mysqlModel->escape($key) . '%") LIMIT ' . ($page - 1) . ',' . $limit)
        ->all();
    foreach ($result as $product) {
      $product->link = urlProduct($product);
      $product->link_click = urlProduct($product, 'click');
    }
    return $result;
  }

  /**
   * 通过关键词获取商品并进行分页
   * @param string $key 商品查询关键词
   * @return int 符合条件的商品个数
   */
  public function searchProductsCount($key)
  {
    if (!isset($key)) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    return $mysqlModel->query('SELECT COUNT(0) FROM products WHERE `status` = "1" AND (`title` LIKE "%' .
        $mysqlModel->escape($key) . '%" OR `feature` LIKE "%' . $mysqlModel->escape($key) . '%")')
        ->field();
  }

  /**
   * 获取商品并进行分页
   * @param array $conditions 商品查询条件数组
   * @param int $limit 分页显示数 默认20
   * @param int $page 页码 默认1
   * @return array 商品数组
   */
  public function getProducts($conditions = null, $page = 1, $limit = 15)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder();
    if (isset($conditions['date']) && is_array($conditions['date'])) {
      $query->select('products.*, products_clicks.counter')
          ->from('products')
          ->join('products_clicks', 'products_clicks.pid = products.pid AND products_clicks.date >= ' . $conditions['date']['scheduling'] . 
                 ' AND products_clicks.date <= ' . $conditions['date']['expired'], 'left');
    } else {
      $query->select('products.*')->from('products');
    }
    if (isset($conditions['where'])) {
      foreach ($conditions['where'] as $k => $value ) {
        $query->where('products.' . trim($k), $value);
      }
    }
    if (isset($conditions['tags'])) {
      $query->join('products_tags', 'products_tags.pid = products.pid')
          ->where('products_tags.tid IN', $conditions['tags']);
    }
    if (isset($conditions['channel'])) {
      $query->join('products_channels', 'products_channels.pid = products.pid')
          ->where('products_channels.cid', $conditions['channel']);
    }
    if (isset($conditions['groupby'])) {
      $query->groupby($conditions['groupby']);
    }
    if (isset($conditions['orderby'])) {
      if ($conditions['orderby'] === 'RAND()') {
        $query->orderby('RAND()');
      } else {
        $query->orderby(trim($conditions['orderby']));
      }
    } else {
      $query->orderby('products.created DESC');
    }
    try {
      $result = $query->limitPage($limit, $page)->query()->all();
      foreach ($result as $product) {
        $product->link = urlProduct($product);
        $product->link_click = urlProduct($product, 'click');
      }
      return $result;
    } catch ( BpfException $e ) {
      return array();
    }
  }

  /**
   * 获取商品总数
   * @param array $conditions 商品查询条件数组
   * @return int/bool 符合商品总数, 查询条件出错返回false
   */
  public function getProductsCount($conditions = null)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder()
        ->select('COUNT(0)')
        ->from('products')
        ->join('products_clicks', 'products_clicks.pid = products.pid AND products_clicks.date = ' . date('Ymd', REQUEST_TIME), 'left');
    if (isset($conditions['where'])) {
      foreach ($conditions['where'] as $k => $value ) {
        $query->where('products.' . trim($k), $value);
      }
    }
    if (isset($conditions['tags'])) {
      $query->join('products_tags', 'products_tags.pid = products.pid')
          ->where('products_tags.tid IN', $conditions['tags']);
    }
    if (isset($conditions['channel'])) {
      $query->join('products_channels', 'products_channels.pid = products.pid')
          ->where('products_channels.cid', $conditions['channel']);
    }
    if (isset($conditions['groupby'])) {
      $query->groupby($conditions['groupby']);
    }
    try {
      $result = $query->query()->field();
      return $result;
    } catch ( BpfException $e ) {
      return false;
    }
  }

  /**
   * 通过商品id数组 获取商品数组
   * @return array/bool 商品数组 失败返回false
   */
  public function getProductsByIds($productIds)
  {
    $result = $this->getModel('mysql')
        ->getSqlBuilder()
        ->from('products')
        ->where('pid IN', $productIds)
        ->query()
        ->all();
    if($result){
      $result = getAssocArray($result, 'pid');
      foreach ($result as $product) {
        $product->link = urlProduct($product);
        $product->link_click = urlProduct($product, 'click');
      }
      return $result;
    } else {
      return array();
    }
  }

  /**
   * 批量设置商品图片
   * @param int $productId 商品Id
   * @param array image 商品图片信息
   * @return bool 操作成功返回true， 操作失败返回false
   */
  public function setProductImages($productId, $fileIds)
  {
    $mysqlModel = $this->getModel('mysql');
    $fileModel = $this->getModel('file');
    //进行文件的维护
    $ordFiles = array_keys($this->getProductImages($productId));
    $deleFiles = array_diff($ordFiles, $fileIds);
    $fileModel->delete('pro_img', $deleFiles);
    //进行数据库的维护
    $mysqlModel->delete('products_images', array('pid' => $productId));
    //进行数据的从新插入
    $sets = array();
    $files = getAssocArray($fileModel->getFiles('pro_img', $fileIds), 'file_id');
    $count = count($fileIds);
    foreach ($fileIds as $fileId) {
      $sets[] = array(
        'pid' => $productId,
        'file_id' => $files[$fileId]->file_id,
        'file_path' =>$files[$fileId]->file_path,
        'sort_weight' => $count--,
        'created' => $files[$fileId]->created,
      );
    }
    try {
      if ($sets) {
        $mysqlModel->insert('products_images', $sets);
      }
      return true;
    } catch (BpfException $e) {
      return false;
    }


    return $deleFiles;
  }


  /**
   * 获取商品图片
   * @param int $imageId 商品图片Id
   * @return arrry/bool 图片对象的集合， 操作失败返回false
   */
  public function getProductImages($productId)
  {
    $mysqlModel = $this->getModel('mysql');
    $result = $mysqlModel->query('SELECT file_id, file_path FROM `products_images`  WHERE `pid` = ' . $mysqlModel->escape($productId) . ' ORDER BY sort_weight DESC');
    return $result->columnWithKey('file_id', 'file_path');
  }

  /**
   * 批量设置商品频道
   * @param int $productId 商品Id
   * @param arrry $channelIds 频道id集合
   * @return bool 失败返回 false,成功返回true
   */
  public function setProductChannels($productId, $channelIds)
  {
    $mysqlModel = $this->getModel('mysql');
    $set = array();
    foreach ($channelIds as $cid) {
      array_push($set, array(
        'pid' => $productId,
        'cid' => $cid,
      ));
    }
    try {
      $mysqlModel->delete('products_channels', array('pid' => $productId));
      $mysqlModel->insert('products_channels', $set)->insertId();
      return true;
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
   * 获取商品所有频道
   * @param int $productId 商品Id
   * @return array 商品频道集合
   */
  public function getProductChannels($productId)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder()
        ->from('channels')
        ->join('products_channels','channels.cid = products_channels.cid')
        ->where('products_channels.pid', $productId);
    return $query->query()->columnWithKey('cid', 'title');
  }

  /**
   * 批量设置商品标签
   * @param int $productId 商品Id
   * @param arrry $tagIds 标签id集合
   * @return bool 失败返回 false,成功返回true
   */
  public function setProductTags($productId, $tagIds)
  {
    $mysqlModel = $this->getModel('mysql');
    $set = array();
    foreach ($tagIds as $tid) {
      array_push($set, array(
        'pid' => $productId,
        'tid' => $tid,
      ));
    }
    try {
      $mysqlModel->delete('products_tags', array('pid' => $productId));
      $mysqlModel->insert('products_tags', $set)->insertId();
      return true;
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
   * 获取商品所有标签
   * @param int $productId 商品Id
   * @return array 商品标签集合
   */
  public function getProductTags($productId)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder()
        ->from('tags')
        ->select('tags.tid, tags.title')
        ->join('products_tags','tags.tid = products_tags.tid')
        ->where('products_tags.pid', $productId);
    return $query->query()->columnWithKey('tid', 'title');
  }

  /**
   * 获取一系列商品的共同标签
   * @param array $productIds 商品Id集合
   * @return array 商品标签集合
   */
  public function getProductsTags($productIds)
  {
    $mysqlModel = $this->getModel('mysql');
    $result = $mysqlModel->getSqlBuilder()
        ->select('tags.*')
        ->from('products_tags')
        ->join('tags', 'tags.tid = products_tags.tid')
        ->distinct()
        ->where('tags.status', '1')
        ->where('pid IN', $productIds)
        ->query()
        ->all();
    foreach ($result as $tag) {
     $tag->link = urlTag($tag);
    }
    return $result;
  }

  /**
   * 采集商品
   * @param int $id 淘宝商品id
   * @return object
   */
  public function collectProduct($id, $pic = null)
  {
    if(!isset($id) || !is_numeric($id)) {
      return false;
    }
    //$this->serviceUrl
    $url = BpfConfig::get('java.url'). 'product_get.do?token=kladkiewj4389jdsadf923cvmsdksa&collect=null&item_id=' . $id;
    if (!empty($pic)) {
      $url = $url . '&pic=' . $pic;
    }
    return $this->get($url);
  }

  /**
   * 获取收藏过商品的用户
   * @param int $productId 商品Id
   * @return array 用户数组
   */
  public function getProductLikes($productId, $page = null, $limit = 15)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder()
        ->select('uid')
        ->from('users_likes')
        ->where('pid', $productId);
    if (isset($page)) {
      $query->limitPage($limit, $page);
    }
    $uids = $query->query()->column();
    if (empty($uids)) {
      return array();
    }
    return $this->getModel('user')->getUsersByIds($uids);
  }

  /**
   * 获取收藏过商品的用户的数量
   * @param int $productId 商品Id
   * @return int 用户数
   */
  public function getProductLikesCount($productId)
  {
    $mysqlModel = $this->getModel('mysql');
    return $mysqlModel->getSqlBuilder()
        ->select('COUNT(0)')
        ->from('users_likes')
        ->where('pid', $productId)
        ->query()
        ->field();
  }

  /**
   * 增加浏览次数
   * @param int $productId 商品Id
   * @return void
   */
  public function increaseView($productId)
  {
    $mysqlModel = $this->getModel('mysql');
    $mysqlModel->update('products', array(
      'views' => array(
        'escape' => false,
        'value' => '`views` + 1',
      ),
    ), array('pid' => $productId));
  }

  /**
   * 增加点击次数
   * @param int $productId 商品Id
   * @return void
   */
  public function increaseClick($productId)
  {
    $mysqlModel = $this->getModel('mysql');
    $mysqlModel->update('products', array(
      'clicks' => array(
        'escape' => false,
        'value' => '`clicks` + 1',
      ),
    ), array('pid' => $productId));
  }

  /**
   * 增加点击次数
   * @param int $productId 商品Id
   * @return void
   */
  public function increaseProductClick($productId)
  {
    $mysqlModel = $this->getModel('mysql');
    try {
      $mysqlModel->query('INSERT INTO `products_clicks` (`pid`, `counter`, `date`)
          VALUES ("' . $productId . '", "1", "' . date('Ymd', REQUEST_TIME) . '")
          ON DUPLICATE KEY UPDATE `counter` = `counter` + 1')->affected();
      return true;
    } catch (BpfException $e) {
      return false;
    }
  }
  
  /**
   * 检查商城商品是否存在，存在则返回商品数组
   * @param string $mallPid 商城Id
   * @return arrary/false
   */
  public function checkMallPid($mallPid)
  {
     $p = $this->getModel('mysql')
        ->getSqlBuilder()
        ->from('products')
        ->where('mall_pid', $mallPid)
        ->query()
        ->row();
    if ($p) {
      return $p;
    } else {
      return false;
    }
  }

  /**
   * 获取频道不到分类最新商品
   * @param string $channelIds 频道Id
   * @return arrary/false
   */
  public function getChannelProducts($channelIds)
  {
    if (!isset($channelIds)) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    try {
      $result = $mysqlModel->query('SELECT * FROM `products`  INNER JOIN
        (SELECT MAX(products.`updated`) updated, products.`cid` cid FROM `products` INNER JOIN `products_channels` ON
          products_channels.`pid` = products.`pid` WHERE products.`status` = "1" AND products_channels.`cid` = ' . $mysqlModel->escape($channelIds) . '
          GROUP BY products.`cid`) t
        ON products.`updated` = t.`updated` AND products.cid = t.cid AND products.status = "1" ORDER BY products.`updated` DESC LIMIT 4')->all();
      foreach ($result as $product) {
        $product->link = urlProduct($product);
        $product->link_click = urlProduct($product, 'click');
      }
      return $result;
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
   * 获取所有商品mall_pid
   * @param array $conditions 商品查询条件数组
   * @return array 商品数组
   */
  public function getProductsMall($conditions = null)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder()
        ->select('products.mall_pid')
        ->distinct()
        ->from('products');
    if (isset($conditions['where'])) {
      foreach ($conditions['where'] as $k => $value ) {
        $query->where('products.' . trim($k), $value);
      }
    }
    try {
      $result = $query->query()->all();
      return $result;
    } catch ( BpfException $e ) {
      return array();
    }
  }

  /**
   * 插入许愿用户对象
   * @param arrary $desire 
   * @return int/bool 许愿ID, 失败返回 false
   */
  public function insertDesire($desire)
  {
    if (!isset($desire['uid'])) {
      return false;
    }
    $mysqlModel = $this->getModel('mysql');
    $set = array(
      'uid' => trim($desire['uid']),
      'nickname' => isset($desire['nickname']) ? trim($desire['nickname']) : null,
      'telphone' => isset($desire['telphone']) ? trim($desire['telphone']) : null,
      'content' => isset($desire['content']) ? $desire['content'] : null,
      'address' => isset($desire['address']) ? $desire['address'] : null,
      'status' => isset($desire['status']) ? $desire['status'] : 0,
      'pid' => isset($desire['pid']) ? trim($desire['pid']) : 0,
      'created' => REQUEST_TIME,
    );
    try {
      $dId = $mysqlModel->insert('desires', $set)->insertId();
      return $dId;
    } catch (BpfException $e) {
      return false;
    }
  }

  /**
   * 获取许愿用户并进行分页
   * @param array $conditions 许愿查询条件数组
   * @param int $limit 分页显示数 默认20
   * @param int $page 页码 默认1
   * @return array 许愿用户数组
   */
  public function getDesires($conditions = null, $page = 1, $limit = 15)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder()
        ->select('desires.*')
        ->distinct()
        ->from('desires');
    if (isset($conditions['where'])) {
      foreach ($conditions['where'] as $k => $value ) {
        $query->where('desires.' . trim($k), $value);
      }
    }
    if (isset($conditions['orderby'])) {
      $query->orderby(trim('desires.' . $conditions['orderby']));
    } else {
      $query->orderby('desires.created DESC');
    }
    try {
      $result = $query->limitPage($limit, $page)->query()->all();
      return $result;
    } catch ( BpfException $e ) {
      return array();
    }
  }

  /**
   * 获取许愿用户总数
   * @param array $conditions 许愿查询条件数组
   * @return int/bool 符合许愿总数, 查询条件出错返回false
   */
  public function getDesiresCount($conditions = null)
  {
    $mysqlModel = $this->getModel('mysql');
    $query = $mysqlModel->getSqlBuilder()
        ->select('COUNT(0)')
        ->distinct()
        ->from('desires');
    if (isset($conditions['pid'])) {
        $query->where('pid' , $conditions['pid']);
    }
    if (isset($conditions['uid'])) {
        $query->where('uid' , $conditions['uid']);
    }
    if (isset($conditions['nickname'])) {
        $query->where('nickname LIKE', '%' . $mysqlModel->escape($conditions['nickname']) . '%');
    }
    try {
      $result = $query->query()->field();
      return $result;
    } catch ( BpfException $e ) {
      return false;
    }
  }
}
