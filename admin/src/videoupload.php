<?php
require_once '../dbHelper.php';
require_once 'functions.php';
$db = new dbHelper();
require '../../vendor/autoload.php';
use OpenCloud\Rackspace; 
if(isset($_FILES["file"])){
$fileTmpLoc   = $_FILES["file"]["tmp_name"];
$client = new Rackspace(Rackspace::UK_IDENTITY_ENDPOINT, array(
'username' => 'castit',
'apiKey'   => '187a515209d0affd473fedaedd6d770b'
));
$profile_id         = $_REQUEST['profile_id'];
$user_profile_id    = $profile_id;
$time 				= time();
$location 		    = $_SERVER['DOCUMENT_ROOT'].'/images/uploads/';
$objectStoreService = $client->objectStoreService(null, 'LON');
$container          = $objectStoreService->getContainer('video_original_files');
$date_dir           = date("o-m-d");
$fileName			= $_FILES["file"]["name"];
$ext = ".".pathinfo($fileName, PATHINFO_EXTENSION);
$fileName			= unique_code(10).$ext;
$localFileName      = $location.$fileName;
$remoteFileName     = "/profiles/".$date_dir."/".$time."__".$fileName;
$cdnfilepath     	= "/videos/profiles/".$date_dir;
$cdnfilename		= $time."__".$fileName;
$thumbnail			= "thumb_".$cdnfilename.".png";
$_FILES["file"]["cdnfilepath"] = $cdnfilepath;
$_FILES["file"]["cdnfilename"] = $cdnfilename;
$_FILES["file"]["thumbnail"] = $thumbnail;

// move_uploaded_file($fileTmpLoc, $location.$fileName);
if(move_uploaded_file($fileTmpLoc, $location.$fileName)){
$handle = fopen($localFileName, 'r');
$container->uploadObject($remoteFileName, $handle);
unset($handle);

$zencoder_input   	= "cf+uk://castit:187a515209d0affd473fedaedd6d770b@video_original_files".$remoteFileName;
$zencoder_output  	= "cf+uk://castit:187a515209d0affd473fedaedd6d770b@videos_public/videos".$remoteFileName;
$zencoder_base_url  = "cf+uk://castit:187a515209d0affd473fedaedd6d770b@videos_public".$cdnfilepath;

$zencoder_array = [
"input_file"		=> $zencoder_input,
"output_file"		=> $zencoder_output,
"base_url"			=> $zencoder_base_url,
"filename"			=> $cdnfilename,
];

// $zencoder_json = json_encode($zencoder_array);
$zencoder_json = build_json_zencoder($zencoder_array);

// ppe($zencoder_json);
$url = 'https://app.zencoder.com/api/v2/jobs';
$ch = curl_init( $url );
curl_setopt( $ch, CURLOPT_POST, 1);
curl_setopt( $ch, CURLOPT_POSTFIELDS, $zencoder_json);
curl_setopt( $ch, CURLOPT_RETURNTRANSFER , 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
'Content-Type: application/json ',
'Zencoder-Api-Key: 9477541a57e1eb2471b1ff256ca4b92c'
));

$response = curl_exec( $ch );
echo json_encode(['status_message'=>'file upload success', 'filename'=>$fileName, 'cdnfilepath'=>$cdnfilepath, 't'=>$time, 'imgpath'=>$thumbnail, 'zencode'=>[$zencoder_input, $zencoder_output]]);

$filename = $cdnfilename;
						$location = $cdnfilepath;
						$thumbnail = $thumbnail;
						$cloud_orig_path = str_replace('/videos',"", $location);
							$query = "INSERT INTO `videos` (
									`profile_id`,
									`path`,
									`uploaded_as_filename`,
									`filename`,
									`video_original_path`,
									`video_original_filename`,
									`video_original_file_basename`,
									`thumbnail_original_photo_path`,
									`thumbnail_photo_path`,
									`thumbnail_photo_filename`,
									`thumbnail_at_time`,
									`published`,
									`position`) 
								VALUES (
									'".$user_profile_id."',
									'".$location."',
									'".$fileName."',
									'".$filename."',
									'".$cloud_orig_path."',
									'".$filename."',
									'".$filename."',
									'".$location."',
									'".$location."',
									'".$thumbnail."',
									'3',
									'1',
									'1')";
						$query_prepared = $db->prepare($query);
						$query_prepared->execute();
								}
								else{
									echo "move_uploaded_file function failed";
								}
}