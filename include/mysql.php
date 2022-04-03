<?php
function insertFormData($ip,$url)
{
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (mysqli_connect_errno())
    {
        die("Connection failed: " . mysqli_connect_error());
    }
    $stmt = $conn->prepare("insert into " . DB_TABLE . " (ip,url) values (?,?)");
    $stmt->bind_param("ss", $ip, $url);
    if ($stmt->execute())
    {
        $last_id = mysqli_insert_id($conn);
        $sql = "select subdomain from " . DB_TABLE . " where id='" . $last_id . "' limit 1";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        $url = "http://" . $row['subdomain'] . "." . SERVER_HOST;
        echo "Your preview link has been generated <a href=\"" . $url . "\">" . $url . "</a>";
        displayForm();
    }
    else
    {
        echo "Error: <br>" . mysqli_error($conn);
    }
    mysqli_close($conn);
}

function getDomainAndIpFromDatabase($previewID,$sourceSubdomain)
{
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (mysqli_connect_errno())
    {
        die("Connection failed: " . mysqli_connect_error());
    }
    $stmt = $conn->prepare("SELECT * FROM " . DB_TABLE . " where subdomain=? limit 1");
    $stmt->bind_param("s", $previewID);
    $stmt->execute();
    $result = $stmt->get_result();
    if (mysqli_num_rows($result) > 0)
    {
        if ($row = mysqli_fetch_assoc($result))
        {
            $returnArray=["ip" => $row['ip'], "url" => $row['url']];
            mysqli_close($conn);
            return $returnArray;
        }
    }
    mysqli_close($conn);
}