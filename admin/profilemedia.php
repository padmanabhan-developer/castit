<?php
    if(!isset($_GET['type'])){
        $_GET['type'] = 'all';
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Castit</title>
<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
<link href="css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<link href="css/bootstrap-toggle.css" rel="stylesheet">
<link type="text/css" rel="stylesheet" href="css/lightgallery.css" /> 
<link type="text/css" rel="stylesheet" href="css/jquery-ui.min.css" /> 
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.2/css/all.css" integrity="sha384-/rXc/GQVaYpyDdyxK+ecHPVYJSN9bmVFBvjA/9eOB+pb3F2w2N6fc5qB9Ew5yIns" crossorigin="anonymous">
<link rel="stylesheet" href="style.css">
</head>
	
<body>
<div id="wrapper">
  <header id="header">
  		<div class="container">
  		<div class="logo"><a href="#"><img src="images/logo.png" alt=""></a></div>
        
    	<div id="navbar">    
              <nav class="navbar navbar-default navbar-static-top" role="navigation">
                        <div class="navbar-header">
                            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-1">
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                        </div>
                        
                        <div class="collapse navbar-collapse" id="navbar-collapse-1">
                            <ul class="nav navbar-nav">
                                <li class="current-menu-item"><a href="/admin">Profiler</a></li>
                                <li><a href="#">opret job</a></li>
                                <li><a href="#">too do</a></li>
                                <li><a href="#">tekst</a></li>
                                <li><a href="#">intro billeder</a></li>
                                <li><a href="#">alle profiler</a></li>	
                            </ul>
                        </div><!-- /.navbar-collapse -->
                    </nav>
            </div>
            
            </div>
  </header><!--close header-->
	
  <div id="uploadModal" class="modal fade" role="dialog">
          <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <a href="/admin/profilemedia?id=<?php echo $_GET['id']; ?>&type=all"><button type="button" class="close">&times;</button></a>
                    <h4 class="modal-title">Upload Media</h4>
                </div>
                <div class="modal-body">
                    <!-- Form -->
                    <form id="uploadmediaform" method='post' action='' enctype="multipart/form-data">
                    <div class="upload_selection">
                    <a href="#"> <div class="radio radio-info"><input type="radio" name="uploadmediatype" value="image" checked="checked" autocomplete="off"><label>Image</label></div></a>
                        <div class="radio radio-info"><input type="radio" name="uploadmediatype" value="video" autocomplete="off"><label>Video</label></div>
                    </div>
                        Select file : <input type='file' name='file' id='file' class='form-control' autocomplete="off"><br>
                        <input type='button' class='btn btn-info' value='Upload' id='upload' profile-id=<?php echo $_GET['id']?>>
                        <!-- <span class="ajax_loading_container"></span> -->
                        <div class="ajax_loading_container" style="display:none"></div>
                        <!-- <div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div> -->
                        <!-- <input type='button' class='btn btn-info' value='Complete' id='complete' data-dismiss="modal"> -->
                    </form>

                    <!-- Preview-->
                    <div id='preview'></div>
                </div>
                
            </div>

          </div>
        </div>	
	
	
  
  <div id="content"> 
	  
       <div class="page-top">
       		<div class="container">
            	 <h2 class="profile_caption"></h2>
                 <div class="upload-sec"><a class="upload-btn" href="#" class="btn btn-info" data-toggle="modal" data-target="#uploadModal">Upload</a> <a class="upload-close" href="/admin"></a></div>
                 <!-- <button type="button" class="btn btn-info" data-toggle="modal" data-target="#uploadModal">Upload file</button> -->
            </div>
       </div><!--close page-top-->
       
       
       <div class="page-bottom">
       		<div class="container">
            	 <div class="toolbar">
                    <?php
                        $checked = '';
                        if($_GET['type'] == 'images'){
                            $checked = 'checked="checked"';
                        }
                    ?>
                 	  <div class="radio radio-info">
                           <input type="radio" name="mediatype" id="Radios1" value="images" <?php echo $checked;?>>
                           <label>Billeder</label>
                      </div>
                      <?php
                        $checked = '';
                        if($_GET['type'] == 'videos'){
                            $checked = 'checked="checked"';
                        }
                      ?>
                      <div class="radio radio-info">
                           <input type="radio" name="mediatype" id="Radios2" value="videos" <?php echo $checked;?>>
                           <label>Videoer</label>
                      </div>
                      <?php
                        $checked = '';
                        if($_GET['type'] == 'all'){
                            $checked = 'checked="checked"';
                        }                      
                      ?>
                      <div class="radio radio-info">
                           <input type="radio" name="mediatype" id="Radios3" value="all" <?php echo $checked;?>>
                           <label>Blandet</label>
                      </div>
                      
                 </div>
                 
            	 <div class="product-sec">
                    <?php 
                    $sort_class = '';
                    if($_GET['type'] == 'images' || $_GET['type'] == 'videos'){ 
                       $sort_class = "sortable-ui"; 
                    }
                    ?>
                 	  <div class="product-row mediarow ">

                      </div><!--close product-row-->
                 </div><!--close product-sec-->

                 <div class="toolbar-bottom">
                      <div class="tool-left">
                           <a class="back-btn" href="/admin">Tilbage</a>
                      </div>
                      <div class="tool-right">
                      		<ul>
                            	<li><a href="/admin/profileinfo?id=<?php echo $_GET['id']; ?>">INFO</a></li>
                                <li><a href="#">KALENDER</a></li>
                                <li><a href="#">CASTINGSHEET</a></li>
                            </ul>
                      </div>
                 </div>
            </div>
       </div><!--close page-bottom-->
	  
  </div><!--close content-->
 
</div><!--close wrapper--> 



<script src="js/jquery-3.3.1.min.js"></script>
<script src="js/jquery-ui.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/scripts.js"></script>
<script src="js/bootstrap-toggle.js"></script>
<script src="js/jquery.simplePopup.js" type="text/javascript"></script>
</body>
</html>