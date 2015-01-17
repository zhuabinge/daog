/*
 * Mobile for ttgg.com
 * @webuffer 2014.12
 */
$(document).ready(function() {
  //返回顶部
  $('#backTop').click(function() {
	 $('html, body').animate({'scrollTop':'0'}, 200);
  });
  //商品框爱心点击变色
  $('.deal-con figure > sub > a').click(function() {
	 $(this).find('i').toggleClass('icon-lvred');
  });
  $('#btn-checkin').click(function(e) {
    alert('手机版签到暂未开放，请前往网页版签到！');
  });
});