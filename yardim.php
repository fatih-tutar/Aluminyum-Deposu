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

    <title>Alüminyum Deposu</title>

    <?php include 'template/head.php'; ?>

  </head>

  <body>

    <?php include 'template/banner.php' ?>

    <div class="container">
    	
    	<div class="div4">
    		
    		<div class="row">
    			
    			<div class="col-12">
    				
    				<ul>
    					
    					<li style="margin: 10px 0px 10px 0px;">Sipariş butonunun kırmızı yanması, sipariş listesindeki bir ürünün termin tarihi geçmesine rağmen hâlâ teslim alınmadığını gösterir.</li>

    					<li style="margin: 10px 0px 10px 0px;">Ürün adı kırmızı yanıyorsa ürünün stokta az kaldığını gösterir. Ürüne dair stok uyarı adedini düzenle butonuna tıklayıp açılan düzenleme formundaki Uyarı Adedi kısmından değiştirebilirsiniz.</li>

    				</ul>

    			</div>

    		</div>

    	</div>

    </div>

    <?php include 'template/script.php'; ?>

</body>
</html>