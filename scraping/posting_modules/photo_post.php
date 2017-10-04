<?php
include_once("../php_includes/image_resize.php");//this could be used as a trait as well
include_once('../php_includes/check_login_status.php');
require($_SERVER["DOCUMENT_ROOT"]."/libs/aws/aws-autoloader.php");
class image_to_post{
    //declare variables
    public $postLink;
    public $db_file_name;
    public $filenameIn;
    public $fileTmpLoc;
    public $fileSize;
    public $fileExt;
    public $poster;
    public $loop_tracker;
    public $final_img_location;
    public $img_ratio;
    public $r_g_b = array();
    public $public_url;
    public $destination;
    //contruct by passing in variable to post
    function __construct($url_to_post, $poster, $destination) {
        $this->postLink = $url_to_post;
        $this->poster = $poster;
        $this->loop_tracker = "one_chance";
        $this->destination = $destination;
    }
    //get rid of everything after the ?
    function remove_url_variables() {
        if (strpos($this->postLink, "?") !== false) {
            $newLink = explode("?", $this->postLink);
            $this->filenameIn = $newLink[0];
        } else {
            $this->filenameIn = $this->postLink;
        }
    }
    //check to make sure that the tmp directory exists, if not then make
    function make_tmp() {
        if (!file_exists("/tmp")) {
            mkdir($_SERVER["DOCUMENT_ROOT"]."/tmp", 0755);
        }
    }
    //get and put contents
    function file_get_and_put() {
        //replace spaces and get contents
        $newFileName = str_replace(" ", "%20", $this->filenameIn);
        $contentOrFalseOnFailure = file_get_contents($newFileName);
        //chose tmp location and put contents
        $this->fileTmpLoc = $_SERVER["DOCUMENT_ROOT"]."/tmp/" . basename($newFileName);
        if($this->loop_tracker === "exit"){
            $this->fileTmpLoc = $_SERVER["DOCUMENT_ROOT"]."/tmp/" . date("DMjGisY") . "" . rand(1000, 9999);
        }
        $this->fileSize = file_put_contents($this->fileTmpLoc, $contentOrFalseOnFailure);
    }
    //check if too small or large
    function check_size(){
        //too small?
        list($width, $height) = getimagesize($this->fileTmpLoc);
        if ($width < 10 || $height < 10) {
            //return file get and put but with unprocessed url, but only once to avoid infinite loop
            if($this->loop_tracker === "one_chance"){
                $this->filenameIn = $this->postLink;
                $this->loop_tracker = "exit";
                $this->file_get_and_put();
            }else{
                header("location: ../message.php?msg=ERROR: That image has no dimensions loopTrack: ".$this->loop_tracker."  postLink: ".$this->postLink."|||");
                exit();
            }
        }
        //too large?
        if ($this->fileSize > (1048576*12)) {
            header("location: ../message.php?msg=ERROR: Your image file was larger than 12mb");
            exit();
        }
    }
    //get file extension and generate random db name
    function gen_db_name() {
        //separate $filename inot pieces and collect file ext into variable
        $this->fileExt = pathinfo($this->fileTmpLoc, PATHINFO_EXTENSION);
        $this->db_file_name = date("DMjGisY") . "" . rand(1000, 9999) . "." . $this->fileExt; // WedFeb272120452013RAND.jpg
    }
    //could check the type but does it matter really?
    //
    //move image to permanent location
    function move_to_permanent() {
        $bucket = 'TribeSay_images';
        //create client
        $client = Aws\S3\S3Client::factory(array(
                    'key' => 'AKIAI3E72U4J2Q3264AA',
                    'secret' => '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'
        ));
        //upload an object
        $result = $client->putObject(array(
            'Bucket' => $bucket,
            'Key' => $this->destination.'/'.$this->poster."/".$this->db_file_name,
            'SourceFile' => $this->fileTmpLoc
        ));
        // Get the URL the object can be downloaded from
        $this->public_url = $result['ObjectURL'];
    }
    //resize images to within reasonable parameters
    function resize_image() {
        $target_file = $this->fileTmpLoc;
        $resized_file = $this->fileTmpLoc;
        $this->final_img_location = $resized_file;
        $wmax = 400;
        $hmax = 600;
        img_resize($target_file, $resized_file, $wmax, $hmax, $this->fileExt);
    }
    //get img ratio and rgb values
    function get_img_specs(){
        $img = imagecreatefromjpeg($this->final_img_location);//also detect type and and png and gif
        $h = imagesy($img);
        $w = imagesx($img);
        $this->img_ratio = $h/$w;
        $this->r_g_b = array(1,1,1);
    }
    //old move to perm
   /* function old_move_to_permanent(){
        if (!file_exists("../stream/$this->poster")) { mkdir("../stream/$this->poster", 0755); }
        $moveResult = rename($this->fileTmpLoc, "../stream/$this->poster/$this->db_file_name");
        if ($moveResult !== true) {
            header("location: ../message.php?msg=ERROR: File upload failed");
            exit();
        }
    }*/
}
?>