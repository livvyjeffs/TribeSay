//these commands require that analyticstracking.php be included first

//Prototype

ga('send', 'event', 'category', 'action', 'label');

//----------------CONVERSION EVENTS - BY TRIBE

//Filter

ga('send', 'event', 'filter_drop', 'drag_or_click_or_trend_or_fav', 'tagName');
ga('send', 'event', 'filter_drop', 'favorite_tags', tagName); //logged
ga('send', 'event', 'filter_drop', 'trending_tags', tagName); //logged
ga('send', 'event', 'filter_drop', 'view_all_favorites', name); //logged
ga('send', 'event', 'filter_drop', 'from_content', tag_title); //logged
ga('send', 'event', 'filter_drop', 'search', tagName); //logged

//Open Modal

ga('send', 'event', 'open_modal', 'from_link', tagArray[i]); //logged //repeat from below

//Comment

ga('send', 'event', 'post_comment', 'starter', $(this).attr('title')); //logged
ga('send', 'event', 'post_comment', 'reply', $(this).attr('title')); //logged

//Favorites Tags

ga('send', 'event', 'tagStream', 'favorite_tags', data); //logged

//Post

ga('send', 'event', 'post_media', media, tag1); //logged

//----------------MISCELLANEOUS CONVERSION EVENTS

//LOGGING IN
ga('send', 'event', 'single_click', '[Sign Up][Login]', 'from_index'); //logged
ga('send', 'event', 'single_click', 'open_modal', 'signup_success_notification'); //logged
ga('send', 'event', 'single_click', 'sign_up', username); //logged

//DEBUGGING

ga('send', 'event', 'single_click', 'open_modal', 'debug'); //logged

//----------------CONVERSION EVENTS - SINGLE CLICK

//Filter
ga('send', 'event', 'single_drag', 'filter_drag', data); //logged
ga('send', 'event', 'single_click', 'filter_drop', '[favorite_tags],[trending_tags],[view_all_favorites],[from_content],[search]'); //logged
ga('send', 'event', 'single_click', 'filter_remove', 'clear_filter'); //logged
ga('send', 'event', 'single_click', 'to_smokesignals', $(this).attr('title')); //logged

//Navigation

ga('send', 'event', 'single_click', 'nav_bar', $(this).attr('title')); //logged
ga('send', 'event', 'single_click', 'logo'); //logged
ga('send', 'event', 'single_click', 'you_are_here'); //logged
ga('send', 'event', 'single_click', 'explode', e); //logged
ga('send', 'event', 'single_click', 'insplode', e); //logged

ga('send', 'event', 'single_click', '[next][previous]', order); //logged
ga('send', 'event', 'single_click', '[next][previous]', media); //logged
ga('send', 'event', 'single_click', '[next][previous]', uid); //logged

ga('send', 'event', 'single_keypress', 'open_modal', '[up_arrow][left_arrow][right_arrow][down_arrow][escape]'); //logged

//Open

ga('send', 'event', 'single_click', 'open_modal', 'link_or_click');
ga('send', 'event', 'single_click', 'open_modal', 'from_link'); //logged
ga('send', 'event', 'single_click', 'open_modal', 'from_stream'); //logged

//make above match format of below
ga('send', 'event', 'single_click', 'open_modal', 'sign_up'); //logged
ga('send', 'event', 'single_click', 'open_modal', 'sign_up'); //logged


//Content Curation

ga('send', 'event', 'single_click', 'delete_content'); //logged

//SHARING
ga('send', 'event', 'single_click', 'share_content'); //logged

//Comment

ga('send', 'event', 'single_click', 'post_comment', 'start'); //logged
ga('send', 'event', 'single_click', 'post_comment', 'reply'); //logged


//Post

ga('send', 'event', 'single_click', 'post_media', 'media_type');
ga('send', 'event', 'single_click', 'post_button'); //logged
ga('send', 'event', 'single_click', 'from_stream', 'upload_more'); //logged



//TagStream

ga('send', 'event', 'single_click', 'tagStream', 'minimize'); //logged
ga('send', 'event', 'single_click', 'tagStream', 'maximize'); //logged
ga('send', 'event', 'single_click', 'tagStream', 'edit'); //logged
ga('send', 'event', 'single_click', 'tagStream', 'un-edit'); //logged

ga('send', 'event', 'drag', 'tagStream', 'favorite_tags'); //logged

//TribeStream

ga('send', 'event', 'single_click', 'from_stream', '[upvote][share_content]'); //logged

//CONVERSION EVENTS - CONTENT VIEWING IN MODAL

ga('send', 'event', 'open_modal', 'from_link', media); //logged
ga('send', 'event', 'open_modal', 'from_link', uid); //logged
ga('send', 'event', 'open_modal', 'from_link', tagArray[i]); //logged

ga('send', 'event', 'open_modal', 'from_stream', order); //logged
ga('send', 'event', 'open_modal', 'from_stream', string); //logged
ga('send', 'event', 'open_modal', 'from_stream', media); //logged
ga('send', 'event', 'open_modal', 'from_stream', uid); //logged
ga('send', 'event', 'open_modal', 'from_stream', $(this).attr('title')); //logged

//CONVERSION EVENTS - 1. VOTING & SHARING


ga('send', 'event', 'single_click', $(this).attr('class'), 'from_stream');//logged
ga('send', 'event', $(this).attr('class'), 'from_stream', 'order: ' + $(this).parents('.media_container').attr('order'));//logged
ga('send', 'event', $(this).attr('class'), 'from_stream', 'media_type: ' + $(this).parents('.media_container').attr('media'));//logged
ga('send', 'event', $(this).attr('class'), 'from_stream', 'uid: ' + $(this).parents('.media_container').attr('uid'));//logged
ga('send', 'event', $(this).attr('class'), 'from_stream', 'explode_status: ' + get_splode_status());//logged

ga('send', 'event', 'single_click', 'share_content', 'from_modal'); //logged

ga('send', 'event', 'share_content', 'from_modal', media); //logged
ga('send', 'event', 'share_content', 'from_modal', uid); //logged
ga('send', 'event', 'share_content', 'from_modal', order); //logged


ga('send', 'event', 'single_click', 'share_comment', 'from_stream'); //logged
ga('send', 'event', 'single_click', 'share_comment', 'from_modal'); //logged

ga('send', 'event', 'share_comment', 'from_modal', media); //logged
ga('send', 'event', 'share_comment', 'from_modal', uid); //logged
ga('send', 'event', 'share_comment', 'from_modal', order); //logged

ga('send', 'event', 'share_comment', 'from_stream', media); //logged
ga('send', 'event', 'share_comment', 'from_stream', uid); //logged
ga('send', 'event', 'share_comment', 'from_stream', order); //logged

//CONVERSION EVENTS - 2. COMMENTING

//CONVERSION EVENTS - 3. POSTING
ga('send', 'event', 'post_button', media); //logged
ga('send', 'event', 'from_stream', 'upload_more', $(this).attr('media')); //logged

ga('send', 'event', 'post_button', 'exploded', media); //logged
ga('send', 'event', 'post_button', 'from_index', 'auto_article'); //logged
ga('send', 'event', 'post_button', 'from_header', $(this).attr('media'));

ga('send', 'event', 'single_click', 'scrape', scrape_url); //logged
ga('send', 'event', 'single_click', 'scrape', media); //logged

ga('send', 'event', 'single_click', 'post_media', media); //logged