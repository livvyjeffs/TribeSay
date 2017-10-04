

//service function for nytimes
function nytimes(url){
    
    var url = url.split("?")[0];
    
    var kyarray;
    var full_src;
    var new_img;
    var newer_img;
    var response_html = "";
    
    var date = url.match(/(\/)\d{4}(\/)\d{2}(\/)\d{2}(\/)/g);
    date = date[0].replace(/(\/)/g,"");
    
    //parse the url is generate and appropriate request
    var title = url.match(/(\w+-\w+-\w+)/);
    if(title === null){
        title = url.match(/\/[a-z]+\/[a-z]+\/[a-z]+/);
        kyarray = title[0].split("/");
    }else{
        kyarray = title[0].split("-");                                          //NEED TO TEST THESE REG EX AGAINST ALOT OF DATA
    }
    //&fq=\'"+url+"\'
    var request = "http://api.nytimes.com/svc/search/v2/articlesearch.json?q="+kyarray[0]+"+"+kyarray[1]+"&begin_date="+date+"&end_date="+date+"&api-key=269923da90c6a5c01301007abd230aea:16:68794920";
    //var request = "http://api.nytimes.com/svc/search/v2/articlesearch.json?fq=\'"+url+"\'&begin_date="+date+"&end_date="+date+"&api-key=269923da90c6a5c01301007abd230aea:16:68794920";
    var xhr = new XMLHttpRequest();
    xhr.open("GET", request, false);
    xhr.send();
    ////console.log(xhr.responseText);
    var images = xhr.responseText.match(/"url":"images.{8,100}jpg/g);
    var id = 0;
    images.forEach(function(img){
        new_img = img.replace("\"url\":\"","");
        newer_img = new_img.replace(/\\/g,"");
        full_src = "http://static01.nyt.com/" + newer_img;
        response_html += "<img id='"+id+"' class='notSelectedPicture' src='"+full_src+"' onclick='toggleSelectedPicture(\""+id+"\")' alt='none'>";      
        id++;
    });
    response_html += '<img onload="clean_images()" alt="holderImage" src="sourceImagery/spaceholder.jpg">';
    response_html += "|delimiter|"+url;
    return response_html;
    
    
    
    
    
    //var json = JSON.parse(xhr.responseText);
    //need to return an array with 0=>imagehtml 1=>url
    //make an array for image sources to handle multiple options... possibly 
    //there is a standard number of options so maybe not foreach...
    //array.forEach(functionOne); with function functionOne(value, index, ar){}
    /*var image_src = json.response.docs[0].multimedia[0].url;//this wasnt defined in last call, need to find better access? perhaps with regex...
    var full_src = "http://static01.nyt.com/" + image_src;
    
    ////console.log(full_src);
    
    document.getElementById("pictures").innerHTML = "<img src='"+full_src+"'>";*/
}