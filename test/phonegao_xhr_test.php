<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<script>
    
var key = "130ce75a704544ad9007ea0d381c1d6b";
    var endpoint = "http://freegeoip.net/json/98.190.221.98";
    var xhr = new XMLHttpRequest();
    xhr.open("GET", endpoint, false);
    xhr.send();
    var json = JSON.parse(xhr.responseText);
    console.log(json);

</script>