<?php
//include database connection file
require_once 'dbHelper.php';

$db = new dbHelper();

// verifying user from database using PDO
$query_string = "SELECT email from profiles WHERE email='".$_POST['user_email']."' && password='".$_POST['user_password']."'";
$user_profile_query = $db->prepare($query_string);
$user_profile_query->execute();
$row = $user_profile_query->rowCount();
if ($row > 0){
  foreach ($user_profile_query->fetchAll(PDO::FETCH_ASSOC) as $key => $value) {
		$user_profile = json_encode($value);
    (session_status() === PHP_SESSION_ACTIVE ) ? '' : session_start();
    echo $user_profile;
  }
} 
else{
  echo 'wrong';
}

?>