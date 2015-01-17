<?php
class AdminCategoryController extends BpfController
{
  public static function __permissions()
  {
    return array(
      'name' => '商品类目管理',
      'permissions' => array(
        'category-view' => '查询类目',
        'category-edit' => '编辑类目',
        'category-delete' => '删除类目',
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
    $productModel = $this->getModel('product');
    $categoryList = $productModel->getCategoryTree();

    $this->addCss('css/plugins/nestable.css'); //导航拖动插件
    $this->addJs('js/plugins/nestable/jquery.nestable.min.js'); //导航拖动插件
    $view->assign('categoryList', $categoryList);
    $view->display('admin/product/category.phtml');
  }

  public function editAction($cid = 0)
  {
    $view = $this->getView();
    $productModel = $this->getModel('product');
    $fileModel = $this->getModel('file');
    $categoryList = $productModel->getCategoryTree();

    if ($cid === 0 && is_numeric($cid)) {
      //新增类目
      if ($this->isPost()) {
        $set = array(
          'parent_cid'  =>  $_POST['cid'],
          'name'  =>  $_POST['name'],
          'show_on_home'  =>  empty($_POST['show_on_home']) ? 0 : $_POST['show_on_home'],
          'seo_path'  =>  $_POST['seo_path'],
          'seo_keyword' =>  $_POST['seo_keyword'],
          'seo_description' =>  $_POST['seo_description'],
          // 'sort_weight' =>  $_POST['sort_weight'],
          'status'  =>  $_POST['status'],
        );
        if (isset($_FILES['image_path']) && $_FILES['image_path']['tmp_name'] != '')
        { //图片处理
          $fileName = date('Ymd/', REQUEST_TIME) . $_FILES['image_path']['name'];
          $fileContent = file_get_contents($_FILES['image_path']['tmp_name']);
          $file = $fileModel->write('cate_img', $fileName, $fileContent);
          if (isset($file) && $file) {
            $set['file_id'] = $file->file_id;
          }
        }
        //检测路径是否存在
        $checkSeoPath = $productModel->checkCategoryPathIsExists($_POST['seo_path']);
        if ($checkSeoPath && $_POST['seo_path'] != '') {
          setMessage('SEO路径已存在', 'error');
          gotoUrl('admin/category/edit');
        }
        $cid = $productModel->insertCategory($set);
        if ($cid) {
          setMessage('类目添加成功', 'success');
          gotoUrl('admin/category');
        } else {
          setMessage('类目添加失败', 'error');
        }
      }
    } else if (isset($cid) && is_numeric($cid)) {
      //修改类目
      $category = $productModel->getCategory($cid);
      if (!$category) {
        throw new BpfException();
      }
      $cateSubclass = $productModel->getCategories($cid);
      if (is_array($cateSubclass) && !empty($cateSubclass))
      { //检测是否有子类
        $view->assign('cateSubclass', true);
      }
      if ($this->isPost()) {
        $set = array(
          'parent_cid'  =>  $_POST['cid'],
          'name'  =>  $_POST['name'],
          'show_on_home'  =>  empty($_POST['show_on_home']) ? 0 : $_POST['show_on_home'],
          'seo_path'  =>  $_POST['seo_path'],
          'seo_keyword' =>  $_POST['seo_keyword'],
          'seo_description' =>  $_POST['seo_description'],
          // 'sort_weight' =>  $_POST['sort_weight'],
          'status'  =>  $_POST['status'],
        );
        if ($category->parent_cid == 0 && $category->cid == $_POST['cid'] || (is_array($cateSubclass) && !empty($cateSubclass)))
        { //一级类目
          unset($set['parent_cid']);
        }
        if (isset($_FILES['image_path']) && $_FILES['image_path']['tmp_name'] != '')
        { //图片处理
          $fileName = date('Ymd/', REQUEST_TIME) . $_FILES['image_path']['name'];
          $fileContent = file_get_contents($_FILES['image_path']['tmp_name']);
          $file = $fileModel->write('cate_img', $fileName, $fileContent);
          if (isset($file) && $file) {
            $set['file_id'] = $file->file_id;
          }
        }
        if ($category->seo_path != $_POST['seo_path'])
        { //SEO路径处理
          $checkSeoPath = $productModel->checkCategoryPathIsExists($_POST['seo_path']);
          if ($checkSeoPath && $_POST['seo_path'] != '') {
            setMessage('SEO路径已存在', 'error');
            gotoUrl('admin/category/edit/' . $cid);
          }
        } else {
          unset($set['seo_path']);
        }

        $cid = $productModel->updateCategory($category->cid, $set);
        if ($cid) {
          setMessage('类目修改成功', 'success');
          gotoUrl('admin/category');
        } else {
          setMessage('类目修改失败', 'error');
          gotoUrl('admin/category/edit/' . $cid);
        }
      }
      $view->assign('category', $category);
    } else {
      throw new BpfException();
    }

    $view->assign('categoryList', $categoryList);
    $this->addJs('js/plugins/select2/select2.min.js'); //表单美化插件
    $view->display('admin/product/category_edit.phtml');
  }

  // 保存类目顺序
  public function ajaxordersaveAction()
  {
    $result = array('result' => false);
    if ($this->isPost() && $this->isAjax() && isset($_POST['list']) &&
        ($list = json_decode($_POST['list'], true))) {
      $productModel = $this->getModel('product');
      $result['result'] = $productModel->updateCategoriesOrder($list);
    }
    echo json_encode($result);
  }

  public function removeAction($cid)
  {
    if (isset($cid) && is_numeric($cid)) {
      $productModel = $this->getModel('product');
      $cid = $productModel->deleteCategory($cid);
      if ($cid) {
        setMessage('删除操作成功', 'success');
      } else {
        setMessage('删除操作失败', 'error');
      }
      gotoUrl('admin/category');
    } else {
      throw new BpfException();
    }
  }
}
