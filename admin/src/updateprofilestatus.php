<?php
require_once '../dbHelper.php';
require_once 'functions.php';
$db = new dbHelper();

$profile_id   = $_POST['profile_id'];
$update_value = $_POST['update_value'];

$profile_update_query = "UPDATE profiles SET profile_status_id = ".$update_value." WHERE id = ".$profile_id;
$profile_update = $db->prepare($profile_update_query);
$profile_update->execute();

echo "success";