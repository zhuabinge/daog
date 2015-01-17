$(document).ready(function(){
  // 左侧导航
  $(".box > ul > li").mouseover(function(){
    $(this).addClass("dealon");
  });
  $(".box > ul > li").mouseout(function(){
    $(this).removeClass("dealon");
  });
});

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