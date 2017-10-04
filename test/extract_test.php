<?php


?>

<script>
    var key = "130ce75a704544ad9007ea0d381c1d6b";
    var endpoint = "http://api.embed.ly/1/extract?key=" + key + "&url=" + "http://www.wired.com";
    var xhr = new XMLHttpRequest();
    xhr.open("GET", endpoint, false);
    xhr.send();
    var json = JSON.parse(xhr.responseText);
    //console.log(json);
    
    if (json.content === null){
        alert("is null no quotes");
    }else{
        alert("no null");
    }
</script>