<?php

// load library
require 'php-excel.class.php';

$con = mysql_connect("localhost", "root", "wooTh5quaighoo4");
$sl_db = mysql_select_db("castitupdated", $con);

/*$result = mysql_query("SELECT profiles.id,profiles.first_name, profiles.last_name, profiles.email, profiles.job, profiles.phone, profiles.cellphone, memberships.profile_number FROM profiles LEFT JOIN memberships ON profiles.id = memberships.profile_id;");*/

/*$result = mysql_query("select id,first_name, last_name, email, job, phone, cellphone from profiles");*/

/*$result = mysql_query("SELECT p.id, m.profile_number, p.first_name, p.last_name, p.gender_id, p.shoe_size_from, p.shoe_size_to, p.email, p.job, p.phone, p.cellphone
FROM profiles p
INNER JOIN memberships m ON m.profile_id = p.id
WHERE (
p.profile_status_id =  '1'
OR p.profile_status_id =  '2'
)
AND p.gender_id = 2 AND p.shoe_size_from = 42 AND p.shoe_size_to = 42
AND m.current =  '1' ");
*/

$result = mysql_query("SELECT p.id, p.gender_id , g.name as gender_name, p.hair_color_id, p.eye_color_id,p.profile_status_id,p.shoe_size_from, p.shoe_size_to, m.profile_group_id, m.profile_number, m.profile_number_first_name_last_name, m.version, m.current FROM profiles p INNER JOIN memberships m ON m.profile_id = p.id INNER JOIN genders g ON g.id = p.gender_id INNER JOIN hair_colors hc ON hc.id = p.hair_color_id INNER JOIN eye_colors ec ON ec.id = p.eye_color_id WHERE (p.profile_status_id = '1' OR p.profile_status_id = '2') AND m.current ='1' AND p.shoe_size_from = '42' AND p.shoe_size_to = '42'");





// create a simple 2-dimensional array
// $data = array(
//          array ('Name', 'Surname'),
//         array('Schwarz', 'Oliver'),
//         array('Test', 'Peter')
//         );

$data = [1 => array ('id', 'gender_id', 'gender_name', 'hair_color_id', 'eye_color_id', 'profile_status_id', 'shoe_size_from', 'shoe_size_to', 'profile_group_id', 'profile_number', 'profile_number_first_name_last_name'),];

while($row = mysql_fetch_assoc($result)) {
		$data[] = $row;
     }

// generate file (constructor parameters are optional)
$xls = new Excel_XML('UTF-8', false, 'My Test Sheet');
$xls->addArray($data);
$xls->generateXML('full_profile_details');

?>