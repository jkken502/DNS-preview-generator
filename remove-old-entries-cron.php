<?php
$require_once(dir(__FILE__) . "/settings.php");
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
$sql="select * from " . DB_TABLE . " where createdAt <= current_timestamp - interval 21 day";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
  // output data of each row
  while($row = mysqli_fetch_assoc($result)) {
    echo "id: " . $row["id"]. " - subdomain: " . $row["subdomain"]. " - url: " . $row["url"]. " - ip: " . $row["ip"]. "<br>";
  }
} else {
  echo "0 results";
}

$sql="delete from " . DB_TABLE . " where createdAt <= current_timestamp - interval 21 day";
$result = mysqli_query($conn, $sql);
$count=mysqli_affected_rows($conn);
if($count >0 ){
echo $count . "rows deleted.";
}
mysqli_close($conn);
?>
