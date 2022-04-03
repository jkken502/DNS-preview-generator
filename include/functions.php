<?php
require_once(dirname(__FILE__, 2) . "/settings.php");
require_once(dirname(__FILE__) . "/headers.php");
require_once(dirname(__FILE__) . "/mysql.php");
require_once(dirname(__FILE__) . "/curl.php");
require_once(dirname(__FILE__) . "/subdomains.php");
//$host = SERVER_HOST;

function isArrayValid($array)
{
    if(!isset($array)) {
        return false;
    }
    
    foreach ($array as $item) {
        if (!isset($item)) {
            unset($item);
            return false;
        }
    }
    return true;
}

function areResultsValid($results,$expectedArrayLength)
{
    return (isset($results) && count($results) >= $expectedArrayLength && isArrayValid($results));
}

function displayPreview()
{
    $path = $_SERVER['REQUEST_URI'];
    $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
    $allSubdomains = str_replace("." . SERVER_HOST, "", $_SERVER['HTTP_HOST']);
    if (!isSubdomainValid($allSubdomains, SERVER_HOST))
    {
        die("Error please try again.");
    }
    $sourceSubdomain = getSubdomainsFromOriginalRequest($allSubdomains);
    $sourceSubdomain = $sourceSubdomain?$sourceSubdomain:"";
    $previewID = getPreviewID($allSubdomains);
    if ($previewID != "")
    {
        $previewURL = $previewID . "." . SERVER_HOST;
        $mysqlResults = getDomainAndIpFromDatabase($previewID,$sourceSubdomain);
        
        
        
        if (!areResultsValid($mysqlResults,2))
        {
            die("Link is expired.");
        }
        
        $ip = $mysqlResults['ip'];
        $domain = $mysqlResults['url'];
        $curl = displayPreviewCurl($protocol . $ip . $path, $sourceSubdomain?"$sourceSubdomain.$domain":"$domain",$previewURL);
        if(!areResultsValid($curl,2))
        {
            die("Curl was unable to fetch the website.");
        }
        
        $header = updateHeadersToPreviewURL($sourceSubdomain,$domain,$previewURL,$curl['header']);
        $headerArray = explode("\r\n", $header);
        $body = updateBodyToPreviewURL($protocol, $sourceSubdomain, $domain, $previewURL, $curl['body']);
        $sentHeaders = sendHeaders($headerArray);
        echo $body;
    }
}


function handleForm()
{
    $ip=$_POST['ip'];
    $url=$_POST['url'];
    insertFormData($ip,$url);
}

function displayForm()
{
    echo '<form method="post"> <div><input type="text" placeholder="IP Address" title="IP Address" name="ip" required></div> <div><input type="text" placeholder="domain.com" title="domain" name="url" required></div> <div><input type="submit" value="submit"></div></form>';
}