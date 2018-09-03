<?php
require '../../vendor/autoload.php';

use OpenCloud\Rackspace;

    $client = new Rackspace(Rackspace::UK_IDENTITY_ENDPOINT, array(
      'username' => 'castit',
      'apiKey'   => '187a515209d0affd473fedaedd6d770b'
    ));

    $objectStoreService = $client->objectStoreService(null, 'LON');
    $container = $objectStoreService->getContainer('video_original_files')->getContainer('profiles');
    ppe($container);
    $localFileName  = $location.$fileName;
    $remoteFileName = time().$fileName;

    $handle = fopen($localFileName, 'r');
    $container->uploadObject($remoteFileName, $handle);
    unset($handle);


function pp($q){
  echo '<pre>';
  print_r($q);
  echo '</pre>';
}

function ppe($q){
  pp($q);exit;
}


$zencoder_array = array();
$zencoder_array = [
  "input" => "cf+uk://castit:187a515209d0affd473fedaedd6d770b@test_api_padmanabhan/1535970374__0123123.mp4",
  "outputs" => array(
    "label" => "mp4 high",
    "url" => "cf+uk://castit:187a515209d0affd473fedaedd6d770b@test_api_padmanabhan/testdirone/22/1535970374__0123123.mp4",
    "h264_profile" => "high",
  ),
];

?>

{
  "input": "cf+uk://castit:187a515209d0affd473fedaedd6d770b@test_api_padmanabhan/1535970374__0123123.mp4",
  "outputs": [{
    "label": "mp4 high",
    "url": "cf+uk://castit:187a515209d0affd473fedaedd6d770b@test_api_padmanabhan/testdirone/22/1535970374__0123123.mp4",
    "h264_profile": "high"
  }]
}
