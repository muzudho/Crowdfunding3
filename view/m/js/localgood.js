/**
 * Created by m454k1 on 2014/08/21.
 */
$ = jQuery;

$(function(){

    // グロナビ, プルダウン
    $('.nav_inner ul li').hover(
        function(){
            $(this).children('ul.sub:hidden').slideToggle();
        },
        function(){
            $(this).children('ul.sub:visible').slideToggle();
        }
    );

});
