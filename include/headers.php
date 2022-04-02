<?php
function isHeaderSafe($header)
{
    $lowerCaseHeader = strtolower($header);
    return (strpos($lowerCaseHeader, "transfer-encoding") === false && strpos($lowerCaseHeader, "content-encoding") === false  && strpos($lowerCaseHeader, "content-length") === false && $lowerCaseHeader != "");
}

function sendHeaders($headers){
    $sentHeaders = [];
        foreach($headers as $header)
        {
            if (isHeaderSafe($header))
            {
                header($header,false);
                array_push($sentHeaders, $header);
            }
        }
        return $sentHeaders;
}