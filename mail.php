<?php
	$content .= '<!DOCTYPE html>';
	$content .= '<html lang="en">';
	$content .= '<head>';
	$content .= '<meta charset="utf-8">';
	$content .= '</head>';
	$content .= '<body style="background:#fff; margin:0; padding:0;">';
	$content .= '<div style="float:left; width:100%;">';
	$content .= '<div style="width:100%; margin:auto">';
	$content .= '<p style="color:#6f6f6f; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:18px; margin:0; padding:0 0 15px 0;">
	your new password is
	<strong>'.$data['name'].'</strong><br/><br/>
	</p>';
	$content .= '</div></div></body></html>';
echo $content;
?>