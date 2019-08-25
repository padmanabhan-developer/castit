<?php

require_once '../dbHelper.php';
require_once 'functions.php';
$db = new dbHelper();

$id = (isset($_GET['id'])) ? $_GET['id'] : '';
$mediatype = (isset($_GET['mediatype'])) ? $_GET['mediatype'] : 'all';
$value  = [];
$has_media = false;
$output = '';

$picture_table_query = $db->prepare("select * from photos where profile_id = $id AND media_slet_status = 0 order by position");
$picture_table_query->execute();
$rows = $picture_table_query->rowCount();
if($rows > 0){
  $has_media = true;
  foreach ($picture_table_query->fetchAll(PDO::FETCH_ASSOC) as $p_item){
    $value['pics'][] = $p_item;
  }
}

$video_table_query = $db->prepare("select * from videos where profile_id = $id AND media_slet_status = 0 order by position");
$video_table_query->execute();
$rows = $video_table_query->rowCount();
if($rows > 0){
  $has_media = true;
  foreach ($video_table_query->fetchAll(PDO::FETCH_ASSOC) as $v_item){
    $value['vids'][] = $v_item;
  }
}

if(is_numeric($id)){
  $output = '';
  $name_query = $db->prepare("SELECT CONCAT(first_name, ' ', last_name) as name from profiles where id = $id");
  $name_query->execute();
  $name = $name_query->fetch(0)['name'];
  
  $recently_updated = $db->prepare("UPDATE profiles set recently_updated = 'reviewed' where id = $id");
  $recently_updated->execute();

  $profile_number_query = $db->prepare("SELECT profile_number, profile_id from memberships where profile_id = $id");
  $profile_number_query->execute();
  $profile_number = $profile_number_query->fetch(0)['profile_number'];
  $value['profile_number'] = $profile_number;
  $value['name'] = $name;
}

if($has_media){
  switch($mediatype){
      case 'images':
        $output .= form_images_html($value, $name, $profile_number, true);
        break;
      case 'videos':
        $output .= form_videos_html($value, $name, $profile_number, true);
        break;
      default:
        $output .= form_images_html($value, $name, $profile_number) . form_videos_html($value, $name, $profile_number);
        break;
  }
}

$return = ['htmloutput'=>$output, 'jsonvalue'=>$value];
// echo $output;
echo json_encode($return);