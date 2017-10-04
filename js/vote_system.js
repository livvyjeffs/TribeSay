//this js file will be included in all user.php stream template files.
//the postVote function wil be called upon clicking any of the up or down vote buttons
//the parser file which handles the ajax post should be generic to all content types, and yet 
//discriminate between them to chose the correct databases to post to/update.
function postVote(content, token) {
    
    if (frenetic['user'].login_status === 'not_logged_in') {
        frenetic.modal.login.open('vote on content');
        return;
    }
    
    //action, content_id, content_date, token, vote_state, voter, metaType
    var action = content.media;
    var content_id = content.uid;
    var content_date = content.upload_date;
    var vote_state = content.vote_state;
    var voter = frenetic['user'].username;
    var previous = content.previous_vote;
    var new_token = token;
    var new_vote = vote_state + 1;
    var metaType = content.metaType;
    
    var media_t = content.media;
    var comment_id = "";//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    
    
    if (token === content.previous_vote) {
        token = 'DOWN';
        new_token = 'no_vote';
        new_vote = vote_state - 1;
    }
    
    var all_votes = $('[uid="' + content_id + '"] [type="content"] .upvote, [uid="' + content_id + '"] [type="content"] .downvote,[uid="' + content_id + '"] [type="content"].vote_container');

    content.vote_state = new_vote;
    content.previous_vote = new_token;

    all_votes.each(function() {
        $(this).attr('votestate', new_vote);
        $(this).attr('previous', new_token);
        $(this).children().attr('previous', new_token);
    }); 
    
    $('[uid="' + content_id + '"] .vote_tally').text(new_vote);
   
    var ajax = ajaxObj("POST", frenetic.root + "/php_parsers/voting_parser.php");

    ajax.onreadystatechange = function() {
        if (ajaxReturn(ajax) === true) {
            var responseArray = ajax.responseText.split('|');
            if (responseArray[0] === "vote_up" || responseArray[0] === "vote_down") {
                
                //generate notification if it was an upvote
                if (responseArray[0] === "vote_up") {
                    var noti_jax = ajaxObj("POST", frenetic.root + "/php_parsers/gen_notifications.php");
                    noti_jax.onreadystatechange = function() {
                        if (ajaxReturn(noti_jax) === true) {
                           
                        }
                    };
                    noti_jax.send("vote_note=" + content_id + "&media_type=" + media_t + "&comment_id=" + comment_id + "&category="+"vote");
                } else {
                   
                }

            } else {

            }
        }
    };
    
    ajax.send("token=" + token + "&action=" + action + "&content_id=" + content_id + "&vote_state=" + vote_state + "&voter=" + voter + "&content_date=" + content_date + "&metaType=" + metaType + "&author=" + content.poster);
}
;

//comment voting
function voteComment(content, elem) {
    //purge incoming variables
    
    var previous = content.previous;
    var comment_unique = content.comment_id;
    var content_unique = content.content_id;
    var vote_state = content.vote_state;
    var token = elem.attr('token');
    var new_vote_state;
    var new_token = token;
    
    var media_t = content.media;

    if (token === previous) {
        token = 'DOWN';
        new_token = 'no';
    }

    if (token === 'UP') {
        new_vote_state = vote_state + 1;
    } else if (token === 'DOWN') {
        new_vote_state = vote_state - 1;
    }
        
    elem.parents('.vote_container').attr('previous', new_token);
    elem.parents('.vote_container').children().attr('previous', new_token);
    
        
    content.vote_state = new_vote_state;
    content.previous = token;
    

    if (new_vote_state === 1) {
        elem.parents('.comment_wrapper').find('.vote_state').text(new_vote_state + " vote");
    } else {
        elem.parents('.comment_wrapper').find('.vote_state').text(new_vote_state + " votes");
    }
    
    //ajax
    var ajax = ajaxObj("POST", frenetic.root + "/php_parsers/voting_parser.php");
    ajax.onreadystatechange = function() {
        if (ajaxReturn(ajax) === true) {
            if (token === "UP") {
                var noti_jax = ajaxObj("POST", frenetic.root + "/php_parsers/gen_notifications.php");
                noti_jax.onreadystatechange = function() {
                    if (ajaxReturn(noti_jax) === true) {
                       }
                };
                noti_jax.send("vote_note=" + content_unique + "&media_type=" + media_t + "&comment_id=" + comment_unique + "&category=comment_vote");
            }
        }
    };
    
    ajax.send("comment_unique=" + comment_unique + "&content_unique=" + content_unique + "&token=" + token + "&vote_state=" + vote_state + "&previous=" + previous + "&author=" + content.poster);
}