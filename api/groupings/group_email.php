<?php

$group_email_body = <<< EOM

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Castit</title>
<link rel="stylesheet" type="text/css" href="style.css" media="all">
<style>

html, body, div, span, applet, object, iframe, h1, h2, h3, h4, h5, h6, p, blockquote, a, abbr, acronym, big, font, img, small, center, dl, dt, dd, ol, ul, li, fieldset, form, label, legend, table, tbody, tfoot, thead, tr, th, td, input, textarea, select {margin:0; padding:0; border:0; outline:0; font-size:100%; vertical-align:baseline; font-weight:normal; box-sizing:border-box;}
body {line-height:1; }
ol, ul {list-style:none;}
blockquote, q {quotes:none;}
:focus {outline:0;}
ins {text-decoration:none;}
del {text-decoration:line-through;}
table {border-collapse:collapse; border-spacing:0;}


.clear{clear:both;}
h1,h2,h3,h4,h5,h6{font-weight:normal; margin:0; padding:0; }


img{max-width:100%;}

a {
color: #157E9C;-webkit-transition: 0.1s;
-moz-transition: 0.1s;
transition: 0.1s;
outline: none !important;
}
a:focus{color:#000; text-decoration:none;}

@font-face {
  font-family: 'helveticaneueltstdbd';
  src: url('fonts/helveticaneueltstdbd.eot');
  src: url('fonts/helveticaneueltstdbd.eot') format('embedded-opentype'),
       url('fonts/helveticaneueltstdbd.woff2') format('woff2'),
       url('fonts/helveticaneueltstdbd.woff') format('woff'),
       url('fonts/helveticaneueltstdbd.ttf') format('truetype'),
       url('fonts/helveticaneueltstdbd.svg#helveticaneueltstdbd') format('svg');
}

body{background:#fff; font-family:Arial, Helvetica, sans-serif; font-size:14px; overflow-x:hidden;} 

#popup-wrapper{float:left; width:100%; padding:54px 0 0 0;}
.popup-container{margin:auto; width:730px; padding:0 15px; max-width:100%;}
.popup-row1{border-bottom:solid 1px #2d2e32; float:left; width:100%; padding:0 0 20px 0; margin:0 0 10px 0;}
.popup-logo{float:left; width:182px;}
.popup-text{display:block; padding:0 0 0 182px;}
.popup-text h4{color:#000; font-size:16px; line-height:20px; font-weight:bold; font-family:Arial, Helvetica, sans-serif; margin:0 0 20px 0;}
.popup-text p{color:#dddddd; font-size:14px; line-height:20px; font-weight:normal; font-family:Arial, Helvetica, sans-serif; margin:0;}
.popup-row2{clear:both; margin:0 -10px;}
.pop-col3{float:left; width:25%; padding:0 10px; margin:0 0 20px 0;}
.pop-col-inner{float:left; width:100%; position:relative;}
.pop-thumb{float:left; width:100%; position:relative; margin:0 0 10px 0;}
.pop-thumb img{float:left; width:100%; }
.pop-thumb h6{color:#fff; font-size:10px; line-height:20px; font-weight:normal; font-family:Arial, Helvetica, sans-serif; padding:13px; background:rgba(0,0,0,0.75); position:absolute; left:0; bottom:0; width:100%; text-align:center; }
.pop-col-inner h5{color:#000; font-size:12px; line-height:16px; font-weight:bold; font-family:Arial, Helvetica, sans-serif; margin:0 0 0 0;}
.pop-col-inner p{color:#d1d1d1; font-size:12px; line-height:16px; font-weight:normal; font-family:Arial, Helvetica, sans-serif; margin:0 0 0 0;}

.popup-row3{float:left; width:100%; margin:60px 0 0 0 ;border-top:solid 1px #2d2e32; padding:30px 0;}
.popup-icon1{float:left; }
.popup-row3 h3{float:right; font-size:32px; color:#000;font-family: 'helveticaneueltstdbd';}
</style>
</head>

<body>

<div id="popup-wrapper">
	 <div class="popup-container">
     	  <div class="popup-row1">
          	   <div class="popup-logo"><a href="#"><img src="images/logo.png" alt="" /></a></div>
               <div class="popup-text">
               	    <h4>Castit Lightbox:</h4>
                    <p>If you’re struggling to come up with your next great concept, put down that other ipsum text and drop this like it’s hot into your design comp. This colorful blend of inspirational text is sure to get those lost creative juices flowing and generate a big thumbs up from your client, creative director, or professor. You’re already one step closer to wild success.</p>
               </div>
          </div><!--popup-row1-->
          
          <div class="popup-row2">
          	   <div class="pop-col3">
               		<div class="pop-col-inner">
                    	 <div class="pop-thumb"><img src="$PROFILE_IMAGE" alt="" /><h6>Navn. NR0012</h6></div>
                         <h5>Note: </h5>
                         <p>$NOTES</p>
                    </div>
               </div>
               
               <div class="pop-col3">
               		<div class="pop-col-inner">
                    	 <div class="pop-thumb"><img src="images/thumb8.png" alt="" /><h6>Navn. NR0012</h6></div>
                         <h5>Note: </h5>
                         <p>If you’re struggling to come up with your next great concept, put down that other ipsum text and drop this like it’s hot into your design.</p>
                    </div>
               </div>
          </div><!--popup-row2-->
          
          <div class="popup-row3">
          	   <span class="popup-icon1"><img src="images/icon.png" alt="" /></span>
               <h3>$GROUP_DATE__20.05.17</h3>
          </div><!--popup-row3-->
          
     </div>
</div>

</body>
</html>

EOM;

