<?php
if (isset($_COOKIE["user"])) {
    if ($_COOKIE["user"] === "martin") {
        header("location: logout.php");
    }
}
if (isset($_SESSION["username"])) {
    if ($_SESSION["username"] === "martin") {
        header("location: logout.php");
    }
}
?>
