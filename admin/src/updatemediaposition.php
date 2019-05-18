<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '../dbHelper.php';
require_once 'functions.php';
$db = new dbHelper();

$mediatype  = $_POST['mediatype'];
$new_order = $_POST['new_order'];

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

if($table_name != ''){
    foreach($new_order as $key => $media_id){
        $media_update_query = "UPDATE ".$table_name." SET position = ".$key." WHERE id = ".$media_id;
        $media_update = $db->prepare($media_update_query);
        $media_update->execute();
    }
}
