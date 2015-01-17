<?php
class AdminChannelController extends BpfController
{
  public static function __permissions()
  {
    return array(
      'name' => '频道管理',
      'permissions' => array(
        'channel-view' => '查询频道',
        'channel-reply' => '回复频道',
        'channel-delete' => '删除频道',
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

  public function indexAction()
  {
  	$rows =  15;//默认一页显示记录
    $conditions = array( //过滤条件
      'search' =>  isset($_GET['title']) ? $_GET['title'] : '',
    );
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
    ;
  	$channelModel=$this->getModel('channel');
  	$channels = $channelModel->getChannels($conditions, $page, $rows);
  	$total = $channelModel->getChannelsCount($conditions);
  	$view = $this->getView();
  	$view->assign('channels', $channels);
  	$view->assign('page', $page ? $page : 1);
  	$view->assign('total', $total);
  	$view->assign('rows', $rows);
    $this->addCss('css/plugins.css'); //包含所有插件CSS
    $this->addJs('js/plugins/select2/select2.min.js'); //下拉框插件
    $view->display('admin/product/channel.phtml');
  }

  public function deleteAction()
  {
  	$channelModel=$this->getModel('channel');
  	if (isset($_GET['id'])) {
      $cid = trim($_GET['id']);
  	}
  	$del = $channelModel->deleteChannel($cid);
  	if ($del){
      setMessage('删除成功', 'success');
  	} else{
      setMessage('删除失败', 'error');
  	}
  	gotoUrl('admin/channel/');
  }

  public function editAction()
  {
    $channelModel = $this->getModel('channel');
    $view = $this->getView();
    if ($this->isPost()) {
      //表单提交
      $cid = is_numeric($_POST['cid']) ? $_POST['cid'] : null;
      if(!isset($cid)) {
        throw new BpfException();
      }
      $rules = array();
      if (isset($_POST['rules'])) {
        if (isset($_POST['rules']['title']) && trim($_POST['rules']['title']) != '') {
          $rules['title'] = trim($_POST['rules']['title']);
        }
        if (isset($_POST['rules']['feature']) && trim($_POST['rules']['feature']) != '') {
          $rules['feature'] = trim($_POST['rules']['feature']);
        }
        if (isset($_POST['rules']['price1']) && trim($_POST['rules']['price1']) != '') {
          $price = trim($_POST['rules']['price1']);
          if (strpos($price, ' ')) {
            $price = explode(' ', $price, 2);
            if (in_array($price[0], array('<', '>', '<=', '>=', '='))) {
              $op = $price[0];
            } else {
              $op = '<';
            }
            $price = trim($price[1]);
            if (is_numeric($price)) {
              $rules['price1'] = array(
                'op' => $op,
                'value' => $price,
              );
            }
          } else if (is_numeric($price)) {
            $rules['price1'] = array(
              'op' => '<',
              'value' => $price,
            );
          }
        }
        if (isset($_POST['rules']['price2']) && trim($_POST['rules']['price2']) != '') {
          $price = trim($_POST['rules']['price2']);
          if (strpos($price, ' ')) {
            $price = explode(' ', $price, 2);
            if (in_array($price[0], array('<', '>', '<=', '>=', '='))) {
              $op = $price[0];
            } else {
              $op = '<';
            }
            $price = trim($price[1]);
            if (is_numeric($price)) {
              $rules['price2'] = array(
                'op' => $op,
                'value' => $price,
              );
            }
          } else if (is_numeric($price)) {
            $rules['price2'] = array(
              'op' => '<',
              'value' => $price,
            );
          }
        }
      }
      $channel = array(
        'title' => trim($_POST['title']),
        'status' => $_POST['status'],
        'show_on_home' => isset($_POST['showOnHome']) ? $_POST['showOnHome'] : 0,
        'seo_path' => isset($_POST['seoPath']) ? trim($_POST['seoPath']) : null,
        'seo_keyword' => isset($_POST['seoKey']) ? trim($_POST['seoKey']) : null,
        'seo_description' => isset($_POST['seoDescription']) ? $_POST['seoDescription'] : null,
        'template' => isset($_POST['template']) ? $_POST['template'] : null,
        'weight' => isset($_POST['weight']) ? $_POST['weight'] : 0,
        'rules' => $rules ? json_encode($rules) : '',
        'scheduling' => !empty($_POST['scheduling']) ? strtotime($_POST['scheduling']) : 0,
        'expired' => !empty($_POST['expired']) ? strtotime($_POST['expired']) : 0,
      );
      $checkSeoPath = $channelModel->checkChannelPathIsExists($_POST['seoPath']);
      if (empty($channel['title'])) {
        setMessage('标题不能为空！！', 'error');
      } else if( $cid == 0 ){
        //插入频道
        if ($_POST['seoPath'] != '' && $checkSeoPath) {
          setMessage('SEO路径已存在', 'error');
        } else {
          $channelId = $channelModel->insertChannel($channel);
          if ($channelId) {
            setMessage('频道保存成功', 'success');
            gotoUrl('admin/channel/');
          } else {
            setMessage('频道保存失败', 'error');
          }
        }
      } else {
        //更新频道
        if ($checkSeoPath && $_POST['seoPath'] != '' && $checkSeoPath != $cid) {
          setMessage('SEO路径已存在', 'error');
        } else {
          $channelId = $channelModel->updateChannel($cid, $channel);
          if ($channelId) {
            setMessage('更新频道成功', 'success');
            gotoUrl('admin/channel/');
          } else {
            setMessage('更新频道失败', 'error');
          }
        }
      }

      $view->assign('title', $channel['title']);
      $view->assign('status', $channel['status']);
      $view->assign('showOnHome', $channel['show_on_home']);
      $view->assign('seoPath', $channel['seo_path']);
      $view->assign('seoKey', $channel['seo_keyword']);
      $view->assign('seoDescription', $channel['seo_description']);
      $view->assign('showOnHome', $channel['show_on_home']);
      $view->assign('status', $channel['status']);
      $view->assign('weight', $channel['weight']);
      $view->assign('cid', $cid);
    } else {
      //页面展示
      $cid = isset($_GET['id']) && is_numeric($_GET['id'])? $_GET['id'] : null;
      if (isset($cid)) {
        $channel = $channelModel->getChannel($cid);
        if ($channel) {
            $view->assign('title', $channel->title);
            $view->assign('status', $channel->status);
            $view->assign('showOnHome', $channel->show_on_home);
            $view->assign('seoPath', $channel->seo_path);
            $view->assign('seoKey', $channel->seo_keyword);
            $view->assign('seoDescription', $channel->seo_description);
            $view->assign('showOnHome', $channel->show_on_home);
            $view->assign('template', $channel->template);
            $view->assign('weight', $channel->weight);
            $view->assign('status', $channel->status);
            $view->assign('rules', $channel->rules);
            $view->assign('expired', $channel->expired);
            $view->assign('scheduling', $channel->scheduling);
            $view->assign('cid', $channel->cid);
        } else {
          setMessage('该频道不存在', 'error');
        }
      } else {
        $view->assign('showOnHome', '0');
        $view->assign('status', '1');
      }
    }

    $this->addJs('js/jquery-ui.js');
    $this->addJs('js/jquery-ui-timepicker-addon.min.js');
    $this->addCss('css/plugins.css');
    $view->display('admin/product/channel_edit.phtml');
  }

  public function setProductsAction()
  {
    if ( $this->isPost()) {
      if (empty($_POST['pid']) || empty($_POST['channelId']) || !is_numeric($_POST['channelId'])|| !isset($_POST['status'])) {
        return json_encode(array('msg' => '参数错误', 'code'=>'-1'));
      }
      $channelModel = $this->getModel('channel');
      $result = array('msg' => '设置成功');
      if ($_POST['status'] == 0) {
        $channelModel->deleteChannelProducts($_POST['channelId'], $_POST['pid']);
        $result['code'] = 0 ;
      } else if ($_POST['status'] == 1) {
        $channelModel->insertChannelProducts($_POST['channelId'], $_POST['pid']);
         $result['code'] = 1 ;
      }
      return json_encode($result);
    }
    return json_encode(array('msg' => '无效请求', 'code'=>'-1'));
  }

  public function getProductsAction($page = 1)
  {
    $view = $this->getView();
    $rows = 8;
    $productModel = $this->getModel('product');
    $conditions = array(
      'where' => array(
        'status' => 1,
      ),
    );
    if(!empty($_GET['title'])) {
      $conditions['where']['title LIKE'] = '%' . $_GET['title'] . '%';
    }
    $productlist =  $productModel->getProducts($conditions, $page, $rows);
    $total =  $productModel->getProductsCount($conditions);
    $ctp = array();
    if (!empty($_GET['cid'])) {
      $pids = array();
      foreach ($productlist as $product) {
        $pids[] = $product->pid;
      }
      $channelModel = $this->getModel('channel');
      $ctp = $channelModel->checkProducts($_GET['cid'], $pids);
    }
    $view->assign('ctp', $ctp);
    $view->assign('productlist', $productlist);
    $view->assign('page', $page ? $page : 1);
    $view->assign('total', $total);
    $view->assign('rows', $rows);
    $view->assign('title', !empty($_GET['title']) ? $_GET['title'] : '');
    $view->assign('cid', !empty($_GET['cid']) ? $_GET['cid'] : '');
    $view->display('admin/product/channel_product.phtml');
  }

  public function batchAction()
  {
    $batchtid = $_POST['cid'];
    if ($this->isPost()) {
      if (empty($batchtid) || !is_array($batchtid)) {
        setMessage('操作失败，请重试', 'error');
        gotoUrl('admin/channel');
      } else if (is_array($batchtid) && is_numeric($_POST['status'])) {
        $channelModel = $this->getModel('channel');
        $status = $_POST['status'] == 1 ? 'pass' : 'delect';
        $channelModel->updateChannels($batchtid, $status);
        setMessage('批量操作成功', 'success');
        gotoUrl('admin/channel');
      }
    } else {
      throw new BpfException();
    }
  }

  // 许愿临时列表
  public function wishingAction()
  {
    $productModel = $this->getModel('product');
    $userModel = $this->getModel('user');
    $rows = 15;
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;

    $conditions = array();
    if(!empty($_GET['productId'])){
      $conditions['where']['pid'] = $_GET['productId'];
    }
    if(!empty($_GET['userName'])){
      $conditions['where']['nickname'] = $_GET['userName'];
    }
    if(isset($_GET['rewardStatus']) && ($_GET['rewardStatus'] == 1 || $_GET['rewardStatus'] == 0)){
      $conditions['where']['status'] = $_GET['rewardStatus'];
    }
    $wishingList = $productModel->getDesires($conditions, $page, $rows);
    $count = $productModel->getDesiresCount($conditions);
    $view = $this->getView();
    $view->assign(array(
      'count' => $count,
      'page' => $page,
      'rows' => $rows,
      'wishingList' => $wishingList,
    ));
    $this->addJs('js/plugins/select2/select2.min.js'); //下拉框插件
    $view->display('admin/product/channel_wishing.phtml');
  }

  // 抽奖临时列表
  public function prizesAction()
  {
    $userModel = $this->getModel('user');
    $rows = 15;
    $page = isset($_GET['page'])&&is_numeric($_GET['page']) ? $_GET['page'] : 1;

    $conditions = array();
    if(!empty($_GET['stime'])){
      $conditions['where']['date'] = $_GET['stime'];
    }
    $prizesList = $userModel->getPrizeLogs($conditions, $page, $rows);
    $count = $userModel->getPrizeLogsCount($conditions);
    $view = $this->getView();
    $view->assign(array(
      'count' => $count,
      'page' => $page,
      'rows' => $rows,
      'prizesList' => $prizesList,
    ));
    $this->addJs('js/plugins/select2/select2.min.js'); //下拉框插件
    $view->display('admin/product/channel_prizes.phtml');
  }
}
