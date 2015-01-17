<?php /* Smarty version Smarty-3.1.19, created on 2015-01-17 15:04:14
         compiled from "/home/bingo/ttgg/sys/views/default/header.phtml" */ ?>
<?php /*%%SmartyHeaderCode:192572184354ba096e43d8f7-32778239%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '019b89339f1f850a826a8cae663b77818aa7d6d1' => 
    array (
      0 => '/home/bingo/ttgg/sys/views/default/header.phtml',
      1 => 1420512687,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '192572184354ba096e43d8f7-32778239',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'conditions' => 0,
    'channelModel' => 0,
    'head' => 0,
    'channelList' => 0,
    'sa' => 0,
    'tpldir' => 0,
    'keyword' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54ba096e488950_42033042',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54ba096e488950_42033042')) {function content_54ba096e488950_42033042($_smarty_tpl) {?><div class="topheader_bg"></div>

<div class="header_bg">
  <div class="area header">
    <div class="logo"><a href="<?php echo url('');?>
" title="天天逛逛"></a></div>
    <ul class="menut">
<?php $_smarty_tpl->tpl_vars['channelModel'] = new Smarty_variable(BpfCore::getModel('channel'), null, 0);?>
<?php $_smarty_tpl->createLocalArrayVariable('conditions', null, 0);
$_smarty_tpl->tpl_vars['conditions']->value['where']['status'] = "1";?>
<?php $_smarty_tpl->createLocalArrayVariable('conditions', null, 0);
$_smarty_tpl->tpl_vars['conditions']->value['where']['show_on_home'] = "1";?>
<?php $_smarty_tpl->createLocalArrayVariable('conditions', null, 0);
$_smarty_tpl->tpl_vars['conditions']->value['orderby'] = "`weight` DESC";?>
<?php $_smarty_tpl->tpl_vars['channelList'] = new Smarty_variable($_smarty_tpl->tpl_vars['channelModel']->value->getChannels($_smarty_tpl->tpl_vars['conditions']->value), null, 0);?>
      <li <?php if (isset($_smarty_tpl->tpl_vars['head']->value)&&$_smarty_tpl->tpl_vars['head']->value=="home") {?>class="cur"<?php }?>><span class="fl"></span><a href="<?php echo url('');?>
">首页</a></li>
<?php if (isset($_smarty_tpl->tpl_vars['channelList']->value)&&$_smarty_tpl->tpl_vars['channelList']->value) {?>
<?php  $_smarty_tpl->tpl_vars['sa'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['sa']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['channelList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['sa']->key => $_smarty_tpl->tpl_vars['sa']->value) {
$_smarty_tpl->tpl_vars['sa']->_loop = true;
?>
      <li <?php if (isset($_smarty_tpl->tpl_vars['head']->value)&&$_smarty_tpl->tpl_vars['head']->value==$_smarty_tpl->tpl_vars['sa']->value->seo_path) {?>class="cur"<?php }?>><span class="fl"></span><a href="<?php echo url($_smarty_tpl->tpl_vars['sa']->value->link);?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['sa']->value->title, ENT_QUOTES, 'UTF-8', true);?>
</a><?php if ($_smarty_tpl->tpl_vars['sa']->value->seo_path=='yuanwang'||$_smarty_tpl->tpl_vars['sa']->value->seo_path=='choujiang') {?><i class="icon"><img src="<?php echo $_smarty_tpl->tpl_vars['tpldir']->value;?>
/images/new.gif"></i><?php }?></li>
<?php } ?>
<?php }?>
      <li <?php if (isset($_smarty_tpl->tpl_vars['head']->value)&&$_smarty_tpl->tpl_vars['head']->value=="checkin") {?>class="cur"<?php }?>><i class="icon"><img src="<?php echo $_smarty_tpl->tpl_vars['tpldir']->value;?>
/images/new.gif"></i><span class="fl"></span><a href="<?php echo url('user/checkin');?>
">天天签到</a></li>
    </ul>
    <div class="search">
      <form target="_self" action="<?php echo url('search');?>
" method="GET">
        <input type="text" name="keyword" class="txt" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['keyword']->value)===null||$tmp==='' ? '' : $tmp);?>
" autocomplete="off" placeholder="输入搜索宝贝">
        <input type="submit" value="" class="smt">
      </form>
    </div>
  </div>
</div><?php }} ?>
