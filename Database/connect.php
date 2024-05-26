<?php

$host ="";
$sdbname="";
$username="";
$password="";

$mysqli= new mysqli($host,$username,$password,$sdbname);

if($mysqli->connect_errno){
    die("Connection Error: " . $mysqli->connect_error);

}else{
    return $mysqli;
}
?>