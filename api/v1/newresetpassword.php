<?php

require_once 'dbHelper.php';
$db = new dbHelper();

// var_dump($_POST);
$new_password = $_POST['resetpassword'];
$resethash = $_POST['resethash'];
$email = $_POST['email'];
if(isset($_POST['type']) && ($_POST['type'] == 'customer')){
    $query_string = "UPDATE customer_user set password = '".$new_password."' WHERE password = '".$resethash."' and email = '".$email."'";
}else{
    $query_string = "UPDATE profiles set password = '".$new_password."' WHERE password = '".$resethash."' and email = '".$email."'";
}
$user_profile_query = $db->prepare($query_string);
$user_profile_query->execute();
$row = $user_profile_query->rowCount();
if($row > 0){
    echo 'success';
}