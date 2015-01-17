<?php /* Smarty version Smarty-3.1.19, created on 2015-01-17 15:04:14
         compiled from "/home/bingo/ttgg/sys/views/default/user/login_info.phtml" */ ?>
<?php /*%%SmartyHeaderCode:176444712854ba096ec80820-10197397%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '00b47bb7c21369f763e8ec93d9b810ab90980ed0' => 
    array (
      0 => '/home/bingo/ttgg/sys/views/default/user/login_info.phtml',
      1 => 1420512687,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '176444712854ba096ec80820-10197397',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'account' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54ba096ec9f4a1_51535613',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54ba096ec9f4a1_51535613')) {function content_54ba096ec9f4a1_51535613($_smarty_tpl) {?>  <div class="topheader area">
    <div class="fr">
      您好，欢迎来到天天逛逛！
<?php if (!isLogin()) {?>
      [ <a class="blue loginPrompt" islogin="0" href="javascript:;" rel="nofollow">马上登录</a> | <a class="blue loginPrompt" islogin="1" href="javascript:;" rel="nofollow">快速注册</a> ]
      <!--登录注册弹出框 start--><span id="prompt"></span><!--登录注册弹出框 end-->
      <a href="javascript:;" class="loginPrompt" islogin="0" rel="nofollow">我的积分</a>
      | <a href="javascript:;" class="loginPrompt" islogin="0" rel="nofollow">个人中心</a>
<?php } else { ?>
      [ <a class="blue" href="<?php echo url('user/center');?>
" rel="nofollow"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['account']->value->nickname, ENT_QUOTES, 'UTF-8', true);?>
</a> | <a class="blue" href="<?php echo url('user/logout');?>
" rel="nofollow">退出登录</a> 
<?php if (isAdmin()) {?>
      | <a class="blue" href="<?php echo url('admin');?>
" target="_blank">管理后台</a>
<?php }?> ]
      <a href="<?php echo url('user/score');?>
" target="_blank" rel="nofollow">我的积分</a>
      | <a href="<?php echo url('user/likes');?>
" target="_blank" rel="nofollow">个人中心</a>
<?php }?>
      | <a href="<?php echo url('help.html');?>
" target="_blank" rel="nofollow">帮助中心</a>
      | <a rel="sidebar" href="javascript:;" onclick="AddFavorite('天天逛逛', '<?php echo url('',true);?>
')" rel="nofollow">收藏</a>
    </div>
  </div><?php }} ?>
