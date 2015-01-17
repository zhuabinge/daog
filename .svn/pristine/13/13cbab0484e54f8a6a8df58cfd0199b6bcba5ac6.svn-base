<?php
class AdminCommentController extends BpfController
{
  public static function __permissions()
  {
    return array(
      'name' => '评论管理',
      'permissions' => array(
        'comment-view' => '查询评论',
        'comment-reply' => '回复评论',
        'comment-delete' => '删除评论',
        'comment-audit' => '审核评论',
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
     $rows=15;//设置一页显示的行数
    $commentModel = $this->getModel('comment');
    $proModel = $this->getModel('product');
    $userModel = $this->getModel('user');
    if (isset($_GET['page'])) {
      $page = $_GET['page'];
    } else {
      $page = 1;
    }
    if(isset($_GET['sort'])) {
      $sort = $_GET['sort'];
    } else {
      $sort = "comTime";//默认的排序
    }
    if ($sort == "comTime") {
      $comments = $commentModel->getComments(null, $page, $rows);//查询数据库
    } else {
      $comments = $commentModel->getComments(array('orderby' =>'last_reply_time' ), $page, $rows);
    }
    if (!empty($comments)) {
      foreach ($comments as $comment) {
        $pid[] = $comment->pid;
        $uid[] = $comment->uid;
        $cids[] = $comment->cid;
      }
    }
    if (!empty($pid) && !empty($uid) && !empty($cids)) {
      // $products = $proModel->getProductsByIds($pid);
      $critics = $userModel->getUsersByIds($uid);
    }
    $total = $commentModel->getCommentsCount();
    $view = $this->getView();
    $view->assign('critics', isset($critics) ? $critics : '');
    // $view->assign('products', isset($products) ? $products : '');
    $view->assign('comments', $comments);
    $view->assign('option', $sort);
    $view->assign('total', $total);
    $view->assign('rows', $rows);
    $view->assign('page', $page);
    $view->assign('seakey', null);
    $view->assign('actionname', 'index');
    $view->assign('replyInfo', isset($replyInfo) ? $replyInfo : '');
    $this->addJs('js/plugins/select2/select2.min.js'); //下拉框插件
    $view->display('admin/comment/comment.phtml');
  }

  public function searchAction()
  {
    $rows=15;
    $commentModel = $this->getModel('comment');
    $proModel = $this->getModel('product');
    $userModel = $this->getModel('user');
    $view = $this->getView();
    if (isset($_GET['keyword']) && $_GET['keyword']!='') {
      $keyword = $_GET['keyword'];
    } else {
      gotoUrl('admin/comment');
    }
    if (isset($_GET['page'])) {
      $page = $_GET['page'];
    } else {
      $page = 1;
    }
    if (isset($_GET['sort'])) {
      $sort = $_GET['sort'];
    } else {
      $sort = "comTime";
    }
    $condition = array('search' =>$keyword);
    $total = $commentModel->getCommentsCount($condition);//get the user sum
    //get all the user info
    if ($sort == "comTime") {
      $comments = $commentModel->getComments($condition, $page, $rows);
    } else {
      $comments = $commentModel->getComments(array('orderby' =>'username','search' =>$keyword), $page, $rows);
    }
    if (!empty($comments)) {
      foreach ($comments as $comment) {
        $pid[] = $comment->pid;
        $uid[] = $comment->uid;
      }
    }
    if (!empty($pid)&& !empty($uid)) {
      $products = $proModel->getProductsByIds($pid);
      $critics = $userModel->getUsersByIds($uid);
    }
    $view->assign('critics', isset($critics) ? $critics : '');
    $view->assign('products', isset($products) ? $products : '');
    $view->assign('comments', $comments);
    $view->assign('option', $sort);
    $view->assign('total', $total);
    $view->assign('rows', $rows);
    $view->assign('curPage', $page);
    $view->assign('actionname','search');
    $view->assign('seakey', $keyword);
    $view->display('admin/comment/comment.phtml');
  }
  //特定的某个用户的评论列表
  public function usercomAction()
  {
    if (isset($_GET['id']) && trim($_GET['id']!='')) {
      $userid = $_GET['id'];
    }
    $rows=15;//设置一页显示的行数
    $commentModel = $this->getModel('comment');
    $proModel = $this->getModel('product');
    $userModel = $this->getModel('user');
    if (isset($_GET['page'])) {
      $page = $_GET['page'];
    } else {
      $page = 1;
    }
    if(isset($_GET['sort'])) {
      $sort = $_GET['sort'];
    } else {
      $sort = "comTime";//默认的排序
    }
    if ($sort == "comTime") {
      $comments = $commentModel->getComments(array('uid' => $userid, ), $page, $rows);//查询数据库
    } else {
      $comments = $commentModel->getComments(array('orderby' =>'last_reply_time', 'uid' => $userid,), $page, $rows);
    }
    if (!empty($comments)) {
      foreach ($comments as $comment) {
        $pid[] = $comment->pid;
        $uid[] = $comment->uid;
        $cids[] = $comment->cid;
      }
    }
    if (!empty($pid) && !empty($uid) && !empty($cids)) {
      $products = $proModel->getProductsByIds($pid);
      $critics = $userModel->getUsersByIds($uid);
    }
    $total = $commentModel->getCommentsCount(array('uid' => $userid,));
    $view = $this->getView();
    $view->assign('critics', isset($critics) ? $critics : '');
    $view->assign('products', isset($products) ? $products : '');
    $view->assign('comments', $comments);
    $view->assign('option', $sort);
    $view->assign('total', $total);
    // var_dump($total);
    // var_dump($comments);
    // exit();
    $view->assign('rows', $rows);
    $view->assign('page', $page);
    $view->assign('seakey', null);
    $view->assign('userid', $userid);
    $view->assign('actionname', 'usercom');
    $view->assign('replyInfo', isset($replyInfo) ? $replyInfo : '');
    $view->display('admin/comment/comment.phtml');

  }

  public function deleteAction()
  {
    $commentModel = $this->getModel('comment');
    if (isset($_GET['id']) && trim($_GET['id'])!='') {
      $deRst = $commentModel->deleteComment(trim($_GET['id']));
      if ($deRst!=0) {
        setMessage('删除成功', 'success');
      } else {
        setMessage('删除失败','error');
      }
      if (isset($_GET['action']) && trim($_GET['action'])!='') {
        gotoUrl('admin/comment/audit?id=' . trim($_GET['rid']));
      }
    }
    gotoUrl('admin/comment/');
  }

  public function editAction()
  {
    $commentModel = $this->getModel('comment');
    $proModel = $this->getModel('product');
    $userModel = $this->getModel('user');
    if (isset($_POST['comcid']) && trim($_POST['comcid'])!='' && isset($_POST['status'])) {
      $status = ($_POST['status'] == 1) ? 'pass' : 'delect';
      $commentModel->updateComment(trim($_POST['comcid']), $status);
      setMessage('保存成功', 'success');
      gotoUrl('admin/comment/');
    }
    if (isset($_GET['id']) && trim($_GET['id'])!='') {
      $id = trim($_GET['id']);
    } else {
      gotoUrl('admin/comment/');//exception
    }
    $comment = $commentModel->getComment($id);

    if (!$comment) {//id not exit in db
      //gotoUrl('admin/comment/');//exception
      throw new BpfException();
    }
    // var_dump($comment);
    if (isset($comment->replys) && is_array($comment->replys)) {
      foreach ($comment->replys as $value) {
        $replysIds[] = $value->uid;
      }
      $replyser = $userModel->getUsersByIds($replysIds);
      // var_dump($replyser);
    }

    $product = $proModel->getProductsByIds(array($comment->pid));
    $critic = $userModel->getUsersByIds(array($comment->uid));

    // exit();
    $view = $this->getView();
    $view->assign('comment', $comment);
    $view->assign('replyser', isset($replyser) ? $replyser : '');
    $view->assign('product', array_values($product)[0]);
    $view->assign('critic', array_values($critic)[0]);
    $view->display('admin/comment/comment_edit.phtml');
  }

  public function replyAction()
  {
    $commentModel = $this->getModel('comment');
    if (isset($_POST['replycontent']) && trim($_POST['replycontent'])!='' && isset($_POST['cid']) && trim($_POST['cid']!='')) {//回复的post请求参数
      $comment = $commentModel->getComment(trim($_POST['cid']));
      $commentData = array('body' => trim($_POST['replycontent']), 'pid'=>$comment->pid, 'uid'=>$GLOBALS['user']->uid, 'status'=>1, 'reply_cid'=>trim($_POST['cid']));
      $commentModel->insertComment($commentData);
      setMessage('操作成功', 'success');
    } else {
      throw new BpfException();
    }
  }

  public function batchAction()
  {
    $batchtid = $_POST['cid'];
    if ($this->isPost()) {
      if (empty($batchtid) || !is_array($batchtid)) {
        setMessage('操作失败，请重新选择', 'error');
        gotoUrl('admin/comment');
      } else if (is_array($batchtid) && $_POST['status'] != -1) {
        $commentModel = $this->getModel('comment');
        if ($_POST['status'] == 1 || $_POST['status'] == 0) {//status
          $status = ($_POST['status'] == 1) ? 'pass' : 'delect';
          $commentModel->updateComments($batchtid, $status);
        } else if ($_POST['status'] == 2) {//delete
          $commentModel->deleteComments($batchtid);
        }
        setMessage('批量操作成功', 'success');
        gotoUrl('admin/comment/');
      }
    } else {
      throw new BpfException();
    }
  }
}