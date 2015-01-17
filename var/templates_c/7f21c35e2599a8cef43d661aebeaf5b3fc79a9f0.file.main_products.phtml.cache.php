<?php /* Smarty version Smarty-3.1.19, created on 2015-01-17 15:04:14
         compiled from "/home/bingo/ttgg/sys/views/default/main_products.phtml" */ ?>
<?php /*%%SmartyHeaderCode:20213170854ba096e4de636-45685272%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7f21c35e2599a8cef43d661aebeaf5b3fc79a9f0' => 
    array (
      0 => '/home/bingo/ttgg/sys/views/default/main_products.phtml',
      1 => 1420512687,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '20213170854ba096e4de636-45685272',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'products' => 0,
    'product' => 0,
    'tpldir' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54ba096e5520b0_86478019',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54ba096e5520b0_86478019')) {function content_54ba096e5520b0_86478019($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_price')) include '/home/bingo/ttgg/lib/smarty_plugins/modifier.price.php';
if (!is_callable('smarty_modifier_date_format')) include '/home/bingo/ttgg/lib/smarty/plugins/modifier.date_format.php';
?><div class="dealbox">
<?php if ($_smarty_tpl->tpl_vars['products']->value) {?>
<?php  $_smarty_tpl->tpl_vars['product'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['product']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['products']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['product']->key => $_smarty_tpl->tpl_vars['product']->value) {
$_smarty_tpl->tpl_vars['product']->_loop = true;
?>
  <div class="deal">
    <div class="con">
      <div class="images"><a rel="nofollow" target="_blank" href="<?php echo $_smarty_tpl->tpl_vars['product']->value->link_click;?>
"><img class="lazy" src="<?php echo $_smarty_tpl->tpl_vars['tpldir']->value;?>
/images/ttgg.png" data-original="<?php echo urlStatic($_smarty_tpl->tpl_vars['product']->value->image_path,286,286);?>
" width="284" height="284" alt="<?php echo $_smarty_tpl->tpl_vars['product']->value->title;?>
"/></a></div>
      <div class="title">
        <span>【包邮】</span>
        <a target="_blank" href="<?php echo $_smarty_tpl->tpl_vars['product']->value->link;?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product']->value->title, ENT_QUOTES, 'UTF-8', true);?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product']->value->title, ENT_QUOTES, 'UTF-8', true);?>
</a>
      </div>
      <div class="price">
        <div class="price-left">
<?php echo smarty_modifier_price($_smarty_tpl->tpl_vars['product']->value->sell_price);?>

          <div class="discount">
            <div class="tag">
              <span></span>
              <em>省<?php echo intval($_smarty_tpl->tpl_vars['product']->value->list_price-$_smarty_tpl->tpl_vars['product']->value->sell_price);?>
元</em>
            </div>
            <del>原价: <?php echo floatval($_smarty_tpl->tpl_vars['product']->value->list_price);?>
</del>
          </div>
        </div>
        <div class="price-right">
<?php if ($_smarty_tpl->tpl_vars['product']->value->data=='B') {?>
          <a class="to-tianmao" href="<?php echo $_smarty_tpl->tpl_vars['product']->value->link_click;?>
" target="_blank" rel="nofollow"></a>
<?php } else { ?>
          <a class="to-taobao" href="<?php echo $_smarty_tpl->tpl_vars['product']->value->link_click;?>
" target="_blank" rel="nofollow"></a>
<?php }?>
          <span>已售<?php echo $_smarty_tpl->tpl_vars['product']->value->sellcount;?>
件</span>
        </div>
        <div class="product-buy">
<?php if ($_smarty_tpl->tpl_vars['product']->value->data=='B') {?>
          <a class="btn-buy" href="<?php echo $_smarty_tpl->tpl_vars['product']->value->link_click;?>
" target="_blank" rel="nofollow">
            <span>去天猫抢购</span>
          </a>
<?php } else { ?>
          <a class="btn-buy" href="<?php echo $_smarty_tpl->tpl_vars['product']->value->link_click;?>
" target="_blank" rel="nofollow">
            <span>去淘宝抢购</span>
          </a>
<?php }?>
          <span></span>
        </div>
      </div>
      <div class="bottom-info">
        <div class="lover">
<?php if (isset($_smarty_tpl->tpl_vars['product']->value->user->nickname)) {?>
          <a href="<?php echo urlUser($_smarty_tpl->tpl_vars['product']->value->user->uid);?>
" class="lover-img" target="_blank" title="去逛逛ta的主页">
            <img src="<?php echo urlAvatar($_smarty_tpl->tpl_vars['product']->value->user,50);?>
" width="30" height="30">
            <div class="icon-mask"></div>
          </a>
          <a href="<?php echo urlUser($_smarty_tpl->tpl_vars['product']->value->user->uid);?>
" class="lover-name" target="_blank" title="去逛逛ta的主页"><?php echo (($tmp = @htmlspecialchars(maskString($_smarty_tpl->tpl_vars['product']->value->user->nickname), ENT_QUOTES, 'UTF-8', true))===null||$tmp==='' ? '匿名用户' : $tmp);?>
</a>已购买
<?php } else { ?>
          <span class="lover-img">
            <img src="<?php echo $_smarty_tpl->tpl_vars['tpldir']->value;?>
/images/avatar_50.jpg" width="30" height="30">
            <div class="icon-mask"></div>
          </span>
          <span class="lover-name">匿名用户</span>已购买
<?php }?>
        </div>
        <div class="grade">
          <span class="score"><?php echo $_smarty_tpl->tpl_vars['product']->value->buyerscore;?>
</span>
          <div class="star">
            <div class="color"></div>
            <div class="mask"></div>
          </div>
        </div>
      </div>
<?php if (smarty_modifier_date_format($_smarty_tpl->tpl_vars['product']->value->updated,'Y.m.d')==smarty_modifier_date_format(time(),'Y.m.d')) {?>
      <div class="newicon"></div>
<?php }?>
    </div>
  </div>
<?php } ?>
<?php }?>
</div><?php }} ?>
