<?php
require_once '../dbHelper.php';
require_once 'functions.php';
$db = new dbHelper();

/*
// file name
$filename = $_FILES['file']['name'];

// Location
$location = 'upload/'.$filename;

// file extension
$file_extension = pathinfo($location, PATHINFO_EXTENSION);
$file_extension = strtolower($file_extension);

// Valid image extensions
$response = 0;
// Upload file
if(move_uploaded_file($_FILES['file']['tmp_name'],$location)){
  $response = $location;
}
echo $response;
*/

$fileName     = isset($_FILES["file"]["name"]) ? $_FILES["file"]["name"] : "";
$filename     = $fileName;
$fileTmpLoc   = isset($_FILES["file"]["tmp_name"]) ? $_FILES["file"]["tmp_name"] : "";
$fileType     = isset($_FILES["file"]["type"]) ? $_FILES["file"]["type"] : "";
$fileSize     = isset($_FILES["file"]["size"]) ? $_FILES["file"]["size"] : "";
$fileErrorMsg = isset($_FILES["file"]["error"]) ? $_FILES["file"]["error"] : "";
$location     = $_SERVER['DOCUMENT_ROOT'].'/images/uploads/';
$profile_id   = $_REQUEST['profile_id'];

if (!$fileTmpLoc) { // if file not chosen
    echo "ERROR: Please select a file before clicking the upload button.";
    exit();
}
if(move_uploaded_file($fileTmpLoc, $location.$fileName)){
  $query = "INSERT INTO `photos` (`path`,`original_path`,`profile_id`,`filename`,`published`,`position`,`phototype_id`,`image`,`created_at`,`updated_at`,`image_tmp`,`image_processing`,`image_token`) VALUES ('".$location."','".$location."','".$profile_id."','".$filename."','1','1','1','".$filename."',now(),now(),'".$filename."','1','".$filename."')";
						$db->exec($query);
  echo json_encode(['status_message'=>'file upload success', 'filename'=>$fileName, 'imgpath'=>'/images/uploads/'.$fileName]);
} 
else {
  echo "move_uploaded_file function failed";
}