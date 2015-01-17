$(document).ready(function(){
  /*头部导航使用*/
  $('.topheader_bg').load('/user/loginInfo');
  $('.topheader_bg').delegate('a.loginPrompt', 'click', function() {
    if ($(this).attr('islogin') == 0) {
      $('#prompt').load('/user/loginLoad');
    } else if ($(this).attr('islogin') == 1) {
      $('#prompt').load('/user/registerLoad');
    };
  });

  /**签到点击关闭**/
  $('.side_checkin > span').click(function() {
    $(this).parent().remove();
  });

  /**顶部左侧导航**/
  $(".navigation > ul > li").mouseover(function() {
    $(this).addClass("dealon");
    $(this).stop().animate({'padding-left' : '10px'}, 100);
  });
  $(".navigation > ul > li").mouseout(function() {
    $(this).removeClass("dealon");
    $(this).stop().animate({'padding-left' : '0'}, 100);
  });

  /**返回顶部**/
  $('.tab-top a').click(function() {
    $('html,body').animate({scrollTop : '0'}, 200);
  });
  
  /**反馈**/
  $('.feedback').click(function() {
    $('#feedbackContent').load('/user/feedback');
  });

  /**首页广告移动**/
  $(".banner_column .banner-socket > img")
  .bind("mouseenter", function(e) {
    $(this).stop().animate({
      left: '-5px'
    },200);
  }).bind("mouseleave", function(e) {
    $(this).stop().animate({
      left: '0'
    },200);
  });

  /**商品框中星级评分**/
  var score = $('.grade .score');
  for(var i = 0; i < score.length; i ++) {
    var colorWidth = parseFloat(($(score[i]).text() / 5) * 70);
    $(score[i]).siblings('.star').find('.color').width(colorWidth);
  }

  /**商品框鼠标进入时显示抢购按钮**/
  $('.dealbox .deal .con').hover(function() {
    $(this).find('.product-buy > span').show();
    $(this).find('.product-buy a.btn-buy').show().animate({ right:'5px' }, 250);
    $(this).find('.price .price-right').hide();
  }, function() {
    $(this).find('.product-buy > span').hide();
    $(this).find('.product-buy a.btn-buy').hide().css('right', '-80px');
    $(this).find('.price .price-right').show();
  });

  /**顶部主导航，以及左右侧导航悬停效果**/
  //鼠标移上导航滑入的标题框
  $('.left-panel .tab-content ul li').hover(function() {
    $(this).find('.tab-tip').show().animate({ left: '34px' }, 250);
    $(this).siblings().find('.tab-tip').hide().css({ left: '60px' });
  }, function() {
    $(this).find('.tab-tip').hide().css({ left: '60px' });
  });
  $('.right-panel .tab-content ul li').hover(function() {
    $(this).find('.tab-tip').show().animate({ right: '28px' }, 250);
    $(this).siblings().find('.tab-tip').hide().css({ right: '60px' });
  }, function() {
    $(this).find('.tab-tip').hide().css({ right: '60px' });
  });
  //最后一个列框去虚线
  $('.left-panel .tab-content ul li:last-child').css({ 'height':'40px', 'border-bottom':'1px solid #ddd' });
  $('.left-panel .tab-content ul li').hover(function () {
    $(this).addClass('dealon');
  }, function() {
    $(this).removeClass('dealon');
  });
});

//输入框点击事件 validate(target, value)，目标表单项与其值
function validate(t, v) {
  var v = $(t).val();
  $(t).focus(function() {
    if($(this).val() == v) {
      $(this).val('');
    }
    $(this).removeClass('error').siblings().removeClass('yes');
  });
  $(t).blur(function() {
    if($(this).val() == '' || $(this).val() == v) {
      $(this).val(v).addClass('error').siblings().addClass('no');  
    } else {
      $(this).siblings().removeClass('no').addClass('yes');
    }
  });
}

function AddFavorite(title, url) {
 try {
   window.external.addFavorite(url, title);
 } catch (e) {
  try {
    window.sidebar.addPanel(title, url, "");
  } catch (e1) {
     alert("抱歉，您所使用的浏览器无法完成此操作。\n\n加入收藏失败，请进入新网站后使用Ctrl+D进行添加");
   }
 }
}

var dialog = function(content) {
  return new function() {
    var dEl = $([
      '<div class="mui-dialog">',
      '<div class="messageDiv">',
      '<div class="messageDiv_top"><a href="javascript:;" class="close"></a></div>',
      '<div class="messageDiv_content"></div>',
      '<div class="messageDiv_bottom"></div>',
      '</div>',
      '<div class="messageBg"></div>',
      '</div>'
    ].join('\n'));
    var callbackClose = null;
    this.open = function(cbOpen, cbClose) {
      $(document.body).append(dEl);
      dEl.show();
      if (typeof cbOpen === 'function') {
        cbOpen();
      }
      callbackClose = cbClose;
    };
    this.close = function() {
      if (typeof callbackClose === 'function') {
        callbackClose();
      }
      dEl.remove();
    };
    var dMessage = dEl.children('div.messageDiv');
    dMessage.find('> div.messageDiv_content').html(content);
    dMessage.find('> div.messageDiv_top > a.close').click(this.close);
    dMessage.find('> div.messageDiv_content > .close').click(this.close);
  };
};