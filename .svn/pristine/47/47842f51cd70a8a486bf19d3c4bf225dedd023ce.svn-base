// function throttle(n,t){var r,e,u,i,a=0,o=function(){a=new Date,u=null,i=n.apply(r,e)};return function(){var c=new Date,l=t-(c-a);return r=this,e=arguments,0>=l?(clearTimeout(u),u=null,a=c,i=n.apply(r,e)):u||(u=setTimeout(o,l)),i}} 
// var F_createEle = function(ele){
//   var div = document.createElement('div');
//   div.id = "likeico";
//   div.innerHTML = "<span class='heart_left'></span><span class='heart_right'></span>";
//   console.log(div);
//   ele.append(div);
// }
// var Mylike_add = function(event) {
//   var obj = $(this);
//   var parent = obj;
//   var id = parent.attr('lkid');
//   var s = parent.attr('lks');
//   F_createEle(obj);
//   setTimeout(function(){$("#likeico").remove()}, 600);
//   if(s == 1) {
//       // like success
//       adduser(id);
//       parent.attr('lks',0);
//       parent.attr('title', '取消收藏');
//       obj.find('i.like-ico').addClass('l-active');
//       obj.closest('li').find('.like-ceng').show();
//       $("#likeico").removeClass('unliked').addClass('like-big').addClass('demo1');
//       parent.css('display','block');
//       //post
//   } else {
//       // un like success
//       deleteuser(id);
//       parent.attr('lks',1);
//       parent.attr('title', '加入收藏');
//       obj.find('i.like-ico').removeClass('l-active');
//       $("#likeico").removeClass('l-active').addClass('unliked').removeClass('demo1');
//       obj.closest('li').find('.like-ceng').hide();
//       parent.css('display','');
//   }
// }
// var Pause_click = function(){
//   $("a.my-like").on('click', throttle( Mylike_add, 300 ) );
// }
// Pause_click();
//详细页面收藏
$('.icon_sc').click(function(){
  var id = $(this).attr('lpid'),
      s = $(this).attr('lks');
  if(s == 1) {
    adduser(id);
  } else {
    deleteuser(id);
  }
});
function adduser(id){
  $.post('/user/addlikes', {pid:id}, function(data) {
    if (data == 'flase') {
      $('#prompt').load('/user/loginLoad');
      return false;
    } else {
      var Icon = $('.icon_sc');
      Icon.attr('lks', 0);
      Icon.html('<i class="curr"></i>已收藏');
    }
  }, 'text');
}
function deleteuser(id){
  $.post('/user/deletelikes', {pid:id}, function(data) {
    if (data == 'flase') {
      $('#prompt').load('/user/loginLoad');
      return false;
    } else {
      var Icon = $('.icon_sc');
      Icon.attr('lks', 1);
      Icon.html('<i class=""></i>收藏');
    }
  }, 'text');
}