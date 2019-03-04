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
if($update_value == 5 && $profile_id != ''){
    $get_current_profile_number = "SELECT profile_number from memberships where profile_id = '".$profile_id."'";
    $current_pn = $db->prepare($get_current_profile_number);
    $current_pn->execute();
    $current_profile_number = $current_pn->fetch(0)['profile_number'];
    $new_profile_number = 'B'.substr($current_profile_number, 1);
    
    $profile_number_update_bureau = "UPDATE memberships set profile_number = '".$new_profile_number."' where profile_id = '".$profile_id."'";
    $profile_number_update_bureau_query = $db->prepare($profile_number_update_bureau);
    $profile_number_update_bureau_query->execute();
    
}

    echo "success";