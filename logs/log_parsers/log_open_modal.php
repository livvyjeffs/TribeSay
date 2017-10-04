<?php
if (isset($_POST["log_this"])) {
    $log_this = $_POST["log_this"].",".date('Y-m-d H:i:s')."||";
    //$file = '../log_files/log_modal.log';
    //destination can be: log_modal, no_article_txt, no_media_content
    $file = '../log_files/'.$_POST["destination"].'.log';
    file_put_contents($file, $log_this, FILE_APPEND | LOCK_EX);
    echo "success";
    exit();
}
?>
