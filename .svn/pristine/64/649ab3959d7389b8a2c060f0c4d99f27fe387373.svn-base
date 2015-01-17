<?php
//api工具类
class apiUtilController extends BpfController
{
  // 删除下架商品
  public function deleteOfflineAction()
   {
   if ($this->isPost() && isset($_POST['token']) && $_POST['token'] ===  'kladkiewj4389jdsadf923cvmsdksa') {
       $productModel = $this->getModel('product');
       $conditions = array(
        'where' =>  array(
        'status' => -1, // 商品状态
        'mid' => 0,
        'editor_uid' =>0,
         ),
        );
      $products = $productModel->getProductsMall($conditions);
      foreach ($products as $key => $value) {
        $productModel->deleteProduct($value->pid);
      }
    }
   }  
}
