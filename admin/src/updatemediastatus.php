<?php
require_once '../dbHelper.php';
require_once 'functions.php';
$db = new dbHelper();

$media_id   = $_POST['media_id'];
$mediatype  = $_POST['mediatype'];
$update_value = $_POST['update_value'];

switch($mediatype){
  case 'image'  :
  case 'images' :
    $table_name = 'photos';
    break;
  case 'video'  :
  case 'videos' :
    $table_name = 'videos';
    break;
  default:
    $table_name = '';
    break;
}

$media_update_query = "UPDATE ".$table_name." SET published = ".$update_value." WHERE id = ".$media_id;
$media_update = $db->prepare($media_update_query);
$media_update->execute();

echo $media_update_query;