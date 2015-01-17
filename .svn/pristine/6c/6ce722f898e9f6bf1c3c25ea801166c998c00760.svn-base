 <?php
class CategoriesController extends BpfController
{   
	public function indexAction()
  	{
    $productModel = $this->getModel('product');
    $view = $this->getView();
	$cates = $productModel->getHomeCategories();
	$view->assign('cates', $cates);
    $view->display('categories.phtml');
	}
}

