var bullets = document.getElementById('position').getElementsByTagName('li');
var banner = Swipe(document.getElementById('mySwipe'), {
  auto: 3000,
  continuous: true,
  disableScroll:false,
  callback: function(pos) {
    var i = bullets.length;
    while (i--) {
      bullets[i].className = ' ';
    }
    bullets[pos].className = 'cur';
  }
});