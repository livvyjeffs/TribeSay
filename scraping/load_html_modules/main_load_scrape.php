<?php
//this train will be used in scraped page class and contain all function for loading html
trait loader{
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
}
?>
