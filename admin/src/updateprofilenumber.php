<?php
require_once '../dbHelper.php';
require_once 'functions.php';
$db = new dbHelper();
pp($_POST);
$profile_id   = $_POST['profileid'];
$new_profile_number = $_POST['new_profile_number'];

$profile_update_query = "UPDATE memberships SET `profile_number` = '".$new_profile_number."' WHERE `profile_id` = '".$profile_id . "' ORDER BY version limit 1";
pp($profile_update_query);
$profile_update = $db->prepare($profile_update_query);
$profile_update->execute();

echo "success";

