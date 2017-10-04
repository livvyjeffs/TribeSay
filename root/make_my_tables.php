<?php
include_once("../php_includes/db_conx.php");

$tbl_users = "CREATE TABLE IF NOT EXISTS users (
                id INT(11) NOT NULL AUTO_INCREMENT,
                username VARCHAR(16) NOT NULL,
                email VARCHAR(255) NOT NULL,
                password VARCHAR(255) NOT NULL,
                gender ENUM('m', 'f') NOT NULL,
                website VARCHAR(255) NULL,
                country VARCHAR(255) NULL,
                userlevel ENUM('a', 'b', 'c', 'd') NOT NULL DEFAULT 'a',
                avatar VARCHAR(255) NULL,
                ip VARCHAR(255) NOT NULL,
                signup DATETIME NOT NULL,
                lastlogin DATETIME NOT NULL,
                notescheck DATETIME NOT NULL,
                activated ENUM('0', '1') NOT NULL DEFAULT '0',
                PRIMARY KEY (id),
                UNIQUE KEY username (username,email)
            )";
$query = mysqli_query($db_conx, $tbl_users);
if($query === TRUE) {
    echo "<h3>user table created OK :) </h3>";
} else {
    echo "<h3>user table NOT created :(</h3>";
}
////////////////////////////////////////////////////////////////////////////////
$tbl_useroptions = "CREATE TABLE IF NOT EXISTS useroptions (
                    id INT(11) NOT NULL,
                    username VARCHAR(16) NOT NULL,
                    background VARCHAR(255) NOT NULL,
                    question VARCHAR(255) NULL,
                    answer VARCHAR(255) NULL,
                    PRIMARY KEY (id),
                    UNIQUE KEY username (username)
                )";
$query = mysqli_query($db_conx, $tbl_useroptions);
if ($query === TRUE) {
    echo "<h3>useroptions table created OK :) </h3>";
} else {
    echo "<h3>useroptions table NOT created :( </h3>";
}
////////////////////////////////////////////////////////////////////////////////
$tbl_friends = "CREATE TABLE IF NOT EXISTS friends (
                id INT(11) NOT NULL AUTO_INCREMENT,
                user1 VARCHAR(16) NOT NULL,
                user2 VARCHAR(16) NOT NULL,
                datemade DATETIME NOT NULL,
                accepted ENUM('0', '1') NOT NULL DEFAULT '0',
                PRIMARY KEY (id)
            )";
$query = mysqli_query($db_conx, $tbl_friends);
if ($query === TRUE) {
    echo "<h3>friends table created OK :) <h3>";
} else {
    echo "<h3>friends table NOT created :( </h3>";
}
////////////////////////////////////////////////////////////////////////////////
$tbl_blockedusers = "CREATE TABLE IF NOT EXISTS blockedusers (
                    id INT(11) NOT NULL AUTO_INCREMENT,
                    blocker VARCHAR(16) NOT NULL,
                    blockee VARCHAR(16) NOT NULL,
                    blockdate DATETIME NOT NULL,
                    PRIMARY KEY (id)
                )";
$query = mysqli_query($db_conx, $tbl_blockedusers);
if ($query === TRUE) {
    echo "<h3>blockedusers table created OK :) </h3>";
} else {
    echo "<h3>blockedusers table NOT created :( </h3>";
}
////////////////////////////////////////////////////////////////////////////////
$tbl_status = "CREATE TABLE IF NOT EXISTS status (
                id INT(11) NOT NULL AUTO_INCREMENT,
                osid INT(11) NOT NULL,
                account_name VARCHAR(16) NOT NULL,
                author VARCHAR(16) NOT NULL,
                type ENUM('a', 'b', 'c') NOT NULL,
                data TEXT NOT NULL,
                postdate DATETIME NOT NULL,
                PRIMARY KEY (id)
            )";
$query = mysqli_query($db_conx, $tbl_status);
if ($query === TRUE) {
    echo "<h3>status table created OK :) </h3>";
} else {
    echo "<h3>status table NOT created :( </h3>";
}
////////////////////////////////////////////////////////////////////////////////
$tbl_photos = "CREATE TABLE IF NOT EXISTS photos (
                id INT(11) NOT NULL AUTO_INCREMENT,
                user VARCHAR(16) NOT NULL,
                gallery VARCHAR(16) NOT NULL,
                filename VARCHAR(255) NOT NULL,
                description VARCHAR(255) NULL,
                uploaddate DATETIME NOT NULL,
                PRIMARY KEY (id)
            )";
$query = mysqli_query($db_conx, $tbl_photos);
if ($query === TRUE) {
    echo "<h3>photos table created OK :) </h3>";
} else {
    echo "<h3>photos table NOT created :( </h3>";
}
////////////////////////////////////////////////////////////////////////////////
$tbl_notifications = "CREATE TABLE IF NOT EXISTS notifications (
                    id INT(111) NOT NULL AUTO_INCREMENT,
                    username VARCHAR(16) NOT NULL,
                    initiator VARCHAR(16) NOT NULL,
                    app VARCHAR(255) NOT NULL,
                    note VARCHAR(255) NOT NULL,
                    did_read ENUM('0', '1') NOT NULL DEFAULT '0',
                    date_time DATETIME NOT NULL,
                    PRIMARY KEY (id)
            )";
$query = mysqli_query($db_conx, $tbl_notifications);
if ($query === TRUE) {
    echo "<h3>notifications table created OK :) </h3>";
} else {
    echo "<h3>notifications table NOT created :( </h3>";
}
////////////////////////////////////////////////////////////////////////////////
$tbl_photostream = "CREATE TABLE IF NOT EXISTS photostream (
                id INT(11) NOT NULL AUTO_INCREMENT,
                user VARCHAR(16) NOT NULL,
                filename VARCHAR(255) NOT NULL,
                description VARCHAR(255) NULL,
                uploaddate DATETIME NOT NULL,
                vote_state INT(11) NOT NULL DEFAULT 1,
                PRIMARY KEY (id)
            )";
$query = mysqli_query($db_conx, $tbl_photostream);
if ($query === TRUE) {
    echo "<h3>photoStream table created OK :) </h3>";
} else {
    echo "<h3>photoStream table NOT created :( </h3>";
}
///////////////////////////////////////////////////////////////////////////////
$tbl_comments = "CREATE TABLE IF NOT EXISTS comments (
                id INT(11) NOT NULL AUTO_INCREMENT,
                image_id VARCHAR(80) NOT NULL,
                poster VARCHAR(16) NOT NULL,
                image_author VARCHAR(16) NOT NULL,
                data TEXT NOT NULL,
                postdate DATETIME NOT NULL,
                PRIMARY KEY (id)
            )";
$query = mysqli_query($db_conx, $tbl_comments);
if ($query === TRUE) {
    echo "<h3>comments table created OK :) </h3>";
} else {
    echo "<h3>comments table NOT created :( </h3>";
}
////////////////////////////////////////////////////////////////////////////////
$tbl_votes = "CREATE TABLE IF NOT EXISTS votes (
             id INT(11) NOT NULL AUTO_INCREMENT,
             image_id VARCHAR(80) NOT NULL,
             voter VARCHAR(16) NOT NULL,
             token VARCHAR(16) NOT NULL,
             postdate DATETIME NOT NULL,
             PRIMARY KEY (id)
             )";
$query = mysqli_query($db_conx, $tbl_votes);
if($query === true){
    echo "<h3>votes table created OK :)</h3>";
} else {
    echo "<h3>votes table NOT created :(</h3>";
}
///////////////////////////////////////////////////////////////////////////////
$tbl_videos = "CREATE TABLE IF NOT EXISTS videos (
                id INT(11) NOT NULL AUTO_INCREMENT,
                poster VARCHAR(16) NOT NULL,
                videoID VARCHAR(80) NOT NULL,
                uploaddate DATETIME NOT NULL,
                PRIMARY KEY (id)
            )";
$query = mysqli_query($db_conx, $tbl_videos);
if ($query === TRUE) {
    echo "<h3>videos table created OK :) </h3>";
} else {
    echo "<h3>videos table NOT created :( </h3>";
}
////////////////////////////////////////////////////////////////////////////////
$tbl_articles = "CREATE TABLE IF NOT EXISTS articles (
                id INT(11) NOT NULL AUTO_INCREMENT,
                poster VARCHAR(16) NOT NULL,
                title VARCHAR(150) NOT NULL,
                imagesrc VARCHAR(300) NOT NULL,
                summary TEXT NOT NULL,
                postdate DATETIME NOT NULL,
                summary TEXT NOT NULL,
                vote_state int(11) NOT NULL,
                uniqueID varchar(400) NOT NULL,
                link varchar(400) NOT NULL,
                PRIMARY KEY (id)
            )";
$query = mysqli_query($db_conx, $tbl_articles);
if ($query === TRUE) {
    echo "<h3>articles table created OK :) </h3>";
} else {
    echo "<h3>articles table NOT created :( </h3>";
}
////////////////////////////////////////////////////////////////////////////////
$tbl_imageLinks = "CREATE TABLE IF NOT EXISTS imagelinks (
                    id int(11) NOT NULL AUTO_INCREMENT,
                    poster VARCHAR(16) NOT NULL,
                    source VARCHAR(100) NOT NULL,
                    postdate DATETIME NOT NULL,
                    PRIMARY KEY (id)
                    )";
$query = mysqli_query($db_conx, $tbl_imageLinks);
if ($query === true){
    echo "<h3>imagelinks table created OK :) </h3>";
} else {
    echo "<h3>imagelinks table NOT created :( </h3>";
}
////////////////////////////////////////////////////////////////////////////////
$tbl_articlevotes = "CREATE TABLE IF NOT EXISTS articlevotes (
             id INT(11) NOT NULL AUTO_INCREMENT,
             content_id VARCHAR(200) NOT NULL,
             voter VARCHAR(16) NOT NULL,
             token VARCHAR(16) NOT NULL,
             postdate DATETIME NOT NULL,
             PRIMARY KEY (id)
             )";
$query = mysqli_query($db_conx, $tbl_articlevotes);
if($query === true){
    echo "<h3>articlevotes table created OK :)</h3>";
} else {
    echo "<h3>articlevotes table NOT created :(</h3>";
}
///////////////////////////////////////////////////////////////////////////////
$tbl_videovotes = "CREATE TABLE IF NOT EXISTS videovotes (
             id INT(11) NOT NULL AUTO_INCREMENT,
             content_id VARCHAR(200) NOT NULL,
             voter VARCHAR(16) NOT NULL,
             token VARCHAR(16) NOT NULL,
             postdate DATETIME NOT NULL,
             PRIMARY KEY (id)
             )";
$query = mysqli_query($db_conx, $tbl_videovotes);
if($query === true){
    echo "<h3>videovotes table created OK :)</h3>";
} else {
    echo "<h3>videovotes table NOT created :(</h3>";
}
///////////////////////////////////////////////////////////////////////////////
$tbl_imagevotes = "CREATE TABLE IF NOT EXISTS imagevotes (
             id INT(11) NOT NULL AUTO_INCREMENT,
             content_id VARCHAR(200) NOT NULL,
             voter VARCHAR(16) NOT NULL,
             token VARCHAR(16) NOT NULL,
             postdate DATETIME NOT NULL,
             PRIMARY KEY (id)
             )";
$query = mysqli_query($db_conx, $tbl_imagevotes);
if($query === true){
    echo "<h3>imagevotes table created OK :)</h3>";
} else {
    echo "<h3>imagevotes table NOT created :(</h3>";
}
///////////////////////////////////////////////////////////////////////////////
$tbl_audio = "CREATE TABLE IF NOT EXISTS audio (
                id INT(11) NOT NULL AUTO_INCREMENT,
                poster VARCHAR(16) NOT NULL,
                audioCode VARCHAR(80) NOT NULL,
                uniqueID VARCHAR(100) NOT NULL,
                title VARCHAR(250) NOT NULL,
                uploaddate DATETIME NOT NULL,
                description VARCHAR(500) NOT NULL,
                tag1 VARCHAR(50) NOT NULL,
                tag2 VARCHAR(50) NOT NULL,
                tag3 VARCHAR(50) NOT NULL,
                tag4 VARCHAR(50) NOT NULL,
                tag5 VARCHAR(50) NOT NULL,
                PRIMARY KEY (id)
            )";
$query = mysqli_query($db_conx, $tbl_audio);
if ($query === TRUE) {
    echo "<h3>audio table created OK :) </h3>";
} else {
    echo "<h3>audio table NOT created :( </h3>";
}
////////////////////////////////////////////////////////////////////////////////
$tbl_audiovotes = "CREATE TABLE IF NOT EXISTS audiovotes (
             id INT(11) NOT NULL AUTO_INCREMENT,
             content_id VARCHAR(200) NOT NULL,
             voter VARCHAR(16) NOT NULL,
             token VARCHAR(16) NOT NULL,
             postdate DATETIME NOT NULL,
             PRIMARY KEY (id)
             )";
$query = mysqli_query($db_conx, $tbl_audiovotes);
if($query === true){
    echo "<h3>audiovotes table created OK :)</h3>";
} else {
    echo "<h3>audiovotes table NOT created :(</h3>";
}
///////////////////////////////////////////////////////////////////////////////
?>
