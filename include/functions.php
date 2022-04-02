<?php
require_once(dirname(__FILE__, 2) . "/settings.php");
require_once(dirname(__FILE__) . "/headers.php");
require_once(dirname(__FILE__) . "/mysql.php");
$host = SERVER_HOST;




function isSubdomainValid($subdomain, $host)
{
    return str_replace($host, "", $subdomain) != "";
}

function isArrayValid($array)
{
    foreach ($array as $item)
    {
        if (!isset($item))
        {
            unset($item);
            return false;
        }
    }
    return true;
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
    
    $requestHeaders=[];
    
    foreach (getallheaders() as $header => $value)
    {
        if (strtolower($header) == "host")
        {
            array_push($requestHeaders, "Host: " . $domain);
        }
        else
        if(strtolower($header)=="content-length"){
            
        }
        else
        if(strtolower($header)=="referer"){
            
        }
        else
        {
            $headerReplaced = str_replace("-".$serverDomain ,".".$domain, $value);
            $header_replaced = str_replace($serverDomain ,$domain, $headerReplaced);
            
            array_push($requestHeaders, "$header: ". $headerReplaced);
        }
    }
    
    $requestCookies = "";
    foreach ($_COOKIE as $name => $value)
    {
       
            $requestCookies.="$name=$value; ";
    }
    $requestCookies = substr($requestCookies,0,-2);
    curl_setopt($ch,CURLOPT_COOKIE,$requestCookies);
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
    $returnValue = [$header, $body];
    return $returnValue;
}

function displayPreview()
{
    $path = $_SERVER['REQUEST_URI'];
    $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
    $allSubdomains = str_replace("." . SERVER_HOST, "", $_SERVER['HTTP_HOST']);
    if (!isSubdomainValid($allSubdomains, SERVER_HOST))
    {
        echo "Error please try again.<br>";
        displayForm();
    }
    $sourceSubdomain = implode('-', explode('-', $allSubdomains, -1));
    $subdomainArray = explode('.', $allSubdomains);
    $lastSubdomainArray = explode('-',$subdomainArray[count($subdomainArray) - 1]);
    $previewID = $lastSubdomainArray[count($lastSubdomainArray) - 1];
    
    //echo $sourceSubdomain ."<br>";
    //echo $previewID . "<br>";
    //die();

    if ($previewID != "")
    {
        $mysqlResults = getDomainAndIpFromDatabase($previewID,$sourceSubdomain);
        if (count($mysqlResults) < 3 || !isArrayValid($mysqlResults))
        {
            die("Unable to retrive domain and IP from the database.");
        }
        $ip = $mysqlResults[0];
        $domain = $mysqlResults[1];
        $sourceSubdomain = $mysqlResults[2]?$mysqlResults[2]:"";
        $curl = displayPreviewCurl($protocol . $ip . $path, $sourceSubdomain?"$sourceSubdomain.$domain":"$domain",$previewID . "." . SERVER_HOST);
        if (count($curl) < 2 || !isArrayValid($curl))
        {
            die("Curl was unable to fetch the website.");
        }
        $header = str_replace($domain, $previewID . "." . SERVER_HOST, $curl[0]);
        $header = str_replace($sourceSubdomain . "." . $domain, $sourceSubdomain . "-" . $previewID . "." . SERVER_HOST, $header);
        $header = str_replace("." . $domain, "-" . $previewID . "." . SERVER_HOST, $header);
        $responseHeaders = explode("\r\n", $header);
        $body = $curl[1];
        if ($protocol == "http://")
        {
            $body = str_replace("https://" . $domain, "http://" . $previewID . "." . SERVER_HOST, $body);
        }
        $body = str_replace($sourceSubdomain . "." . $domain, $sourceSubdomain . "-" . $previewID . "." . SERVER_HOST, $body);
            $body = str_replace("." . $domain, "-" . $previewID . "." . SERVER_HOST, $body);
        $body = str_replace($domain, $previewID . "." . SERVER_HOST, $body);
        $sentHeaders = sendHeaders($responseHeaders);
        //die(var_dump($sentHeaders));
        echo $body;
    }
    else
    {
        echo "Link is expired.";
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