$(document).ready(function(){
   $('[label="faq"] h1 .button').click(function() {
        if ($(this).attr('action') === 'expand') {
            $('.faq').addClass('open');
            $(this).attr('action', 'minimize');
            $(this).find('div').text('-');
        } else {
            $('.faq').removeClass('open');
            $(this).attr('action', 'expand');
            $(this).find('div').text('+');
        }
    });
});