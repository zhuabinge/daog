<?php
class MerchantHelpController extends BpfController
{
  public function indexAction()
  {
    $action = 'help_2716';
    $configModel = $this->getModel('config');
    $content = json_decode($configModel->get($action));
    $title = isset($content->title) ? $content->title : '';
    $content = isset($content->content) ? $content->content : '';
    $view = $this->getView();
    $view->assign('title', $title);
    $view->assign('content', $content);
    $view->display('merchant/help.phtml');
  }

  public function detailAction($id = 0)
  {
  	if ($id == '2716') {
      $action = 'help_' . $id;
  	} else if ($id == '3716') {
      $action = 'help_' . $id;
    } else if ($id == '4716') {
      $action = 'help_' . $id;
    } else if ($id == '5716') {
      $action = 'help_' . $id;
    } else if ($id == '6716') {
      $action = 'help_' . $id;
    } else if ($id == '7716') {
      $action = 'help_' . $id;
    } else {
      return BPF_NOT_FOUND;
    }

    $configModel = $this->getModel('config');
    $content = json_decode($configModel->get($action));
    $title = isset($content->title) ? $content->title : '';
    $content = isset($content->content) ? $content->content : '';
    $view = $this->getView();
    $view->assign('id', $id);
    $view->assign('title', $title);
    $view->assign('content', $content);
    $view->display('merchant/help.phtml');
  }
}