<?php 

	include 'fonksiyonlar/bagla.php'; 

	if (!isLoggedIn()) {
		
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

	    	$sayac = 0;

	    	$geridon = 0; // renk kodları 10 tane kategori sayısı ise 10dan fazla olabilir geridon değişkeni ile renk sayacının geri dönmesini sağlıyoruz.

	    	$renkstringi = "#006466,#065a60,#0b525b,#144552,#1b3a4b,#212f45,#272640,#312244,#3e1f47,#4d194d";

			$renkarrayi = explode(",", $renkstringi);

			$cek = $db->query("SELECT * FROM kategori WHERE kategori_ust = '{$kategori_id}' AND kategori_tipi = '1' AND sirketid = '{$user->company_id}'", PDO::FETCH_ASSOC);

			if ( $cek->rowCount() ){

				foreach( $cek as $wor ){

					$alt_kategori_id = $wor['kategori_id'];

					$alt_kategori_adi = $wor['kategori_adi'];

					$resim = "img/kategoriler/".$wor['resim'];

		?>
						
					<div class="col-md-3">

						<a href="urunler.php?id=<?= $alt_kategori_id; ?>">

							<img src="<?= $resim; ?>" class="img-thumbnail" style="width: 100%; height: auto; padding: 20px;">

							<button class="btn btn-dark btn-sm btn-block" style="background-color: black;"><?= $alt_kategori_adi; ?></button>

							<!--<div style="border-radius: 50px; text-align: center; background-color: <?= $renkarrayi[$sayac]; ?>; color: white; font-weight: bolder; text-align: center; height: 90%; padding: 30% 20% 30% 20%; margin-bottom: 20px; font-size: 25px;">

								<?= $alt_kategori_adi; ?>

							</div>-->

						</a>

					</div>		

		<?php

					if($geridon == 0){ $sayac++; }else{ $sayac--; }

					if ($sayac == 9) {
						
						$geridon = 1;

					}

					if($sayac == 1){

						$geridon = 0;

					}

				}

			}

		?>

		</div>

	</div>

    <?php include 'template/script.php'; ?>

</body>
</html>