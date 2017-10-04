<?php
//this trait will be used in scraped page and handles all service based scraping
trait servicer{
    //all known api interfacing hosts should go into this array
    //corresponsing cases in service picker and must be included also
    protected $api_hosts = array("nytimes.com");
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
}
?>
