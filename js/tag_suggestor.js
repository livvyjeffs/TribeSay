jQuery(document).ready(function($) {

    var tag_input = document.getElementById("tag_input"),
            tag_ul = document.getElementById("tag_selector"),
            inputTerms, termsArray, prefix, terms, results, sortedResults;

    var getTagIndex = function() {

        var ajax = ajaxObj("POST", frenetic.root + "/php_parsers/tagFilter.php");

        ajax.onreadystatechange = function() {
            if (ajaxReturn(ajax) === true) {
                var searchIndex = ajax.responseText.split(",");


                for (var i = 0; i < searchIndex.length; i++) {
                    searchIndex[i] = searchIndex[i].split(" x ");
                }
                
                var search = function() {
                    inputTerms = tag_input.value.toLowerCase();
                    results = [];
                    termsArray = inputTerms.split(' ');
                    prefix = termsArray.length === 1 ? '' : termsArray.slice(0, -1).join(' ') + ' ';
                    terms = termsArray[termsArray.length - 1].toLowerCase();
                 
                    for (var i = 0; i < searchIndex.length; i++) {
                        //searches only by word and not the number
                        var a = searchIndex[i][0].toLowerCase(),
                                t = a.indexOf(terms);

                        if (t > -1) {
                            var result_number = [[a],[searchIndex[i][1]]];
                            results.push(result_number);
                        }
                    }
                    evaluateResults();
                };

                var evaluateResults = function() {
                    if (results.length > 0 && inputTerms.length > 0 && terms.length !== 0) {
                        
                        //sortedResults = results.sort(sortResults);
                        
                        sortedResults = results;
                                                
                        appendResults();
                    }
                    else if (inputTerms.length > 0 && terms.length !== 0) {
                        tag_ul.innerHTML = '<li>Whoah! <strong>'
                                + inputTerms
                                + '</strong> is not in the index. <br><small><a href="http://google.com/search?q='
                                + encodeURIComponent(inputTerms) + '">Try Google?</a></small></li>';

                    }
                    else if (inputTerms.length !== 0 && terms.length === 0) {
                        return;
                    }
                    else {
                        clearResults();
                    }
                };

                var appendResults = function() {
                    clearResults();

                    for (var i = 0; i < sortedResults.length && i < 5; i++) {
                        var li = document.createElement("li"),
                                result = "<span title='" + sortedResults[i][0] + "'>" + prefix
                                + sortedResults[i][0].toString().toLowerCase().replace(terms, '<strong>'
                                + terms
                                + '</strong>') + "</span> x " + sortedResults[i][1];
                        
                        li.setAttribute("tag", sortedResults[i][0]);
                        li.innerHTML = result;
                        tag_ul.appendChild(li);
                        
                        li.addEventListener("click", function(e) {
                            e.preventDefault();
                            add_to_filter(null, null, this.getAttribute("tag"));
                            clearResults();
                            tag_input.value = "";
                        }, false);

                        li.addEventListener("mouseenter", function(e) {
                            e.preventDefault();
                            if ($(this) === $('#search_bar li.selected')) {
                                return;
                            } else {
                                $('#search_bar li.selected').removeClass('selected');
                                $(this).addClass('selected');
                            }
                        }, false);
                        
                    }
                    
                    $('#search_bar li:first-child').addClass('selected');

                    if (tag_ul.className !== "term-list") {
                        tag_ul.className = "term-list";
                    }
                };

                var clearResults = function() {
                    tag_ul.className = "term-list hidden";
                    tag_ul.innerHTML = '';
                };

                var navigate = function(e, elem) {
                    e.preventDefault();
                    elem.selectionStart = elem.selectionEnd = elem.value.length;
                    var current = $('#search_bar li.selected');
                    switch (e.keyCode) {
                        case 13:
                            current.trigger('click');
                            exit();
                            break;
                        case 27:
                            //esc
                            clearResults();
                            tag_input.value = "";
                            exit();
                            break;
                        case 37:
                            //left
                            if (current.prev().length > 0) {
                                current.removeClass('selected');
                                current.prev().addClass('selected');
                            }
                            exit();
                            break;
                        case 38:
                            //up
                            alert(current.prev().length)
                            if (current.prev().length > 0) {
                                current.removeClass('selected');
                                current.prev().addClass('selected');
                            }else if (current.prev().length === 1){
                                alert('la')
                                //$('.tag_selector').prepend('<li tag="'+$('.tag_input').val()+'" class="selected"></li>');
                            }
                            exit();
                            break;
                        case 39:
                            //right
                            if (current.next().length > 0) {
                                current.removeClass('selected');
                                current.next().addClass('selected');
                            }
                            exit();
                            break;
                        case 40:
                            //down
                            if (current.next().length > 0) {
                                current.removeClass('selected');
                                current.next().addClass('selected');
                            }
                            exit();
                            break;
                    }
                    current = $('#search_bar li.selected');
                };

                tag_input.addEventListener("keyup", function(event) {
                    navigate(event, this);
                    search();                    
                }, false);

                $('html').click(function(event) {
                    //event.preventDefault();
                    if ($.contains(document.getElementById('search_bar'), event.target) === false) {
                        clearResults();
                        tag_input.value = "";
                    }
                });

            }
        };
        ajax.send("get_all_tags=please");

    };

    getTagIndex();
    
    

});