<?php
class AdminProductController extends BpfController
{
  public static function __permissions()
  {
    return array(
      'name' => '商品管理',
      'permissions' => array(
        'product-view' => '查询商品',
        'product-edit' => '编辑商品',
        'product-delete' => '删除商品',
        'product-check' => '审核商品',
      ),
    );
  }

  public function __init()
  {
    // 管理员权限判断
    if (!isLogin() || !isAdmin()) {
      throw new Bpf404Exception();
    } else if (!isAdminLogin()) {
      gotoUrl('admin/login');
    }
    $GLOBALS['user']->adminTime = REQUEST_TIME;
  }

  public function indexAction($page = 1)
  {
    if (!access('product-view')) {
      throw new BpfException();
    }
    $rows = 15; //每页数量
  	$view = $this->getView();
    $productModel = $this->getModel('product');
    $tagModel = $this->getModel('tag');
    $userModel = $this->getModel('user');
    $channelModel = $this->getModel('channel');
    $merchantModel = $this->getModel('merchant');
    $cacheModel = $this->getModel('cache');

    $conditions = array( //过滤条件
      'where' =>  array(),
      'tags'  =>  NULL,
      'orderby' => '`updated` DESC',
    );
    // 时间过滤
    if (empty($_GET['scheduling']) && empty($_GET['expired'])) {
      $conditions['date']['scheduling'] = date('Ymd', REQUEST_TIME);
      $conditions['date']['expired'] = date('Ymd', REQUEST_TIME);
    } else {
      $scheduling = substr($_GET['scheduling'], 0, 8);
      $expired = substr($_GET['expired'], 0, 8);
      $conditions['date']['scheduling'] = $scheduling;
      $conditions['date']['expired'] = $expired;
      $conditions['where']['updated >='] = strtotime($scheduling);
      $conditions['where']['updated <='] = strtotime($expired);
    }
    isset($_GET['title']) ? ($conditions['where']['title LIKE'] = '%' . $_GET['title'] . '%') : '';
    isset($_GET['cid']) && ($_GET['cid'] > 0) && is_numeric($_GET['cid']) ? ($conditions['where']['cid'] = $_GET['cid']) : '';
    isset($_GET['tids']) && ($_GET['tids'] > 0) && is_numeric($_GET['tids']) ? ($conditions['tags']['tids'] = $_GET['tids']) : '';
    isset($_GET['uids']) && ($_GET['uids'] > 0) && is_numeric($_GET['uids']) ? ($conditions['where']['editor_uid'] = $_GET['uids']) : '';
    isset($_GET['channelids']) && ($_GET['channelids'] > 0) && is_numeric($_GET['channelids']) ? ($conditions['channel'] = $_GET['channelids']) : '';
    isset($_GET['status']) && ($_GET['status'] != 'all') && is_numeric($_GET['status']) ? ($conditions['where']['status'] = $_GET['status']) : '';
    isset($_GET['mid']) && ($_GET['mid'] > 0) && is_numeric($_GET['mid']) ? ($conditions['where']['mid'] = $_GET['mid']) : '';
    $filter = array();
    // 排序规则
    if (!empty($_GET['ratepercent'])) {
      $_GET['ratepercent'] == 1 ? array_push($filter, '`ratepercent` DESC') : array_push($filter, '`ratepercent` ASC');
    }
    if (!empty($_GET['totalnum'])) {
      $_GET['totalnum'] == 1 ? array_push($filter, '`totalnum` DESC') : array_push($filter, '`totalnum` ASC');
    }
    if (!empty($_GET['sellcount'])) {
      $_GET['sellcount'] == 1 ? array_push($filter, '`sellcount` DESC') : array_push($filter, '`sellcount` ASC');
    }
    if (!empty($_GET['buyerscore'])) {
      $_GET['buyerscore'] == 1 ? array_push($filter, '`buyerscore` DESC') : array_push($filter, '`buyerscore` ASC');
    }
    if (!empty($_GET['wantcount'])) {
      $_GET['wantcount'] == 1 ? array_push($filter, '`wantcount` DESC') : array_push($filter, '`wantcount` ASC');
    }
    if (!empty($_GET['clicks'])) {
      $_GET['clicks'] == 1 ? array_push($filter, '`clicks` DESC') : array_push($filter, '`clicks` ASC');
    }
    if (!empty($_GET['counter'])) {
      $_GET['counter'] == 1 ? array_push($filter, '`counter` DESC') : array_push($filter, '`counter` ASC');
    }
    if (!empty($_GET['uptime'])) {
      $_GET['uptime'] == 1 ? array_push($filter, '`updated` DESC') : array_push($filter, '`updated` ASC');
    }
    if (count($filter) > 0) {
      $conditions['orderby'] = implode(',', $filter);
    }
    $count = $productModel->getProductsCount($conditions);
  	$productlist = $productModel->getProducts($conditions, $page, $rows);
    // 获取编辑用户，产品分类
    foreach ($productlist as $key => $sa) {
      $sa->cate = $productModel->getCategory($sa->cid);
      $sa->user = $userModel->getUser($sa->editor_uid);
    }
    // 缓存搜索条件
    $userList = $cacheModel->get('userList');
    $tagsList = $cacheModel->get('tagsList');
    $channelsList = $cacheModel->get('channelsList');
    $merchantsList = $cacheModel->get('merchantsList');
    $categoryList = $cacheModel->get('categoryList');
    if (!$userList || !$tagsList || !$channelsList || !$merchantsList || !$categoryList) {
      $userList = $userModel->getRoleUsers(22); // 产品编辑用户列表
      $cacheModel->set('userList', $userList, 300);

      $tagsList = $tagModel->getTagsMap();
      $cacheModel->set('tagsList', $tagsList, 300);

      $channelsList = $channelModel->getChannelMap();
      $cacheModel->set('channelsList', $channelsList, 300);

      $categoryList = $productModel->getCategoryTree();
      $cacheModel->set('categoryList', $categoryList, 300);

      $conditions = array(
        'where' => array(
          'status' => 1,
        ),
      );
      $merchantsList = $merchantModel->getMerchants($conditions, 1, 100); // 查询前100个商家
      $cacheModel->set('merchantsList', $merchantsList, 300);
    }

    $this->addJs('js/jquery-ui.js');
    $this->addJs('js/jquery-ui-timepicker-addon.min.js');
    $this->addJs('js/plugins/select2/select2.min.js'); //下拉框插件
    $view->assign(array(
      'page' => $page,
      'rows' => $rows,
      'count' => $count,
      'tagsList' => $tagsList,
      'categoryList' => $categoryList,
      'productlist' => $productlist,
      'channelsList' => $channelsList,
      'merchantsList' => $merchantsList,
      'userList' => $userList,
    ));
  	$view->display('admin/product/product.phtml');
  }

  public function editAction($pid = 0)
  {
    if (!access('product-edit')) {
      throw new BpfException();
    }
    $view = $this->getView();
    $productModel = $this->getModel('product');
    $tagModel = $this->getModel('tag');
    $channelModel = $this->getModel('channel');
    $systemModel = $this->getModel('system');
    $logModel = $this->getModel('log');
    $channelsList = $channelModel->getChannelMap();
    $categoryList = $productModel->getCategoryTree();
    $tagsList = $tagModel->getTagsMap();
    if (empty($pid)) {
      //添加商品
      if ($this->isPost()) {
        $set = array(
          'title' =>  $_POST['title'],
          'feature' =>  $_POST['feature'],
          'body' =>  $_POST['body'],
          'url' =>  $_POST['url'],
          'list_price' =>  $_POST['list_price'],
          'sell_price' =>  $_POST['sell_price'],
          'status'  =>  $_POST['status'],
          'data'  =>  $_POST['data'],
          'editor_uid'  =>  isset($GLOBALS['user']->uid) ? $GLOBALS['user']->uid : 0,
          'cid' => empty($_POST['cid']) ? $_POST['parent_cid'] : $_POST['cid'],
          'is_ads' => empty($_POST['is_ads']) ? 0 : $_POST['is_ads'],
          'sort'  =>  $_POST['sort'],
          'scheduling'  =>  isset($_POST['scheduling']) ? strtotime($_POST['scheduling']) : 0,
          'expired'  =>  isset($_POST['expired']) ? strtotime($_POST['expired']) : 0,
        );
        if (isset($_POST['files']) && $_POST['files'] != '') {
          $set['files'] = json_decode($_POST['files']);
        }
        $pid = $productModel->insertProduct($set);
        if ($pid) {
          if (isset($_POST['channels']) && is_array($_POST['channels'])) {
            $productModel->setProductChannels($pid, $_POST['channels']);
          }
          if (isset($_POST['tags']) && $_POST['tags'] != '') {
            $tags = explode(',', $_POST['tags']);
            $tids = $tagModel->saveTags($tags);
            $productModel->setProductTags($pid, array_keys($tids));
          }
          //检测是否编辑一下商品
          if ($_POST['edit_status'] == 1) {
            setMessage('商品添加成功', 'success');
            gotoUrl('admin/product/edit');
          } else {
            setMessage('商品添加成功', 'success');
            gotoUrl('admin/product');
          }
        } else {
          gotoUrl('admin/product/edit');
        }
      } else {
        $product = isset($_SESSION['collectProduct'])? $_SESSION['collectProduct']: null;
        unset($_SESSION['collectProduct']);
        if (isset($product)) {
          $fileModel = $this->getModel('file');
          $productImagesList = getAssocArray($fileModel->getFiles('pro_img', array_keys($product->images)), 'file_id');
          $view->assign('product', $product);
          $view->assign('productImagesList', $productImagesList);
          $view->assign('productChannels', $product->channels);
        }
      }
    } else if (isset($pid) && is_numeric($pid)) {
      //修改商品
      $product = $productModel->getProduct($pid);
      if (!$product) {
        throw new BpfException();
      }
      $fileModel = $this->getModel('file');
      $category = $productModel->getCategory($product->cid);
      $productTags = $productModel->getProductTags($pid);
      $productChannels = $productModel->getProductChannels($pid);
      $productImagesList = getAssocArray($fileModel->getFiles('pro_img', array_keys($product->images)), 'file_id');

      if ($this->isPost()) {
        $set = array(
          'title' =>  $_POST['title'],
          'feature' =>  $_POST['feature'],
          'body'  =>  $_POST['body'],
          'url' =>  $_POST['url'],
          'list_price'  =>  $_POST['list_price'],
          'sell_price'  =>  $_POST['sell_price'],
          'status'  =>  $_POST['status'],
          'cid' => empty($_POST['cid']) ? $_POST['parent_cid'] : $_POST['cid'],
          'is_ads' => empty($_POST['is_ads']) ? 0 : $_POST['is_ads'],
          'sort'  =>  $_POST['sort'],
          'scheduling'  =>  isset($_POST['scheduling']) ? strtotime($_POST['scheduling']) : 0,
          'expired'  =>  isset($_POST['expired']) ? strtotime($_POST['expired']) : 0,
        );
        if ($product->editor_uid === 0) {
          $set['editor_uid'] = isset($GLOBALS['user']->uid) ? $GLOBALS['user']->uid : 0;
        }
        if (isset($_POST['tags']) && $_POST['tags'] != '') {
          $tags = explode(',', $_POST['tags']);
          $tids = $tagModel->saveTags($tags);
          $productModel->setProductTags($pid, array_keys($tids));
        }
        if (isset($_POST['channels']) && is_array($_POST['channels'])) {
          $productModel->setProductChannels($pid, $_POST['channels']);
        } else {
          $productModel->setProductChannels($pid);
        }
        if (isset($_POST['files']) && $_POST['files'] != '') {
          $files = json_decode($_POST['files']);
          if (isset($_POST['cover']) && is_numeric($_POST['cover'])) {
            array_unshift($files, intval($_POST['cover']));
            $files = array_unique($files);
          }
          $set['files'] = $files;
        }
        $pids = $productModel->updateProduct($pid, $set);
        if ($pids) {
          // 后台日志记录操作
          $nickname = empty($GLOBALS['user']->nickname) ? $GLOBALS['user']->username : $GLOBALS['user']->nickname;
          $logs = array(
            'uid' => $GLOBALS['user']->uid,
            'op' => 'productEdit',
            'body' => $nickname . '对商品进行修改，操作成功！ 操作Pid ' . $pid,
            'data' => date('Ymd', REQUEST_TIME),
            'url' => REQUEST_URI,
          );
          $logModel->insertAdminLog($logs);
          //检测是否编辑一下商品
          if ($_POST['edit_status'] == 1) {
            setMessage('商品修改成功', 'success');
            $conditions = array(
              'where' => array(
                'status' => 0,
              ),
              'orderby' => 'RAND()',
            );
            $pro = $productModel->getProducts($conditions ,1 ,1);
            if (isset($pro)) {
              gotoUrl('admin/product/edit/' . $pro[0]->pid);
            } else {
              gotoUrl('admin/product');
            }
          } else {
            setMessage('商品修改成功', 'success');
            gotoUrl('admin/product');
          }
        } else {
          $logs['body'] = $nickname . '对商品进行修改，操作失败！ 操作Pid ' . $pid;
          $logModel->insertAdminLog($logs);
          setMessage('修改失败，请重试', 'error');
          gotoUrl('admin/product/edit/' . $pid);
        }
      }
      $view->assign('productImagesList', $productImagesList);
      $view->assign('category', $category);
      $view->assign('product', $product);
      $view->assign('productTags', is_array($productTags) ? implode(',', $productTags) : '');
      $view->assign('productChannels', is_array($productChannels) ? array_keys($productChannels) : array());
    } else {
      throw new BpfException();
    }

    $this->addJs('js/jquery-ui.js');
    $this->addJs('js/jquery-ui-timepicker-addon.min.js');
    $this->addJs('js/plugins/select2/select2.min.js'); //下拉框插件
    $this->addCss('css/plugins/dropzone.css'); //批量上传插件
    $this->addJs('js/plugins/dropzone/dropzone.min.js'); //批量上传插件
    $view->assign('channelsList', is_array($channelsList) ? $channelsList: array() );
    $view->assign('tagsList', json_encode(array_values($tagsList)));
    $view->assign('categoryList', $categoryList);
    $view->display('admin/product/product_edit.phtml');
  }

  // 百度编辑器上传图片
  public function ditorFileAction()
  {
    if (!access('product-edit')) {
      throw new BpfException();
    }
    if (isset($_FILES['upfile']) && $_FILES['upfile']['tmp_name'] != '')
    { //图片处理
      $fileModel = $this->getModel('file');
      $fileName = date('Ymd/', REQUEST_TIME) . $_FILES['upfile']['name'];
      $fileContent = file_get_contents($_FILES['upfile']['tmp_name']);
      $file = $fileModel->write('ditor_img', $fileName, $fileContent);
      if (isset($file)) {
        $file->originalName = $file->org_filename;
        $file->url = $file->file_path;
        $file->size = $file->size;
        $file->type = $file->mime;
        $file->state = 'SUCCESS';
        return json_encode($file);
      }
    }
  }

  // 商品预览
  public function previewAction()
  {
    if (!access('product-view')) {
      throw new BpfException();
    }
    $view = $this->getView();
    $view->display('admin/product/product_preview.phtml');
  }

  // 商品审核
  public function checkAction()
  {
    if (!access('product-check')) {
      throw new BpfException();
    }
    if ($this->isPost()) {
      // 后台日志记录操作
      $logModel = $this->getModel('log');
      $nickname = empty($GLOBALS['user']->nickname) ? $GLOBALS['user']->username : $GLOBALS['user']->nickname;
      $logs = array(
        'uid' => $GLOBALS['user']->uid,
        'op' => 'productEdit',
        'data' => date('Ymd', REQUEST_TIME),
        'url' => REQUEST_URI,
      );
      if (isset($_POST['pid']) && $_POST['pid']) {
        $productModel = $this->getModel('product');
        $result = $productModel->updateProduct($_POST['pid'], array('status' => 2));
        $logs['body'] = $nickname . '对商品进行驳回，操作成功！ 操作Pid ' . $_POST['pid'];
        $logModel->insertAdminLog($logs);
        return json_encode(array(
          'result' => $result,
        ));
      } else {
        $logs['body'] = $nickname . '对商品进行驳回，操作失败！ 操作Pid ' . $_POST['pid'];
        $logModel->insertAdminLog($logs);
        return json_encode(array(
          'result' => -1,
        ));
      }
    }
  }

  // 禁止商品删除
  public function removeAction($pid)
  {
    return null;
    if (!access('product-delete')) {
      throw new BpfException();
    }
    if (isset($pid) && is_numeric($pid)) {
      $productModel = $this->getModel('product');
      $pid = $productModel->deleteProduct($pid);
      if ($pid) {
        setMessage('删除操作成功', 'success');
      } else {
        setMessage('删除操作失败', 'error');
      }
      gotoUrl('admin/product');
    } else {
      throw new BpfException();
    }
  }

  // 批量上传图片
  public function dropfilesAction()
  {
    if (!access('product-edit')) {
      throw new BpfException();
    }
    if (isset($_FILES['image_path']) && is_array($_FILES['image_path'])) {
      $set = array();
      $fileModel = $this->getModel('file');
      foreach ($_FILES as $key => $value) {
        for ($i = 0; $i < count($value['tmp_name']); $i++) {
          $fileName = date('Ymd/', REQUEST_TIME) . $value['name'][$i];
          $fileContent = file_get_contents($value['tmp_name'][$i]);
          $file = $fileModel->write('pro_img', $fileName, $fileContent);
          if (isset($file) && $file) {
            $set[$file->file_id] = basename($file->org_filename);
          }
        }
      }
      return json_encode($set);
    } else {
      return BPF_NOT_FOUND;
    }
  }

  // 批量操作状态
  public function batchAction()
  {
    if (!access('product-edit')) {
      throw new BpfException();
    }
    $batchPid = $_POST['pid']; //批量操作PID
    if ($this->isPost()) {
      // 后台日志记录操作
      $logModel = $this->getModel('log');
      $logs = array(
        'uid' => $GLOBALS['user']->uid,
        'op' => 'productEdit',
        'data' => date('Ymd', REQUEST_TIME),
        'url' => REQUEST_URI,
      );
      $nickname = empty($GLOBALS['user']->nickname) ? $GLOBALS['user']->username : $GLOBALS['user']->nickname;
      if (empty($batchPid) || !is_array($batchPid)) {
        $logs['body'] = $nickname . '对商品进行批量操作，操作失败！ 操作Pid ' . implode(',', $batchPid);
        $logModel->insertAdminLog($logs);
        setMessage('操作失败，请重试', 'error');
        gotoUrl('admin/product');
      } else if (is_array($batchPid) || is_numeric($_POST['status'])) {
        $productModel = $this->getModel('product');
        $set = array();
        isset($_POST['cid']) && ($_POST['cid'] > 1) && is_numeric($_POST['cid']) ? $set['cid'] = $_POST['cid'] : '';
        isset($_POST['status']) && is_numeric($_POST['status']) ? $set['status'] = $_POST['status'] : '';
        $productModel->updateProducts($batchPid, $set);
        $logs['body'] = $nickname . '对商品进行批量操作，操作成功！ 操作Pid ' . implode(',', $batchPid);
        $logModel->insertAdminLog($logs);
        setMessage('批量操作成功', 'success');
        gotoUrl('admin/product');
      }
    } else {
      throw new BpfException();
    }
  }

  public function logsAction($pid = 0)
  {
    $view = $this->getView();
    $logModel = $this->getModel('log');
    $userModel = $this->getModel('user');
    if (!empty($pid)) {
      $conditions = array(
        'where' => array(
          'body LIKE' => '%' . $pid . '%',
          'op' => 'productEdit',
        ),
      );
      $logsList = $logModel->getAdminLogs($conditions, 1, 20);
    }
    $view->assign('logsList', isset($logsList) ? $logsList : null);
    $view->display('admin/product/product_logs.phtml');
  }

  //商品采集
  public function collectAction()
  {
    if (!access('product-edit')) {
      throw new BpfException();
    }
    $view = $this->getView();
    $msg = '商品链接填写错误，请输入内容';
    if ($this->isPost()) {
      if (isset($_POST['productUrl'])) {
        preg_match('/\Wid=(\d+)/', $_POST['productUrl'], $result);
        if (isset($result[1])) {
          $id = trim($result[1]);
          $productModel = $this->getModel('product');
          $ch = curl_init();
          curl_setopt($ch,CURLOPT_URL, BpfConfig::get('java.url') . 'product_get.do?');
          curl_setopt($ch,CURLOPT_CUSTOMREQUEST, "POST");
          curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, FALSE);
          curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, FALSE);
          curl_setopt($ch,CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
          curl_setopt($ch,CURLOPT_FOLLOWLOCATION, 1);
          curl_setopt($ch,CURLOPT_AUTOREFERER, 1);
          curl_setopt($ch, CURLOPT_TIMEOUT, 15);  
          curl_setopt($ch,CURLOPT_POSTFIELDS, 'token=kladkiewj4389jdsadf923cvmsdksa&collect=null&item_id=' . $id);
          curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
          $product = curl_exec($ch);
          $product = iconv("utf-8", "utf-8//IGNORE",$product);
          curl_close($ch);
          if (trim($product) == 'success') {
            $fileModel = $this->getModel('file');
            $product = $productModel->checkMallPid($id);
            if ($product) {
              $files = $productModel->getProductImages($product->pid);
              $product->images = $files;
              $channelModel = $this->getModel('channel');
              $product->channels = $channelModel->getMatchChannels($product);
              $_SESSION['collectProduct'] = $product;
              gotoUrl('admin/product/edit');
            }
          }
        }
      }
      setMessage($msg, 'error');
    }
    $view->display('admin/product/product_collect.phtml');
  }
}
