$(document).ready(function() {

    if (window.location.href.indexOf('index.php') === -1) {
        $('.nav_button.scope[scope="tribe"]').attr('href', 'index.php?rn=tribe');
        $('.nav_button.scope[scope="friends"]').attr('href', 'index.php?rn=friends');
        $('.nav_button.scope[scope="single"]').attr('href', 'index.php?rn=single');

        var pagetype = window.location.pathname.replace('/myStream/', '');

        toggleNavigation($('.nav_button[href="' + pagetype + '"]'));
    }

});