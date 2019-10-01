<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '../dbHelper.php';
require_once 'functions.php';
$db = new dbHelper();

$profile_id   = $_POST['profileid'];
$new_profile_number = $_POST['new_profile_number'];

$profile_update_query = "UPDATE memberships SET `profile_number` = '".$new_profile_number."' WHERE `profile_id` = '".$profile_id . "' ORDER BY version desc limit 1";
$profile_update = $db->prepare($profile_update_query);
$profile_update->execute();

$profile_status_check_query = "select profile_status_id from profiles where id = ".$profile_id;
$profile_status_check = $db->prepare($profile_status_check_query);
$profile_status_check->execute();

$current_status = $profile_status_check->fetch(0)['profile_status_id'];
if($current_status == '5'){
    $profile_update_query = "UPDATE profiles SET profile_status_id = '2' WHERE id = ".$profile_id;
    $profile_update = $db->prepare($profile_update_query);
    $profile_update->execute();
}

echo "success";

