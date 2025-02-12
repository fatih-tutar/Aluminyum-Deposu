<?php 

	include 'fonksiyonlar/bagla.php'; 

	if ($girdi != '1') {
		
		header("Location:giris.php");

		exit();

	}elseif($user->type == '0' || $user->type == '1'){

		header("Location:index.php");

		exit();

	}else{

	if($user->type != '3'){

		if (isset($_POST['teklifkaydet'])) {
			
			$musteri = guvenlik($_POST['musteri']);

			$ilgilikisi = guvenlik($_POST['ilgilikisi']);

			$urunmiktar = guvenlik($_POST['urunmiktar']);

			$fiyat = guvenlik($_POST['fiyat']);

			$fabrika = guvenlik($_POST['fabrika']);

			$fabrikafiyat = guvenlik($_POST['fabrikafiyat']);

			$aciklama = guvenlik($_POST['aciklama']);

			$query = $db->prepare("INSERT INTO teklif_listesi SET musteri = ?, ilgilikisi = ?, urunmiktar = ?, fabrika = ?, aciklama = ?, teklifveren = ?, tarih = ?, silik = ?");

			$insert = $query->execute(array($musteri,$ilgilikisi,$urunmiktar,$fabrika,$aciklama,$user->id,time(),'0'));

			header("Location:tekliflistesi.php");

			exit();

		}

		if (isset($_POST['teklifguncelle'])) {
 			
			$teklifid = guvenlik($_POST['teklifid']);
			
			$musteri = guvenlik($_POST['musteri']);

			$ilgilikisi = guvenlik($_POST['ilgilikisi']);

			$urunmiktar = guvenlik($_POST['urunmiktar']);

			$fiyat = guvenlik($_POST['fiyat']);

			$fabrika = guvenlik($_POST['fabrika']);

			$fabrikafiyat = guvenlik($_POST['fabrikafiyat']);

			$aciklama = guvenlik($_POST['aciklama']);

			$query = $db->prepare("UPDATE teklif_listesi SET musteri = ?, ilgilikisi = ?, urunmiktar = ?, fiyat = ?, fabrika = ?, fabrikafiyat = ?, teklifveren = ?, aciklama = ?, tarih = ? WHERE teklifid = ?"); 

			$guncelle = $query->execute(array($musteri,$ilgilikisi,$urunmiktar,$fiyat,$fabrika,$fabrikafiyat,$user->id,$aciklama,time(),$teklifid));

			header("Location:tekliflistesi.php");

			exit();

		}

		if (isset($_POST['arsivsil'])) {
			
			$teklifid = guvenlik($_POST['teklifid']);

			$query = $db->prepare("UPDATE teklif_listesi SET silik = ? WHERE teklifid = ?"); 

			$guncelle = $query->execute(array('3',$teklifid));

			header("Location:tekliflistesi.php");

			exit();

		}

		if (isset($_POST['listeyegonder'])) {
			
			$teklifid = guvenlik($_POST['teklifid']);

			$query = $db->prepare("UPDATE teklif_listesi SET silik = ? WHERE teklifid = ?"); 

			$guncelle = $query->execute(array('0',$teklifid));

			header("Location:tekliflistesi.php");

			exit();

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

    <div class="container-fluid">

    	<div class="div4">

    		<div class="row">
    			
    			<div class="col-md-6"><h2>Teklif Kayıt Formu</h2></div>

    			<div class="col-md-6" style="text-align:right;"><a href="tekliflistesi.php"><button class="btn btn-primary">Geri Dön</button></a></div>

    		</div>

	    	<hr/>

	    	<form action="" method="POST">
	    		
	    		<div class="row">
	    			
	    			<div class="col-md-2">

	    				<h5><b>Müşteri İsmi</b></h5>
	    				
	    				<input type="text" name="musteri" class="form-control" placeholder="Müşteri İsmi">

	    			</div>

	    			<div class="col-md-2">

	    				<h5><b>İlgili Kişi</b></h5>
	    				
	    				<input type="text" name="ilgilikisi" class="form-control" placeholder="İlgili Kişi">

	    			</div>

	    			<div class="col-md-3">

	    				<h5><b>Ürün / Miktar</b></h5>
	    				
	    				<input type="text" name="urunmiktar" class="form-control" placeholder="Ürün / Miktar">

	    			</div>

	    			<div class="col-md-2">

	    				<h5><b>Fiyat</b></h5>
	    				
	    				<input type="text" name="fiyat" class="form-control" placeholder="Fiyat">

	    			</div>

	    		</div>

	    		<div class="row" style="margin-top: 15px;">
	    			
	    			<div class="col-md-5">

	    				<h5><b>Açıklama</b></h5>

			    		<input type="text" name="aciklama" class="form-control" placeholder="Buraya açıklama girebilirsiniz." value="<?= $aciklama; ?>">

	    			</div>

	    			<div class="col-md-2">

	    				<h5><b>Fabrika</b></h5>
	    				
	    				<input type="text" name="fabrika" class="form-control" placeholder="Fabrika">

	    			</div>

	    			<div class="col-md-2">

	    				<h5><b>Fabrika Fiyatı</b></h5>
	    				
	    				<input type="text" name="fabrikafiyat" class="form-control" placeholder="Fabrika Fiyatı">

	    			</div>

	    			<div class="col-md-2" style="margin-top:30px;">
	    				
	    				<button type="submit" name="teklifkaydet" class="btn btn-dark btn-block">Kaydet</button>

	    			</div>

	    		</div>

	    	</form>

	    </div>

	    <div class="div4">

	    	<div class="row">
	    		
	    		<div class="col-md-2"><h5><b>Müşteri İsmi</b></h5></div>

	    		<div class="col-md-2"><h5><b>İlgili Kişi</b></h5></div>

	    		<div class="col-md-3"><h5><b>Ürün / Miktar</b></h5></div>

	    		<div class="col-md-2"><h5><b>Fiyat</b></h5></div>

	    		<div class="col-md-2"><h5><b>Teklif Veren</b></h5></div>

	    		<div class="col-md-1"><h5><b>Tarih</b></h5></div>

	    	</div><hr style="border: 1px solid black;" />

	    	<?php

	    		$tekliflistesicek = $db->query("SELECT * FROM teklif_listesi WHERE silik = '1' || silik = '2' ORDER BY teklifid DESC", PDO::FETCH_ASSOC);

					if ( $tekliflistesicek->rowCount() ){

						foreach( $tekliflistesicek as $tlc ){

							$teklifid = guvenlik($tlc['teklifid']);

							$musteri = guvenlik($tlc['musteri']);

							$ilgilikisi = guvenlik($tlc['ilgilikisi']);

							$urunmiktar = guvenlik($tlc['urunmiktar']);

							$fiyat = guvenlik($tlc['fiyat']);

							$fabrika = guvenlik($tlc['fabrika']);

							$fabrikafiyat = guvenlik($tlc['fabrikafiyat']);

							$teklifveren = guvenlik($tlc['teklifveren']);

							$teklifveren = uyeadcek($teklifveren);

							$aciklama = guvenlik($tlc['aciklama']);

							$tekliftarih = guvenlik($tlc['tarih']);

							$tekliftarih = date("d-m-Y", $tekliftarih);

							$silik = guvenlik($tlc['silik']);

				?>


							<?php if($silik == 1){ ?>
							
								<div class="alert alert-success">
							
							<?php }elseif($silik == 2){ ?>
							
								<div class="alert alert-danger">
							
							<?php }else{ ?>

								<div>

							<?php } ?>

								<form action="" method="POST">
		    		
					    		<div class="row">
					    			
					    			<div class="col-md-2">
					    				
					    				<input type="text" name="musteri" class="form-control" placeholder="Müşteri İsmi" value="<?= $musteri; ?>">

					    			</div>

					    			<div class="col-md-2">
					    				
					    				<input type="text" name="ilgilikisi" class="form-control" placeholder="İlgili Kişi" value="<?= $ilgilikisi; ?>">

					    			</div>

					    			<div class="col-md-3">
					    				
					    				<input type="text" name="urunmiktar" class="form-control" placeholder="Ürün / Miktar" value="<?= $urunmiktar; ?>">

					    			</div>

					    			<div class="col-md-2">
					    				
					    				<input type="text" name="fiyat" class="form-control" placeholder="Fiyat" value="<?= $fiyat; ?>">

					    			</div>

					    			<div class="col-md-2">
					    				
					    				<?= $teklifveren; ?>

					    			</div>

					    			<div class="col-md-1">
					    				
					    				<?= $tekliftarih; ?>

					    			</div>

					    		</div>

					    		<div class="row" style="margin-top: 5px;">
					    			
					    			<div class="col-md-5">

					    				<input type="text" name="aciklama" class="form-control" placeholder="Buraya açıklama girebilirsiniz." value="<?= $aciklama; ?>">

					    			</div>

					    			<div class="col-md-2">
					    				
					    				<input type="text" name="fabrika" class="form-control" placeholder="Fabrika" value="<?= $fabrika; ?>">

					    			</div>

					    			<div class="col-md-2">
					    				
					    				<input type="text" name="fabrikafiyat" class="form-control" placeholder="Fabrika Fiyat" value="<?= $fabrikafiyat; ?>">

					    			</div>

					    			<div class="col-md-1" style="padding:3px;">

					    				<input type="hidden" name="teklifid" value="<?= $teklifid; ?>">
					    				
					    				<button type="submit" name="teklifguncelle" class="btn btn-dark btn-block">Güncelle</button>

					    			</div>

					    			<div class="col-md-1" style="padding:3px;">
					    				
					    				<button type="submit" name="arsivsil" class="btn btn-danger btn-block"><i class="fas fa-plus"></i> Sil</button>

					    			</div>

					    			<div class="col-md-1" style="padding:3px;">
					    				
					    				<button type="submit" name="listeyegonder" class="btn btn-info btn-block">Geri Al</button>

					    			</div>

					    		</div>

					    	</form>

					    </div>

				<?php

						}

					}

	    	?>

	    </div>

    </div>

    <br/><br/><br/><br/><br/><br/><br/><br/>

    <?php include 'template/script.php'; ?>

</body>
</html>