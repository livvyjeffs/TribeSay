
<!DOCTYPE html>
<html>
    <head>  
        <script src="../js/extractor_api.js"></script>
    </head>
    <body>
        <input id="url" type="text" name="url">
        <input id="type" type="text" name="type">
        <button onclick="process();">Submit</button>
        <div id="content_here"></div>
        <script>
            function process(){
                var url = document.getElementById("url").value;
                var type = document.getElementById("type").value;
                alert(url);
                alert(type);
                extract(url,type);
            }
        </script>      
    </body>
</html>