/*function tag(id, type, state) {
    if (id === 'null') {
        return "";
    }
    var action = "<div class='tag_text button'>" + id + "</div>";
    switch (state) {
        case 'delete_favorite':
            action += "<div class='delete-tag' title='remove this tag' onclick='removeTag($(this).parent()); removeFavorite(this.parentNode.title);'></div>";
            break;
        case 'add_favorite':
            action += "<div class='add-tag' title='" + id + "' onclick='add_to_filter(event);'></div>";
            break;
        case 'delete':
            action += "<div class='delete-tag' title='remove this tag' onclick='removeTag($(this).parent()); remove_from_filter(this.parentNode.title);'></div>";
            break;
        case 'add':
            action += "<div class='add-tag' title=" + id + " onclick='add_to_filter(event);'></div>";
            break;
        case 'delete_new':
            action += "<div class='delete-tag' title='remove this tag' onclick='removeTag($(this).parent());'></div>";
            break;
        case undefined:
            action = id;
            break;
    }

    return "<div title='" + id + "' class='tag_module button' id='" + id + "' type='" + type + "' draggable='true' ondragstart='drag(event)'>" + action + "</div>";
}*/

function allowDrop(ev){
    ev.preventDefault();
}

function drag(ev) {
    ev.dataTransfer.setData("Title", ev.srcElement.getAttribute("title"));
    ev.dataTransfer.setData("Object-Type", ev.srcElement.getAttribute("object"));
}

function drop(ev) {
    ev.preventDefault();
    var data = ev.dataTransfer.getData("Title");
}

function add_to_favorites(ev) {

    ev.preventDefault();

    if ($('#my_tribetags .tag_module').length === 0) {
        $('#my_tribetags p').remove();
    }

    var data = ev.dataTransfer.getData("Title");

    ga('send', 'event', frenetic['user'].username, 'add_to_favorites', 'tag_'+data);


    //check if already there and if it is a .tag_module
    if ($('#my_tribetags .tag_text[title=' + data + ']').length === 0 && ev.dataTransfer.getData("Object-Type") === 'tag') {
        $('#my_tribetags')[0].appendChild(tag_module(data, 'favorites'));
        updateFavorites(ev);
    }
}

function dropInFilter(ev) {
    set_filter_drop_count();

    var data = ev.dataTransfer.getData("Title");

    ga('send', 'event', frenetic['user'].username, 'filter_drop', 'drag');
    ga('send', 'event', frenetic['user'].username, 'filter_drop[drag]', data);

    add_to_filter(ev, data);
}

function updateSelFavorites(ev) {

    //insert loading Graphic
    //$('.logo').addClass("rotate");

    ev.preventDefault();
    var tagName = ev.target.value;

    var ajax = ajaxObj("POST", frenetic.root + "/php_includes/update_favorites.php");
    ajax.onreadystatechange = function() {
        if (ajaxReturn(ajax) === true) {
            location.reload();
        }
    };
    
    ajax.send("selectedtagname=" + tagName);

}


function remove_from_filter(tag) {
    set_filter_drop_count();
    //insert loading Graphic
    //$('.logo').addClass("rotate");

    if (frenetic['user'].login_status === 'not_logged_in') {
        new_add_to_filter(tag, 'remove');
    } else {
        var ajax = ajaxObj("POST", frenetic.root + "/php_includes/update_favorites.php");
        ajax.onreadystatechange = function() {
            if (ajaxReturn(ajax) === true) {
                //once favorites DB is updated then load content                

                updateURL('filter');

                var load = new Object();
                load.type = 'filter';
                load.scope = frenetic.scope;
                load.id = close_the_gate();
                load_content(load);
            }
        };
        ajax.send("tagToDeselect=" + tag);
    }
}

function filter_on_drag(ev) {
    set_filter_drop_count();

    ev.preventDefault();
    var data = ev.dataTransfer.getData("Title");

    if (frenetic['user'].login_status === 'not_logged_in') {
        new_add_to_filter(data, 'add');
    } else {
        //insert loading Graphic
      //  $('.logo').addClass("rotate");
        var ajax = ajaxObj("POST", frenetic.root + "/php_includes/update_favorites.php");
        ajax.onreadystatechange = function() {
            if (ajaxReturn(ajax) === true) {
                var load = new Object();
                load.type = 'filter';
                load.scope = frenetic.scope;
                load.id = close_the_gate();
                load_content(load);
            }
        };
        ajax.send("selectedtagname=" + data);
    }

    updateURL('filter');
}

function new_add_to_filter(tag, type) {
    //insert loading Graphic
    //  $('.logo').addClass("rotate");

    //type can be 'add','remove', or 'clear_all'   

    var ajax = ajaxObj("POST", frenetic.root + "/php_parsers/set_filter_session.php");
    ajax.onreadystatechange = function() {
        if (ajaxReturn(ajax) === true) {

            updateURL('filter');

            var load = new Object();
            load.type = 'filter';
            load.scope = frenetic.scope;
            load.id = close_the_gate();
            load_content(load);
        }
    };

    switch (type) {
        case 'one_at_a_time':
            ajax.send("mobile_filter=" + tag);
            break;
        case 'add':
            ajax.send("add_tag=" + tag);
            break;
        case 'remove':
            ajax.send("remove_tag=" + tag);
            break;
        case 'clear':
            ajax.send("clear_all=clear_all");
            break;
    }

}

function add_all_favorites_to_filter(){
    $('#my_tribetags .add-tag').each(function(){
       $(this).trigger('click');
    });
}

//adds all of the current users' favorites to selected favorites for filtering
function add_to_filter(ev, tagName) {

    var type = $(ev.srcElement).attr('type');

    //check if already in filter
    if ($("#tribe_bar .tag_text[tag=" + tagName + "]").length === 0) {
        //if not logged in        
        if (frenetic['user'].login_status === 'not_logged_in') {
            $('#tribe_bar .tag_module').remove();
            $(tag_module(tagName, 'tribe_bar')).insertBefore($('#media_filter_dropdown'));
            //$('#tribe_bar').prepend(tag_module(tagName, 'tribe_bar'));
            new_add_to_filter(tagName, 'one_at_a_time');
        } else {
            //if logged in
            $('#tribe_bar .tag_module').remove();
            $(tag_module(tagName, 'tribe_bar')).insertBefore($('#media_filter_dropdown'));
            //$('#tribe_bar').prepend(tag_module(tagName, 'tribe_bar'));
            var ajax = ajaxObj("POST", frenetic.root + "/php_includes/update_favorites.php");
            ajax.onreadystatechange = function() {

                if (ajaxReturn(ajax) === true) {
                    
                    updateURL('filter');
                    
                    $('.tag_text').each(function() {
                        if ($(this).attr('tag') === tagName) {
                            $(this).addClass('filter');
                        } else {
                            $(this).removeClass('filter');
                        }
                    });
                                                          
                    var load = new Object();
                    load.type = 'filter';
                    load.id = close_the_gate();
                    load_content(load);
                }
            };
            
            ajax.send("add_remove=" + tagName);
            //ajax.send("selectedtagname=" + tagName);
        }
    }

    set_filter_drop_count();

    //console.log(ev);
}

//clears all selected favorites so that only scope filtering is applied
function clear_filter() {
    //insert loading Graphic

    set_filter_drop_count();

    ga('send', 'event', frenetic['user'].username, 'clear_filter', 'single_click');

    $('#tribe_bar .tag_module').remove();
    if (frenetic['user'].login_status === 'not_logged_in') {
        new_add_to_filter(null, 'clear');
    } else {
        var ajax = ajaxObj("POST", frenetic.root + "/php_includes/update_favorites.php");
        ajax.onreadystatechange = function() {
            if (ajaxReturn(ajax) === true) {
                var load = new Object();
                load.type = 'filter';
                load.scope = frenetic.scope;
                load.id = close_the_gate();
                load_content(load);
            }
        };
        ajax.send("clear_all=" + "true");
    }
    
}

function updateFavorites(ev) {
    ev.preventDefault();
    var tagName = ev.dataTransfer.getData("Title");

    var ajax = ajaxObj("POST", frenetic.root + "/php_includes/update_favorites.php");
    ajax.onreadystatechange = function() {
        if (ajaxReturn(ajax) === true) {

        }
    };
    ajax.send("tagname=" + tagName);
}

function removeFavorite(tagName) {
    var ajax = ajaxObj("POST", frenetic.root + "/php_includes/update_favorites.php");
    ajax.onreadystatechange = function() {
        if (ajaxReturn(ajax) === true) {
        }
    };
    ajax.send("tagToRemove=" + tagName);
}

function filterTags() {
    var selectBox = document.getElementById("tagSearchFilter");
    var selectedValue = selectBox.options[selectBox.selectedIndex].value;
    var filterString = _("tagFilter").value;
    var ajax = ajaxObj("POST", frenetic.root + "/php_parsers/tagFilter.php");
    ajax.onreadystatechange = function() {
        if (ajaxReturn(ajax) === true) {
            _("tag_container").innerHTML = ajax.responseText;
        }
    };
    ajax.send("filterString=" + filterString + "&sortParameter=" + selectedValue);
}

function toggle_edit(element) {

    //direct user to sign up
    if (frenetic['user'].login_status === 'not_logged_in') {
        frenetic.modal.login.open();
        return;
    }

    switch (element.attr("status")) {
        case "edit":
            ga('send', 'event', frenetic['user'].username, 'edit_favorite', 'single_click');
            $('#my_tribetags .tag_module .add-tag').replaceWith("<div class='delete-tag' title='remove this tag' onclick='removeTag($(this).parent()); removeFavorite(this.parentNode.title);'>x</div>");
            element.replaceWith('<div id="un_edit_favorites" status="un-edit" onclick="toggle_edit($(this));"></div>');
            break;
        case "un-edit":

            ga('send', 'event', frenetic['user'].username, 'un_edit_favorite', 'single_click');
            $('#my_tribetags .tag_module .delete-tag').replaceWith("<div class='add-tag' title='add this tag to filterbar' onclick='add_to_filter(event);'>+</div>");
            element.replaceWith('<div id="edit_favorites" status="edit" onclick="toggle_edit($(this));"></div>');
            break;
    }

}
