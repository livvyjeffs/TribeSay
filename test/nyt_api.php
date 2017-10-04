<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<!DOCTYPE html>
<html>
    <head>
        <script>
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "http://api.nytimes.com/svc/search/v2/articlesearch.json?q=hoffman+addiction+recovery&begin_date=20140209&end_date=20140209&api-key=269923da90c6a5c01301007abd230aea:16:68794920", false);
            // Add your code below!
            xhr.send();
            var json = JSON.parse(xhr.responseText);
            ////console.log(json.response.docs[0].multimedia[0].url);
            //////console.log(xhr.response);
            ////console.log(xhr.status);
            ////console.log(xhr.statusText);
            //////console.log(xhr.responseText);
        </script>
    </head>
    <body></body>
</html>