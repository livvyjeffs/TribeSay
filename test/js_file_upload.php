<?php
    if(isset($_FILES["image"]["name"])){
        $fileName = $_FILES["image"]["name"];
        $fileTmpLoc = $_FILES["image"]["tmp_name"];
	$fileType = $_FILES["image"]["type"];
	$fileSize = $_FILES["image"]["size"];
	$fileErrorMsg = $_FILES["image"]["error"];
        echo "<br>";
        echo "<br>";
        echo "name: ".$fileName;
        echo "<br>";
        echo "tmp_location: ".$fileTmpLoc;
        echo "<br>";
        echo "type: ".$fileType;
        echo "<br>";
        echo "size: ".$fileSize;
        echo "<br>";
        echo "error_msg: ".$fileErrorMsg;
        echo "<br>";
        echo $_POST["subject"];
        echo "<br>";
        echo $_POST["message"];
        echo "<br>";
        echo $_POST["bob"];
        exit();
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
        <script src="../js/ajax.js"></script>
        <script>
            $(document).ready(function (e) {
                $('#debug_form').on('submit',(function(e) {
                    e.preventDefault();
                    alert("submit is triggered");
                    var formData = new FormData(this);
                    var subject = $('[name="subject"]').val();
                    var message = $('[name="message"]').val();
                    formData.append("bob","millet");
                    $.ajax({
                        type:'POST',
                        url: 'js_file_upload.php',
                        data:formData,
                        cache:false,
                        contentType: false,
                        processData: false,
                        success:function(data){
                            alert("success");
                            //console.log(data);
                            $('#info_here').html(data);
                        },
                        error: function(data){
                            alert("error");
                            //console.log(data);
                        }
                    });
                }));
            });           
        </script>
    </head>
    <body>
        <form name='photo' id='debug_form' enctype='multipart/form-data'  method='post'>
            <input name='subject' type='text' placeholder='Bug Subject'>
            <textarea name='message' rows='15' placeholder='Report bugs here. Below you can upload a screenshot. Thank you for beta testing!'></textarea>
            <input type='file' id='ImageBrowse'  name='image' size='30'/>
            <input type='submit' value='submit'>
        </form>
        <div id="info_here"></div>
    </body>
</html>
