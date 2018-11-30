<?php
include_once "functions.php";
require_once '../v1/dbHelper.php';
global $db;
$db = new dbHelper();
/**
 * tables : 
 * profile_grouping - group_id, token_id, group_name, user_id, status, added_on, user_ip_address
 * grouping - profile_id, group_id, profile_notes
 * 
 */

$group_id = $_REQUEST['group_id'];
$token_id = $_REQUEST['token_id'];
$group_name = $_REQUEST['group_name'];
$user_id = $_REQUEST['user_id'];
$status = "1";
$added_on = date("Y-m-d H:i:s");
$user_ip_address = $_SERVER['REMOTE_ADDR'];

$profile_id = $_REQUEST['user_id'];
$group_id = $group_id;
$profile_notes = $_REQUEST['profile_notes'];

