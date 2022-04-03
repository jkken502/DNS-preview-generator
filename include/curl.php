<?php
require_once(dirname(__FILE__) . "/headers.php");

function getAllCookiesString()
{
    $requestCookies = "";
    foreach ($_COOKIE as $name => $value)
    {
            $requestCookies.="$name=$value; ";
    }
    //removes the "; " at the end of the $requestCookies string
    return substr($requestCookies,0,-2);
}

function updateBodyToPreviewURL($protocol, $subdomain, $domain, $previewURL, $body)
{
    $newBody=$body;
    if ($protocol == "http://")
        {
            $newBody = str_replace("https://" . $domain, "http://" . $domain, $newBody);
        }
        $newBody = str_replace($subdomain . "." . $domain, $subdomain . "-" . $domain, $newBody);
            $newBody = str_replace("." . $domain, "-" . $domain, $newBody);
        $newBody = str_replace($domain, $previewURL, $newBody);
        return $newBody;
}

function displayPreviewCurl($url, $domain,$serverDomain)
{
    $ch = curl_init($url);
    
    if($_SERVER['REQUEST_METHOD'] != 'GET')
    {
        $requestItems="";
        foreach ($_REQUEST as $key => $value) {
            $requestItems .= htmlspecialchars($key)."=".htmlspecialchars($value)."&";
    }
    $requestItems = substr($requestItems,0,-1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $_SERVER['REQUEST_METHOD']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $requestItems);
    }
    $requestHeaders=getHeadersForCurlRequest($domain,$serverDomain);
    curl_setopt($ch,CURLOPT_COOKIE,getAllCookiesString());
    curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_ENCODING, '');
    curl_setopt($ch, CURLOPT_HEADER, 1);
    $result = curl_exec($ch);
    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $header = substr($result, 0, $header_size);
    $body = substr($result, $header_size);
    $returnValue = ["header" =>$header, "body"=>$body];
    return $returnValue;
}