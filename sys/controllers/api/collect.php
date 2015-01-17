<?php
//api接受数据类
class apiCollectController extends BpfController
{
  // 接收java商品订单数据
  public function orderAction()
   {
   if ($this->isPost() && isset($_POST['token']) && $_POST['token'] ===  'kladkiewj4389jdsadf923cvmsdksa') {
    if(isset($_POST['order_json']) && $_POST['order_json'] != '')
    {
    $merchantModel = $this->getModel('merchant');
    $jsonData = json_decode($_POST['order_json']);
       $order = array(
          'mid' => $jsonData->mId,
          'pid' => $jsonData->pId,
          'created' => $time = strtotime($jsonData->date),
          'price' => $jsonData->price,
          'count' => $jsonData->amount,
          'buyer' => $jsonData->buyer,
          'data' => $jsonData->date,
       );
    }
      $oid = $merchantModel->insertOrder($order);
    if($oid)
     {
       return 'success';
     }else{
       return 'fail';
     }
    }
   }  
  // 接收java淘宝客活动数据
  public function taobaokeActivitiesAction()
  {
   if ($this->isPost() && isset($_POST['token']) && $_POST['token'] ===  'kladkiewj4389jdsadf923cvmsdksa') {
    if(isset($_POST['tbkactivies_json']) && $_POST['tbkactivies_json'] != '')
    {
    $tbkactivityModel = $this->getModel('tbkactivity');
    $jsonData = json_decode($_POST['tbkactivies_json']);
    foreach ($jsonData as $key => $value) {
    $tbkactivities[] = (array)$value;
    }
    $oid = $tbkactivityModel->insertTaobaokeActivities($tbkactivities);
     if($oid)
     {
       return 'success';
     }else{
       return 'fail';
     }
    }
   }
  }

  // 更新java淘宝客活动数据
  public function updateaobaokeActivitiesAction()
  {
   if ($this->isPost() && isset($_POST['token']) && $_POST['token'] ===  'kladkiewj4389jdsadf923cvmsdksa') {
    if(isset($_POST['eventids']) && $_POST['eventids'] != '')
    {
    $eventIds = json_decode($_POST['eventids']);
    var_dump($eventIds);
     exit();
    $tbkactivityModel = $this->getModel('tbkactivity');
    foreach ($eventIds as $key => $id) {
      $tbkactivityModel->deleteTbkActivities($id);
     }
    }
   }
  }

 // 接受JAVA推送的商品接口
  public function productAction()
  {
    if ($this->isPost() && isset($_POST['token']) && $_POST['token'] ===  'kladkiewj4389jdsadf923cvmsdksa') {
    $productModel = $this->getModel('product');
    $jsonData = json_decode($_POST['product_json']);
    $files = json_decode($_POST['file_ids']);
    $pid = trim($jsonData->mallId);
    $click_url = isset($jsonData->url) ? $jsonData->url : '';
    $click_url = trim($click_url);
    $pc = $productModel->checkMallPid($pid);
      if (!$pc) {
        $product = array(
          'cid' => $jsonData->cateId,
          'title' => $jsonData->title,
          'feature' => $jsonData->feature,
          'body' => $jsonData->desc,
          'list_price' => $jsonData->sellPrice,
          'sell_price' => $jsonData->promoPrice,
          'url' => $jsonData->url,
          'mall_pid' => $jsonData->mallId,
          'data' => isset($jsonData->comment) ? $jsonData->comment : '',
          'sellcount' => isset($jsonData->sellCount) ? $jsonData->sellCount : 0, // 月销量
          'buyerscore' => isset($jsonData->buyerScore) ? $jsonData->buyerScore : 0, // 用户评分
          'wantcount' => isset($jsonData->wantCount) ? $jsonData->wantCount : 0, // 想买人数
          'ratepercent' => isset($jsonData->ratepercent) ? $jsonData->ratepercent : 0, // 佣金比例
          'commission' => isset($jsonData->commission) ? $jsonData->commission : 0, // 佣金
          'totalnum' => isset($jsonData->totalNum) ? $jsonData->totalNum : 0, // 30天推广量
          'totalfeemoney' => isset($jsonData->totalfeemoney) ? $jsonData->totalfeemoney : 0, // 30天支出佣金
          'history_price' => isset($jsonData->lowPrice) && !empty($jsonData->lowPrice) ? $jsonData->lowPrice : $jsonData->promoPrice, // 最近最低价
        );
        if (!empty($files)) {
          $product['files'] = $files;
        }
        if ($productId = $productModel->insertProduct($product)) {
          $channelModel = $this->getModel('channel');
          $channelIds = $channelModel->getMatchChannels((object) $product);
          $productModel->setProductChannels($productId, $channelIds);
          return $jsonData->mallId . '产品保存成功';
        } else {
          return $jsonData->mallId . '产品保存失败';
        }
      } else if ($pc->status != 1){
        if (isset($jsonData) && isset($pc)) {
          $productId = $pc->pid;
          if (!empty($click_url)) {
            $pc->url = $click_url;
          }
          $product = array(
            'cid' =>  $pc->cid == 0 ? $jsonData->cateId : $pc->cid,
            'status' =>  $jsonData->status == -1 ? -1 : $pc->status,
            'list_price' => ( isset($jsonData->sellPrice)  && $jsonData->sellPrice != '' ) ? $jsonData->sellPrice : $pc->list_price,
            'sell_price' => ( isset($jsonData->promoPrice)  && $jsonData->promoPrice != '' ) ? $jsonData->promoPrice : $pc->sell_price,
            'url' => ( isset($jsonData->url) && $jsonData->url != '' ) ? $jsonData->url : $pc->url,
            'data' => ( isset($jsonData->comment) && $jsonData->comment != '' ) ? $jsonData->comment : $pc->data,
            'body' => ( !empty($jsonData->desc) && isset($jsonData->desc) ) ? $jsonData->desc : $pc->body,
            'sellcount' => ( isset($jsonData->sellCount) && $jsonData->sellCount != '' ) ? $jsonData->sellCount : $pc->sellcount, // 月销量
            'buyerscore' => ( isset($jsonData->buyerScore) && $jsonData->buyerScore != '') ? $jsonData->buyerScore : $pc->buyerscore, // 用户评分
            'wantcount' => ( isset($jsonData->wantCount) && $jsonData->wantCount != '' ) ? $jsonData->wantCount : $pc->wantcount, // 想买人数
            'ratepercent' => ( isset($jsonData->ratepercent) && $jsonData->ratepercent != '' ) ? $jsonData->ratepercent : $pc->ratepercent, // 佣金比例
            'commission' => ( isset($jsonData->commission) && $jsonData->commission != '' ) ? $jsonData->commission : $pc->commission, // 佣金
            'totalnum' => ( isset($jsonData->totalNum) && $jsonData->totalNum != '' ) ? $jsonData->totalNum : $pc->totalnum, // 30天推广量
            'totalfeemoney' => ( isset($jsonData->totalfeemoney) && $jsonData->totalfeemoney != ''  ) ? $jsonData->totalfeemoney : $pc->totalfeemoney, // 30天支出佣金
            'history_price' => isset($jsonData->lowPrice) && !empty($jsonData->lowPrice) ? $jsonData->lowPrice : $pc->history_price, // 最近最低价

          );
          $productModel->updateProduct($productId, $product, 1);
          if($jsonData->status == -1)
          {
            $msg = $pid . '产品已下架';
          }else{
            $msg = $pid . '产品已经更新';
          }
          return $msg;
        }
      }
    }
  }

 // 接受JAVA推送的商品接口
  public function productOnlineAction()
  {
    if ($this->isPost() && isset($_POST['token']) && $_POST['token'] ===  'kladkiewj4389jdsadf923cvmsdksa') {
    $productModel = $this->getModel('product');
    $jsonData = json_decode($_POST['product_json']);
    $pid = trim($jsonData->mallId);
    $pc = $productModel->checkMallPid($pid);
        if (isset($jsonData) && isset($pc)) {
          $productId = $pc->pid;
          if (!empty($click_url)) {
            $pc->url = $click_url;
          }
          $product = array(
            'status' =>  $jsonData->status == -1 ? -1 : $pc->status,
            'list_price' => ( isset($jsonData->sellPrice)  && $jsonData->sellPrice != '' ) ? $jsonData->sellPrice : $pc->list_price,
            'sell_price' => ( isset($jsonData->promoPrice)  && $jsonData->promoPrice != '' ) ? $jsonData->promoPrice : $pc->sell_price,
            'url' => ( isset($jsonData->url) && $jsonData->url != '' ) ? $jsonData->url : $pc->url,
            'data' => ( isset($jsonData->comment) && $jsonData->comment != '' ) ? $jsonData->comment : $pc->data,
            'sellcount' => ( isset($jsonData->sellCount) && $jsonData->sellCount != '' ) ? $jsonData->sellCount : $pc->sellcount, // 月销量
            'buyerscore' => ( isset($jsonData->buyerScore) && $jsonData->buyerScore != '') ? $jsonData->buyerScore : $pc->buyerscore, // 用户评分
            'wantcount' => ( isset($jsonData->wantCount) && $jsonData->wantCount != '' ) ? $jsonData->wantCount : $pc->wantcount, // 想买人数
            'ratepercent' => ( isset($jsonData->ratepercent) && $jsonData->ratepercent != '' ) ? $jsonData->ratepercent : $pc->ratepercent, // 佣金比例
            'commission' => ( isset($jsonData->commission) && $jsonData->commission != '' ) ? $jsonData->commission : $pc->commission, // 佣金
            'totalnum' => ( isset($jsonData->totalNum) && $jsonData->totalNum != '' ) ? $jsonData->totalNum : $pc->totalnum, // 30天推广量
            'totalfeemoney' => ( isset($jsonData->totalfeemoney) && $jsonData->totalfeemoney != ''  ) ? $jsonData->totalfeemoney : $pc->totalfeemoney, // 30天支出佣金
            'history_price' => isset($jsonData->lowPrice) && !empty($jsonData->lowPrice) ? $jsonData->lowPrice : $pc->history_price, // 最近最低价          
            );
          $productModel->updateProduct($productId, $product, 1);
          if($jsonData->status == -1)
          {
            $msg = $pid . '产品已下架';
          }else{
            $msg = $pid . '产品已经更新';
          }
          return $msg;
        }
      }
    }
}
