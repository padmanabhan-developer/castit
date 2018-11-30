<?php
require_once '../dbHelper.php';
require_once 'functions.php';
$db = new dbHelper();

$media_id   = $_POST['media_id'];
$mediatype  = $_POST['mediatype'];

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

$media_update_query = "UPDATE ".$table_name." SET media_slet_status = 1 WHERE id = ".$media_id;
$media_update = $db->prepare($media_update_query);
$media_update->execute();
