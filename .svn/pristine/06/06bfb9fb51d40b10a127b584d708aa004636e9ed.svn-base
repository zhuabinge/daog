<?php
class AdminDefaultController extends BpfController
{
  public static function __permissions()
  {
    return array(
      'name' => '后台首页',
      'permissions' => array(
        'dashboard-index' => '控制台',
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
    $view = $this->getView();
    $this->addJs('js/plugins/highcharts/highcharts.js');
    $view->display('admin/index.phtml');
  }

  public function dashboardAction()
  {
    if (!access('dashboard-index')) {
      exit();
    }
    $systemModel = $this->getModel('system');
    $cacheModel = $this->getModel('cache');
    if ($cacheModel->get('infodash')) {
      // 缓存存在读取缓存
      $infodash = $cacheModel->get('infodash');
      $online = $cacheModel->get('online');
      $dashboard = $cacheModel->get('dashboard');
    } else {     
      $curDate = date('Ymd', (REQUEST_TIME - 86400 * 8)); // 前七天时间
      $befTime = date('Ymd', REQUEST_TIME - 86400); // 前一天时间

      // 获取汇总数据
      $conditions = array(
        'start' => $curDate,
        'end' => $befTime,
      );
      $dashboard_toal = $systemModel->getStatistic($conditions);
      $infodash = array(
        'date' => array(),
        'users_count' => array(),
        'users_incre' => array(),
        'login_count' => array(),
        'checkins_count' => array(),
        'comments_count' => array(),
        'scores_amount' => array(),
        'scores_incre' => array(),
        'jf_amount' => array(),
        'jf_incre' => array(),
        'page_views' => array(),
        'unique_vistors' => array(),
        'item_clicks' => array(),
      );
      foreach ($dashboard_toal as $key => $sa) {
         $infodash['date'][] = $sa->date;
         $infodash['users_count'][] = $sa->users_count;
         $infodash['users_incre'][] = $sa->users_incre;
         $infodash['login_count'][] = $sa->login_count;
         $infodash['checkins_count'][] = $sa->checkins_count;
         $infodash['comments_count'][] = $sa->comments_count;
         $infodash['scores_amount'][] = $sa->scores_amount;
         $infodash['scores_incre'][] = $sa->scores_incre;
         $infodash['jf_amount'][] = $sa->jf_amount;
         $infodash['jf_incre'][] = $sa->jf_incre;
         $infodash['page_views'][] = $sa->page_views;
         $infodash['unique_vistors'][] = $sa->unique_vistors;
         $infodash['item_clicks'][] = $sa->item_clicks;
      }
      $infodash['date'] = implode(',', $infodash['date']);
      $infodash['users_count'] = implode(',', $infodash['users_count']);
      $infodash['users_incre'] = implode(',', $infodash['users_incre']);
      $infodash['login_count'] = implode(',', $infodash['login_count']);
      $infodash['checkins_count'] = implode(',', $infodash['checkins_count']);
      $infodash['comments_count'] = implode(',', $infodash['comments_count']);
      $infodash['scores_amount'] = implode(',', $infodash['scores_amount']);
      $infodash['scores_incre'] = implode(',', $infodash['scores_incre']);
      $infodash['jf_amount'] = implode(',', $infodash['jf_amount']);
      $infodash['jf_incre'] = implode(',', $infodash['jf_incre']);
      $infodash['page_views'] = implode(',', $infodash['page_views']);
      $infodash['unique_vistors'] = implode(',', $infodash['unique_vistors']);
      $infodash['item_clicks'] = implode(',', $infodash['item_clicks']);

      // 获取当天数据
      $dashboard = $systemModel->getStatistic();
      // 获取站外统计数据
      // $online = $systemModel->getPiwikData();
      $online = array();
      // 缓存所有数据
      $cacheModel->set('dashboard', $dashboard, 3600);
      $cacheModel->set('online', $online, 3600);
      $cacheModel->set('infodash', $infodash, 3600);
    }
    $view = $this->getView();
    $view->assign(array(
      'dashboard' => $dashboard,
      'online' => $online,
      'infodash' => $infodash,
    ));
    $view->display('admin/dashboard.phtml');
  }
}
