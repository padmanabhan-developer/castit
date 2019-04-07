<?php

require_once '../dbHelper.php';
require_once 'functions.php';
$db = new dbHelper();

$id = (isset($_GET['id'])) ? $_GET['id'] : '';
$mediatype = (isset($_GET['mediatype'])) ? $_GET['mediatype'] : 'all';
$value  = [];
$has_media = false;
$output = '';
$picture_table_query  = "select * from photos where profile_id = $id";
$video_table_query    = "select * from videos where profile_id = $id";

$picture_table_query = $db->prepare("select * from photos where profile_id = $id");
$picture_table_query->execute();
$rows = $picture_table_query->rowCount();
if($rows > 0){
  $has_media = true;
  foreach ($picture_table_query->fetchAll(PDO::FETCH_ASSOC) as $p_item){
    $value['pics'][] = $p_item;
  }
}

$video_table_query = $db->prepare("select * from videos where profile_id = $id");
$video_table_query->execute();
$rows = $video_table_query->rowCount();
if($rows > 0){
  $has_media = true;
  foreach ($video_table_query->fetchAll(PDO::FETCH_ASSOC) as $v_item){
    $value['vids'][] = $v_item;
  }
}

if($has_media){
  $output = '';
  $name_query = $db->prepare("SELECT CONCAT(first_name, ' ', last_name) as name from profiles where id = $id");
  $name_query->execute();
  $name = $name_query->fetch(0)['name'];

  $profile_number_query = $db->prepare("SELECT profile_number from memberships where profile_id = $id");
  $profile_number_query->execute();
  $profile_number = $profile_number_query->fetch(0)['profile_number'];
  $value['profile_number'] = $profile_number;
  $value['name'] = $name;
  
  switch($mediatype){
      case 'images':
        $count = 0;
        $wrap_begin = '<div class="product-sec"><div class="product-row">';
        $wrap_end   = '</div></div>';
        $output .= $wrap_begin;
        foreach ($value['pics'] as $key=>$pic){
          // $new_img_file = 'http://' . $_SERVER['SERVER_ADDR'] . '/images/uploads/' . $pic["image"];
          $new_img_file = 'https://castit.dk/images/uploads/' . $pic["image"];
          if(file_exists($new_img_file)){
            $img_src = $new_img_file;
          }
          else{
            $img_timestamp = strtotime($pic['created_at']);
            $img_year   = date('Y', $img_timestamp);
            $img_month  = date('m', $img_timestamp);
            $img_day    = date('d', $img_timestamp);
            $img_id     = $pic['id'];
            // $img_src = 'http://' . $_SERVER['SERVER_ADDR'] . '/profile_images/' . $img_year . '/' . $img_month . '/' . $img_day . '/' . $img_id . '/big_' . $pic['image'];
            $img_src = 'https://castit.dk/profile_images/' . $img_year . '/' . $img_month . '/' . $img_day . '/' . $img_id . '/big_' . $pic['image'];
            // if(file_exists($old_img_file)){
            //   $img_src = $old_img_file;
            // }
          }
          if($img_src != ''){
            if($count < 6){
              $output .= form_media_inner_html($img_src, $name, $profile_number);
              $count++;
            }
            else{
              $output .= $wrap_end . $wrap_begin;
              $count = 0;
            }
            
          }
        }
        break;
      case 'videos':
        foreach($value['vids'] as $key => $video){
          pp($video);
        }
        $vdo_thumb_url = "http://assets3.castit.dk/videos/profiles/2016-03-29/f71e9bde-9521-4a00-a81b-c4329b6a6e70_Maja_v_full.jpg";
        $output = '';
        break;
  
  }
}

$return = ['htmloutput'=>$output, 'jsonvalue'=>$value];
// echo $output;
echo json_encode($return);