function openslide(number) {

    var slide_container = document.getElementById('slideshow_copy_text');
    var image_container = document.getElementById('slideshow_image');
    var arrow = document.getElementById('arrow');
    var all_links = document.querySelectorAll('.counter a');
    for (i = 0; i < all_links.length; i++) {
        all_links[i].setAttribute("style", "");
    }
    document.getElementById('slide_' + number).setAttribute("style", "font-weight: bold");

    switch (number) {
        case '1':
            slide_container.innerHTML = '<h1 style="margin-top: 30%;">brain·tribe n.</h1>\n\
                                        <p style="margin-left: 20%; text-align: left;">A group of people with shared <br><br> interests, passions, or ideas.</p>';
            arrow.setAttribute("onclick", "openslide('2'); setFontSizes();");
            image_container.innerHTML = '<img src="ui-ux-designs/slide1.png">\n\
                                        <div id="image_describer">get the latest tribe news</div>';

            break;
        case '2':
            slide_container.innerHTML = '<h1 style="margin-top: 35%;">Explore your interests</h1>\n\
                                        <p style="text-align: right;">and save your favorite tribetags <img style="height: 20px; width: auto; vertical-align: middle;" src="sourceImagery/tagIcon_signup.png"></p>';
            arrow.setAttribute("onclick", "openslide('3'); setFontSizes();");
            image_container.innerHTML = '<img src="ui-ux-designs/slide2.gif">\n\
                                        <div id="image_describer">drag\'n\'drop your tribetags here</div>';
            break;
        case '3':
            slide_container.innerHTML = '<h1></h1>\n\
                                        <p style="text-align: center; margin-top: 45%;">You don\'t have to be friends in real life...</p>';
            arrow.setAttribute("onclick", "openslide('4'); setFontSizes();");
            image_container.innerHTML = '<img src="ui-ux-designs/slide3.png">\n\
                                        <div id="image_describer">get in with the whole tribe</div>';
            break;
        case '4':
            slide_container.innerHTML = '<h1></h1>\n\
                                        <p style="text-align: center; margin-top: 45%;">But it\'s always better when you are.</p>';
            image_container.innerHTML = '<img src="ui-ux-designs/slide4.png">\n\
                                        <div id="image_describer">or just with your friends</div>';
            arrow.setAttribute("onclick", "openslide('1'); setFontSizes();");
            break;
    }
}

function setFontSizes(){
    var height = document.getElementById('main').clientHeight;
    document.querySelector('#large_copy').style.height = '' + (height * 0.07) + 'px';
    document.querySelector('#large_copy').style.marginTop = '' + (height * 0.1) + 'px';
    document.querySelector('#lower_container').style.top = '' + (height * 0.23) + 'px';
    
    document.getElementById('lower_container').style.height = '' + document.getElementById('slideshow_image').offsetWidth + 'px';
   
    var slideContainerHeight = document.getElementById('lower_container').offsetHeight;
    var slideText = document.getElementById('lower_container').querySelectorAll('*');
    for(var i=0; i<slideText.length; i++){
        slideText[i].style.fontSize = '' + (slideContainerHeight * 0.05) + 'px';
    }
    
    var loginouttext = document.getElementById('profile_bar_login_logout').querySelectorAll('*');
    for(var i=0; i<slideText.length; i++){
        loginouttext[i].style.fontSize = '' + (slideContainerHeight * 0.05) + 'px';
        loginouttext[i].style.color = 'white';
    }
    
   
}