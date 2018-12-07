<?php
require_once '../dbHelper.php';
require_once 'functions.php';
$db = new dbHelper();
$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : '';
$first_name = isset($_REQUEST['first_name']) ? $_REQUEST['first_name'] : '';
$to_email = isset($_REQUEST['email']) ? $_REQUEST['email'] : '';

if($type != ''){
    switch($type){
        case 'activation':
            // $to_email = "prince@mailinator.com";
            $subject = "Castit - Profile Activation Notification from Castit-Admin";
            // $headers = "MIME-Version: 1.0" . "\r\n";
            // $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            // $headers .= 'From: Castit <cat@castit.dk>' . "\r\n";
            // $headers .= 'Reply-To: cat@castit.dk' . "\r\n";
            // $headers .= 'BCC: padmanabhann@mailinator.com, vs@anewnative.com' . "\r\n";

            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= 'From: Castit <cat@castit.dk>' . "\r\n";
            $headers .= 'Reply-To: <cat@castit.dk>' . "\r\n";
            $headers .= 'Return-Path: <cat@castit.dk>' ."\r\n";
            $headers .= "Organization: CASTIT"."\r\n";
            $headers .= "X-Priority: 3\r\n";
            $headers .= "X-Mailer: PHP". phpversion() ."\r\n" ;
            // $headers .= 'BCC: padmanabhann@mailinator.com, vs@anewnative.com, cat@castit.dk' . "\r\n";
            $headers .= 'BCC: padmanabhann@mailinator.com, vs@anewnative.com' . "\r\n";
          
            
            include_once("activation_email.php");
            $html = $activation_email;
            mail( $to_email, $subject, $html, $headers );
            $response['success'] = true;
            $response['message'] = 'Email er sendt!';
        break;
        
        case 'deactivation':
            // $to_email = "prince@mailinator.com";
            $subject = "Castit - Profile Deactivation Notification from Castit-Admin";
            // $headers = "MIME-Version: 1.0" . "\r\n";
            // $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            // $headers .= 'From: Castit <cat@castit.dk>' . "\r\n";
            // $headers .= 'Reply-To: cat@castit.dk' . "\r\n";
            // $headers .= 'BCC: padmanabhann@mailinator.com, vs@anewnative.com' . "\r\n";

            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= 'From: Castit <cat@castit.dk>' . "\r\n";
            $headers .= 'Reply-To: <cat@castit.dk>' . "\r\n";
            $headers .= 'Return-Path: <cat@castit.dk>' ."\r\n";
            $headers .= "Organization: CASTIT"."\r\n";
            $headers .= "X-Priority: 3\r\n";
            $headers .= "X-Mailer: PHP". phpversion() ."\r\n" ;
            // $headers .= 'BCC: padmanabhann@mailinator.com, vs@anewnative.com, cat@castit.dk' . "\r\n";
            $headers .= 'BCC: padmanabhann@mailinator.com, vs@anewnative.com' . "\r\n";

            include_once("deactivation_email.php");
            $html = $deactivation_email;
            mail( $to_email, $subject, $html, $headers );
            $response['success'] = true;
            $response['message'] = 'Email er sendt!';            
        break;
    }


    
}