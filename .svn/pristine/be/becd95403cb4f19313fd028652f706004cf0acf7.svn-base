<?php
class ProductController extends BpfController
{
  // 商品详细
  public function itemAction($productId = '')
  {
    if (!preg_match('/^(20[\d]{4})([a-f0-9]{8})\.html$/', $productId, $matches)) {
      return BPF_NOT_FOUND;
    }
    $month = $matches[1];
    $productId = intval(hexdec($matches[2])) - 22334456;
    $productModel = $this->getModel('product');
    if ($productId <= 0 || !$product = $productModel->getProduct($productId)) {
      return BPF_NOT_FOUND;
    }
    if (date('Ym', $product->created) != $month) {
      return BPF_NOT_FOUND;
    }
    $productModel->increaseView($productId);
    if ($product->status <= 0) {
      return BPF_NOT_FOUND;
    }
    $view = $this->getView();
    $commentModel = $this->getModel('comment');
    $userModel = $this->getModel('user');
    $cacheModel = $this->getModel('cache');

    // 左侧推荐产品 每小时变化一次
    $likeitem = $cacheModel->get('likeitem');
    if (!$likeitem) {
      $conditions = array(
        'orderby' => 'RAND()',
        'where' =>  array(
          'cid' => $product->cid,
          'status' => 1,
        ),
      );
      $likeitem = $productModel->getProducts($conditions ,1 ,8);
      $cacheModel->set('likeitem', $likeitem, 3600);
    }
    // 底部推荐产品 每二小时变化一次
    $hotPros = $cacheModel->get('hotPros');
    if (!$hotPros) {
      $hotPros = $productModel->getProducts(array('orderby' => 'RAND()', 'where'=> array('status' => 1)), 1, 3);
      $cacheModel->set('hotPros', $hotPros, 7200);
    }
    $product->zhe = $product->list_price != 0 ? (round($product->sell_price / $product->list_price, 2) * 10) : 0;
    $product->data = json_decode($product->data);
    $product->tags = $productModel->getProductTags($product->pid);
    $conditions = array(
      'pid' => $product->pid,
      'status' => 1,
    );
    $commList = $commentModel->getComments($conditions);
    foreach ($commList as $key => $sa) {
      $commList[$key]->user = $userModel->getUser($sa->uid);
      $commList[$key]->Replies = $commentModel->getReplies($sa->cid);
    }
    $product->category = $productModel->getCategory($product->cid);
    $product->likes = $productModel->getProductLikes($product->pid, 1, 10);
    $product->home_category = $productModel->getHomeCategories();


    $this->addJs('js/jquery.imagezoom.min.js'); // 产品放大镜
    $this->addJs('js/collect.js'); //商品收藏的js
    $view->assign(array(
      'product' => $product,
      'likeitem' => $likeitem,
      'commList' => $commList,
      'hotPros' => $hotPros,
    ));

    // 以下内容不需要缓存
    if (!empty($GLOBALS['user']->uid)) {
      $userId = $GLOBALS['user']->uid;
      $userModel->getUser($userId);
      $lieks = $userModel->checkLikes($userId, array($product->pid)); // 收藏
      $userCheckinRunning = $userModel->getCheckinRunning($userId); // 连续签到天数
      // 自动添加喜欢
      //$userModel->doLikes($userId, array($product->pid));
      // 浏览轨迹
      $set = array(
        'pid' => $product->pid,
        'uid' => $userId,
        'op' => 'item',
      );
      $userModel->insertUserTrajectory($set);
      $userId = str_pad(dechex($userId + 33445567), 8, '0', STR_PAD_LEFT);
      $view->assign('bduid', $userId);
    } else {
      // 分享回流记录用户使用
      $userId = str_pad(dechex(0 + 33445567), 8, '0', STR_PAD_LEFT);
      $view->assign('bduid', $userId);
    }

    // 分享回流记录用户使用
    if (!empty($_GET['u']) && !isset($_COOKIE['tid']) && $_GET['u'] != '01fe56bf') {
      $userId = $_GET['u'];
      if (!preg_match('/^([a-f0-9]{8})$/', $userId, $matches)) {
        return BPF_NOT_FOUND;
      }
      $userId = intval(hexdec($matches[1])) - 33445567;
      if ($userId <= 0 || !$user = $userModel->getUser($userId)) {
        return BPF_NOT_FOUND;
      }
      $configModel = $this->getModel('config');
      // 分享每天上限次数
      $sTop = intval($configModel->get('scores.share_top', 1)); // 每天上限
      $scores = intval($configModel->get('scores.share', 1)); // 获得积分
      if ($userId != $GLOBALS['user']->uid) {
        $sCount = $userModel->getUserSharesCount(array('uid' => $GLOBALS['user']->uid, 'date' => date('Ymd', REQUEST_TIME)));
        if ($sCount < $sTop) {
          // 写入cookie 记录每天
          if (!isset($_COOKIE['tid'])) {
            $tid = $_COOKIE['tid'] = randomString(16);
            setcookie('tid', $tid, mktime(0, 0, 0) + 86400, '/');
            $set = array(
              'uid' => $userId,
              'pid' => $product->pid,
              'tid' => $tid,
            );
            $insertId = $userModel->insertUserShares($set);
            if (isset($insertId)) {
              $userModel->updateUserScores($userId, $scores);
              $userModel->insertScoresLog($userId, 'shares', $scores, sprintf('分享成功，赚取 %d 个积分', $scores));
              $view->assign('msgShare', '1'); // 回流提示
            }
          }
        }
      }
    }
    $view->assign(array(
      'lieks' => empty($lieks) ? '1' : '0',
      'userCheckinRunning' => isset($userCheckinRunning) ? $userCheckinRunning : '',
      'userInfo' => isset($userInfo) ? $userInfo : '',
    ));
    $view->display('product/item.phtml');
  }

  public function clickAction($productId = '')
  {
    if (!preg_match('/^(20[\d]{4})([a-f0-9]{8})\.html$/', $productId, $matches)) {
      return BPF_NOT_FOUND;
    }
    $month = $matches[1];
    $productId = intval(hexdec($matches[2])) - 22334456;
    $productModel = $this->getModel('product');
    if ($productId <= 0 || !$product = $productModel->getProduct($productId)) {
      return BPF_NOT_FOUND;
    }
    if (date('Ym', $product->created) != $month) {
      return BPF_NOT_FOUND;
    }
    //缓存点击数量
    $cache = $this->getModel('cache');
    $keys = $cache->get('item_clicks');
    if (empty($keys)) {
      $cache->set('item_clicks', 1);
    } else {
      $cache->set('item_clicks', $keys + 1);
    }
    $productModel->increaseClick($productId);
    $productModel->increaseProductClick($productId);
    // 保存用户点击触发数据，临时方案
    if(isLogin()) {
      $userModel = $this->getModel('user');
      global $user;
      $clicks = array(
        'uid' => $user->uid,
        'pid' => $productId,
        'created' => REQUEST_TIME,
        'date' => date('Y-m-d H:i:s', REQUEST_TIME) ,
      );
      $userModel->insertUserClicks($clicks);
    }
    gotoUrl($product->url, 301);
  }

  // 邮件点击缓存统计
  public function itemClickAction($productId = '')
  {
    if (!preg_match('/^(20[\d]{4})([a-f0-9]{8})\.html$/', $productId, $matches)) {
      return BPF_NOT_FOUND;
    }
    $month = $matches[1];
    $productId = intval(hexdec($matches[2])) - 22334456;
    $productModel = $this->getModel('product');
    if ($productId <= 0 || !$product = $productModel->getProduct($productId)) {
      return BPF_NOT_FOUND;
    }
    if (date('Ym', $product->created) != $month) {
      return BPF_NOT_FOUND;
    }
    // 缓存点击数量
    $cache = $this->getModel('cache');
    $keys = $cache->get('item_' . $product->pid);
    if (empty($keys)) {
      $cache->set('item_' . $product->pid, 1);
    } else {
      $cache->set('item_' . $product->pid, $keys + 1);
    }
    gotoUrl($product->link, 301);
  }

  // 商品评论
  public function commentAction()
  {
    return;
    if (!isLogin()) {
      return BPF_NOT_FOUND;
    }
    if ($this->isPost()) {
      $uid = $_POST['luid'];
      $pid = $_POST['lpid'];
      $body = $_POST['emotion'];
      if ($GLOBALS['user']->uid != $uid){
        return BPF_NOT_FOUND;
      }
      $userModel = $this->getModel('user');
      $productModel = $this->getModel('product');
      $view = $this->getView();
      $uid = $userModel->getUserInfo($uid);
      $pid = $productModel->getProduct($pid);

      if (!isset($uid->uid) || !isset($pid->pid) || empty($body)) {
        return BPF_NOT_FOUND;
      }
      // $view->caching = true;
      // 清空缓存
      // if ($view->isCached('product/item.phtml', md5($pid->pid))) {
      //   $view->clearCache('product/item.phtml', md5($pid->pid));
      // }
      $comment = array(
        'uid' => $uid->uid,
        'pid' => $pid->pid,
        'body' => $body,
        'status' => 1,
      );
      $commentModel = $this->getModel('comment');
      $cid = $commentModel->insertComment($comment);
      if ($cid) {
        // 增加积分
        $configModel = $this->getModel('config');
        $scores = $configModel->get('scores.comment', 1);
        $scoresTop = $configModel->get('scores.comment_top', 5);
        try {
          $userId = $uid->uid;
          // 获取当前评论次数
          $scoresCount = $scores * ($commentModel->getCommentsCount(array(
            'uid' => $userId,
            'today' => true,
          )) - 1);
          if ($scoresCount < $scoresTop) {
            // 不超过当天评论总分
            $scores = min($scoresTop - $scoresCount, $scores);
            $userModel = $this->getModel('user');
            if ($userModel->updateUserScores($userId, $scores)) {
              $userModel->insertScoresLog($userId, 'comment', $scores, sprintf('评论成功，赚取 %d 个积分', $scores), $cid);
            }
          } else {
            $scores = 0;
          }
        } catch (Exception $e) {
          // 忽略错误
        }

        $data = array(
          'body' => htmlspecialchars($body),
          'success' => '评论成功' . ($scores ? ('，赚取 ' . $scores . ' 个积分') : ''),
          'time' => date('Y-m-d H:i:s', REQUEST_TIME),
        );
        echo json_encode($data);
      } else {
        echo false;
      }
    } else {
      return BPF_NOT_FOUND;
    }
  }

  // 商品搜索
  public function searchAction()
  {
    $rows = 24;//每页显示的数量
    if (isset($_GET['page'])) {
      $page = $_GET['page'];
    } else {
      $page = 1;
    }
    if (isset($_GET['keyword']) && trim($_GET['keyword'])!='') {
      $keyword = $_GET['keyword'];

    } else {
      gotoUrl('');
    }
    $productModel = $this->getModel('product');
    $adModel = $this->getModel('ad');
    $userModel = $this->getModel('user');
    $count = $productModel->searchProductsCount($keyword);
    $products = $productModel->searchProducts($keyword, $page, $rows);
    foreach ($products as $product) {
      $product->zhe = $product->list_price != 0 ? (round($product->sell_price / $product->list_price, 2) * 10) : 0;
    }
    if (isLogin()) {//用户登录后找到自己的收藏。
      $likes = getAssocArray($userModel->getUserLikes($GLOBALS['user']->uid), 'pid');//获取用户的收藏
    }
    //类目
    $cates = $productModel->getHomeCategories();
    //ads product(hot product)
    $hotPros = $productModel->getProducts(array('orderby' => 'views DESC','where' => array('status' => 1)), 1, 4);
    $view = $this->getView();
    $this->addJs('js/collect.js'); //商品收藏的js
    $this->addJs('js/plugins/lazyload/jquery.lazyload.min.js');//图片延迟加载js插件
    $this->addJs('js/img_load.js');//图片延迟加载js
    $view->assign('page', $page);
    $view->assign('rows', $rows);
    $view->assign('count', $count);
    $view->assign('products', $products);
    $view->assign('keyword', $keyword);
    $view->assign('hotPros', $hotPros);
    $view->assign('cates', $cates);
    $view->assign('likes', isset($likes) ? $likes : '');
    $view->display('product/search.phtml');
  }

  // 类目商品列表
  public function categoryAction($categoryId = null)
  {
    $productModel = $this->getModel('product');
    if (preg_match('/^([\w-]+)\.html$/', $categoryId, $matches)) {
      // 通过 seo_path
      $categoryId = $productModel->checkCategoryPathIsExists($matches[1]);
      if (!$categoryId) {
        return BPF_NOT_FOUND;
      }
    } else if (!preg_match('/^\d+$/', $categoryId)) {
      return BPF_NOT_FOUND;
    }
    $category = $productModel->getCategory($categoryId);
    if (!$category || $category->status==0) {
      return BPF_NOT_FOUND;
    }
    $view = $this->getView();
    $view->caching = true;
    $cacheKey = md5('category' . $categoryId);
    if (!$view->isCached('product/category.phtml', $cacheKey)) {
      //该分类的子分类
      $childCates = $productModel->getCategories($categoryId, 1);
      //底部产品推荐
      $hotPros = $productModel->getProducts(array('orderby' => 'views DESC','where'=> array('status' => 1)), 1, 4);
      //右边热卖推荐
      $clickPros = $productModel->getProducts(array('orderby' => 'clicks DESC','where'=> array('status' => 1, 'cid' => $categoryId,)), 1, 12);
      //类目
      $cates = $productModel->getHomeCategories();
      $this->addJs('js/collect.js'); //商品收藏的js
      $this->addJs('js/plugins/lazyload/jquery.lazyload.min.js');//图片延迟加载js插件
      $view->assign(array(
        'category' => $category,
        'hotPros' => $hotPros,
        'clickPros' => $clickPros,
        'childCates' => $childCates,
        'cates' => $cates,
        'categoryId' => $categoryId,
      ));
    }
    $view->display('product/category.phtml', $cacheKey);
  }

  // 类目商品load
  public function postCateAction($categoryId = null, $page = 1, $sort = 'new')
  {
    $productModel = $this->getModel('product');
    if (preg_match('/^([\w-]+)\.html$/', $categoryId, $matches)) {
      // 通过 seo_path
      $categoryId = $productModel->checkCategoryPathIsExists($matches[1]);
      if (!$categoryId) {
        return BPF_NOT_FOUND;
      }
    } else if (!preg_match('/^\d+$/', $categoryId)) {
      return BPF_NOT_FOUND;
    }
    $category = $productModel->getCategory($categoryId);
    if (!$category || $category->status==0) {
      return BPF_NOT_FOUND;
    }
    if (!is_numeric($page)) {
      return BPF_NOT_FOUND;
    }
    $view = $this->getView();
    $view->caching = true;
    $cacheKey = md5('postcate' . $categoryId * $page . $sort);
    if (!$view->isCached('product/postcate.phtml', $cacheKey)) {
      $limit = 40;  // 每页商品数
      //该分类的子分类
      $childCates = $productModel->getCategories($categoryId, 1);
      $childCatesCid = array();
      $childCatesCid[] = $categoryId;
      foreach ($childCates as $key => $value) {
        $childCatesCid[] = $value->cid;
      }
      // 排序规则
      if ($sort == 'new') {
        $sort = '`updated` DESC';
      } else if ($sort == 'hot') {
        $sort = '`weight` DESC,`sellcount` DESC,`clicks` DESC';
      } else {
        $sort = '`updated` DESC';
      }
      $conditions = array(
        'orderby' => $sort,
        'where' =>  array(
          'cid IN' => $childCatesCid,
          'status' => 1,
        ),
      );
      $products = $productModel->getProducts($conditions, $page, $limit);
      foreach ($products as $product) {
        if (isset(json_decode($product->data)->type)) {
          $product->data = json_decode($product->data)->type;
        }
        $product->user = $productModel->getProductLikes($product->pid, null, 1);
        if (isset($product->user)) {
          $product->user = reset($product->user);
        }
        $product->zhe = $product->list_price != 0 ? (round($product->sell_price / $product->list_price, 2) * 10) : 0;
      }
      $count = $productModel->getProductsCount(array('where' => array('cid IN' => $childCatesCid, 'status' => 1)));
      //用户登录后找到自己的收藏
      // if (isLogin()) {
      //   $userModel = $this->getModel('user');
      //   $likes = getAssocArray($userModel->getUserLikes($GLOBALS['user']->uid), 'pid');//获取用户的收藏
      // }
      $view->assign(array(
        'category' => $category,
        'products' => $products,
        'page' => $page,
        'rows' => $limit,
        'count' => $count,
        'categoryId' => $categoryId,
      ));
    }
    $view->display('product/postcate.phtml', $cacheKey);
  }

  // 频道商品列表
  public function channelAction($channelId = null)
  {
    $channelModel = $this->getModel('channel');
    if (preg_match('/^([\w-]+)\.html$/', $channelId, $matches)) {
      // 通过 seo_path
      $channelId = $channelModel->checkChannelPathIsExists($matches[1]);

      if (!$channelId) {
        return BPF_NOT_FOUND;
      }
    } else if (!preg_match('/^\d+$/', $channelId)) {
      return BPF_NOT_FOUND;
    }
    $channel = $channelModel->getChannel($channelId);
    if (!$channel || $channel->status == 0) {
      return BPF_NOT_FOUND;
    }
    //一级分类类目
    $productModel = $this->getModel('product');
    $cates = $productModel->getTopCategories();
    $view = $this->getView();
    $view->assign(array(
      'channel' => $channel,
      'channelId' => $channelId,
      'cates' => $cates,
    ));
    $this->addJs('js/plugins/lazyload/jquery.lazyload.min.js'); //图片延迟加载js插件
    $this->addJs('js/img_load.js'); //图片延迟加载js
    // 许愿专属效果
    if (isset($channel->template) && $channel->template == 'desire') {
      global $user;
      $productModel = $this->getModel('product');
      $userModel = $this->getModel('user');
      $userAuth = $productModel->getDesiresCount(array('uid' => $user->uid));
      if ($this->isPost() && isLogin()) {
        // 存在则已经许过愿望
        if (isset($userAuth) && $userAuth >= 1) {
          return json_encode(array('result' => -2));
        }
        if (!empty($_POST['pid']) && !empty($_POST['code']) && !empty($_POST['nickname']) && !empty($_POST['telphone']) && !empty($_POST['content']) && !empty($_POST['address'])) {
          $productId = $_POST['pid'];
          // if (!preg_match('/^(20[\d]{4})([a-f0-9]{8})$/', $productId, $matches)) {
          //   return BPF_NOT_FOUND;
          // }
          // $month = $matches[1];
          // $productId = intval(hexdec($matches[2])) - 22334456;
          // if ($productId <= 0 || !$product = $productModel->getProduct($productId)) {
          //   return BPF_NOT_FOUND;
          // }
          $set = array(
            'uid' => $user->uid,
            'nickname' => isset($_POST['nickname']) ? $_POST['nickname'] : null,
            'telphone' => isset($_POST['telphone']) ? $_POST['telphone'] : null,
            'content' => isset($_POST['content']) ? $_POST['content'] : null,
            'address' => isset($_POST['address']) ? $_POST['address'] : null,
            // 'pid' => isset($productId) ? $productId : 0,
            'pid' => $productId,
          );
          $dIds = $productModel->insertDesire($set);
          if ($dIds) {
            $data = array('result' => 1);
          }
        } else {
          $data = array('result' => -1);
        }
        return json_encode($data);
      }
      // 存在则已经许过愿望
      if (isset($userAuth) && $userAuth >= 1) {
        $auth = 1;
      }
      $rows = 15;
      $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
      // $conditions = array( //过滤条件
      //   'where' =>  array(
      //     'status' => 1,
      //   ),
      //   // 'orderby' => '`weight` DESC,`sellcount` DESC',
      // );
      // $productList = $productModel->getProducts($conditions, 1, 12);
      $productList = array(
        (object)array('pids' => 1),
        (object)array('pids' => 2),
        (object)array('pids' => 3),
        (object)array('pids' => 4),
        (object)array('pids' => 5),
        (object)array('pids' => 6),
        (object)array('pids' => 7),
        (object)array('pids' => 8),
        (object)array('pids' => 9),
        (object)array('pids' => 10),
        (object)array('pids' => 11),
        (object)array('pids' => 12),
      );
      foreach ($productList as $key => $value) {
        $conditions = array(
          'pid' => $value->pids,
        );
        $value->count = $productModel->getDesiresCount($conditions);
        // $value->pids = date('Ym', $value->created) . str_pad(dechex($value->pid + 22334456), 8, '0', STR_PAD_LEFT);
      }
      // 用户许愿列表
      $conditions = array();
      $count = $productModel->getDesiresCount($conditions);
      $desireList = $productModel->getDesires($conditions, $page, $rows);
      foreach ($desireList as $key => $value) {
        $value->user = $userModel->getUserInfo($value->uid);
      }
      $view->assign('auth', isset($auth) ? $auth : '');
      $view->assign('count', $count);
      $view->assign('rows', $rows);
      $view->assign('page', $page);
      $view->assign('desireList', $desireList);
      $view->assign('productList', $productList);
    }

    // 抽奖专属效果
    if (isset($channel->template) && $channel->template == 'choujiang') {
      global $user;
      $uid = $user->uid;
      $userModel = $this->getModel('user');
      $conditions = array();
      $conditions['where']['uid'] = $user->uid;
      $conditions['where']['pzid <'] = 6;
      $userPrizeLogs = $userModel->getPrizeLogs($conditions);
      unset($conditions['where']['uid']);
      $allUserPrizeLogs = $userModel->getPrizeLogs($conditions,1,1);
      foreach ($allUserPrizeLogs as $key => $log) {
        $log->user = $userModel->getUser($log->uid);
      }
      unset($allUserPrizeLogs[0]);
      // $allUserPrizeLogs[] = (object)array(
      // 'user' => (object)array('nickname' => '阿森纳'),
      // 'body' =>  '中了50积分',
      // );
      $allUserPrizeLogs[] = (object)array(
      'user' => (object)array('nickname' => '惊风落叶'),
      'body' =>  '中了港澳游大奖',
      );
      $allUserPrizeLogs[] = (object)array(
      'user' => (object)array('nickname' => '请叫我魔爷'),
      'body' =>  '中了港澳游大奖',
      );
      $allUserPrizeLogs[] = (object)array(
      'user' => (object)array('nickname' => '黑阔'),
      'body' =>  '中了港澳游大奖',
      );
      $allUserPrizeLogs[] = (object)array(
      'user' => (object)array('nickname' => '爱心觉罗'),
      'body' =>  '中了港澳游大奖',
      );
      $allUserPrizeLogs[] = (object)array(
      'user' => (object)array('nickname' => 'LIPEITING'),
      'body' =>  '中了港澳游大奖',
      );
      $allUserPrizeLogs[] = (object)array(
      'user' => (object)array('nickname' => '超越神的存在'),
      'body' =>  '中了港澳游大奖',
      );
      $allUserPrizeLogs[] = (object)array(
      'user' => (object)array('nickname' => 'chennishi'),
      'body' =>  '中了港澳游大奖',
      );
      $allUserPrizeLogs[] = (object)array(
      'user' => (object)array('nickname' => '土豪哥'),
      'body' =>  '中了港澳游大奖',
      );
      $allUserPrizeLogs[] = (object)array(
      'user' => (object)array('nickname' => '小参'),
      'body' =>  '中了港澳游大奖',
      );
      $allUserPrizeLogs[] = (object)array(
      'user' => (object)array('nickname' => 'change。'),
      'body' =>  '中了港澳游大奖',
      );
      $allUserPrizeLogs[] = (object)array(
      'user' => (object)array('nickname' => 'chemin'),
      'body' =>  '中了港澳游大奖',
      );
      $allUserPrizeLogs[] = (object)array(
      'user' => (object)array('nickname' => '＆ 停。'),
      'body' =>  '中了港澳游大奖',
      );
      $allUserPrizeLogs[] = (object)array(
      'user' => (object)array('nickname' => '大鹏'),
      'body' =>  '中了港澳游大奖',
      );
      $allUserPrizeLogs[] = (object)array(
      'user' => (object)array('nickname' => '粉粉'),
      'body' =>  '中了港澳游大奖',
      );
      $allUserPrizeLogs[] = (object)array(
      'user' => (object)array('nickname' => 'huangaiyue'),
      'body' =>  '中了港澳游大奖',
      );
      $allUserPrizeLogs[] = (object)array(
      'user' => (object)array('nickname' => '冰山一角'),
      'body' =>  '中了港澳游大奖',
      );
      $allUserPrizeLogs[] = (object)array(
      'user' => (object)array('nickname' => '艾心青'),
      'body' =>  '中了港澳游大奖',
      );
      $allUserPrizeLogs[] = (object)array(
      'user' => (object)array('nickname' => '纯黒ЮSè'),
      'body' =>  '中了港澳游大奖',
      );
      $allUserPrizeLogs[] = (object)array(
      'user' => (object)array('nickname' => 'DoubleFook'),
      'body' =>  '中了港泰游大奖',
      );
      $allUserPrizeLogs[] = (object)array(
      'user' => (object)array('nickname' => '阿仙奴'),
      'body' =>  '中了港泰游大奖',
      );
      $allUserPrizeLogs = array_reverse($allUserPrizeLogs);
      // shuffle($allUserPrizeLogs);
     foreach ($userPrizeLogs as $key => $log) {
        $log->user = $userModel->getUser($log->uid);
      }
      $view->assign('list', isset($allUserPrizeLogs) ? $allUserPrizeLogs : '');
      $view->assign('userList', $userPrizeLogs);
      $view->assign('scores', isset($userModel->getUser($uid)->scores) ? $userModel->getUser($uid)->scores : 0);
      $view->assign('once', empty($userModel->getUserPriceLog($uid, null)) ? 0 : 1);
    }
    // 检测是否设置指定模板
    if (!empty($channel->template)) {
      $view->display('topic/' . $channel->template . '.phtml');
    } else {
      $view->display('product/channel.phtml');
    }
  }

  // 频道商品列表load
  public function postChannelAction($channelId = null, $page = 1, $sort = 'new', $categoryId = null)
  {
    $channelModel = $this->getModel('channel');
    $productModel = $this->getModel('product');
    if (preg_match('/^([\w-]+)\.html$/', $channelId, $matches)) {
      // 通过 seo_path
      $channelId = $channelModel->checkChannelPathIsExists($matches[1]);
      if (!$channelId) {
        return BPF_NOT_FOUND;
      }
    } else if (!preg_match('/^\d+$/', $channelId)) {
      return BPF_NOT_FOUND;
    }
    $channel = $channelModel->getChannel($channelId);
    if (!$channel || $channel->status == 0) {
      return BPF_NOT_FOUND;
    }
    // 检测分类是否有效
    if (!empty($categoryId)) {
      $category = $productModel->getCategory($categoryId);
      if (!$category || $category->status == 0) {
        return BPF_NOT_FOUND;
      }
      $childCates = $productModel->getCategories($categoryId, 1);
      //该分类的子分类
      $childCatesCid = array();
      $childCatesCid[] = $categoryId;
      if (!empty($childCates)) {
        foreach ($childCates as $key => $value) {
          $childCatesCid[] = $value->cid;
        }
      }
    }

    $view = $this->getView();
    $view->caching = true;
    $cacheKey = md5('postchannel' . $channelId * $page . $sort . $categoryId);
    if (!$view->isCached('product/postchannel.phtml', $cacheKey)) {
      $limit = 40;  // 每页商品数
      $productModel = $this->getModel('product');
      // 排序规则
      if ($sort == 'new') {
        $sort = '`updated` DESC';
      } else if ($sort == 'hot') {
        $sort = '`sellcount` DESC, `clicks` DESC';
      } else {
        $sort = '`updated` DESC';
      }
      $conditions = array(
        'orderby' => $sort,
        'channel' => $channelId,
        'where' =>  array(
          'status' => 1,
        ),
      );
      // 传入分类
      if (isset($childCatesCid) && is_array($childCatesCid)) {
        $conditions['where']['cid IN'] = $childCatesCid;
      }
      $products = $productModel->getProducts($conditions, $page, $limit);
      foreach ($products as $product) {
        if (isset(json_decode($product->data)->type)) {
          $product->data = json_decode($product->data)->type;
        }
        $product->user = $productModel->getProductLikes($product->pid, null, 1);
        if (isset($product->user)) {
          $product->user = reset($product->user);
        }
        $product->zhe = $product->list_price != 0 ? (round($product->sell_price / $product->list_price, 2) * 10) : 0;
      }
      $count = $productModel->getProductsCount($conditions);

      $view = $this->getView();
      $this->addJs('js/plugins/lazyload/jquery.lazyload.min.js');//图片延迟加载js插件
      $this->addJs('js/img_load.js');//图片延迟加载js
      $view->assign(array(
        'channelId' => $channelId,
        'products' => $products,
        'page' => $page,
        'rows' => $limit,
        'count' => $count,
      ));
    }
    $view->display('product/postchannel.phtml', $cacheKey);
  }

  // 频道页瀑布流ajax接口
  // public function topicAction($channelId = 0, $page = 1, $limit = 24)
  // {
  //   $userId = $GLOBALS['user']->uid;
  //   $today = date('Ymd', REQUEST_TIME);
  //   $checkinCode = substr(md5($today . $userId), 10, 6);
  //   if (is_numeric($channelId) && is_numeric($page) && is_numeric($limit) && $this->isPost()) {
  //     if (isset($_POST['code']) && $_POST['code'] == $checkinCode) {
  //       $productModel = $this->getModel('product');
  //       $products = $productModel->getProducts(array('channel' => $channelId, 'where'=> array('status' =>1), 'orderby' => '`views` DESC'), $page, $limit);
  //       if (empty($products)) {
  //         return json_encode(array(
  //           'result' => -1,
  //         ));
  //       }
  //       foreach ($products as $key => $value) {
  //         $value->image_path = urlStatic($value->image_path, 286, 286);
  //         list($yuan, $fen) = explode('.', $value->sell_price);
  //         $output = '<em><b>¥</b>' . $yuan;
  //         if (isset($fen) && intval($fen)) {
  //           $output .= '<i>.' . preg_replace('/0$/', '', $fen) . '</i>';
  //         }
  //         $output .= '</em>';
  //         $value->sell_price = $output;
  //       }
  //       return json_encode(array(
  //         'result' => $products,
  //       ));
  //     } else {
  //       return json_encode(array(
  //         'result' => -1,
  //       ));
  //     }
  //   } else {
  //     return BPF_NOT_FOUND;
  //   }
  // }
// 无线端页瀑布流ajax接口
  public function mobileAction($channelId = 0, $page = 1, $limit = 24)
  {
    $view = $this->getView();
    $view->caching = true;
    $view->cache_lifetime = 7200;
    $userId = $GLOBALS['user']->uid;
    $productModel = $this->getModel('product');
    $rows = 40;
    if (isset($_GET['page'])) {
    $page = $_GET['page'];
    } else {
      $page = 1;
    }
    $cacheKey = md5('mobile_index' . $page);

      // 干扰前2页排序规则
    if ($page == 1) {
      $count = $rows * 5;
      // 单品月销量500以上
      $conditions = array(
        'where' => array(
        'status' => 1,
        'weight >=' => 1901,
        'weight <=' => 3000,
        ),
        'orderby' => '`weight` DESC,`sellcount` DESC',
      );
    $products = $productModel->getProducts($conditions, 1, 8);
        // 新品区 让顾客看到新鲜商品
    $conditions = array(
      'where' => array(
      'status' => 1,
      'weight >=' => 1400,
      'weight <=' => 1900,
      ),
      'orderby' => '`sellcount` DESC,`weight` DESC',
    );
    $newProducts = $productModel->getProducts($conditions, 1, 24);
        // 爆款区，按照收入及订单数较高的前60商品
    $conditions = array(
      'where' => array(
      'status' => 1,
      'weight >=' => 702,
      'weight <=' => 899,
      ),
      'orderby' => '`income` DESC,`order_count` DESC,`sellcount` DESC',
    );
    $hotProducts = $productModel->getProducts($conditions, 1, 24);
    $products = array_merge($products, $newProducts, $hotProducts);
      if (count($products) < 56) {
        $conditions = array(
          'where' => array(
          'status' => 1,
          ),
          'orderby' => '`updated` DESC',
        );
    $pw = 56 - count($products);
    $pwProducts = $productModel->getProducts($conditions, 1, $pw);
    $products = array_merge($products, $pwProducts);
      }
    } else if ($page == 2) {
      $count = $rows * 5;
        // 新品区 让顾客看到新鲜商品
      $conditions = array(
        'where' => array(
        'status' => 1,
        'weight >=' => 900,
        'weight <=' => 1000,
        ),
        'orderby' => '`weight` DESC',
      );
      $newProducts = $productModel->getProducts($conditions, 1, 40);
        // 爆款区，按照收入及订单数较高的前60商品
      $conditions = array(
        'where' => array(
        'status' => 1,
        'weight >=' => 2,
        'weight <=' => 262,
        ),
        'orderby' => '`income` DESC,`order_count` DESC,`sellcount` DESC',
      );
      $hotProducts = $productModel->getProducts($conditions, 1, 16);
      $products = array_merge($newProducts, $hotProducts);
        if (count($products) < 56) {
          $conditions = array(
            'where' => array(
              'status' => 1,
            ),
            'orderby' => '`weight` DESC',
          );
       $pw = 56 - count($products);
          $pwProducts = $productModel->getProducts($conditions, 1, $pw);
          $products = array_merge($products, $pwProducts);
        }
      } else {
        //all product info
        $conditions = array( //过滤条件
          'where' =>  array(
            'status' => 1,
            'weight <' => 899,
            'weight >' => 262,
          ),
          'orderby' => '`weight` DESC,`sellcount` DESC',
        );
        $count = $productModel->getProductsCount($conditions);
        $products = $productModel->getProducts($conditions, $page, $rows);
      }
      foreach ($products as $product) {
        if (isset(json_decode($product->data)->type)) {
          $product->data = json_decode($product->data)->type;
        }
        $product->user = $productModel->getProductLikes($product->pid, null, 1);
        if (isset($product->user)) {
          $product->user = reset($product->user);
        }
        $product->zhe = $product->list_price != 0 ? (round($product->sell_price / $product->list_price, 2) * 10) : 0;
      }
      $view->assign('products', $products);
    $view->display('main_product.phtml', $cacheKey);





  }

  // 标签商品列表
  public function tagAction($tagId = null)
  {
    if (!preg_match('/^\d+$/', $tagId)) {
      return BPF_NOT_FOUND;
    }
    $tagModel = $this->getModel('tag');
    $tag = $tagModel->getTag($tagId);

    if (!$tag||$tag->status==0) {
      return BPF_NOT_FOUND;
    }
    if (isset($_GET['page'])) {
      $page = $_GET['page'];
    } else {
      $page = 1;  // 当前页码
    }
    $limit = 24;  // 每页商品数
    $productModel = $this->getModel('product');
    $conditions = array(
      'tags' => array($tagId),
      'orderby' => '`updated` DESC',
      'where' => array(
        'status' => 1,
      ),
    );
    $products = $productModel->getProducts($conditions, $page, $limit);
    foreach ($products as $product) {
      $product->zhe = $product->list_price != 0 ? (round($product->sell_price / $product->list_price, 2) * 10) : 0;
    }
    $count = $productModel->getProductsCount($conditions);
    //类目
    $cates = $productModel->getHomeCategories();
    $view = $this->getView();
    $this->addJs('js/collect.js'); //商品收藏的js
    $this->addJs('js/plugins/lazyload/jquery.lazyload.min.js');//图片延迟加载js插件
    $this->addJs('js/img_load.js');//图片延迟加载js
    $view->assign(array(
      'tag' => $tag,
      'products' => $products,
      'page' => $page,
      'rows' => $limit,
      'count' => $count,
      'cates' => $cates,
    ));
    $view->display('product/tag.phtml');
  }

  // 抽奖
  public function callbackAction()
  {
    return BPF_NOT_FOUND;
      if (isLogin()) {
      global $user;
      $uid = $user->uid;
      $userModel = $this->getModel('user');
      $user = $userModel->getUser($uid);
     if ($user->scores < 10 && !empty($userModel->getUserPriceLog($uid, null))) {
       return '{"result":"6","scores":"'.$userModel->getUser($uid)->scores.'"}';
      }else{

      //消耗积分
      if($user->scores >= 10 && !empty($userModel->getUserPriceLog($uid, null)))
      {
        $userModel->updateUserScores($uid, -10);
        $user->scores = $user->scores -10;
        $userModel->insertScoresLog($user->uid, 'prize', -10 , '抽奖活动，消费 ' . 10 . ' 个积分');
      }
      //运行算法 的出得奖结果
      $p = $this->_prizeResult();
      $json = json_decode($p->data,true);
      if($json['status']==1)
      {
      $userModel->updatePrizeCount($p->pzid,-1);
      }
      $log = array(
            'uid' =>  $uid,
            'pzid' =>   isset($p->pzid) ? $p->pzid : '0',
            'created' => REQUEST_TIME,
            'body' => isset($json['content']) ? $json['content']: '',
          );
       $userModel->insertPrizeLogs($log);
       $userModel->updateUserScores($uid, $json['scores']);
       if($json['scores'] > 0)
       {
        $userModel->insertScoresLog($user->uid, 'prize', $json['scores'] , '抽奖活动，赚取 ' . $json['scores'] . ' 个积分');
       }
       switch ($p->pzid) {
         case '3':
          if(mt_rand(1, 2) == 1)
            $locate = 5;
          else
            $locate = 11;
           break;
         case '4':
         $l = mt_rand(1, 3);
           if($l == 1)
            $locate = 3;
          else if($l == 2)
            $locate = 7;
          else
            $locate = 10;
           break;
        case '5':
           if(mt_rand(1, 2) == 1)
            $locate = 2;
          else
            $locate = 8;
           break;
        case '6':
           if(mt_rand(1, 2) == 1)
            $locate = 6;
          else
            $locate = 12;
           break;

         default:
          $locate = 6;
           break;
       }

      }
      return '{"result":"'.$locate.'","scores":"'.$userModel->getUser($uid)->scores.'"}';
      }
  }

  //抽奖结果
  private function _prizeResult() 
  {
    $userModel = $this->getModel('user');
    $prizes = $userModel->getPrizes();
    $total = 0;
    foreach ($prizes as $key => $p) {
      $total += $p->weight;
    }
    $v = mt_rand(0, $total - 1);
    foreach ($prizes as $key => $p) {
      if ($v < $p->weight) {
        // 剩余权重在当前权重内, 中奖
        return $p;
      } else {
        // 重新计算剩余权重
        $v -= $p->weight;
      }
    }
    return false;
  }
}