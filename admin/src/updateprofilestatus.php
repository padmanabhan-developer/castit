<?php
require_once '../dbHelper.php';
require_once 'functions.php';
$db = new dbHelper();

$profile_id   = $_POST['profile_id'];
$profile_number   = isset($_POST['profile_number']) ? $_POST['profile_number'] : '';
$update_value = $_POST['update_value'];

$profile_update_query = "UPDATE profiles SET profile_status_id = ".$update_value." WHERE id = ".$profile_id;
$profile_update = $db->prepare($profile_update_query);
$profile_update->execute();

if($update_value == 1 && $profile_number != ''){
    $membership_update_query = "UPDATE memberships set current = 1 where profile_id = '".$profile_id."' and profile_number = '". $profile_number."'" ;
    $profile_update = $db->prepare($membership_update_query);
    $profile_update->execute();
}

echo "success";