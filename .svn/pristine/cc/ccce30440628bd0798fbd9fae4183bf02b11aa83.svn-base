<?php
class PageController extends BpfController
{
  public function helpAction()
  {
    $view = $this->getView();
    $view->display('help/index.phtml');
  }

  //关于我们
  public function aboutAction()
  {
  	$configModel = $this->getModel('config');
    $view = $this->getView();
    $view->assign(array(
        'content' => $configModel->get('about', ''),
        'action' => '关于我们',
        'cur' => 'about',
     ));
    $view->display('help/static.phtml');
  }

  //商务合作
  public function coAction()
  {
  	$configModel = $this->getModel('config');
    $view = $this->getView();
    $view->assign(array(
        'content' => $configModel->get('co', ''),
        'action' => '商务合作',
        'cur' => 'co',
     ));
    $view->display('help/static.phtml');
  }

  //联系我们
  public function contactAction()
  {
  	$configModel = $this->getModel('config');
    $view = $this->getView();
    $view->assign(array(
        'content' => $configModel->get('contact', ''),
        'action' => '联系我们',
        'cur' => 'contact',
     ));
    $view->display('help/static.phtml');
  }

  //服务条款
  public function serviceAction()
  {
  	$configModel = $this->getModel('config');
    $view = $this->getView();
    $view->assign(array(
        'content' => $configModel->get('service', ''),
        'action' => '服务条款',
        'cur' => 'service',
     ));
    $view->display('help/static.phtml');
  }

  //人才招聘
  public function jobAction()
  {
  	$configModel = $this->getModel('config');
    $view = $this->getView();
    $view->assign(array(
        'content' => $configModel->get('job', ''),
        'action' => '人才招聘',
        'cur' => 'job',
     ));
    $view->display('help/static.phtml');
  }
}
