
var imgHead = [
        '/images/photos/1.jpg',
        '/images/photos/2.jpg',
        '/images/photos/3.jpg'
    ], i=1;
    
function csaHead(){
    if(i >= imgHead.length)
    {
        i=0;
    }
    $('.csa-head').animate({'opacity':'0'},200,function(){
        $('.csa-head').css({'background':'url('+imgHead[i]+') 100% 100% no-repeat', 'background-size':'cover'});
         i++;
    });
    $('.csa-head').animate({'opacity':'1'},200);
}

$(window).load(function() {		
// Запускаем слайдшоу
setInterval(csaHead,8000);
});

