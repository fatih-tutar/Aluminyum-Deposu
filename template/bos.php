<?php 
	include 'fonksiyonlar/bagla.php'; 
	if ($girdi != '1') {
		header("Location:giris.php");
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