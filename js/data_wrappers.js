

function is_same_day(a, b) {
    if (a.getMonth() === b.getMonth()) {
        if (a.getDate() === b.getDate()) {
            if (a.getDay() === b.getDay()) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function get_duration(a,b){
    
    var duration = b-a;
    
    var s = duration/1000;
    var m = s / 60;
    var h = m / 60;
    var d = h / 24;

    if (h < 24) {
        return h + ' hours';
    } else if (d === 1) {
        return d + ' day';
    } else {
        return d + ' days';
    }

}

function get_day_of_week(a){
       switch (a.getDay()) {
            case 0:
                return 'Sunday';
                break;
            case 1:
                return 'Monday';
                break;
            case 2:
                return 'Tuesday';
                break;
            case 3:
                return 'Wednesday';
                break;
            case 4:
                return 'Thursday';
                break;
            case 5:
                return 'Friday';
                break;
            case 6:
                return 'Saturday';
                break;
        }
}

function get_month(a){
       switch (a.getMonth()) {
            case 0:
                return 'Jan';
                break;
            case 1:
                return 'Feb';
                break;
            case 2:
                return 'March';
                break;
            case 3:
                return 'April';
                break;
            case 4:
                return 'May';
                break;
            case 5:
                return 'June';
                break;
            case 6:
                return 'July';
                break; 
            case 7:
                return 'Aug';
                break;
            case 8:
                return 'Sep';
                break;
            case 9:
                return 'Oct';
                break;
            case 10:
                return 'Nov';
                break;
            case 11:
                return 'Dec';
            break;
    }
}

function get_time(a) {
    
    var hour, minutes, am_pm;
    
    if (a.getHours() < 13) {
        hour = a.getHours();
        am_pm = 'AM';
    } else {
        hour = a.getHours() - 12;
        am_pm = 'PM';
    }


     if (a.getMinutes() === 0) {
        minutes = ' ';
    } else if (a.getMinutes() < 10) {
        minutes = ':0 ' + a.getMinutes();
    } else{
        minutes = ':'+a.getMinutes();
    }
    
    return hour + minutes + am_pm;
}

function datawrapper_media_container(data) {
        
    var tag_limit = 6;
    if(data.media === 'event'){
        
        tag_limit = 4;
  
        this.event_cost = parseFloat(data.ticket_price);

        if (this.event_cost > 0) {
            this.ticket_text = 'Get Tickets ($' + this.event_cost + ')';
        } else {
            this.ticket_text = 'Event Page & RSVP';
        }
        
        this.payment_link = data.payment_link;
        
        this.event_unf_begin = data.event_begin;
        this.event_unf_end = data.event_end;
        this.event_begin = new Date(data.event_begin);
        
        console.log(data.event_begin);
        console.log(this.event_begin);
        
        this.event_end = new Date(data.event_end);
        this.event_duration= get_duration(this.event_begin, this.event_end);
        
        this.event_month = get_month(this.event_begin);
        this.event_day = this.event_begin.getDate();
        
        this.event_today = false;
        this.event_tonight = false;
        this.event_tomorrow = false;
        this.event_this_weekend = false;

        var today = new Date();

        //event is today or tomorrow
        if (is_same_day(this.event_begin, today)) {
            this.relative_time = 'Today';
            this.event_today = true;
            if (this.event_begin.getHours() > 18) {
                //after 6 pm
                this.relative_time = 'Tonight';
                this.event_tonight = true;
            }
        } else if (((this.event_begin - today) / (1000 * 60 * 60 * 24)) < 1) {
            this.event_tomorrow = true;
            this.relative_time = 'Tomorrow';
        } else if (((this.event_begin - today) / (1000 * 60 * 60 * 24)) < 7) {
            if (this.event_begin.getDay() === 0 || this.event_begin.getDay() === 6) {
                this.relative_time = 'This weekend';
                this.event_this_weekend = true;
            }
        } else {
            this.relative_time = get_day_of_week(this.event_begin);
        }

        //event begins and ends on same day
        if (is_same_day(this.event_begin, this.event_end)) {
            this.event_time_text = this.relative_time + ', ' + get_month(this.event_begin) + ' ' + this.event_begin.getDate() + ' from ' + get_time(this.event_begin) + ' to ' + get_time(this.event_end) + ' (' + this.event_duration+')';
        } else {
            this.event_time_text = this.relative_time + ', ' + get_month(this.event_begin) + ' ' + this.event_begin.getDate() + ' from ' + get_time(this.event_begin) + ' to ' + get_day_of_week(this.event_end) + ', ' + get_month(this.event_end) + ' ' + this.event_end.getDate() + ' at ' + get_time(this.event_end)+ ' (' + this.event_duration + ')';
        }

        this.city = data.city;
        this.country = data.country;
        this.description = data.description;
        this.lat = parseFloat(data.lat);
        this.long = parseFloat(data.long);
        this.pinned_status = data.pinned_status;
        this.street_address = data.street_address;
        this.views = data.views;
        this.radius = data.radius;
        this.time_from_now = data.time_from_now;
        this.location_formatted = data.location_formatted;
        this.location_html = data.location_html;
        this.payment_link = data.payment_link;

        this.image_thumbnail = data.thumbnail_source;
        this.image_large = data.imageSource;
    } else {
        this.image_thumbnail = data.imageSource; //permanent internal link
        this.image_large = data.imageLink;
    }

    this.stream_type = data.stream_type;
    this.media = data.media;
    this.title = data.title;
    
    
    this.poster = data.poster;
    
    //votes
    this.vote_state = parseInt(data.vote_state, 10);

    if (this.vote_state === 1) {
        this.vote_state_plural = '';
    } else {
        this.vote_state_plural = 's';
    }

    this.vote = data.vote; //"no_vote", "DOWN", "UP"
    this.previous_vote = data.vote;
    
    //content info
    this.description = data.description;
    this.time_ago = data.time_ago;
   
    this.avatar = data.avatar;
    this.avatar_ratio = parseFloat(data.avatar_ratio);
    this.host_name = data.hostName;
    this.order = data.order;
    this.original_link = data.originalLink;
    this.ratio = data.ratio;
    this.rgb_r = data.rgb_r;
    this.rgb_g = data.rgb_g;
    this.rgb_b = data.rgb_b;
    this.score = data.score;
    this.time_ago = data.time_ago;
    this.uid = data.unique_id;
    this.video_id = data.videoID;
    this.upload_date = data.uploadDate;
    
    //sound
    this.soundcloud_track = data.audio_code;
    this.soundcloud_user = data.sc_user;
    //this.soundcloud_id
    //this.soundcloud_username
    //this.soundcloud_art_url
    //this.soundcloud_title
    //this.soundcloud_html

    //comments
    this.nth = data.nth;
    this.parent_id = data.parent_id;
    this.content_type = data.content_type;
    this.content_id = data.content_id;
    this.comment_text = data.data;

    //tags
    this.tags = [];
    for (var i = 1; i < tag_limit; i++) {
        if (eval('data.tag' + i) !== 'null') {
            this.tags.push(eval('data.tag' + i));
        }
    }

    if (this.media !== 'comment') {
        this.content_type = this.media;
        this.content_id = this.uid;
    }

}

function datawrapper_comment(data) {    
    
    //console.log('DATAWRAPPER COMMENT');
    //console.log(data)
    
    this.profile_pic = data.poster_profile;       
    this.comment_id = data.comment_id;
    this.content_id = data.content_id;
    this.poster = data.poster;
    this.comment_text = data.data;
    this.postdate = data.postdate;
    this.content_type = data.content_type;
    this.parent_id = data.parent_id;
    this.length = data.length;
    this.sid = parseInt(data.sid, 10);
    this.total = parseInt(data.total, 10);
    this.level = parseInt(data.level, 10);
    this.vote_state = parseInt(data.vote_state, 10);
    this.previous = data.previous;

    this.tags = [];
    for (var i = 1; i < 6; i++) {
        if (eval('data.tag' + i) !== 'null') {
            this.tags.push(eval('data.tag' + i));
        }
    }

    if (this.vote_state === 1) {
        this.vote_state_plural = '';
    } else {
        this.vote_state_plural = 's';
    }
    
   
}

function datawrapper_notification(data) {
    this.unique_id = data.unique_id;
    this.media_type = data.media_type;
    this.comment_id = data.comment_id;
    this.comment_url = "&c=" + data.comment_id;
    this.did_read = data.did_read; //0 or 1
  
    if (this.did_read === '0') {
        this.read_status = "new";
        this.mark_status = "read";
    } else if (this.did_read === '1') {
        this.read_status = "read";
        this.mark_status = "new";
    }

    if (this.comment_id === null) {
        this.comment_url = "";
    }
    this.date = data.date;
    this.target = data.target; // 'post' or 'comment'
    this.poster = data.poster;
    this.category = data.category;
    this.avatar = data.avatar;
    this.text = data.text;
    this.time_ago = data.time_ago;
    this.title = '"' + data.title + '"';
    this.note_id = data.note_id;

    if (data.title === undefined) {
        this.title = "";
        this.target = 'picture';
    }

    if (data.target === 'comment') {
        this.target = data.target + ' in';
    }
    
    this.url = 'index.php?p=' + this.poster + '&u=' + this.unique_id + '&m=' + this.media_type + this.comment_url;
    
}

