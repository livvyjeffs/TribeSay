<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script>
    
if (typeof jQuery !== 'undefined') {
        alert("check your console log for extractor output");
        $(document).ready(function() {
            alert("page is ready");
                $.ajax({// ajax call starts
                    url: 'http://ec2-54-82-196-199.compute-1.amazonaws.com/app_parsers/user_authentication.php', // JQuery loads serverside.php
                    data: {
                        e: 'firehose123',
                        p: 'firehose123',//e and p are used only for getting id_token from http://ec2-54-82-196-199.compute-1.amazonaws.com/app_parsers/user_authentication.php
                        /*id_token: '62002121',
                        username: 'TessierAshpool',
                        app_filter_array: '',
                        stream_media_type: 'mixed',
                        current_id_list: '',
                        scope: 'tribe',
                        infinite: 'no',
                        splode_status: 'mixed',
                        trigger: 'fresh_load',
                        page_owner: ''*/
                    },
                        
                        // Send value of the clicked button
                    dataType: 'json', // Choosing a JSON datatype
                    success: function(data) // Variable data contains the data we get from serverside
                    {
                        if (data.message === "success") {
                            console.log(data);
                            alert("SUCCESS: check console for detailed confirmation.");
                        } else {
                            console.log(data);
                            alert(data.message);
                        }
                    }
                });
                return false; // keeps the page from not refreshing 
            });
        } else {
            alert("jQuery library is not found. Run again bookmarklet again please.");
        }
</script>