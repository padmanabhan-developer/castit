<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../dbHelper.php';
require_once 'functions.php';
$db = new dbHelper();

$profile_ids_sql = "select id from profiles";
$profile_ids_query = $db->prepare($profile_ids_sql);
$profile_ids_query->execute();
$profile_ids = $profile_ids_query->fetchAll(PDO::FETCH_OBJ);
pp($profile_ids);
foreach ($profile_ids as $profile_key => $pid) {
    pp($pid);
    $photos_sql = "select * from photos where profile_id = '".$pid->id."' order by id desc";
    $photos_query = $db->prepare($photos_sql);
    $photos_query->execute();
    $photos = $photos_query->fetchAll(PDO::FETCH_OBJ);
    $photo_count = count($photos);

    if($photo_count > 0){
        foreach($photos as $photo_key => $image){           
            if($photo_key != '0'){
                $current_position = $image->position;
                $image_position_update_sql = "update photos set position = '".$photo_count."' where profile_id = '".$pid->id."' and id = '".$image->id."'";
                $image_position_update_query = $db->prepare($image_position_update_sql);
                $image_position_update_query->execute();
            }
            $photo_count = $photo_count - 1;
        }
    }

    $videos_sql = "select * from videos where profile_id = '".$pid->id."' order by id desc";
    $videos_query = $db->prepare($videos_sql);
    $videos_query->execute();
    $videos = $videos_query->fetchAll(PDO::FETCH_OBJ);
    $video_count = count($videos);

    if($video_count > 0){
        foreach($videos as $video_key => $video){           
            if($video_key != '0'){
                $current_position = $video->position;
                $video_position_update_sql = "update videos set position = '".$video_count."' where profile_id = '".$pid->id."' and id = '".$video->id."'";
                pp($video_position_update_sql);
                $video_position_update_query = $db->prepare($video_position_update_sql);
                $video_position_update_query->execute();
            }
            $video_count = $video_count - 1;
        }
    }

}
