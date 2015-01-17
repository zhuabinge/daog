<?php /* Smarty version Smarty-3.1.19, created on 2015-01-17 15:04:14
         compiled from "/home/bingo/ttgg/sys/views/default/index.phtml" */ ?>
<?php /*%%SmartyHeaderCode:115403931354ba096e37d0d6-46770782%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0a4fe34768e442afad9f6c5dee074b439e77dffa' => 
    array (
      0 => '/home/bingo/ttgg/sys/views/default/index.phtml',
      1 => 1420512687,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '115403931354ba096e37d0d6-46770782',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'cates' => 0,
    'cate' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54ba096e3cd082_25183736',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54ba096e3cd082_25183736')) {function content_54ba096e3cd082_25183736($_smarty_tpl) {?><?php if (!is_callable('smarty_function_html_ad')) include '/home/bingo/ttgg/lib/smarty_plugins/function.html_ad.php';
if (!is_callable('smarty_function_html_pagination')) include '/home/bingo/ttgg/lib/smarty_plugins/function.html_pagination.php';
?><?php echo $_smarty_tpl->getSubTemplate ('before_body.phtml', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 9999, null, array('title'=>'天天逛逛独家优惠，天天送集分宝9.9包邮20元封顶秒杀在天天逛逛','keywords'=>'天天逛逛官网,积分签到,天天逛逛独家优惠,天逛网,天天逛逛天天特价,9.9包邮,20元封顶,ttgg','description'=>'汇集独家特约【淘宝网2-5折商品】，以天天有九块九包邮超值宝贝著名。价格足够低，先到先得。找独家淘宝天猫商城折扣秒杀，请到天天逛逛。【9.9包邮 天天有】'), 0);?>

<body>
<?php echo $_smarty_tpl->getSubTemplate ('header.phtml', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 9999, null, array('head'=>"home"), 0);?>

<div class="banner-area">
  <div class="big-banner">
<?php if (isset($_smarty_tpl->tpl_vars['cates']->value)&&$_smarty_tpl->tpl_vars['cates']->value) {?>
    <!--顶部左侧主导航 start-->
    <div class="navigation">
      <ul>
<?php  $_smarty_tpl->tpl_vars['cate'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['cate']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['cates']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['cate']->key => $_smarty_tpl->tpl_vars['cate']->value) {
$_smarty_tpl->tpl_vars['cate']->_loop = true;
?>
        <li><a href="<?php echo $_smarty_tpl->tpl_vars['cate']->value->link;?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['cate']->value->name, ENT_QUOTES, 'UTF-8', true);?>
"><i class="<?php echo $_smarty_tpl->tpl_vars['cate']->value->seo_path;?>
"></i><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['cate']->value->name, ENT_QUOTES, 'UTF-8', true);?>
</a></li>
<?php } ?>
      </ul>
    </div>
    <!--顶部左侧主导航 end-->
<?php }?>
    <!--首页顶部广告栏目 start-->
    <div class="fr">
      <div class="banner_column">
        <div class="fl flw1">
          <?php echo smarty_function_html_ad(array('id'=>"home-1",'width'=>"495",'height'=>"260",'class'=>"zs",'target'=>"1"),$_smarty_tpl);?>

          <?php echo smarty_function_html_ad(array('id'=>"home-2",'width'=>"250",'height'=>"240",'class'=>"zsb",'target'=>"1"),$_smarty_tpl);?>

          <?php echo smarty_function_html_ad(array('id'=>"home-3",'width'=>"250",'height'=>"240",'class'=>"zsb",'target'=>"1"),$_smarty_tpl);?>

        </div>
        <div class="fl">
          <?php echo smarty_function_html_ad(array('id'=>"home-4",'width'=>"245",'height'=>"500",'class'=>"zrsx",'target'=>"1"),$_smarty_tpl);?>

        </div>
        <div class="fl flw3">
          <?php echo smarty_function_html_ad(array('id'=>"home-5",'width'=>"250",'height'=>"260",'class'=>"rs",'target'=>"1"),$_smarty_tpl);?>

          <?php echo smarty_function_html_ad(array('id'=>"home-6",'width'=>"250",'height'=>"240",'class'=>"rsb",'target'=>"1"),$_smarty_tpl);?>

        </div>
      </div>
    </div>
    <!--首页顶部广告栏目 end-->
  </div>
</div>
<div class="area">
  <div class="title_un">
    <i class="icon_start"></i><strong>每日特惠推荐</strong>
  </div>
  <!--产品列表 start-->
  <?php echo $_smarty_tpl->getSubTemplate ('main_products.phtml', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 9999, null, array(), 0);?>

  <!--产品列表 end-->
  <!--分页 start-->
  <div class="newPage clearbox tc">
	  <?php ob_start();?><?php echo url('?page=%page%');?>
<?php $_tmp1=ob_get_clean();?><?php echo smarty_function_html_pagination(array('page'=>((string)$_smarty_tpl->tpl_vars['page']->value),'rows'=>((string)$_smarty_tpl->tpl_vars['rows']->value),'count'=>((string)$_smarty_tpl->tpl_vars['count']->value),'prev'=>"<i class='icon-chevron-left'></i>",'next'=>"<i class='icon-chevron-right'></i>",'url'=>$_tmp1,'showinfo'=>"0"),$_smarty_tpl);?>

	</div>
  <!--分页 end-->
  <!--推荐的产品 start-->
  <?php echo $_smarty_tpl->getSubTemplate ('footer_recommend.phtml', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 9999, null, array(), 0);?>

  <!--推荐的产品 end-->
</div>
<?php echo $_smarty_tpl->getSubTemplate ('footer.phtml', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 9999, null, array(), 0);?>

<!--页面左侧导航 start-->
<div class="side-panel left-panel">
  <div class="tab-content">
    <ul>
      <li class="tab-index cur">
        <a href="<?php echo url('');?>
"><i></i></a>
        <div class="tab-tip">
          <a href="<?php echo url('');?>
">首页</a>
        </div>
      </li>
<?php if (isset($_smarty_tpl->tpl_vars['cates']->value)&&$_smarty_tpl->tpl_vars['cates']->value) {?>
<?php  $_smarty_tpl->tpl_vars['cate'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['cate']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['cates']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['cate']->key => $_smarty_tpl->tpl_vars['cate']->value) {
$_smarty_tpl->tpl_vars['cate']->_loop = true;
?>
      <li>
        <a href="<?php echo $_smarty_tpl->tpl_vars['cate']->value->link;?>
"><i class="<?php echo $_smarty_tpl->tpl_vars['cate']->value->seo_path;?>
"></i></a>
        <div class="tab-tip">
          <a href="<?php echo $_smarty_tpl->tpl_vars['cate']->value->link;?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['cate']->value->name, ENT_QUOTES, 'UTF-8', true);?>
</a>
        </div>
      </li>
<?php } ?>
<?php }?>
    </ul>
  </div>
</div>
<!--页面左侧导航 end-->
<?php echo $_smarty_tpl->getSubTemplate ('after_body.phtml', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 9999, null, array(), 0);?>

</body>
</html><?php }} ?>
