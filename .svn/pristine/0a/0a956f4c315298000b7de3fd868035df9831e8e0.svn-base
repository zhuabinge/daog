<?php
//api提供数据类
class apiPostController extends BpfController
{
  // java获取需要抓取订单数据的商品
  public function orderProductAction()
  {
   if ($this->isPost() && isset($_POST['token']) && $_POST['token'] ===  'kladkiewj4389jdsadf923cvmsdksa') {
      $merchantModel = $this->getModel('merchant');
      $conditions['status'] = 3;
      $activities_products = $merchantModel->getActivitiesProducts($conditions);
      foreach ($activities_products as $key => $ap) {
      	$conditions['mid'] = $ap->mid;
      	$conditions['pid'] = $ap->pid;
      	unset($conditions['status']);
      	$order = $merchantModel->getOrder($conditions);
      	if(!empty($order))
        $ap->created = $order->created;
      }
      $list = new stdClass();
      $list->apList = $activities_products;
      echo json_encode($list);
   }
  }

  // 淘宝ID暴露接口
  public function mallIdAction()
  {
    if ($this->isPost() && isset($_POST['token']) && $_POST['token'] ===  'kladkiewj4389jdsadf923cvmsdksa') {

      $productModel = $this->getModel('product');
      if(isset($_POST['status']))
      {
      $conditions = array(
        'where' =>  array(
        'status' => $_POST['status'], // 商品状态
        'weight > ' => 1, // 招商不动
         ),
        );
      }else{
        $conditions = null;
      }
      $p = $productModel->getProductsMall($conditions);
      $infodash = array();
      foreach ($p as $key => $value) {
        if (empty($value->mall_pid)) {
          continue;
        }
        $infodash[] = $value->mall_pid;
      }
      return implode(',', $infodash);
    }else{
      }
    return BPF_NOT_FOUND;
  }
  
   // 淘宝ID暴露接口
  public function tbkActivitiesAction()
  {
    if ($this->isPost() && isset($_POST['token']) && $_POST['token'] ===  'kladkiewj4389jdsadf923cvmsdksa') {
    $tbkactivityModel = $this->getModel('tbkactivity');
      $list = new stdClass();
      $list->tbkList = $tbkactivityModel->getTbkActivities();
      echo json_encode($list);
    }else{
      return BPF_NOT_FOUND;
      }
  }
}
