<?php
if(isset($_POST["get_html"]) && isset($_POST["url"])){                      
    $url = $_POST["url"];
    //get the host and scheme, use regex on this bitch to fix links
    $parsed_url = parse_url($url);
    $host = $parsed_url["host"];
    $scheme = $parsed_url["scheme"];
    //get html from source
    $html = file_get_contents($url);
    //$url = preg_replace('/<body/', "<div .body ", $url);
    //$url = preg_replace('/<\/body>/', "<\/div>", $url);
    //replace relative image paths with absolutes
    //$html = preg_replace('/src=./', "", $html);
    echo $html;
    exit();
}





//user these rules when reformatting sources above
/*function format_src_array_begin() {
        foreach ($this->src_array as $key => $src) {           
            if ($src[0] === "/" && $src[1] !== '/') {
                $this->src_array[$key] = $this->scheme . "://" . $this->host . $src;
            } elseif ($src[0] === "/" && $src[1] === '/') {
                $this->src_array[$key] = $this->scheme . ":" . $src;
            }
        }
    }*/
?>
