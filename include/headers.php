<?php
function isHeaderSafe($header)
{
    $lowerCaseHeader = strtolower($header);
    return (strpos($lowerCaseHeader, "transfer-encoding") === false && strpos($lowerCaseHeader, "content-encoding") === false  && strpos($lowerCaseHeader, "content-length") === false && $lowerCaseHeader != "");
}

function sendHeaders($headers){
    $sentHeaders = [];
        foreach($headers as $header) {
            if (!isHeaderSafe($header)) {
                continue;
            }
                header($header,false);
                array_push($sentHeaders, $header);
        }
        return $sentHeaders;
}
function getHeadersForCurlRequest($domain,$serverDomain)
{
    $headerArray=[];
    
    foreach (getallheaders() as $header => $value) {
        if (strtolower($header) == "host") {
            array_push($headerArray, "Host: " . $domain);
            continue;
        }
        if(strtolower($header)=="content-length"){
         continue;   
        }
        if(strtolower($header)=="referer"){
         continue;   
        }
        $headerReplaced = str_replace("-".$serverDomain ,".".$domain, $value);
        $header_replaced = str_replace($serverDomain ,$domain, $headerReplaced);
        array_push($headerArray, "$header: ". $headerReplaced);
    }
    return $headerArray;
}
function updateHeadersToPreviewURL($subdomain,$domain,$previewURL,$headers)
{
    $newHeaders = str_replace($subdomain . "." . $domain, $subdomain . "-" . $domain, $headers);
    $newHeaders = str_replace("." . $domain, "-" . $domain, $newHeaders);
    $newHeaders = str_replace($domain, $previewURL, $newHeaders);
    return $newHeaders;
}