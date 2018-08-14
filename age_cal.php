<?php 
$date_debut = date("Y-m-d");
$age = 35;
$date1 = strtotime($date_debut); 
$time1 = $age*31556926; 
$dob1 = $date1 - $time1; 
echo $dob = date("Y-m-d",$dob1); 
?>