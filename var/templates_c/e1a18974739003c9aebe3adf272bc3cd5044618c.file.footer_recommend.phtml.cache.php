<?php /* Smarty version Smarty-3.1.19, created on 2015-01-17 15:04:14
         compiled from "/home/bingo/ttgg/sys/views/default/footer_recommend.phtml" */ ?>
<?php /*%%SmartyHeaderCode:118760229354ba096e55b023-35640754%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e1a18974739003c9aebe3adf272bc3cd5044618c' => 
    array (
      0 => '/home/bingo/ttgg/sys/views/default/footer_recommend.phtml',
      1 => 1420512687,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '118760229354ba096e55b023-35640754',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'hotPros' => 0,
    'hotPro' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54ba096e56b628_99156269',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54ba096e56b628_99156269')) {function content_54ba096e56b628_99156269($_smarty_tpl) {?>
<div class="title_un"><i class="icon_xz"></i><strong>买了的人还想买</strong></div>

<ul class="hot_tuangou pt10">
	<?php if (isset($_smarty_tpl->tpl_vars['hotPros']->value)&&$_smarty_tpl->tpl_vars['hotPros']->value) {?>
	<?php  $_smarty_tpl->tpl_vars['hotPro'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['hotPro']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['hotPros']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['hotPro']->key => $_smarty_tpl->tpl_vars['hotPro']->value) {
$_smarty_tpl->tpl_vars['hotPro']->_loop = true;
?>
	<li>
		<div class="photo">
			<a href="<?php echo $_smarty_tpl->tpl_vars['hotPro']->value->link_click;?>
" target="_blank"><img src="<?php echo urlStatic($_smarty_tpl->tpl_vars['hotPro']->value->image_path,286,286);?>
" width="286" height="286">
			</a>
		</div>
		<div class="name"><a href="<?php echo $_smarty_tpl->tpl_vars['hotPro']->value->link;?>
" target="_blank"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['hotPro']->value->title, ENT_QUOTES, 'UTF-8', true);?>
</a></div>
		<div class="price">
			<div class="fl">
			  <em><b>¥</b><?php echo floatval($_smarty_tpl->tpl_vars['hotPro']->value->sell_price);?>
</em>
			  <del><i>¥</i><?php echo floatval($_smarty_tpl->tpl_vars['hotPro']->value->list_price);?>
</del>
			</div>
			<div class="fr"><?php echo $_smarty_tpl->tpl_vars['hotPro']->value->sellcount;?>
人想买</div>
		</div>
	</li>
	<?php } ?>
	<?php }?>
</ul><?php }} ?>
