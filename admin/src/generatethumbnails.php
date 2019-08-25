<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../dbHelper.php';
require_once 'functions.php';
$db = new dbHelper();

function thumbnailImage($abspath, $destpath) {
    $image = new \Imagick(realpath($abspath));
    // $image->setbackgroundcolor('rgb(64, 64, 64)');
    $image->thumbnailImage(300,0);
    // Set to use jpeg compression
    $image->setImageCompression(Imagick::COMPRESSION_JPEG);
    // Set compression level (1 lowest quality, 100 highest quality)
    $image->setImageCompressionQuality(95);
    // Strip out unneeded meta data
    $image->stripImage();
    // Writes resultant image to output directory
    $image->writeImage($destpath);
    // Destroys Imagick object, freeing allocated resources in the process
    $image->destroy();
}
function saveThumbnails($abspath, $destpath){
    $image = new Imagick();
}

$picture_table_query = $db->prepare("select * from photos where 1 order by id");
$picture_table_query->execute();
$rows = $picture_table_query->rowCount();
if($rows > 0){
  $has_media = true;
  foreach ($picture_table_query->fetchAll(PDO::FETCH_ASSOC) as $p_item){
    $value['pics'][] = $p_item;
  }
}
// pp($value);
foreach ($value['pics'] as $key=>$pic){
    if(trim($pic["image"]) != ""){
      if( substr($pic['path'], 0, 4) != '/var'){
        $pic['path'] = '/var/www/vhost/castit.dk'.$pic['path'];
      }
      $img_src = $pic['path'].'/'.$pic['image'];
      if(!file_exists($img_src)){
        $new_img_file = $_SERVER['DOCUMENT_ROOT'] . '/images/uploads/' . $pic["image"];
        // $new_img_file = 'https://castit.dk/images/uploads/' . $pic["image"];
        // pp($new_img_file);
        if(file_exists($new_img_file)){
          $img_src = '/var/www/vhost/castit.dk/images/uploads/' . $pic["image"];
        }
        else{
          $img_timestamp = strtotime($pic['created_at']);
          $img_year   = date('Y', $img_timestamp);
          $img_month  = date('m', $img_timestamp);
          $img_day    = date('d', $img_timestamp);
          $img_id     = $pic['id'];
          // $img_src = 'http://' . $_SERVER['SERVER_NAME'] . '/profile_images/' . $img_year . '/' . $img_month . '/' . $img_day . '/' . $img_id . '/big_' . $pic['image'];
          $img_src = "/var/www/vhost/castit.dk/profile_images/" . $img_year . '/' . $img_month . '/' . $img_day . '/' . $img_id . '/big_' . $pic['image'];
          if(!file_exists($img_src)){
            $img_src = "/var/www/vhost/castit.dk/profile_images/" . $img_year . '/' . $img_month . '/' . $img_day . '/' . $img_id . '/' . $pic['image'];;
          }
          if(!file_exists($img_src)){
            $img_src = $pic['path'].'/'.$pic['image'];
          }
        }
      }

    if($img_src != ''){
        echo "pic-image"; pp($pic["image"]);
        echo "path" ; pp($img_src);
        thumbnailImage($img_src, "/var/www/vhost/castit.dk/phpthumbnails/".$pic['image']);
    }
    }
  }