$(document).ready(function(){
    $("#gallery_nav").on("click", "a", function () {
       $(this).addClass("current").siblings().removeClass("current")
       $("#product_images img").attr("src", $(this).prop("href"))
       return false;
    })
});