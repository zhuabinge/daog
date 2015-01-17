<?php
class MerchantDefaultController extends BpfController
{
  public function indexAction()
  {
    $view = $this->getView();
    $channelModel = $this->getModel('channel');
    $conditions = array(
      'where' => array(
        'status' => 1,
      ),
      'orderby' => '`weight` DESC',
    );
    $channelsList = $channelModel->getChannels($conditions);
    $view->assign(array(
      'channelsList' => $channelsList,
    ));
    $view->display('merchant/index.phtml');
  }
}
