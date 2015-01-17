<?php /* Smarty version Smarty-3.1.19, created on 2015-01-17 15:04:14
         compiled from "/home/bingo/ttgg/sys/views/default/footer.phtml" */ ?>
<?php /*%%SmartyHeaderCode:208981670754ba096e56d0e7-53415786%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0661a34ec66f1aa3d4495869d373317fc0fb0f3f' => 
    array (
      0 => '/home/bingo/ttgg/sys/views/default/footer.phtml',
      1 => 1420512687,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '208981670754ba096e56d0e7-53415786',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'config' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54ba096e57fd54_41076521',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54ba096e57fd54_41076521')) {function content_54ba096e57fd54_41076521($_smarty_tpl) {?>
<div class="footer" id="foot">
  <div class="footer_main">
      <div class="about">
    <dl>
      <dt>用户帮助</dt>
      <dd><a href="<?php echo url('help.html');?>
" target="_blank">新手上路</a></dd>
      <dd><a href="<?php echo url('help.html#cj');?>
" target="_blank">常见问题</a></dd>
      <dd><a href="<?php echo url('help.html#jf');?>
" target="_blank">签到问题</a></dd>
      <dd><a href="<?php echo url('help.html#jf');?>
" target="_blank">积分问题</a></dd>
    </dl>
    <div class="line"></div>
    <dl>
      <dt>公司信息</dt>
      <dd><a href="<?php echo url('about.html');?>
" target="_blank">关于天天逛逛</a></dd>
      <dd><a href="<?php echo url('contact.html');?>
" target="_blank">联系我们</a></dd>
      <dd><a href="<?php echo url('job.html');?>
" target="_blank">人才招聘</a></dd>
      <dd><a href="<?php echo url('service.html');?>
" target="_blank">服务条款</a></dd>
    </dl>
    <div class="line"></div>
    <dl>
      <dt>下次怎么来？</dt>
      <dd>记住域名：<span class="green" style="font-size:130%">ttgg.com</span></dd>
      <dd>百度搜索：<input type="text" value="天天逛逛" class="bdtxt" style="background:#fff" readonly><a href="http://www.baidu.com/s?wd=%E5%A4%A9%E5%A4%A9%E9%80%9B%E9%80%9B" target="_blank" class="smt" style="background:#fff;border-left:0;margin-top:-3px;padding:4px 0"><i class="icon-search"></i></a></dd>
      <dd>收藏本站：<a href="javascript:;" class="jr" onclick="AddFavorite('天天逛逛', '<?php echo url('',true);?>
')">加入收藏</a></dd>
      <dd><a href="http://list.qq.com/cgi-bin/qf_invite?id=531df4835789cbabe8bd9882100871ebed19e54c7c5faa24" target="_blank">邮件订阅</a></dd>
    </dl>
    <div class="line"></div>
    <dl>
      <dt>获得更新</dt>
      <dd><a href="http://list.qq.com/cgi-bin/qf_invite?id=531df4835789cbabe8bd9882100871ebed19e54c7c5faa24" target="_blank">邮件订阅</a></dd>
     <!--  <dd><a href="#">·点评团QQ空间</a></dd> -->
      <dd><a href="<?php echo url('rssfeed.xml');?>
" target="_blank">RSS订阅</a></dd>
    </dl>
    <div class="line"></div>
    <dl>
      <dt>商户合作</dt>
      <dd><a href="<?php echo url('co.html');?>
" target="_blank">商家合作</a></dd>
    </dl>
    <div class="line"></div>
    <div class="contact">
      <strong>天天逛逛服务号</strong>
      <span></span>
    </div>
    </div>

    <div class="cory"><?php echo $_smarty_tpl->tpl_vars['config']->value->get('footer','');?>
</div>
  </div>
</div>
<!--右侧菜单 start-->
<div class="side-panel right-panel">
  <div class="tab-content">
    <ul>
      <li class="tab-qiandao">
        <a href="<?php echo url('user/checkin');?>
"><i></i></a>
        <div class="tab-tip">
          <a href="<?php echo url('user/checkin');?>
">去签到</a>
        </div>
      </li>
      <li class="tab-fb">
        <a href="javascript:void(0);" class="feedback"><i></i></a>
        <div class="tab-tip">
          <a href="javascript:void(0);" class="feedback">意见反馈</a>
        </div>
      </li>
      <li class="tab-top" style="display:none;">
        <a href="javascript:void(0);"><i></i></a>
        <div class="tab-tip">
          <a href="javascript:void(0);">返回顶部</a>
        </div>
      </li>
    </ul>
  </div>
</div>
<!-- 弹出层 -->
<div id="feedbackContent"></div>
<!--右侧菜单 end-->
<?php echo $_smarty_tpl->tpl_vars['config']->value->get('open.baidu','');?>

<?php echo $_smarty_tpl->tpl_vars['config']->value->get('open.wpaqq','');?>
<?php }} ?>
