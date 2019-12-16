function fullScreen(screenFull) {
  var docElm = document.documentElement;
  if(screenFull) { //true则全屏，否则退出全屏
    if (docElm.requestFullscreen) { //W3C
      docElm.requestFullscreen();
    }else if (docElm.mozRequestFullScreen) { //FireFox
      docElm.mozRequestFullScreen();
    }else if (docElm.webkitRequestFullScreen) { //Chrome等
      docElm.webkitRequestFullScreen();
    } else if (docElm.msRequestFullscreen) { //IE11
      docElm.msRequestFullscreen();
    }
  } else {
    if(document.exitFullScreen) {
        document.exitFullScreen();
    } else if(document.mozCancelFullScreen) {
        document.mozCancelFullScreen();
    } else if(document.webkitExitFullscreen) {
        document.webkitExitFullscreen();
    } else if(document.msExitFullscreen) {
        document.msExitFullscreen();
    }
  }
}

