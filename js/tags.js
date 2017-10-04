var tagWidth = 0;

function tagCreator(e) {

    var tagHolder = document.getElementsByClassName('tag_holder')[0];
    var tagInput = document.getElementById('tag_selector');

    var numberOfTags = document.querySelectorAll('.tag_holder .tag_module').length;

    if (e.keyCode === 13) {

        if (numberOfTags >= 5) {
            tagInput.value = "you've reached your limit";
        } else {
            //sanitize tagInput
            var tagName = tagInput.value;
            tagName = tagName.toLowerCase();
            tagName = tagName.replace(/[^a-z]/gi, "");
            if (tagName === "") {
                tagInput.value = "";
                exit();
            }
            tagHolder.innerHTML += tag(tagName, 'tag', 'delete');
            tagInput.value = "";
        }
    }


}

function removeTag(element) {

    element.remove();
    
    
}

function resize_tag_suggestor(){
      var suggestor;

    if (frenetic.pagename === 'news') {
        suggestor = $('#modal_upload');
    } else {
        suggestor = $('#modal_event_posting');
    }
    suggestor.find('.tag_input').css('width', suggestor.find('.tag_input_container').width() - suggestor.find('.selected_tags').outerWidth(true) - 2 - suggestor.find('.tag_input_container').width() * 0.01);

}

function add_to_tag_suggestor(elem) {
        
    var limit, limit_message, suggestor; 
    
    if(frenetic.pagename === 'news') {
        limit = 5;
        limit_message = 'maximum of five tags';       
        suggestor = $('#modal_upload');
    } else {
        limit = 3;
        limit_message = 'maximum of three tags';
        suggestor = $('#modal_event_posting');
    }
    
    //alert('adding')
    //check if already there and under 5
    var tagname;
    if(typeof elem === 'string'){
        tagname = elem;
        tagname = tagname.toLowerCase();
        tagname = tagname.replace(/[^a-z0-9]/gi, "");
    }else{
        //alert('should be here')
        tagname = elem.attr('tag');
    }
    
   if(elem === undefined){
       //$('.tag_input').val()
       //alert('but is it here?')
   }

    if (suggestor.find('.selected_tags .tag_module').length === limit) {
        suggestor.find('.tag_input').attr('placeholder', limit_message);

        suggestor.find('.tag_input').val('');
        if (frenetic.pagename === 'news') {

            news_tag_selector.clearResults();
        } else {
            event_tag_selector.clearResults();
        }


        return;
    }

    if (suggestor.find('.selected_tags .tag_text[tag="' + tagname + '"]').length === 0) {
        suggestor.find('.selected_tags').append(tag_module(tagname, 'upload'));
       resize_tag_suggestor(); 
        if (suggestor.find('.selected_tags .tag_module').length === limit) {
            suggestor.find('.tag_input').attr('placeholder', limit_message);
        } else {
           suggestor.find('.tag_input').attr('placeholder', 'add another tag');
        }
        
    } else {
        suggestor.find('.tag_input').attr('placeholder', 'you\'ve already added this tag');
  
    }
    
    suggestor.find('.tag_input').val('');
    news_tag_selector.clearResults();
    event_tag_selector.clearResults();

}

