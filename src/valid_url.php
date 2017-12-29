<?php
/**
 * Created by PhpStorm.
 * User: devJacob
 * Date: 12/28/17
 * Time: 9:47 PM
 */

function isValidUrl($url){
    // first do some quick sanity checks:
    if(!$url || !is_string($url)){
        return false;
    }
    // quick check url is roughly a valid http request: ( http://blah/... )
    if( ! preg_match('/^http(s)?:\/\/[a-z0-9-]+(\.[a-z0-9-]+)*(:[0-9]+)?(\/.*)?$/i', $url) ){
        return false;
    }
    // the next bit could be slow:
    if(getHttpResponseCode_using_curl($url) != 200){
//      if(getHttpResponseCode_using_getheaders($url) != 200){  // use this one if you cant use curl
        return false;
    }
    // all good!
    return true;
}

function getHttpResponseCode_using_curl($url, $followredirects = true){
    // returns int responsecode, or false (if url does not exist or connection timeout occurs)
    // NOTE: could potentially take up to 0-30 seconds , blocking further code execution (more or less depending on connection, target site, and local timeout settings))
    // if $followredirects == false: return the FIRST known httpcode (ignore redirects)
    // if $followredirects == true : return the LAST  known httpcode (when redirected)
    if(! $url || ! is_string($url)){
        return false;
    }
    $ch = @curl_init($url);
    if($ch === false){
        return false;
    }
    @curl_setopt($ch, CURLOPT_HEADER         ,true);    // we want headers
    @curl_setopt($ch, CURLOPT_NOBODY         ,true);    // dont need body
    @curl_setopt($ch, CURLOPT_RETURNTRANSFER ,true);    // catch output (do NOT print!)
    if($followredirects){
        @curl_setopt($ch, CURLOPT_FOLLOWLOCATION ,true);
        @curl_setopt($ch, CURLOPT_MAXREDIRS      ,10);  // fairly random number, but could prevent unwanted endless redirects with followlocation=true
    }else{
        @curl_setopt($ch, CURLOPT_FOLLOWLOCATION ,false);
    }
//      @curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,5);   // fairly random number (seconds)... but could prevent waiting forever to get a result
//      @curl_setopt($ch, CURLOPT_TIMEOUT        ,6);   // fairly random number (seconds)... but could prevent waiting forever to get a result
//      @curl_setopt($ch, CURLOPT_USERAGENT      ,"Mozilla/5.0 (Windows NT 6.0) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1180.89 Safari/537.1");   // pretend we're a regular browser
    @curl_exec($ch);
    if(@curl_errno($ch)){   // should be 0
        @curl_close($ch);
        return false;
    }
    $code = @curl_getinfo($ch, CURLINFO_HTTP_CODE); // note: php.net documentation shows this returns a string, but really it returns an int
    @curl_close($ch);
    return $code;
}

function getHttpResponseCode_using_getheaders($url, $followredirects = true){
    // returns string responsecode, or false if no responsecode found in headers (or url does not exist)
    // NOTE: could potentially take up to 0-30 seconds , blocking further code execution (more or less depending on connection, target site, and local timeout settings))
    // if $followredirects == false: return the FIRST known httpcode (ignore redirects)
    // if $followredirects == true : return the LAST  known httpcode (when redirected)
    if(! $url || ! is_string($url)){
        return false;
    }
    $headers = @get_headers($url);
    if($headers && is_array($headers)){
        if($followredirects){
            // we want the the last errorcode, reverse array so we start at the end:
            $headers = array_reverse($headers);
        }
        foreach($headers as $hline){
            // search for things like "HTTP/1.1 200 OK" , "HTTP/1.0 200 OK" , "HTTP/1.1 301 PERMANENTLY MOVED" , "HTTP/1.1 400 Not Found" , etc.
            // note that the exact syntax/version/output differs, so there is some string magic involved here
            if(preg_match('/^HTTP\/\S+\s+([1-9][0-9][0-9])\s+.*/', $hline, $matches) ){// "HTTP/*** ### ***"
                $code = $matches[1];
                return $code;
            }
        }
        // no HTTP/xxx found in headers:
        return false;
    }
    // no headers :
    return false;
}