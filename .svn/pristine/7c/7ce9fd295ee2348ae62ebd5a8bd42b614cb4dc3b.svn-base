document.write('<script type="text/javascript" src="/static/default/js/plugins/cookie/jquery.cookie.js"></script>'); //cookie插件
document.write('<script type="text/javascript" src="/static/default/js/plugins/bootbox/bootbox.min.js"></script>'); //提示框
document.write('<script type="text/javascript" src="/static/default/js/app.js"></script>'); //导航
document.write('<script type="text/javascript" src="/static/default/js/plugins/validation/jquery.validate.min.js"></script>'); //验证表单插件
document.write('<script type="text/javascript" src="/static/default/js/plugins/uniform/jquery.uniform.min.js"></script>'); //表单美化插件
document.write('<script type="text/javascript" src="/static/default/js/plugins.form-components.js"></script>'); //初始化所有表单
document.write('<script type="text/javascript" src="/static/default/js/plugins/fileinput/fileinput.js"></script>'); //上传图片插件
document.write('<script type="text/javascript" src="/static/default/js/plugins/webox/jquery-webox.js"></script>'); //提示框webox

$(document).ready(function() {
  App.init();
  FormComponents.init();
  $(".bs-tooltip").tooltip({container:"body"});
  /* 导航隐藏显示 */
  $(".toggle-sidebar").click(function(){
    if ($.cookie('sidebar')) {
      $.cookie('sidebar', '', { expires: -1, path: '/' });
    } else {
      $.cookie('sidebar', 1, { expires: 7, path: '/' });
    }
  });
  /* 左边菜单高亮 */
  var $subnav = $("#nav");
  url = window.location.pathname + window.location.search;
  url = url.replace(/(\/(p)\/\d+)|(&p=\d+)|(\/(id)\/\d+)|(&id=\d+)|(\/(group)\/\d+)|(&group=\d+)/, "");
  $subnav.find("a[href='" + url + "']").parent().parent().parent().addClass("current open");
  $subnav.find("a[href='" + url + "']").parent().addClass("current");
  //提示
  $("a.confirm-dialog").click(function(b) {
    var url = this.href;
    b.preventDefault();
    bootbox.dialog({
      message: "删除后将不可恢复，请谨慎操作！！",
      buttons: {
        danger: {
          label: "确认",
          className: "btn-danger",
          callback: function() {
            window.location.href = url;
          }
        },
        main: {
          label: "返回",
          className: "btn",
          callback: function() {}
        }
      }
    })
  });
});