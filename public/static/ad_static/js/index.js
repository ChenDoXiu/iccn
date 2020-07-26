$(function(){
  $(".menu").click(function(){
    $( $(".menu").attr("data-menu")).toggleClass("active");
  });
  $(".left li.active").parent().addClass("show").prev().addClass("active");
})
 
