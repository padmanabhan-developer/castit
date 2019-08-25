<?php
require '.././libs/Slim/Slim.php';
require_once 'dbHelper.php';
//require_once 'SimpleImage.php';

// Get Slim instance
\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();
$app = \Slim\Slim::getInstance();
if (version_compare(PHP_VERSION, '5.4.0', '<')) {
        if(session_id() == '') {session_start();}
    } else  {
       if (session_status() == PHP_SESSION_NONE) {session_start();}
    }
// call our own dbHelper class
$db = new dbHelper();

//$imageClass = new SimpleImage();
/*****************************************
Purpose: Home page
Parameter : NIL
Type : POST
******************************************/
$app->get('/test',function () use ($app) { 
    global $db;
	
	//$response = array();
	
	echo "Test";
	//echoResponse(200, $response);
});
/******************************************
Purpose: Home page Landing page
Parameter : NIL
Type : POST
******************************************/
$app->get('/',function () use ($app) { 
    global $db;
echo	$query = $db->prepare("SELECT p.*, m.profile_group_id, m.profile_number, m.profile_number_first_name_last_name, m.version, m.current, g.name as gender_name, hc.name as hair_color_name, ec.name as eye_color_name FROM profiles p INNER JOIN memberships m ON m.profile_id = p.id INNER JOIN genders g ON g.id = p.gender_id INNER JOIN hair_colors hc ON hc.id = p.hair_color_id INNER JOIN eye_colors ec ON ec.id = p.eye_color_id  WHERE (p.profile_status_id = '1' OR  p.profile_status_id = '2') AND m.current ='1'  ORDER BY p.created_at"); 

	echo $query;exit;
	$query->execute();
    $rows = $query->fetchAll(PDO::FETCH_ASSOC);

});


//JSON coneverion
function echoResponse($status_code, $response) {
    global $app;
    $app->status($status_code);
    $app->contentType('application/json');
    echo json_encode($response);
}
function getIP() {
        $ip = '';
        // cloudFlare IP check
        if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $ip = strip_tags($_SERVER['HTTP_CF_CONNECTING_IP']);
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']) AND strlen($_SERVER['HTTP_X_FORWARDED_FOR']) > 6) {
            $ip = strip_tags($_SERVER['HTTP_X_FORWARDED_FOR']);
        } elseif (!empty($_SERVER['HTTP_CLIENT_IP']) AND strlen($_SERVER['HTTP_CLIENT_IP']) > 6) {
            $ip = strip_tags($_SERVER['HTTP_CLIENT_IP']);
        } elseif (!empty($_SERVER['REMOTE_ADDR']) AND strlen($_SERVER['REMOTE_ADDR']) > 6) {
            $ip = strip_tags($_SERVER['REMOTE_ADDR']);
        }//endif
        if (empty($ip))
            $ip = '127.0.0.1';
        return strip_tags($ip);
    }
	
//Related functions

function generate_uuid() {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
        mt_rand( 0, 0xffff ),
        mt_rand( 0, 0x0fff ) | 0x4000,
        mt_rand( 0, 0x3fff ) | 0x8000,
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}	
	
	
$app->run();

?>