<?php 

	include 'fonksiyonlar/bagla.php'; 

	if ($girdi != '1') {
		
		header("Location:giris.php");

		exit();

	}else{

	if($uye_tipi != '3'){

		if (isset($_POST['kaydet'])) {
			
			$allow = array('pdf');

            $temp = explode(".", $_FILES['uploadfile']['name']);

            $dosyaadi = $temp[0];

            $extension = end($temp);

            $randomsayi = rand(0,10000);;

            $upload_file = $dosyaadi.$randomsayi.".".$extension;

		}

	}}

?>

<!DOCTYPE html>

<html>

  <head>

    <title>Alüminyum Deposu</title>

    <?php include 'template/head.php'; ?>

  </head>

  <body>

    <?php include 'template/banner.php' ?>

    <form action="" method="POST" enctype="multipart/form-data">
    	
    	<input type="file" name="uploadfile" style="margin-bottom: 10px;">

    	<button type="submit" name="kaydet">kaydet</button>

    </form>

    <?php include 'template/script.php'; ?>

</body>
</html>