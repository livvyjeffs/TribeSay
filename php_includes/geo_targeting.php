<?php
function rad($x) {
    return $x * pi() / 180;
}
function getDistance($p1,$p2){//pass in associative arrays
    $R = 6378137; // Earth’s mean radius in meter
    $dLat = rad($p2["lat"] - $p1["lat"]);
    $dLong = rad($p2["lng"] - $p1["lng"]);
    $a = sin($dLat / 2) * sin($dLat / 2) + cos(rad($p1["lat"])) * cos(rad($p2["lat"])) * sin($dLong / 2) * sin($dLong / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    $d = $R * $c;
    return $d / (1609.34); // returns the distance in miles
}

//98.190.221.98
//echo getenv('REMOTE_ADDR');
function ip_to_point($ip) {
    return json_decode(file_get_contents("http://freegeoip.net/json/" . $ip));
    //return array("lat" => $json->latitude, "lng" => $json->longitude);
}

class geo_target {
    public $user_lat,$user_long;
    public $lat_min,$lat_max,$long_min,$long_max;
    public $ad_lat, $ad_long, $max_radius;
    public $target_type;
    public $u_arr, $ad_arr, $geo_data;
    
    public function __construct($ip, $target_type, $geo_data) {
        $this->geo_data = $geo_data;
        $this->target_type = $target_type;
        $u_coor = ip_to_point($ip);
        $this->user_lat = $u_coor->latitude;
        $this->user_long = $u_coor->longitude; 
        $this->u_arr = array("lat" => $this->user_lat, "lng" => $this->user_long);
    }
    public function get_bounds(){
        $this->geo_data = explode(",", $bounds);
        $this->lat_min = $this->geo_data[0];
        $this->lat_max = $this->geo_data[1];
        $this->long_min = $this->geo_data[2];
        $this->long_max = $this->geo_data[3];       
    }
    public function check_in_bounds(){
        if($this->lat_min <= $this->user_lat && $this->user_lat <= $this->lat_max && $this->long_min <= $this->user_long && $this->user_long <= $this->long_max){
            return true;
        }else{
            return false;
        }
    }
    public function get_radius(){
        $this->geo_data = explode(",", $radius_data);
        $this->ad_lat = $this->geo_data[0];
        $this->ad_long = $this->geo_data[1];
        $this->max_radius = $this->geo_data[2];
        $this->ad_arr = array("lat" => $this->ad_lat, "lng" => $this->ad_long);
    }
    public function check_in_radius(){
        $distance = getDistance($this->u_arr, $this->ad_arr);
        if($distance <= $this->max_radius){
            return true;
        }else{
            return false;
        }
    }
    public function evaluate_geo(){
        if($this->target_type === "bounds"){
            $this->get_bounds();
            return $this->check_in_bounds();
        }elseif($this->target_type === "radius"){
            $this->get_radius();
            return $this->check_in_radius();
        }else{
            echo "invalid target type";
        }
    }
}
?>