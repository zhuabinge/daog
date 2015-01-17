<?php
class MerchantRuleController extends BpfController
{
  public function indexAction()
  {
    $view = $this->getView();
    $view->display('merchant/rule.phtml');
  }

  public function detailAction($id = 0)
  {
  	if ($id == '1140') {
      $action = 'rule_' . $id;
  	} else if ($id == '1240') {
      $action = 'rule_' . $id;
    } else if ($id == '1340') {
      $action = 'rule_' . $id;
    } else if ($id == '1440') {
      $action = 'rule_' . $id;
    } else if ($id == '1540') {
      $action = 'rule_' . $id;
    } else if ($id == '1640') {
      $action = 'rule_' . $id;
    } else if ($id == '1740') {
      $action = 'rule_' . $id;
    } else if ($id == '295') {
      $action = 'rule_' . $id;
    } else if ($id == '390') {
      $action = 'rule_' . $id;
    } else {
      return BPF_NOT_FOUND;
    }
    
    $configModel = $this->getModel('config');
    $content = json_decode($configModel->get($action));
    $title = isset($content->title) ? $content->title : '';
    $content = isset($content->content) ? $content->content : '';
    $view = $this->getView();
    $view->assign('title', $title);
    $view->assign('content', $content);
    $view->display('merchant/rule_detail.phtml');
  }
}