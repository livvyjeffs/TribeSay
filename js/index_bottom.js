$(document).ready(function() {

//    if (window.history && window.history.pushState) {
//        
//        $(window).on('popstate', function() {
//            
//           
//            var hashLocation = location.hash;
//            var hashSplit = hashLocation.split("#!/");
//            var hashName = hashSplit[1];
//
//            if (hashName !== '') {
//                var hash = window.location.hash;
//                if (hash === '') {
//                    window.location.reload();
//                    //window.history.pushState('object or string', 'Title', frenetic.root + '/buuttt');
//                }
//            }
//        });
//       
//    }
    
    mobile_searchbar = new searchbar($('#modal_search input')[0], $('#modal_search.modalBackground .term-list')[0], 'desktop_search');
    tablet_searchbar = new searchbar($('#search_icon input')[0], $('#modal_search.modalBackground .term-list')[0], 'desktop_search');
    //desktop_searchbar = new searchbar($('#header .search-field')[0], $('#modal_search.modalBackground .term-list')[0], 'desktop_search');
    news_tag_selector = new searchbar($('#modal_upload .tag_input')[0], $('#modal_upload .tag_selector')[0], 'desktop_tag_suggestor');
    event_tag_selector = new searchbar($('#modal_event_posting .tag_input')[0], $('#modal_event_posting .tag_selector')[0], 'desktop_tag_suggestor');

    create_modals();

});
//detects if going to specific content

if (frenetic['link'].load_status === 'yes') {

    var ajax = new ajaxObj("POST", frenetic.root + "/php_parsers/get_link_data.php");
    ajax.onreadystatechange = function() {
        if (ajaxReturn(ajax) === true) {
            if (ajax.responseText === "no_data") {
                return;
            }

            $(document).ready(function() {

                var load = new Object();
                load.type = 'fresh_load';
                load.id = close_the_gate();
                load_content(load);
            });

            var json = JSON.parse(ajax.responseText);
            var content = new datawrapper_media_container(json);
            $(document).ready(function() {
                frenetic.modal.viewer.open(content);
            });
        }
    };
    ajax.send("uid=" + frenetic['link'].uid + "&media=" + frenetic['link'].media + "&cid=" + frenetic['link'].cid);

} else if (frenetic['link'].specific_user_status === 'yes') {
//detects if going to specific person

    frenetic.scope = 'single';

    var person = new Object();
    
    set_pageowner(frenetic['link'].username);

    person.username = frenetic['link'].username;
    person.avatar = frenetic['link'].avatar;
    person.avatar_ratio = frenetic['link'].avatar_ratio;

    update_scope();

    $(document).ready(function() {
        
        var load = new Object();
        load.type = 'scope';
        load.id = close_the_gate();
        load_content(load);
    });
  

} else {
    //detects rn case
   
    var load = new Object();

    switch (frenetic['link'].rn) {

        case 'tribe':
            load.type = 'fresh_load';
            load.id = close_the_gate();
            break;
        case 'friends':
            load.type = 'fresh_load';
            load.id = close_the_gate();
            frenetic.scope = 'friends';
            update_scope();
            break;
        case 'single':
            load.type = 'fresh_load';
            load.id = close_the_gate();
            frenetic.scope = 'single';
            update_scope();
            break;
        default:
            load.type = 'fresh_load';
            load.id = close_the_gate();
            break;
    }
    ;

   load_content(load); //first time it gets called
    
}

function update_scope() {
    $(document).ready(function() {
        $('#scope_navigation .scope').each(function() {
            $(this).removeClass('selected');
            if ($(this).attr('scope') === frenetic.scope) {
                $(this).addClass('selected');
            }
        });
    });
}

$(document).ready(function() {
        
    //new linking system to avoid using <a> hrefs
    $('[data-link]').click(function() {
        window.location.href = frenetic.root + "/" + $(this).attr("data-link");
        return false;
    });

    $('[trigger]').keypress(function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            $($(this).attr('trigger')).trigger('click');
            event.stopPropogation();
        }
    });


    $('#scope_navigation .scope.' + frenetic.scope).addClass('selected');

    $('#modal_search.modalBackground').click(function(e) {
        if ($(e.target).hasClass('modalBackground')) {
            mobile_searchbar.close();
        }

    });

    $('#login_with_facebook img').load(function() {
        
        //size_login();
    });

    $('#scope_navigation .scope').click(function(e) {

        //set global scope variable            

        frenetic.scope = $(this).attr('scope');

        //page owner global scope variable
        if (!e.isTrigger) {
            
            if(frenetic['page_owner'].username !== frenetic['user'].username){
                 set_pageowner(frenetic['user'].username);
            }
            
        }

        updateURL('scope');

        //visual scope selection

        update_scope();

        //trigger a scope change on loading new content

        var load = new Object();
        load.type = 'scope';
        load.id = close_the_gate();
        load_content(load);

    });

    spinner_loading($('#stream_container')[0], '#000');

    move_furniture();

});

function updateURL(type) {
    
    //type FILTER, SCOPE, CONTENT, DEFAULT

    var link = frenetic.root + '/';
    
    if(frenetic.pagename === 'events'){
        link += 'classifieds/';
        return;
    }

    switch (type) {
        case 'filter':
            link += get_filter_tags();
            break;
        case 'scope':
            switch (frenetic.scope) {
                case 'tribe':
                    break;
                case 'friends':
                    link += '?rn=' + frenetic.scope;
                    break;
                case 'single':
                    link += '?p=' + frenetic.page_owner.username;
                    break;
            }
            break;
        case 'content':
            link += frenetic.content.media + '/' + frenetic.content.uid;
            break;
        default:
            if(frenetic.scope === 'tribe'){
                updateURL('filter');
            }else{
                updateURL('scope');
            }
            return;
    }

    window.history.pushState('object or string', 'Title', link);

}



function set_media_type(type) {
    frenetic.media = type;
    $('#media_filter span').text($('#media_options li[media="' + type + '"]').text());
}

function go_to_person(person) {

    set_pageowner(person.username);

    $(document).ready(function() {

        $('#scope_navigation .scope.single').trigger('click');

    });

}

$(window).resize(function() {
    ad_resize();
    size_login();
    resizeCommentContainer();

    frenetic.column_count = get_columns();
    frenetic.column_width = preload_column_width();  

    reset_columntops('all');
    masonry(get_columns(), 10, get_splode_status());
});



 