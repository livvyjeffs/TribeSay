var remote_push = function() {
////assorted remote connectivity tests for bookmarket fuck you
//extract connection
    var current_url = document.URL;
    /*var key = "130ce75a704544ad9007ea0d381c1d6b";
    var endpoint = "http://api.embed.ly/1/extract?key=" + key + "&url=" + current_url;
    var xhr = new XMLHttpRequest();
    xhr.open("GET", endpoint, false);
    xhr.send();
    var json = JSON.parse(xhr.responseText);
    //console.log(json);
    var image = json.images[0].url;
    var title = json.title;
    var content = json.content;
    alert("image is: " + image+'\n\ncontent is: ' + content+ '\n\ntitle is: ' + title);
    */

    var username = prompt("enter username");
    var description = prompt("enter description");
    var tags = prompt("enter comma separated tags");
    var auth = prompt("enter authentication code");

    var proceed = confirm("do you want to post this to BT?");
    if (proceed === false) {
        return;
    }
//test remote_post service connectivity

//test is jQuery is loaded
    if (typeof jQuery !== 'undefined') {
        alert("check your console log for extractor output");
        $(document).ready(function() {
            $.ajax({// ajax call starts
                url: frenetic.root + '/test/remote_jax.php', // JQuery loads serverside.php
                data: {
                    username: username,
                    description: description,
                    tags: tags,
                    auth: auth,
                    url: current_url}, // Send value of the clicked button
                dataType: 'json', // Choosing a JSON datatype
                success: function(data) // Variable data contains the data we get from serverside
                {
                    if(data.message === "success"){
                        //console.log(data);
                        alert("SUCCESS: check console for detailed confirmation.");
                    }else{
                        //console.log(data);
                        alert(data.message);
                    }
                }
            });
            return false; // keeps the page from not refreshing 
        });
    } else {
        alert("jQuery library is not found. Run again bookmarklet again please.");
    }
};

remote_push();