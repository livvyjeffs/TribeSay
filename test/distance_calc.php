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

class geo {
    public $user_lat,$user_long;
    public $lat_min,$lat_max,$long_min,$long_max;//encoded in db string geo_data BOUNDS
    public $ad_lat, $ad_long, $max_radius;//encoded in db string geo_data RADIUS
    public $target_type;//specified in db
    public $u_arr, $ad_arr, $geo_data, $distance;
    
    public function __construct($ip, $target_type, $geo_data) {
        $this->geo_data = $geo_data;
        $this->target_type = $target_type;
        $u_coor = ip_to_point($ip);
        $this->user_lat = $u_coor->latitude;
        $this->user_long = $u_coor->longitude; 
        $this->u_arr = array("lat" => $this->user_lat, "lng" => $this->user_long);
    }
    public function get_bounds(){
        $bds = explode(",", $this->geo_data);
        $this->lat_min = $bds[0];
        $this->lat_max = $bds[1];
        $this->long_min = $bds[2];
        $this->long_max = $bds[3];       
    }
    public function check_in_bounds(){
        if($this->lat_min <= $this->user_lat && $this->user_lat <= $this->lat_max && $this->long_min <= $this->user_long && $this->user_long <= $this->long_max){
            return true;
        }else{
            return false;
        }
    }
    public function get_radius(){
        $rd = explode(",", $this->geo_data);
        $this->ad_lat = $rd[0];
        $this->ad_long = $rd[1];
        $this->max_radius = $rd[2];
        $this->ad_arr = array("lat" => $this->ad_lat, "lng" => $this->ad_long);
    }
    public function check_in_radius(){
        $this->distance = getDistance($this->u_arr, $this->ad_arr);
        if($this->distance <= $this->max_radius){
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





//this point should be fixed to an ad
$y = array("lat" => 36.428331, "lng" => -89.776991);
//echo "distance: ".getDistance($x,$y)." miles";
//check lat/long parameters




$ip = '98.190.221.98';
$target_type = "radius";
$b_data = '39.5,40,-78,-76';
$r_data = "38.17,-78.40,1200";

$tt = new geo($ip, $target_type, $r_data);
$tt->evaluate_geo();
print_r($tt);
echo "<br><br>";
if($tt->evaluate_geo() === true){
    echo "true";
}else{
    echo "false";
}












?>
<script>
    var rad = function(x) {
  return x * Math.PI / 180;
};

var getDistance = function(p1, p2) {
  var R = 6378137; // Earth’s mean radius in meter
        var dLat = rad(p2.lat() - p1.lat());
        var dLong = rad(p2.lng() - p1.lng());
        var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(rad(p1.lat())) * Math.cos(rad(p2.lat())) *
                Math.sin(dLong / 2) * Math.sin(dLong / 2);
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        var d = R * c;
        return d / (1609.34); // returns the distance in miles
    };
    
  var calculate = function(){
     var lat = document.getElementById("lat").value;
     var long = document.getElementById("long").value; 
     
     var p1 = {};
     p1.lat = function(){
         return 83.2344;
     };
     p1.lng = function(){
         return 71.2345;
     };
     var p2 = {};
     p2.lat = function(){
         return 83.2344;
     };
     p2.lng = function(){
         return 78.2345;
     };
     var distance = getDistance(p1,p2);
     alert(distance);
 };
</script>
<!--
<!DOCTYPE html>
<html>
    <input id="lat" type="number" placeholder="latitude"><br>
    <input id="long" type="number" placeholder="longitude"><br>
    <button onclick="calculate();">Calculate</button><br>
    <input id="result" type="number" placeholder="result">
</html>

-->