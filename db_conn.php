<?php

$con = mysql_connect("localhost","root",'wooTh5quaighoo4') or die(mysql_error());

$db = mysql_select_db("castitnew") or die(mysql_error());
if($db) echo 'connected';
else 'not connected';

/*$con = mysql_connect("castit.dk","castit",'j3pw8=E49AYDC6N$fZ7R') or die(mysql_error());

$db = mysql_select_db("castit") or die(mysql_error());*/


?> 