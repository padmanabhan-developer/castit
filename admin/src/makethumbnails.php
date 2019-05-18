<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../dbHelper.php';
require_once 'functions.php';
$db = new dbHelper();
pp('aaaa');
function thumbnailImage($abspath, $destpath) {
    $image = new Imagick(realpath($abspath));
    // $image->setbackgroundcolor('rgb(64, 64, 64)');
    $image->thumbnailImage(200,0);
    // Set to use jpeg compression
    $image->setImageCompression(Imagick::COMPRESSION_JPEG);
    // Set compression level (1 lowest quality, 100 highest quality)
    $image->setImageCompressionQuality(85);
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

$ch = curl_init("https://castit.dk/api/v1/getprofiles");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
$output = curl_exec($ch);
// pp($output);
$php_output = json_decode($output);
pp($php_output);
// exit;


// pp($value);
foreach ($php_output->profiles as $pic){
  pp($pic);
  $image_parts = explode('/', $pic->profile_image);
  $image_filename = $image_parts[count($image_parts)-1];
  pp($image_filename);
  $img_src = $pic->profile_image;
  $img_src = str_ireplace("https://castit.dk", "/var/www/vhost/castit.dk", $img_src);
  pp($img_src);
  if($img_src != ''){
    thumbnailImage($img_src, "/var/www/vhost/castit.dk/phpthumbnails_new/".$image_filename);
  }
}
