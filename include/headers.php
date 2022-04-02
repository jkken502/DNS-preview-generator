<?php
function isHeaderSafe($header)
{
    return strpos($header, "Transfer-Encoding") === false && strpos($header, "Content-Encoding") === false  && strpos($header, "Content-Length") === false && $header != "";
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