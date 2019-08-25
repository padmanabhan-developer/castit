<?php 
				$content = '';
				$content .= '<!DOCTYPE html><html lang="en"><head>
								<meta content="text/html; charset=UTF-8" http-equiv="content-type">
								</head>
								<body style="background:#fff; font-family:Calibri;">
								<div style="background:#fff;width:100%;float:left;">
							  <div style="width:100%; margin:auto; text-align:center;">
								<div style="display:inline-block; background:#fff; border:solid 3px #313743; 	
								width:580px;-webkit-border-radius: 8px;-moz-border-radius: 8px;border-radius: 8px; 	
								padding:0 0 13px; margin:18px 0 50px 0;">
								<div style="color: #20be93;font-size: 23px;font-family: Calibri; 
								background:#313743;float:left; width:100%; text-align:center; margin:0 0 16px 0; 
								padding:8px 0 4px;"><img src="http://dev.tailtracking.com/assets/img/logo.png" 
								width="90px"/> </div>
								<div style="padding:0 30px;">';
					$content .= '<h5 style="color: #646e78;font-size: 16px;padding:0;margin: 0; text-align:left;">Hi '.$rows[0]['fname'].' '.$rows[0]['lname'].',</h5>';
				
					$content .= '<p style="color: #646e78;font-size: 16px;text-align:left;">Your new requested password is,</p>';
					$content .= '<p style="color: #646e78;font-size: 16px;padding:0; line-height:18px; text-align:left; 
								">Username :'.$email.'<br/>Password :'.$newpass.'<br/></p>
								<p style="color: #646e78;font-size: 16px;padding:0; line-height:18px; text-align:left; 
								">Please login in to your account and change the password<br/></p>';
					$content .= '<p style="color: #646e78;text-align:left;font-size: 16px;padding:0 0 45px 0; margin:51px 0 0; 
								line-height:20px; text-align:left;font-family:Calibri">Thank you!<br><br>Best 
								regards,<br>TailTracking.</p>';
					$content .= '<div style="float:left; width:100%; margin:40px 0 0 0; border-top:solid 1px #dddddd;padding:20px 0 0 0;"> 					
								<span style="color: #646e78;font-size: 12px; float:left;line-height:34px;">Copyrights @ 2017. All Rights Reserved.</span> ';
					$content.= '</div></div></div></div></div></body></html>';	

echo $content;
?>