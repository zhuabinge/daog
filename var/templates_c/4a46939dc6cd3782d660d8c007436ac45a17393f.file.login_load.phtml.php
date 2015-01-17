<?php /* Smarty version Smarty-3.1.19, created on 2015-01-17 15:04:19
         compiled from "/home/bingo/ttgg/sys/views/default/user/login_load.phtml" */ ?>
<?php /*%%SmartyHeaderCode:130589911254ba0973a8fc63-74056626%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4a46939dc6cd3782d660d8c007436ac45a17393f' => 
    array (
      0 => '/home/bingo/ttgg/sys/views/default/user/login_load.phtml',
      1 => 1420512687,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '130589911254ba0973a8fc63-74056626',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'login' => 0,
    'username' => 0,
    'loginMsg' => 0,
    'captchError' => 0,
    'userError' => 0,
    'msg' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54ba0973b11af4_84576768',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54ba0973b11af4_84576768')) {function content_54ba0973b11af4_84576768($_smarty_tpl) {?><?php if (!is_callable('smarty_function_html_ad')) include '/home/bingo/ttgg/lib/smarty_plugins/function.html_ad.php';
?><!--透明遮罩 start-->
<div class="messageBg" style="display:none;"></div>
<!--透明遮罩 end-->
<div class="login_main login-type1" style="display:none;">
  <div class="login_top"></div>
  <div class="close"></div>
  <div class="login_content">
    <div class="loginlt">
      <?php echo smarty_function_html_ad(array('id'=>"login-1",'width'=>"370",'height'=>"380",'target'=>"1"),$_smarty_tpl);?>

    </div>
    <form id="login" method="POST" autocomplete="off" <?php if (!isset($_smarty_tpl->tpl_vars['login']->value)) {?> style="display:none" <?php }?>>
      <div class="loginrt">
        <ul>
          <li>
            <div class="grey"><span class="user"><i></i></span><input type="text" value="<?php echo $_smarty_tpl->tpl_vars['username']->value;?>
" name="username"placeholder="用户名" class="text"></div>
          </li>
          <li>
            <div class="grey"><span class="lock"><i></i></span><input type="password"  name="password" placeholder="密码" class="text"></div>
          </li>
          <li><label><input class="ck" type="checkbox" checked="checked">下次自动登入</label><a href="<?php echo url('user/forgot');?>
">忘记密码？</a></li>
          <li><input type="submit" class="btn-green-ttgg height46 btn-from" value="立即登陆"></li>
          <li class="hezuow">用合作网站账号登录</li>
          <li><a href="<?php echo url('user/qqLogin');?>
" class="btn-green-ttgg height46"><i></i>QQ登陆</a></li>
        </ul>
      </div>
    </form>

    <form id="regist" method="POST" autocomplete="off" <?php if (isset($_smarty_tpl->tpl_vars['login']->value)) {?> style="display:none" <?php }?>>
      <div class="loginrt">
        <ul>
          <li>
            <div class="grey"><span class="user"><i></i></span><input type="text" value="" name="username" placeholder="用户名" class="text"></div>
          </li>
          <li>
            <div class="grey"><span class="lock"><i></i></span><input type="password" value="" name="password" placeholder="密码" class="text"></div>
          </li>
          <li>
            <div class="grey"><span class="lock"><i></i></span><input type="password" value="" name="repassword" placeholder="确认密码" class="text"></div>
          </li>
          <li>
            <div class="grey"><span class="email"><i></i></span><input type="text" value="" name="email" placeholder="邮箱" class="text"></div>
          </li>
          <li>
            <div class="grey capcha"><input type="text" name="captcha" placeholder="验证码" class="text"></div>
            <a href="#" class="fr"><img src="<?php echo url('user/captcha');?>
" onclick="this.src='<?php echo url('user/captcha');?>
?_=' + Math.random()" width="150" height="38"></a>
            <div class="clearbox"></div>
          </li>
          <li class="pt5"><label><input class="ck" type="checkbox" checked="checked">我已阅读并同意使用天天逛逛的<a target="_blank" href="<?php echo url('service.html');?>
">《服务条款》</a></label></li>
          <li><input type="submit" class="btn-green-ttgg height46 btn-from" value="快速注册"></li>
        </ul>
      </div>
    </form>
  </div>
  <div class="login_bottom"><?php if (isset($_smarty_tpl->tpl_vars['login']->value)) {?> <a href="javascript:;" islogin="1">快速注册></a> <?php } else { ?> <a href="javascript:;" islogin="0">直接登录></a> <?php }?></div>
</div>
<script type="text/javascript">
/**登录注册框显示转场特效**/
$(function() {
  $('#prompt .messageBg').show();
  $('.login_main').show().animate({
    'top':'50%',
    'left':'50%'
  }, 800, 'easeOutExpo');
});

<?php if (isset($_smarty_tpl->tpl_vars['loginMsg']->value)) {?>
    appendMsg($('#login').find("input[name='username']"), '<?php echo $_smarty_tpl->tpl_vars['loginMsg']->value;?>
', 'error');
<?php }?>
<?php if (isset($_smarty_tpl->tpl_vars['captchError']->value)) {?>
    appendMsg($('#regist').find("input[name='captcha']"), '<?php echo $_smarty_tpl->tpl_vars['captchError']->value;?>
', 'error');
<?php }?>
<?php if (isset($_smarty_tpl->tpl_vars['userError']->value)) {?>
    appendMsg($('#regist').find("input[name='username']"), '<?php echo $_smarty_tpl->tpl_vars['userError']->value;?>
', 'error');
<?php }?>
<?php if (isset($_smarty_tpl->tpl_vars['msg']->value)) {?>
  dialog('<?php echo $_smarty_tpl->tpl_vars['msg']->value;?>
').open();
<?php }?>

function isEmpy(str) {
  if ( str == '' || str == null) return true;
  var regu = '^[ ]+$';
  var re = new RegExp(regu);
  return re.test(str);
}
function isEmail(str){
  var regu = '^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$';
  var re = new RegExp(regu);
  return re.test(str);
}
function appendMsg(object, msg, type){
  if (msg !== '') {
    object.parent().parent().append('<div class="org from-msg">' + msg + '</div>');
  }
  if (type === 'error') {
    object.after('<em class="cha from-msg"></em>');
  } else if (type === 'success') {
    object.after('<em class="dui from-msg"></em>');
  }
}

$(function() {
  $('.login_bottom > a').click(function() {
    if ($(this).attr('islogin') == 0) {
      $('#prompt').load('/user/loginLoad');
    } else if ($(this).attr('islogin') == 1) {
      $('#prompt').load('/user/registerLoad');
    };
  });
  $('.login_main > div.close').click(function() {
    $('.messageBg').remove();
    $(this).parent().remove();
  });
  $('#login').submit(function(e) {
    var from = $(this), error = false,
    username = from.find("input[name='username']"),
    password = from.find("input[name='password']");
    $('.from-msg').remove();
    if (isEmpy(username.val())) {
      appendMsg(username, '用户名不能为空', 'error');
      error = true;
    }
    if (isEmpy(password.val())) {
      appendMsg(password, '密码不能为空', 'error');
      error = true;
    }
    if (error){
      return false;
    } else {
      $.post('/user/loginLoad', { username: username.val(), password: password.val() }, function(data) {
        var data = data.msg;
        if (data == -1) {
          appendMsg(password, '用户名或者密码错误，请重试！', 'error');
        } else if (data == 1) {
          $('.topheader_bg').load('/user/loginInfo');
        } else {
          appendMsg(password, '用户名或者密码错误，请重试！', 'error');
        }
      }, 'json');
      return false;
    }
  });

  $('#regist').submit(function() {
    var from = $(this), error = false,
    username = from.find("input[name='username']"),
    email = from.find("input[name='email']"),
    captcha = from.find("input[name='captcha']"),
    repassword = from.find("input[name='repassword']"),
    password = from.find("input[name='password']");
    $('.from-msg').remove();
    if (isEmpy(username.val())) {
      appendMsg(username, '用户名不能为空', 'error');
      error = true;
    } else if(!username.val().match(/\w{0,16}/)) {
        appendMsg(username, '用户名只能由字母或下划线或数字组合哦,最长只能16位', 'error');
        error = true;
    } else {
        appendMsg(username, '', 'success');
    }

    if (isEmpy(password.val())) {
      appendMsg(password, '密码不能为空', 'error');
      error = true;
    } else if (!password.val().match(/\w{6,20}/)) {
      appendMsg(password, '只能由6~20位的字母或下划线或数字组合', 'error');
      error = true;
    } else {
      appendMsg(password, '', 'success');
      if (password.val() === repassword.val()) {
        appendMsg(repassword, '', 'success');
      } else {
        appendMsg(repassword, '两次密码不一样', 'error');
        error = true;
      }
    }

    if (!isEmpy(email.val()) && !isEmail(email.val())){
      appendMsg(email, '邮箱地址格式不正确', 'error');
      error = true;
    }

    if (isEmpy(captcha.val())) {
      appendMsg(captcha, '验证码不能为空', 'error');
      error = true;
    }
    if (error){
      return false;
    } else {
      $.post('/user/registerLoad', { username: username.val(), email: email.val(), captcha: captcha.val(), repassword: repassword.val(), password: password.val() }, function(data) {
        var data = data.msg;
        if (data == -2) {
          appendMsg(captcha, '验证码输入错误，请重试！', 'error');
        } else if (data == -1) {
          appendMsg(repassword, '用户名或者密码错误，请重试！', 'error');
        } else if (data == -3) {
          appendMsg(username, '用户名已经被注册，请重试！', 'error');
        } else if (data == 1) {
          $('.topheader_bg').load('/user/loginInfo');
        } else {
          appendMsg(repassword, '用户名或者密码错误，请重试！', 'error');
        }
      }, 'json');
      return false;
    }
  });
});

</script><?php }} ?>
