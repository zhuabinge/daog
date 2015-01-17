<?php /* Smarty version Smarty-3.1.19, created on 2015-01-17 15:04:14
         compiled from "/home/bingo/ttgg/sys/views/default/before_body.phtml" */ ?>
<?php /*%%SmartyHeaderCode:52834832554ba096e3cf659-80906195%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd6d31d8fbff270be1ce8ee8f7c097f823211add5' => 
    array (
      0 => '/home/bingo/ttgg/sys/views/default/before_body.phtml',
      1 => 1420512687,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '52834832554ba096e3cf659-80906195',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'title' => 0,
    'keywords' => 0,
    'description' => 0,
    'tpldir' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54ba096e439ca0_73655031',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54ba096e439ca0_73655031')) {function content_54ba096e439ca0_73655031($_smarty_tpl) {?><?php if (!is_callable('smarty_function_html_css')) include '/home/bingo/ttgg/lib/smarty_plugins/function.html_css.php';
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="zh-CN">  
<head>
<meta charset="utf-8">
<title><?php echo (($tmp = @$_smarty_tpl->tpl_vars['title']->value)===null||$tmp==='' ? '天天逛逛' : $tmp);?>
</title>
<meta name="keywords" content="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['keywords']->value)===null||$tmp==='' ? '' : $tmp);?>
"/>
<meta name="description" content="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['description']->value)===null||$tmp==='' ? '' : $tmp);?>
"/>
<meta property="qc:admins" content="15420514534653616375" />
<link href="<?php echo $_smarty_tpl->tpl_vars['tpldir']->value;?>
/css/fontawesome/font-awesome.min.css" rel="stylesheet" type="text/css" />
<!--[if IE 7]>
<link rel="stylesheet" href="<?php echo $_smarty_tpl->tpl_vars['tpldir']->value;?>
/css/fontawesome/font-awesome-ie7.min.css">
<![endif]-->
<link href="<?php echo $_smarty_tpl->tpl_vars['tpldir']->value;?>
/css/ttgg_v1.css" rel="stylesheet" type="text/css" />
<link href="<?php echo url('rssfeed.xml',true);?>
" rel="alternate" type="application/rss+xml" title="天天逛逛 RSS Feed">
<?php echo smarty_function_html_css(array(),$_smarty_tpl);?>

<link rel="shortcut icon" type="image/x-icon" href="<?php echo $_smarty_tpl->tpl_vars['tpldir']->value;?>
/images/favicon.ico" media="screen" />
<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['tpldir']->value;?>
/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['tpldir']->value;?>
/js/jquery.easing.1.3.js"></script>
</head>
<?php }} ?>
