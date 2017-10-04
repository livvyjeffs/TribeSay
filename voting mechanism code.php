<?php
//use array of images to generate comment area html
foreach($imageArray as $image){
    $who = $image[0];
    $when = $image[1];
    $what = $image[2];
    $vote_state = $image[3];
    $imageName = $image[4];
    $photoList .= '<img src="'.$what.'" alt="streamImage" title="'.$who.'">';
    $photoList .= '<textarea id="commentText'.$what.'"></textarea>';
    $photoList .= '<button id="commentButton" onclick="postToComment(\'comment_post\',\''.$log_username.'\',\'commentText'.$what.'\',\''.$what.'\',\''.$who.'\')">Post</button>';
    //check to see if user has already voted on this photo
    $sql = "SELECT COUNT(id) FROM votes WHERE voter='$log_username' AND image_id='$imageName' LIMIT 1";
    $query = mysqli_query($db_conx, $sql);
    $row = mysqli_fetch_row($query);
    if($row[0] < 1){
        $photoList .= '<button id="upVote'.$imageName.'" onclick="postVote(\'post_vote\',\''.$imageName.'\',\'UP\',\''.$vote_state.'\',\''.$log_username.'\')"><img src="sourceImagery/upVote.png"></button>';
        $photoList .= '<button id="downVote'.$imageName.'" onclick="postVote(\'post_vote\',\''.$imageName.'\',\'DOWN\',\''.$vote_state.'\',\''.$log_username.'\')"><img src="sourceImagery/downVote.png"></button>';
    }
    $photoList .= '<h3>Vote Tally: <span id="vote_tally'.$imageName.'">'.$vote_state.'</span></h3>';
    $photoList .= '<div id="comment_area'.$what.'">';
    //query for all comments on this image  and generate appropriate HTML
    $sql = "SELECT * FROM comments WHERE image_author='$who' AND image_id='$what'";
    $query = mysqli_query($db_conx, $sql);
    while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
        $poster = $row['poster'];
        $data = $row['data'];
        $postDate = $row['postdate'];
        $photoList .= '<br>'.$data.'<br> Posted by: '.$poster. ' at '.$postDate;
    }
    $photoList .= '</div>';
    $photoList .= '</div>';
    $photoList .= '<hr />';
}
?>

<script>
    //creates annoying pop-up
function postVote(action, image_id, token, vote_state, voter){
        _("upVote"+image_id).disabled = true;
        _("downVote"+image_id).disabled = true;
        var ajax = ajaxObj("POST", "php_parsers/photo_system.php");
        ajax.onreadystatechange = function(){
            if(ajaxReturn(ajax) === true){
                var responseArray = ajax.responseText.split('|');
                if(responseArray[0] === "vote_up" || responseArray[0] === "vote_down"){
                    _("vote_tally"+image_id).innerHTML = responseArray[1];
                } else{
                    alert(ajax.responseText);
                } 
            }    
        }; 
            ajax.send("token="+token+"&action="+action+"&image_id="+image_id+"&vote_state="+vote_state+"&voter="+voter);
        };       
    //doesn't create annoying pop-up
function postToComment(action,poster,ta,image_id, image_author){
	var data = _(ta).value;
	if(data === ""){
		alert("Type something first");
		return false;
	}
	_("commentButton").disabled = true;
	var ajax = ajaxObj("POST", "php_parsers/photo_system.php");
	ajax.onreadystatechange = function() {
		if(ajaxReturn(ajax) === true) {
			if(ajax.responseText === "post_ok"){
				data = data.replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/\n/g,"<br />").replace(/\r/g,"<br />");
				var currentHTML = _("comment_area"+image_id).innerHTML;
				_("comment_area"+image_id).innerHTML = '<br>'+data+'<br> Posted by: '+poster+ ' just now'+currentHTML;
				_("commentButton").disabled = false;
				_(ta).value = "";
			} else {
				alert(ajax.responseText);
			}
		}
	};
	ajax.send("action="+action+"&poster="+poster+"&data="+data+"&image_id="+image_id+"&image_author="+image_author);
}
</script>