<?php
class AdminTagController extends BpfController
{
  public static function __permissions()
  {
    return array(
      'name' => '商品标签管理',
      'permissions' => array(
        'tag-view' => '查询标签',
        'tag-edit' => '编辑标签',
        'tag-delete' => '删除标签',
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

  public function indexAction($page = 1)
  {
    $view = $this->getView();
    $tagModel = $this->getModel('tag');
    $rows = 15;
    $conditions = array( //过滤条件
      'search' =>  isset($_GET['title']) ? $_GET['title'] : '',
    );
    $tagsCount = $tagModel->getTagsCount($conditions);
    $tagsList = $tagModel->getTags($conditions, $page, $rows);
    $totalPage = ($tagsCount / $rows);
    if ($tagsCount % $rows > 0 ) {
      $totalPage++;
    }
    if ($page != 1 && $page > $totalPage) { //溢出处理
      $title = isset($_GET['title']) ? '?title=' . $_GET['title'] : '';
      gotoUrl('admin/tag/index/1' . $title);
    }

    $this->addJs('js/plugins/select2/select2.min.js'); //下拉框插件
    $view->assign('page', is_numeric($page) ? $page : 1);
    $view->assign('rows', $rows);
    $view->assign('count', $tagsCount);
    $view->assign('tagsList', $tagsList);
    $view->display('admin/product/tag.phtml');
  }

  public function editAction($tid = 0)
  {
    $view = $this->getView();
    $tagModel = $this->getModel('tag');
    if ($tid === 0 && is_numeric($tid)) {
      // 添加标签
      if ($this->isPost()) {
        if (isset($_POST['title']) && $_POST['title'] == '') {
          setMessage('标签输入错误，请重试', 'error');
          gotoUrl('admin/tag/edit');
        }
        $set = array(
          'title' =>  $_POST['title'],
          'status'  =>  $_POST['status'],
        );
        $tids = $tagModel->insertTag($set);
        if ($tids) {
          setMessage('标签添加成功', 'success');
          gotoUrl('admin/tag');
        } else {
          setMessage('标签添加失败', 'error');
          gotoUrl('admin/tag/edit');
        }
      }
    } else if (isset($tid) && is_numeric($tid)) {
      // 修改标签
      $tags = $tagModel->getTag($tid);
      if (!$tags) {
        throw new BpfException();
      }
      if ($this->isPost()) {
        if (isset($_POST['title']) && $_POST['title'] == '') {
          setMessage('标签输入错误，请重试', 'error');
          gotoUrl('admin/tag/edit');
        }
        $set = array(
          'title' =>  $_POST['title'],
          'status'  =>  $_POST['status'],
        );
        $tids = $tagModel->updateTag($tid, $set);
        if ($tids) {
          setMessage('标签修改成功', 'success');
          gotoUrl('admin/tag');
        } else {
          setMessage('标签修改失败', 'error');
          gotoUrl('admin/tag/edit/' . $tid);
        }
      }
      $view->assign('tags', $tags);
    } else {
      throw new BpfException();
    }
    $view->display('admin/product/tag_edit.phtml');
  }

  public function removeAction($tid)
  {
    if (isset($tid) && is_numeric($tid)) {
      $tagModel = $this->getModel('tag');
      $tids = $tagModel->deleteTag($tid);
      if ($tids) {
        setMessage('删除操作成功', 'success');
      } else {
        setMessage('删除操作失败', 'error');
      }
      gotoUrl('admin/tag');
    } else {
      throw new BpfException();
    }
  }

  // 批量操作状态
  public function batchAction()
  {
    $batchtid = $_POST['tid'];
    if ($this->isPost()) {
      if (empty($batchtid) || !is_array($batchtid)) {
        setMessage('操作失败，请重试', 'error');
        gotoUrl('admin/tag');
      } else if (is_array($batchtid) && is_numeric($_POST['status'])) {
        $tagModel = $this->getModel('tag');
        $status = $_POST['status'] == 1 ? 'pass' : 'delect';
        $tagModel->updateTags($batchtid, $status);
        $page = '';
        if (is_numeric($_POST['page'])) {
          $page = '/index/' . $_POST['page'];
        }
        setMessage('批量操作成功', 'success');
        gotoUrl('admin/tag' . $page);
      }
    } else {
      throw new BpfException();
    }
  }
}
