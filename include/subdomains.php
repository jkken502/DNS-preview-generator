<?php
function isSubdomainValid($subdomain, $host)
{
    return str_replace($host, "", $subdomain) != "";
}

function getSubdomainsFromOriginalRequest($subdomainsOnPreviewGenerator)
{
    return implode('-', explode('-', $subdomainsOnPreviewGenerator, -1));
}

function getPreviewID($subdomainsOnPreviewGenerator)
{
    $lastSubdomainArray =  explode('-', $subdomainsOnPreviewGenerator);
    return $lastSubdomainArray[count($lastSubdomainArray) - 1];
}