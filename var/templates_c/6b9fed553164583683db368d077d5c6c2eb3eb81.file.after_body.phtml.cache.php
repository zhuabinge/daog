<?php /* Smarty version Smarty-3.1.19, created on 2015-01-17 15:04:14
         compiled from "/home/bingo/ttgg/sys/views/default/after_body.phtml" */ ?>
<?php /*%%SmartyHeaderCode:90108854254ba096e5cab94-37505503%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6b9fed553164583683db368d077d5c6c2eb3eb81' => 
    array (
      0 => '/home/bingo/ttgg/sys/views/default/after_body.phtml',
      1 => 1420699920,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '90108854254ba096e5cab94-37505503',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'tpldir' => 0,
    'index' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54ba096e5eed96_60276942',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54ba096e5eed96_60276942')) {function content_54ba096e5eed96_60276942($_smarty_tpl) {?><?php if (!is_callable('smarty_function_html_js')) include '/home/bingo/ttgg/lib/smarty_plugins/function.html_js.php';
?><script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['tpldir']->value;?>
/js/ttgg.js"></script>
<?php if (isset($_smarty_tpl->tpl_vars['index']->value)&&$_smarty_tpl->tpl_vars['index']->value=='index') {?>
<script type="text/javascript">
	//悬停
	var menuStick = function() {
  	if($(window).scrollTop() > 622) {
      $('.left-panel').fadeIn();
      $('.right-panel .tab-top').fadeIn();
    } else {
      $('.left-panel').fadeOut();
      $('.right-panel .tab-top').fadeOut();
    }
  }
  menuStick();
  $(window).scroll(function() {
    menuStick();
  });
</script>

<?php } else { ?>
<script type="text/javascript">
  //左边导航一直显示
  $('.left-panel').css({ 'position':'absolute', 'top':'203px'}).show();
	//悬停
  var menuStick = function() {
    if($(window).scrollTop() > 130) {
      $('.right-panel .tab-top').fadeIn();
      $('.left-panel').css({ 'position':'fixed', 'top':'73px' });
    } else {
      $('.right-panel .tab-top').fadeOut();
      $('.left-panel').css({ 'position':'absolute', 'top':'203px'});
    }
  }
  menuStick();
  $(window).scroll(function() {
    menuStick();
  });
</script>
<?php }?>
<?php echo smarty_function_html_js(array(),$_smarty_tpl);?>

<?php }} ?>
