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
  <body>
    <?php include 'template/banner.php' ?>

    <?php include 'template/script.php'; ?>
</body>
</html>