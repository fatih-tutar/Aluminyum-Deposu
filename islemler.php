<?php 

	include 'fonksiyonlar/bagla.php'; 

	if ($girdi == '0') {
		
		header("Location:giris.php");

		exit();

	}else{

		if($uye_islemleri_gorme_yetkisi != '1'){

			header("Location:index.php");

			exit();

		}else{

			if(isset($_GET['id']) && empty($_GET['id']) === false) {
				$islemurunid = guvenlik($_GET['id']);
			}
		}

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

  	<div class="div4">

  		<div class="d-none d-sm-block">

	  		<div class="row" style="margin-top: 10px;">
	  			
	  			<div class="col-1"><b>Adı</b></div>

	  			<div class="col-2"><b>Ürün</b></div>

				<div class="col-1"><b>Alt K.</b></div>

				<div class="col-2"><b>Üst K.</b></div>  			

	  			<div class="col-1"><b>Eski</b></div>

	  			<div class="col-1"><b>Yeni</b></div>

	  			<div class="col-1"><b>Fark</b></div>

				<div class="col-1"><b>Yer</b></div>

	  			<div class="col-2"><b>Tarih</b></div>

	  		</div>

	  	</div>

  	<?php

  		if (empty($islemurunid) === false) {

  			$query = $db->query("SELECT * FROM islemler WHERE urunid = '{$islemurunid}' AND sirketid = '{$uye_sirket}' ORDER BY saniye DESC LIMIT 300", PDO::FETCH_ASSOC);

  		}else{

  			$query = $db->query("SELECT * FROM islemler WHERE sirketid = '{$uye_sirket}' ORDER BY saniye DESC LIMIT 300", PDO::FETCH_ASSOC);

  		}	  	

		if ( $query->rowCount() ){

			foreach( $query as $row ){

				$yapanid = $row['yapanid'];

				$yapanadicek = $db->query("SELECT * FROM uyeler WHERE id = '{$yapanid}'")->fetch(PDO::FETCH_ASSOC);

				$yapanadi = $yapanadicek['uye_adi'];

				$urunid = $row['urunid'];

				$urunbilgileri = $db->query("SELECT * FROM urun WHERE urun_id = '{$urunid}'")->fetch(PDO::FETCH_ASSOC);

				$urunadi = $urunbilgileri['urun_adi'];

				$kategori_bir = $urunbilgileri['kategori_bir'];

				$katadcek = $db->query("SELECT * FROM kategori WHERE kategori_id = '{$kategori_bir}'")->fetch(PDO::FETCH_ASSOC);

				$kategori_bir_adi = $katadcek['kategori_adi'];

				$kategori_iki = $urunbilgileri['kategori_iki'];

				$katadcek = $db->query("SELECT * FROM kategori WHERE kategori_id = '{$kategori_iki}'")->fetch(PDO::FETCH_ASSOC);

				$kategori_iki_adi = $katadcek['kategori_adi'];

				$eskiadet = $row['eskiadet'];

				$yeniadet = $row['yeniadet'];

				$islem_tipi = $row['islem_tipi'];

				$islem_yeri = $islem_tipi == 0 ? '<button class="btn btn-warning btn-sm">Mağaza</button>' : '<button class="btn btn-info btn-sm">Depo</button>';

				$saniye = $row['saniye'];

				$tarih = date("d-m-Y H:i:s", $saniye);

				$islem = 0; // EKSİLTMEYE AYARLIYORUZ

				if ($eskiadet > $yeniadet) {

					$fark = $eskiadet - $yeniadet;

					$fark = "- ".$fark;

				}else{

					$fark = $yeniadet - $eskiadet;

					$fark = "+ ".$fark;

					$islem = 1; // ARTTIRMA OLURSA DEĞİŞTİRİYORUZ

				}

	?>

				<hr/>

				<div class="row">

					<div class="col-4 d-block d-sm-none"><b style="color: red;">Yapan</b></div>
  			
		  			<div class="col-md-1 col-8"><?= $yapanadi; ?></div>

		  			<div class="col-4 d-block d-sm-none"><b style="color: red;">Ürün</b></div>

		  			<div class="col-md-2 col-8"><?= $urunadi; ?></div>

		  			<div class="col-4 d-block d-sm-none"><b style="color: red;">Alt K.</b></div>

		  			<div class="col-md-1 col-8"><?= $kategori_iki_adi; ?></div>

		  			<div class="col-4 d-block d-sm-none"><b style="color: red;">Üst K.</b></div>

					<div class="col-md-2 col-8"><?= $kategori_bir_adi; ?></div> 

					<div class="col-4 d-block d-sm-none"><b style="color: red;">Eski Adet</b></div>

		  			<div class="col-md-1 col-8"><?= $eskiadet; ?></div>

		  			<div class="col-4 d-block d-sm-none"><b style="color: red;">Yeni Adet</b></div>

		  			<div class="col-md-1 col-8"><?= $yeniadet; ?></div>

		  			<div class="col-4 d-block d-sm-none"><b style="color: red;">Fark</b></div>

		  			<div class="col-md-1 col-8"><?php if($islem == 0){?><button class="btn btn-danger btn-sm"><?php }else{ ?><button class="btn btn-success btn-sm"><?php } ?><?= $fark; ?></button></div>

		  			<div class="col-4 d-block d-sm-none"><b style="color: red;">Yer</b></div>

		  			<div class="col-md-1 col-8"><?= $islem_yeri; ?></div>
					
					<div class="col-4 d-block d-sm-none"><b style="color: red;">Tarih</b></div>

		  			<div class="col-md-2 col-8"><?= $tarih; ?></div>

		  		</div>

	<?php

			}

		}

  	?>

  	</div>    

    <?php include 'template/script.php'; ?>

</body>
</html>