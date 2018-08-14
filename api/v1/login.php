<?php

//include database connection file
require_once 'dbHelper.php';

$db = new dbHelper();

// verifying user from database using PDO
$stmt = $db->prepare("SELECT email, password from profiles WHERE email='".$_POST['user_email']."' && password='".$_POST['user_password']."'");
$stmt->execute();
$row = $stmt->rowCount();
if ($row > 0){
    echo "correct";
} else{
    echo 'wrong';
}

?>