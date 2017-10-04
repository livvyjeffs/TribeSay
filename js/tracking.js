//check out mixpanel
//check out olark

function addTrackingListeners() {
    $('#modal_share iframe').click(function(){
       
    })
    $('#modal_share .share_pinterest').click(function(){
      
    })
}

jQuery(document).ready(function($) {

    $('#post_button').click(function() {
        ga('send', 'event', frenetic['user'].username, 'post_button', 'single_click');
    });

    $('.nav_button').click(function() {
        ga('send', 'event', frenetic['user'].username, 'navbar_'+ $(this).attr('title'), 'single_click');
    });

    $('.logo').click(function() {
        ga('send', 'event', frenetic['user'].username, 'logo', 'single_click');
    });

    $('#you_are_here').click(function() {
        ga('send', 'event', frenetic['user'].username, 'you_are_here', 'single_click');
    });

});

/*
 var data = new FormData();
 var xhr = (window.XMLHttpRequest) ? new XMLHttpRequest() : new activeXObject("Microsoft.XMLHTTP");
 xhr.open( 'post', 'tracking.php', true );
 
 //mouse hovers and time
 $('*').hover(
 function() {
 $(this).data('inTime', new Date().getTime());
 },
 function() {
 var outTime = new Date().getTime();
 var hoverTime = (outTime - $(this).data('inTime')) / 1000;
 ////console.log('you were hovering on #' + $(this).attr('id') + '.' + $(this).attr('class') + ' for ' + hoverTime + 's');
 data.append("data", 'you were hovering on #' + $(this).attr('id') + '.' + $(this).attr('class') + ' for ' + hoverTime + 's');
 xhr.send(data);
 }
 );
 
 //mouse clicks and time
 $('*').click(
 function() {
 var time = new Date().getTime();
 ////console.log('CLICKED #' + $(this).attr('id') + '.' + $(this).attr('class') + ' at ' + time);
 }
 );
 
 //mouse movemetns and time
 $(document).mousemove(function(event){
 var time = new Date().getTime();
 ////console.log('MOUSE at ' + event.pageX + ',' + event.pageY + ' at ' + time);
 });
 
 //user_id
 
 //modal opened
 
 
 //modal closed
 
 //render browser
 $('body').clone(true);
 
 //resize*/

