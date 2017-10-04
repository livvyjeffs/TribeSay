var hello = 'already defined';

var input, ul, inputTerms, termsArray, prefix, terms, results, sortedResults, searchIndex;

var tester = function() {
    ////console.log('tester run');
    hello = 'in tester';
    ////console.log('global variable from inside tester(): ' + hello);

}

var getSearchIndex = function(a, b) {

    input = a;
    ul = b;

    ////console.log('input: ' + a.getAttribute('id') + ' and ul: ' + b.getAttribute('id'))

    ////console.log('getSearchIndex frontof');

    ////console.log('global variable from inside getSearchIndex: ' + hello);


    var ajax = ajaxObj("POST", frenetic.root + "/php_parsers/tagFilter.php");

    ajax.onreadystatechange = function() {
        if (ajaxReturn(ajax) === true) {
            searchIndex = ajax.responseText.split(",");


            for (var i = 0; i < searchIndex.length; i++) {
                searchIndex[i] = searchIndex[i].split(" x ");
            }



        }
    };
    ajax.send("get_all_tags=please");

};

var search = function() {
    ////console.log('search frontof OUTSIDE of jquery(doc)');
    inputTerms = input.value.toLowerCase();
    results = [];
    termsArray = inputTerms.split(' ');
    prefix = termsArray.length === 1 ? '' : termsArray.slice(0, -1).join(' ') + ' ';
    terms = termsArray[termsArray.length - 1].toLowerCase();

    for (var i = 0; i < searchIndex.length; i++) {
        //searches only by word and not the number
        var a = searchIndex[i][0].toLowerCase(),
                t = a.indexOf(terms);

        if (t > -1) {
            var result_number = [[a], [searchIndex[i][1]]];
            results.push(result_number);
        }
    }
    evaluateResults();
};

var evaluateResults = function() {

    ////console.log('evaluateResults frontof');
    if (results.length > 0 && inputTerms.length > 0 && terms.length !== 0) {
        //alert('1')

        //sortedResults = results.sort(sortResults);

        sortedResults = results;
        

        appendResults();
        }
    else if (inputTerms.length >= 0 && terms.length !== 0) {
        //alert('2')
        if (input.getAttribute('id') === 'searchBox') {
            ul.innerHTML = '<li><strong>' + inputTerms + '</strong> is not on TribeSay - why don\'t you try uploading content and creating a <strong>' + inputTerms + '</strong> tribe.</li>';
            $('#searchResults').removeClass('hidden');
        } else if (input.getAttribute('id') === 'tag_input') {
            
            clearResults();
            
            var li = document.createElement("li");
            li.setAttribute("tag", inputTerms);
            li.innerHTML = 'add <strong>' + inputTerms + '</strong> to TribeSay';

            ul.appendChild(li);

            li.addEventListener("click", function(e) {
                e.preventDefault();
                if ($(this).hasClass('selected')) {
                    if (input.getAttribute('id') === 'searchBox') {
                        add_to_filter(null, null, this.getAttribute("tag"));
                        
                        

                    } else if (input.getAttribute('id') === 'tag_input') {
                        add_to_tag_suggestor($(this));
                    }
                    clearResults();
                    input.value = "";
                }
            }, false);

            li.addEventListener("mouseenter", function(e) {
                e.preventDefault();
                if ($(this) === $(ul).find('li.selected')) {
                    return;
                } else {
                    $(this).addClass('selected');
                }
            }, false);

            
            ///
            
            
            
            $('.tag_selector').removeClass('hidden');
        }
        
      

    }
    else if (inputTerms.length !== 0 && terms.length === 0) {
        //alert('3')
        return;
    }
    else {
        //alert('4')
        clearResults();
    }
};

var appendResults = function() {

    ////console.log('appendResults frontof');
    clearResults();

    for (var i = 0; i < sortedResults.length && i < 5; i++) {
        var li = document.createElement("li"),
                result = "<span title='" + sortedResults[i][0] + "'>" + prefix
                + sortedResults[i][0].toString().toLowerCase().replace(terms, '<strong>'
                + terms
                + '</strong>') + "</span> x " + sortedResults[i][1];

        li.setAttribute("tag", sortedResults[i][0]);
        li.innerHTML = result;
        ul.appendChild(li);

        li.addEventListener("click", function(e) {
            e.preventDefault();
            if (input.getAttribute('id') === 'searchBox') {
                //if searching on mainpage
                add_to_filter(e, this.getAttribute("tag"));

                //ANALYTICS
                record_content('consumption', 'add_filter_tag', 'search_desktop', {'tag': this.getAttribute("tag")});

                //add_to_filter(null, $(this), this.getAttribute("tag"));
            } else if (input.getAttribute('id') === 'tag_input') {
                //if uploading and tagging content
                add_to_tag_suggestor($(this));
            }
            clearResults();
            input.value = "";
        }, false);

        li.addEventListener("mouseenter", function(e) {
            e.preventDefault();
            if ($(this) === $(ul).find('li.selected')) {
                return;
            } else {
                $(ul).find('li.selected').removeClass('selected');
                $(this).addClass('selected');
            }
        }, false);

    }
    
    if(input.getAttribute('id') === 'searchBox'){
        $(ul).find('li:first-child').addClass('selected');
    }
    

    if (ul.className !== "term-list") {
        ul.className = "term-list";
    }
};

var clearResults = function() {

    ////console.log('clearResults frontof');
    ul.className = "term-list hidden";
    ul.innerHTML = '';
};

var navigate = function(e, elem) {

    ////console.log('navigate frontof');
    e.preventDefault();
    elem.selectionStart = elem.selectionEnd = elem.value.length;
    var current = $(ul).find('li.selected');
    switch (e.keyCode) {
        case 13:

            if ($(ul).find('li.selected').length === 0) {
                add_to_tag_suggestor($('.tag_input').val());
            } else {
                current.trigger('click');
            }

            return 'stop';
            break;
        case 27:
            //esc
            clearResults();
            input.value = "";
            return 'stop';
            break;
        case 37:
            //left
            if (current.prev().length > 0) {
                current.removeClass('selected');
                current.prev().addClass('selected');
            }
            return 'stop';
            break;
        case 38:
            //up
            if (current.prev().length > 0) {
                current.removeClass('selected');
                current.prev().addClass('selected');
            } else if (current.prev().length === 0) {
                $(ul).find('li.selected').removeClass('selected');
            }
            return 'stop';
            break;
        case 39:
            //right
            if (current.next().length > 0) {
                current.removeClass('selected');
                current.next().addClass('selected');
            }
            return 'stop';
            break;
        case 40:
            //down
            if (current.next().length > 0) {
                current.removeClass('selected');
                current.next().addClass('selected');
            }else if($(ul).find('li.selected').length === 0){
                $(ul).find('li:first-child').addClass('selected');
            }
            return 'stop';
            break;
        case 8:
            if (input.getAttribute('id') === 'tag_input' && input.value === '') {
                var tag = $('.selected_tags').find('.tag_module:last-child');
                if (tag.hasClass('delete_on_click') === false) {
                    tag.addClass('delete_on_click');
                } else {
                    var width = tag.outerWidth(true);
                    tag.remove();
                    $('.tag_input').css('width',$('.tag_input').width() + width);
                }
            }
            return;
            break;
        default:
            if (input.getAttribute('id') === 'tag_input') {
                var tag = $('.selected_tags').find('.tag_module:last-child');
                if (tag.hasClass('delete_on_click')) {
                    tag.removeClass('delete_on_click');
                } 
            }
    }
    current = $(ul).find('li.selected');

};

function setSearchInput(){
    input = document.getElementById("searchBox");
    ul = document.getElementById("searchResults");
}

jQuery(document).ready(function($) {
    
    setSearchInput();

    ////console.log('front of jquery document');
    ////console.log('global variable from inside jquery(doc).ready: ' + hello);
    tester();

    ////console.log(input + "/" + ul + "/" + inputTerms + "/" + termsArray + "/" + prefix + "/" + terms + "/" + results + "/" + sortedResults);



    ////console.log('before adding event listeners');

    input.addEventListener("keyup", function(event) {
        var a = navigate(event, this);
        if(a !== 'stop'){search();}

        $('html').one("click", (function(event) {
            //event.preventDefault();
            ////console.log('html clicked in one function')
            if ($.contains(document.getElementById('search_bar'), event.target) === false) {
                clearResults();
                input.value = "";
            }
        }));

    }, false);



    ////console.log('before calling getSearchIndex()')
    getSearchIndex(input, ul);
    ////console.log('after calling getSearchIndex()')



});