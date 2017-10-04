<?php
//include_once("load_html_modules/main_load_scrape.php");
//include_once("api_modules/main_api_scrape.php");
//this class optimized to generate an array of images based upon the url passed
//in upon instantiation.
class scraped_page{
    //use loader, servicer;
    //traits not supported in php 5.3
    //all known api interfacing hosts should go into this array
    //corresponsing cases in service picker and must be included also
    protected $api_hosts = array();
    protected $service_case;
    //generate the sorting switch statement whic executes appropriate method
    function pick_your_service(){
        switch ($this->service_case) {
            case "nytimes.com":
                $this->nytimes();
                break;
            default:
                break;
        }
    }
    //write the nytimes method
    function nytimes(){  
        //must install: http://www.php.net/manual/en/http.install.php to run http requests from server. for now default to known JS methods
        //$r = new HTTPRequest("http://api.nytimes.com/svc/search/v2/articlesearch.json?q=hoffman+addiction+recovery&begin_date=20140209&end_date=20140209&api-key=269923da90c6a5c01301007abd230aea:16:68794920", HTTP_METH_GET);
        $this->response_html = "api|nyt";
    }
    //END api TRAITS
    //declare variables
    public $src_array = array();
    protected $load_hosts = array("www.wired.com");
    //load function creates a local dom and load html from parents scrape url
    function get_src_array(){
        $local_dom = new DOMDocument; 
        $local_dom->loadHTMLFile($this->scrape_ready_url);
        $images = $local_dom->getElementsByTagName("img");
        foreach($images as $img){
            $src = $img->getAttribute("src");
            array_push($this->src_array, $src);
        }
    }
    //enforce proper format for scheme, host path of sources, unset if too small
    function format_src_array_begin() {
        foreach ($this->src_array as $key => $src) {           
            if ($src[0] === "/" && $src[1] !== '/') {
                $this->src_array[$key] = $this->scheme . "://" . $this->host . $src;
            } elseif ($src[0] === "/" && $src[1] === '/') {
                $this->src_array[$key] = $this->scheme . ":" . $src;
            }
        }
    }
    //get rid of all that are too small or incomplete paths
    function remove_invalids() {//we can also look for trends in transparency and src name (eg trans, space, 1x1, etc)
        //get repeats
        $array_count = array_count_values($this->src_array);
        
        //print_r($array_count);
        //exit();
        
        $repeated_elems = array();
        foreach($array_count as $src => $count){
            if($count > 1){
                array_push($repeated_elems, $src);
            }
        }
        //purge repeats
        $this->src_array = array_diff($this->src_array, $repeated_elems);
        //purge incomplete paths
        foreach ($this->src_array as $key => $src) {
            if ((strpos($src, "http://") === false && strpos($src, "https://") === false)) {
                unset($this->src_array[$key]);
            } else {
                
            }
        }
    }
    //run the standers load and clean sequence
    function run_base_load(){
        $this->get_src_array();
        $this->format_src_array_begin();
        $this->remove_invalids();
        $this->sources_to_html();
    }
    //end scraper trait
    //declare variables
    public $scrape_ready_url;
    public $host;
    public $scheme;
    public $frame;
    //this will require a getter is outside access desired
    public $api_or_load;
    public $response_html = array();
    protected $ifame_black_list;
    //contruct scraped page by passing in uri_encoded link received from ajax
    public function __construct($url_to_scrape) {
        $this->scrape_ready_url = urldecode($url_to_scrape);
        //check if link is an image file
    }
    //method to check if decoded link is an image
    public function check_if_image() {
        if (exif_imagetype($this->scrape_ready_url) !== false) {
            $sources = array($this->scrape_ready_url);  
            $this->response_html["image_sources"] = $sources;
            $this->response_html["warning"] = "image_file";
            $this->pass_back();
            return true;
        } else {
            return false;
        }
    }
    //parse the url and make schema and host available
    public function parse_url(){
        $parsed_url = parse_url($this->scrape_ready_url);
        $this->host = $parsed_url["host"];
        $this->scheme = $parsed_url["scheme"];
    }
    //check if we need to access api or load html for scrape
    public function check_api_or_load(){
        //default to unknown
        $this->api_or_load = "unknown";
        //check if this any of our known api hosts are in this one
        foreach($this->api_hosts as $host){
            if(strpos($this->host, $host) !== false){
                $this->api_or_load = "api";
                $this->service_case = $host;
                break 1;
            }
        }//otherwise check in the known load hosts
        if ($this->api_or_load !== "api") {
            foreach ($this->load_hosts as $host) {
                if (strpos($this->host, $host) !== false) {
                    $this->api_or_load = "load";
                    break 1; //havent tested this break yet
                }
            }
            $this->api_or_load = "load";
        }
    }
    //diverge flow path based on result of api or load result
    public function diverge_service_flow(){
        //if load then get and format source array
        if($this->api_or_load === "unknown" || $this->api_or_load === "load"){
            $this->run_base_load();
        }elseif($this->api_or_load === "api"){
            //run api type chechker then appropriate httpRequest
            $this->pick_your_service();
        }else {
            //throw error
        }
    }
    //thurn the source array into htmlfu
    public function sources_to_html(){
        $i = 0;
        $sources = array();
        foreach ($this->src_array as $source) {
            $id = 'image' . $i;
            array_push($sources, $source);
            $i++;
        }
        $this->response_html["image_sources"] = $sources;
        //add in the masonry trigger
        //$this->response_html .= '<img id="default_image" class="notSelectedPicture" style="visibility: hidden" onclick="toggleSelectedPicture($(this));" onload="clean_images()" alt="holderImage" src="sourceImagery/spaceholder.jpg">';
    }

    //get title
    public function get_title() {
        $titleArray = array();
        //generate new DOMdoc
        $article = new DOMDocument;
        $article->loadHTMLFile($this->scrape_ready_url);
        //get the articles title
        $titles = $article->getElementsByTagName("title");
        foreach ($titles as $title) {
            $articleTitle = $title->textContent;
            array_push($titleArray, $articleTitle);
        }
        $TITLE = $titleArray[0];
        $this->response_html["title"] = $TITLE;
    }

    //check if the iframe will be blocked
    public function check_frame() {
        //declare blacklist of blocked domains
        $this->ifame_black_list = array("yahoo.com", "github.com", "ektoplazm.com");
        foreach($this->ifame_black_list as $black){
            if(strpos($this->host, $black) !== false){
                $this->frame = "blocked";
                break;
            }
        }
        if ($this->frame !== "blocked") {
            //get external html
            $p = file_get_contents($this->scrape_ready_url);
            //check for blocker
            $pattern1 = "/window\.self.{1,10}window\.top/";
            $s1 = preg_match($pattern1, $p);
            //check for blocker2
            $pattern2 = "/window\.top.{1,10}window\.self/";
            $s2 = preg_match($pattern2, $p);
            //condition response
            if ($s1 === 1 || $s2 === 1) {
                $this->frame = "blocked";
            } else {
                $this->frame = "not_blocked";
            }
        }
    }

    //return to calling ajax function in desired format
    public function pass_back() {
        //also json_encode($array); is an options
        //echo json_encode($this->src_array);
        //echo implode($this->src_array);
        //add in the masonry trigger
        $this->response_html["link"] = $this->scrape_ready_url;
        echo json_encode($this->response_html);
    }
}
?>
