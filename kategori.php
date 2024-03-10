<?php 

	include 'fonksiyonlar/bagla.php'; 

	if ($girdi != '1') {
		
		header("Location:giris.php");

		exit();

	}else{

		$kategori_id = guvenlik($_GET['id']);

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

	    <div class="row">

	    <?php

			$cek = $db->query("SELECT * FROM kategori WHERE kategori_ust = '{$kategori_id}' AND kategori_tipi = '1' AND sirketid = '{$uye_sirket}'", PDO::FETCH_ASSOC);

			if ( $cek->rowCount() ){

				foreach( $cek as $wor ){

					$alt_kategori_id = $wor['kategori_id'];

					$alt_kategori_adi = $wor['kategori_adi'];

		?>
						
					<div class="col-md-4">

						<div style="text-align: center;">

							<a href="urunler.php?id=<?php echo $alt_kategori_id; ?>"><button class="btn btn-primary btn-block" style="margin-bottom: 7px; font-weight: bold; font-size: 18px;"><?php echo $alt_kategori_adi; ?></button></a>

						</div>

					</div>		

		<?php

				}

			}

		?>

		</div>

	</div>

    <?php include 'template/script.php'; ?>

</body>
</html>