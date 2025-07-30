<?php 
	include 'functions/init.php';
	if (!isLoggedIn()) {
		header("Location:login.php");
		exit();
	}else{

	}
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Araçlar</title>
    <?php include 'template/head.php'; ?>
  </head>
  <body class="body-white">
    <?php include 'template/banner.php' ?>

    <div class="container-fluid">
        <div class="row">
            <div id="sidebar" class="col-md-3 d-none">
                <?php include 'template/sidebar2.php'; ?>
            </div>
            <div id="mainCol" class="col-md-12 col-12">
            </div>
        </div>
    </div>

    <?php include 'template/script.php'; ?>
  </body>
</html>