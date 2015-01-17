<?php
class DefaultController extends BpfController
{
  public function indexAction()
  {
    $view = $this->getView();
    $view->caching = true;
    $view->cache_lifetime = 7200;
    if (isset($_GET['page'])) {
      $page = $_GET['page'];
    } else {
      $page = 1;
    }
    $cacheKey = md5('index' . $page);
    // 缓存2个小时
    if (!$view->isCached('index.phtml', $cacheKey)) {
      $rows = 40;//每页显示的数量
      if(isMobile()){
      $rows = 10;//每页显示的数量
      }
      $productModel = $this->getModel('product');
      $adModel = $this->getModel('ad');
      $userModel = $this->getModel('user');
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
      // 每小时随机推荐产品
      $hotPros = $productModel->getProducts(array('orderby' => 'RAND()','where' => array('status' => 1)), 1, 4);
      // 专题
      $productJiu = $productModel->getChannelProducts(92);
      foreach ($productJiu as $product) {
        if (isset(json_decode($product->data)->type)) {
          $product->data = json_decode($product->data)->type;
        }
        $product->user = $productModel->getProductLikes($product->pid, null, 1);
        if (isset($product->user)) {
          $product->user = reset($product->user);
        }
        $product->zhe = $product->list_price != 0 ? (round($product->sell_price / $product->list_price, 2) * 10) : 0;
      }
      $productEr = $productModel->getChannelProducts(90);
      foreach ($productEr as $product) {
        if (isset(json_decode($product->data)->type)) {
          $product->data = json_decode($product->data)->type;
        }
        $product->user = $productModel->getProductLikes($product->pid, null, 1);
        if (isset($product->user)) {
          $product->user = reset($product->user);
        }
        $product->zhe = $product->list_price != 0 ? (round($product->sell_price / $product->list_price, 2) * 10) : 0;
      }
      //类目
      $cates = $productModel->getHomeCategories();
      
      $view->assign('index', 'index'); // 临时使用
      $this->addJs('js/collect.js'); //商品收藏的js
      $this->addJs('js/plugins/lazyload/jquery.lazyload.min.js');//图片延迟加载js插件
      $this->addJs('js/img_load.js');//图片延迟加载js
      $view->assign('page', $page);
      $view->assign('rows', $rows);
      $view->assign('count', $count);
      $view->assign('products', $products);
      $view->assign('hotPros', $hotPros);
      $view->assign('cates', $cates);
      $view->assign('productJiu', $productJiu);
      $view->assign('productEr', $productEr);
      $view->assign('likes', isset($likes) ? $likes : '');
    }
    if(isMobile() && (url('',true) == 'http://www.ttgg.com' || url('',true) == 'http://ttgg.com')){
      header('Location: http://m.ttgg.com');
    }
    $view->display('index.phtml', $cacheKey);
    
  }

  public function adclickAction()
  {
    if (empty($_GET['token']) || !($token = base64_decode($_GET['token'])) || !strpos($token, '-')) {
      return BPF_NOT_FOUND;
    }
    list($adId, $socketId) = explode('-', $token, 2);
    $adModel = $this->getModel('ad');
    $ad = $adModel->getAd(intval($adId));
    if (!$ad) {
      return BPF_NOT_FOUND;
    }
    if (!isset($_COOKIE['tid'])) {
      $tid = $_COOKIE['tid'] = randomString(16);
      setcookie('tid', $tid, mktime(0, 0, 0) + 86400, '/');
    } else {
      $tid = $_COOKIE['tid'];
    }
    $adModel->clickAd($ad->aid, $socketId, $tid);
    gotoUrl($ad->url);
  }

  //网站地图sitemap.xml
  public function sitemapAction($type = null, $page = null)
  {
    $sitemapxmlModel = $this->getModel('sitemapxml');
    if (!isset($type)) {
      $xml = $sitemapxmlModel->getXML1();
    } else {
      $xml = $sitemapxmlModel->getXML2($type, $page);
    }
    if (!$xml) {
      return BPF_NOT_FOUND;
    } else {
      echo $xml;
    }
  }

  //网站RSS
  public function rssAction()
  {
    $rssModel = $this->getModel('rss');
    $productModel = $this->getModel('product');
    $conditions = array(
      'where' => array(
        'status' => 1,
      ),
    );
    $products = $productModel->getProducts($conditions);
    foreach ($products as $key => $sa) {
      $rssModel->AddItem($sa->title, $sa->link, $sa->feature, date('Y-m-d H:i:s', $sa->created));
    }
    echo $rssModel->Display();
  }

  // 报表数据入库小程序
  public function statisticCLI()
  {
    $systemModel = $this->getModel('system');
    if ($systemModel->insertStatistics(array('time' => REQUEST_TIME - 86400))) {
      echo 'Success';
    } else {
      echo 'Failure';
    }
  }

  // 广告旧数据清理
  public function adsCLI()
  {
    $mysqlModel = $this->getModel('mysql');
    $date = date('Ymd', REQUEST_TIME - 86400 * 7);
    $mysqlModel->delete('ads_clicks', array(
      'date <=' => $date,
    ));
    $mysqlModel->delete('ads_views', array(
      'date <=' => $date,
    ));
    echo 'Success';
  }
}
