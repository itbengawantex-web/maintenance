<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$host = "localhost";
$username = "root";
$password = "";
$database = "btxprddb";

//$host = "localhost";
//$username = "adminphp";
//$password = "1234";
//$database = "btxprddb";


//koneksi
$con = mysqli_connect($host, $username, $password, $database);

//cek koneksi
if(!$con)
{
    header("location : .../errors/db.php");
    die(mysqli_connect_error($con));
}
else{
   // echo "Database Connected..!";
}
?>