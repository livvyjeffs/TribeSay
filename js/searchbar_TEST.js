
function searchbar(input, list, list_format) {    
    
    var a = this;
    this.input = input;
    this.list = list;
    this.list_format = list_format;

    if (this.list_format === 'desktop_tag_suggestor') {
        this.input.addEventListener("keyup", function(e) { 
            a.navigate(e);
        });
    } else {
        this.input.addEventListener("keyup", function() {
            a.search();
        });
    }

    this.getSearchIndex();
}

searchbar.prototype = {
    search: function() {
                
        this.inputTerms = this.input.value.toLowerCase();
        this.results = [];
        this.termsArray = this.inputTerms.split(' ');
        this.prefix = this.termsArray.length === 1 ? '' : this.termsArray.slice(0, -1).join(' ') + ' ';
        this.terms = this.termsArray[this.termsArray.length - 1].toLowerCase();
        for (var i = 0; i < this.searchIndex.length; i++) {
            //searches only by word and not the number
            var a = this.searchIndex[i][0].toLowerCase(),
                    t = a.indexOf(this.terms);
            if (t > -1) {
                var result_number = [[a], [this.searchIndex[i][1]]];
                this.results.push(result_number);
            }
        }
        this.evaluateResults();

    }, evaluateResults: function() {

        this.clearResults();

        var a = this;

        if (this.results.length > 0 && this.inputTerms.length > 0 && this.terms.length !== 0) {

            this.sortedResults = this.results;
            this.appendResults();


        } else if (this.inputTerms.length >= 0 && this.terms.length !== 0) {
            //alert('2')
            if (this.list_format === 'desktop_search') {
                this.list.innerHTML = '<li><strong>' + this.inputTerms + '</strong> is not on TribeSay - why don\'t you try uploading content and creating a <strong>' + this.inputTerms + '</strong> tribe.</li>';
                $(this.list).removeClass('hidden');
            } else if (this.list_format === 'desktop_tag_suggestor') {

                this.clearResults();
                var li = document.createElement("li");
                li.setAttribute("tag", this.inputTerms);
                li.innerHTML = 'add <strong>' + this.inputTerms + '</strong> to TribeSay';
                this.list.appendChild(li);
                
                li.addEventListener("click", function(e) {
                    e.preventDefault();
                    add_to_tag_suggestor($(this));
                    a.clearResults();
                    a.input.value = "";
                }, false);

                $(li).hover(function() {
                    $(this).addClass('selected');
                }, function() {
                    $(this).removeClass('selected');
                });

                $('.tag_selector').removeClass('hidden');

            } else if (this.list_format === 'mobile_search') {
                this.list.innerHTML = '<strong>' + this.inputTerms + '</strong> is not on TribeSay. Check us out on desktop and create your own <strong>' + this.inputTerms + '</strong> tribe.';
                $(this.list).removeClass('hidden');
            }

        } else if (this.inputTerms.length !== 0 && this.terms.length === 0) {
            return;
        } else {
            if (this.list_format === 'desktop_search' || this.list_format === 'mobile_search') {
                $(this.list).append('<span class="instructions">A tribe is a group of people who share your interests. Try finding your tribe by finding tags that interest you.</span><span class="popular">Popular Tags</span>');
                this.sortedResults = this.searchIndex;
                this.appendResults();
            } else {
                this.clearResults();
            }
        }
    }, getSearchIndex: function() {
        var a = this;
        var ajax = ajaxObj("POST", frenetic.root + "/php_parsers/tagFilter.php");
        ajax.onreadystatechange = function() {
            if (ajaxReturn(ajax) === true) {

                a.searchIndex = ajax.responseText.split(",");
                for (var i = 0; i < a.searchIndex.length; i++) {

                    var tag = a.searchIndex[i].split(" x ")[0];
                    var amount = a.searchIndex[i].split(" x ")[1];

                    a.searchIndex[i] = [tag, amount];
                }

                a.search();

            }
        };
        ajax.send("get_all_tags=please");
    }, appendResults: function() {

        if (this.list.className !== "term-list") {
            this.list.className = "term-list";
        }

        switch (this.list_format) {
            case 'mobile_search':

                for (var i = 0; i < this.sortedResults.length && i < 30; i++) {

                    var data = new Object();
                    data.amount = this.sortedResults[i][1];
                    data.html = this.prefix
                            + this.sortedResults[i][0].toString().toLowerCase().replace(this.terms, '<strong>'
                            + this.terms
                            + '</strong>');

                    this.list.appendChild(tag_module(this.sortedResults[i][0], 'mobile_search', data));

                    if ($(this.list).overflow()) {
                        $(this.list).css('overflow-y', 'scroll');
                    } else {
                        $(this.list).removeAttr('style');
                    }

                }
                break;
            case 'desktop_search':

                for (var i = 0; i < this.sortedResults.length && i < 30; i++) {

                    var data = new Object();
                    data.amount = this.sortedResults[i][1];
                    data.html = this.prefix
                            + this.sortedResults[i][0].toString().toLowerCase().replace(this.terms, '<strong>'
                            + this.terms
                            + '</strong>');

                    this.list.appendChild(tag_module(this.sortedResults[i][0], 'desktop_search', data));

                    if ($(this.list).overflow()) {
                        $(this.list).css('overflow-y', 'scroll');
                    } else {
                        $(this.list).removeAttr('style');
                    }

                };
                
                break;
            case 'desktop_tag_suggestor':
                for (var i = 0; i < this.sortedResults.length && i < 5; i++) {
                    var li = document.createElement("li"),
                            result = "<span title='" + this.sortedResults[i][0] + "'>" + this.prefix
                            + this.sortedResults[i][0].toString().toLowerCase().replace(this.terms, '<strong>'
                            + this.terms
                            + '</strong>') + "</span> x " + this.sortedResults[i][1];
                    li.setAttribute("tag", this.sortedResults[i][0]);
                    li.innerHTML = result;
                    this.list.appendChild(li);
                    li.addEventListener("click", function(e) {
                        e.preventDefault();
                        add_to_tag_suggestor($(this));
                        this.clearResults();
                        this.input.value = "";
                    }, false);

                    $(li).hover(function() {
                        $(this).addClass('selected');
                    }, function() {
                        $(this).removeClass('selected');
                    });
                }

                break;

        }

    }, clearResults: function() {
        this.list.className = "term-list hidden";
        this.list.innerHTML = '';
    }, navigate: function(e) {

        e.preventDefault();
        this.input.selectionStart = this.input.selectionEnd = this.input.value.length;
        
        this.current = $(this.list).find('.selected');
        
        switch (e.keyCode) {
            case 13:
                //enter
                if ($(this.list).find('.selected').length === 0) {
                    add_to_tag_suggestor(this.input.value);
                } else {
                    this.current.trigger('click');
                }

                break;
            case 27:
                //esc
                this.clearResults();
                
                break;
            case 37:
                //left
                if (this.current.prev().length > 0) {
                    this.current.removeClass('selected');
                    this.current.prev().addClass('selected');
                }
                break;
            case 38:
                //up
                if (this.current.prev().length > 0) {
                    this.current.removeClass('selected');
                    this.current.prev().addClass('selected');
                } else if (this.current.prev().length === 0) {
                    $(this.list).find('li.selected').removeClass('selected');
                }
                break;
            case 39:
                //right
                if (this.current.next().length > 0) {
                    this.current.removeClass('selected');
                    this.current.next().addClass('selected');
                }
                break;
            case 40:
                //down
                if (this.current.next().length > 0) {
                    
                    this.current.removeClass('selected');
                    this.current.next().addClass('selected');
                } else if ($(this.list).find('li.selected').length === 0) {
                    $(this.list).find('li:first-child').addClass('selected');
                }
                break;
            case 8:
                //backspace                
                if (this.input.value === '') {
                    var tag = $('.selected_tags').find('.tag_module:last-child');
                    if (tag.hasClass('delete_on_click') === false) {
                        tag.addClass('delete_on_click');
                    } else {
                        var width = tag.outerWidth(true);
                        tag.remove();
                        resize_tag_suggestor();
                    }
                }
                break;
            default:

                this.search();
                
                $('.delete_on_click').removeClass('delete_on_click');

                if (this.input.getAttribute('id') === 'tag_this.input') {
                    var tag = $('.selected_tags').find('.tag_module:last-child');
                    if (tag.hasClass('delete_on_click')) {
                        tag.removeClass('delete_on_click');
                    }
                }
        }
        this.current = $(this.list).find('.selected');
    }, close: function() {
        switch (this.list_format) {
            case 'desktop_search':
                $('#modal_search.modalBackground').removeClass('open');
                $('#search_icon').removeAttr('status');
                this.input.value = '';
                this.search();
                break;
        }

        $('#center_logo_container').removeAttr('style');
    }, open: function() {
        
        
        switch (this.list_format) {
            case 'desktop_search':
                $('#modal_search.modalBackground').addClass('open');
                break;
        }

        $('#mobile_search_input').focus();
       
    }
};

