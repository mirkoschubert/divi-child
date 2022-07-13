/** Scroll function for styling a scrolled fixed menu bar **/

function scrollFunction(offset) {
  if (document.body.scrollTop > offset || document.documentElement.scrollTop > offset) {
    document.getElementsByTagName('body')[0].classList.add('page-scrolled')
  } else {
    document.getElementsByTagName('body')[0].classList.remove('page-scrolled')
  }
}

$('document').ready(function(){
  scrollFunction(80)
  window.onscroll = function() { scrollFunction(80) }
})

// Here you can write your custom JavaScript code
