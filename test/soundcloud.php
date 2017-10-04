<!DOCTYPE html>
<html>
    <head></head>
    <body>
        <script src="http://connect.soundcloud.com/sdk.js"></script>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
        <script>
            var current_sound;
            var track_id;
            function load_sound(track_url) {
                    SC.initialize({
                        client_id: 'ca8c1802896517bc68c9c149f3e9f805'
                            });
                            SC.get('/resolve', {url: track_url}, function(track) {
                                alert("getting soundcloud data");
                                track_id = track.id;
                                alert(track_id);
                                SC.stream("/tracks/" + track.id, function(sound) {
                                    alert("streaming initiated");
                                    current_sound = sound;
                                    
                                                var is_playing = true;
                                                alert("first part")
                                                
                                                $('button[status="playing"]').css("font-size", 50).trigger("click");
                                                document.getElementById(track_url).setAttribute("status", "playing");
                                                
                                                sound.play({
                                                    whileplaying: function() {
                                                        var sofar = sound.position / 60000;
                                                        var total = sound.duration / 60000;
                                                        ////console.log("is playing: "+sofar+" out of: "+total);
                                                    }
                                                });
                                                document.getElementById(track_url).onclick = function() {                                               
                                                    alert("second part")
                                                    if (is_playing === false) {
                                                        $('button[status="playing"]').css("font-size", 50).trigger("click");
                                                        document.getElementById(track_url).setAttribute("status", "playing");
                                                        //pause other tracks by triggering those that are playing status
                                                        sound.resume();
                                                        is_playing = true;
                                                        document.getElementById(track_url).setAttribute("status", "playing");
                                                    } else if (is_playing === true) {
                                                        sound.pause();
                                                        document.getElementById(track_url).setAttribute("status", "not_playing");
                                            is_playing = false;
                                        }   
                                    };                                  
                                });
                            });
                        }
        </script>
        <form>
            <input id="sc_url" type="url">
            <input type="button" onclick="load_sound();" value="load">
        </form>
        <button status='not_playing' id="https://soundcloud.com/8dawn/8dio-cage-strings-maelstrom-by" onclick="load_sound('https://soundcloud.com/8dawn/8dio-cage-strings-maelstrom-by');">ONE</button>
        <br><br>
        <button status='not_playing' id="https://soundcloud.com/royalblooduk/little-monster-1" onclick="load_sound('https://soundcloud.com/royalblooduk/little-monster-1');">TWO</button>
        <br><br>
     
    </body>
</html>