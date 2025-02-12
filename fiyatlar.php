<?php 

	include 'fonksiyonlar/bagla.php'; 

	if (!isLoggedIn()) {
		
		header("Location:giris.php");

		exit();

	}elseif($user->type == '0' || $user->type == '1'){

		header("Location:index.php");

		exit();

	}else{

		// sıralama için kullandığımız sıra numaralarından en sondakini bulabilmek için

		$query = $db->query("SELECT * FROM fiyatlar WHERE silik = '0' ORDER BY sira DESC LIMIT 1", PDO::FETCH_ASSOC);

		if ( $query->rowCount() ){

			foreach( $query as $row ){

				$sonsira = guvenlik($row['sira']);

			}

		}

		$yenisira = $sonsira + 1;

		if (isset($_POST['fiyatekle'])) {
			
			$urunno = uppercase_tr(guvenlik($_POST['urunno']));

			$aciklama = uppercase_tr(guvenlik($_POST['aciklama']));

			$kod1 = uppercase_tr(guvenlik($_POST['kod1']));
			$kod2 = uppercase_tr(guvenlik($_POST['kod2']));
			$kod3 = uppercase_tr(guvenlik($_POST['kod3']));
			$kod4 = uppercase_tr(guvenlik($_POST['kod4']));
			$kod5 = uppercase_tr(guvenlik($_POST['kod5']));
			$kod6 = uppercase_tr(guvenlik($_POST['kod6']));

			$model1 = uppercase_tr(guvenlik($_POST['model1']));
			$model2 = uppercase_tr(guvenlik($_POST['model2']));
			$model3 = uppercase_tr(guvenlik($_POST['model3']));
			$model4 = uppercase_tr(guvenlik($_POST['model4']));
			$model5 = uppercase_tr(guvenlik($_POST['model5']));
			$model6 = uppercase_tr(guvenlik($_POST['model6']));

			$adetmetre1 = uppercase_tr(guvenlik($_POST['adetmetre1']));
			$adetmetre2 = uppercase_tr(guvenlik($_POST['adetmetre2']));
			$adetmetre3 = uppercase_tr(guvenlik($_POST['adetmetre3']));
			$adetmetre4 = uppercase_tr(guvenlik($_POST['adetmetre4']));
			$adetmetre5 = uppercase_tr(guvenlik($_POST['adetmetre5']));
			$adetmetre6 = uppercase_tr(guvenlik($_POST['adetmetre6']));

			$fiyat1 = uppercase_tr(guvenlik($_POST['fiyat1']))." TL";
			$fiyat2 = uppercase_tr(guvenlik($_POST['fiyat2']))." TL";;
			$fiyat3 = uppercase_tr(guvenlik($_POST['fiyat3']))." TL";;
			$fiyat4 = uppercase_tr(guvenlik($_POST['fiyat4']))." TL";;
			$fiyat5 = uppercase_tr(guvenlik($_POST['fiyat5']))." TL";;
			$fiyat6 = uppercase_tr(guvenlik($_POST['fiyat6']))." TL";;

			$temp1 = explode(".", $_FILES['uploadfile1']['name']);
			$dosyaadi1 = $temp1[0];
			$extension1 = end($temp1);
			$randomsayi1 = rand(0,10000);
			$upload_file1 = $dosyaadi1.$randomsayi1.".".$extension1;
			move_uploaded_file($_FILES['uploadfile1']['tmp_name'], "img/fiyatlar/".$upload_file1);

			$temp2 = explode(".", $_FILES['uploadfile2']['name']);
			$dosyaadi2 = $temp2[0];
			$extension2 = end($temp2);
			$randomsayi2 = rand(0,10000);
			$upload_file2 = $dosyaadi2.$randomsayi2.".".$extension2;
			move_uploaded_file($_FILES['uploadfile2']['tmp_name'], "img/fiyatlar/".$upload_file2);

			//satırların doluluğunu kontrol ederek insert into işlemini kaç kere tekrarlayacağımıza karar vereceğiz

			$dolusatirsayisi = 0;

			for ($i=0; $i < 6; $i++) { 
				if(!empty($_POST['model'.$i.''])){ $dolusatirsayisi++; }
			}

			for ($k=0; $k < $dolusatirsayisi; $k++) { 

				$kodadi = "kod".($k+1); $kod = $_POST[$kodadi];

				$modeladi = "model".($k+1); $model = $_POST[$modeladi];

				$adetmetreadi = "adetmetre".($k+1); $adetmetre = $_POST[$adetmetreadi];

				$fiyatadi = "fiyat".($k+1); $fiyat = $_POST[$fiyatadi];
				
				$query = $db->prepare("INSERT INTO fiyatlar SET urunno = ?, kod = ?, model = ?, adetmetre = ?, fiyat = ?, resim1 = ?, resim2 = ?, aciklama = ?, saniye = ?, silik = ?, sira = ?");

				$insert = $query->execute(array($urunno,$kod,$model,$adetmetre,$fiyat,$upload_file1,$upload_file2,$aciklama,time(),'0',$yenisira));

			}    

			header("Location:fiyatlar.php");

			exit();

		}

		if (isset($_POST['guncelle'])) {
			
			$satirsayisi = uppercase_tr(guvenlik($_POST['satirsayisi']));

			$urunno = uppercase_tr(guvenlik($_POST['urunno']));

			$aciklama = uppercase_tr(guvenlik($_POST['aciklama']));

			$resim1 = guvenlik($_POST['resim1']);

			$resim2 = guvenlik($_POST['resim2']);

			$urun_yeni_sira = guvenlik($_POST['urun_yeni_sira']);

			$temp1 = explode(".", $_FILES['uploadfile1']['name']);
      $dosyaadi1 = $temp1[0];
      $extension1 = end($temp1);
      echo "<br/>extension : ".$extension1;
      $randomsayi1 = rand(0,10000);
    	$upload_file1 = $dosyaadi1.$randomsayi1.".".$extension1;
      move_uploaded_file($_FILES['uploadfile1']['tmp_name'], "img/fiyatlar/".$upload_file1);

      if(empty($extension1)){ $upload_file1 = $resim1; }

      $temp2 = explode(".", $_FILES['uploadfile2']['name']);
      $dosyaadi2 = $temp2[0];
      $extension2 = end($temp2);
      $randomsayi2 = rand(0,10000);
    	$upload_file2 = $dosyaadi2.$randomsayi2.".".$extension2;
      move_uploaded_file($_FILES['uploadfile2']['tmp_name'], "img/fiyatlar/".$upload_file2);

      if(empty($extension2)){ $upload_file2 = $resim2; }

      $urun_eski_sira = guvenlik($_POST['urun_eski_sira']);

			$urun_yeni_sira = guvenlik($_POST['urun_yeni_sira']);

			// SIRALAMA AYARI BURADA GÜNCELLEME İLE ALAKALI SIRALAMADAN BAŞKA BİR ŞEY YOK

			if ($urun_eski_sira < $urun_yeni_sira) {

				$kaycaklaricek = $db->query("SELECT * FROM fiyatlar WHERE sira <= '{$urun_yeni_sira}' AND sira > '{$urun_eski_sira}'", PDO::FETCH_ASSOC);

				if ( $kaycaklaricek->rowCount() ){

					foreach( $kaycaklaricek as $kuc ){

						$kayan_urun_id = $kuc['fiyatid'];

						$kayan_urun_sira = $kuc['sira'];

						$kayan_urun_sira--;

						$sira_guncelle = $db->prepare("UPDATE fiyatlar SET sira = ? WHERE fiyatid = ?"); 

						$kaymaguncelle = $sira_guncelle->execute(array($kayan_urun_sira,$kayan_urun_id));

					}

				}

			}elseif ($urun_eski_sira > $urun_yeni_sira) {

				$kaycaklaricek = $db->query("SELECT * FROM fiyatlar WHERE sira >= '{$urun_yeni_sira}' AND sira < '{$urun_eski_sira}'", PDO::FETCH_ASSOC);

				if ( $kaycaklaricek->rowCount() ){

					foreach( $kaycaklaricek as $kuc ){

						$kayan_urun_id = $kuc['fiyatid'];

						$kayan_urun_sira = $kuc['sira'];

						$kayan_urun_sira++;

						$sira_guncelle = $db->prepare("UPDATE fiyatlar SET sira = ? WHERE fiyatid = ?"); 

						$kaymaguncelle = $sira_guncelle->execute(array($kayan_urun_sira,$kayan_urun_id));

					}

				}

			}

			// SIRALAMA AYARI BİTİŞ

			for ($i=1; $i <= $satirsayisi; $i++) { 
				
				$fiyatidadi = "fiyatid".$i; $fiyatid = $_POST[$fiyatidadi]; 

				$kodadi = "kod".$i; $kod = $_POST[$kodadi]; 

				$modeladi = "model".$i; $model = $_POST[$modeladi]; 

				$adetmetreadi = "adetmetre".$i; $adetmetre = $_POST[$adetmetreadi]; 

				$fiyatadi = "fiyat".$i; $fiyat = $_POST[$fiyatadi]; 

				if(!empty($kod) && !empty($model) && !empty($adetmetre) && !empty($fiyat)){

					$query = $db->prepare("UPDATE fiyatlar SET urunno = ?, kod = ?, model = ?, adetmetre = ?, fiyat = ?, resim1 = ?, resim2 = ?, aciklama = ?, saniye = ?, sira = ? WHERE fiyatid = ?"); 

					$guncelle = $query->execute(array($urunno,$kod,$model,$adetmetre,$fiyat,$upload_file1,$upload_file2,$aciklama,time(),$urun_yeni_sira,$fiyatid));

				}

			}

			header("Location:fiyatlar.php");

			exit();

		}

		if (isset($_POST['sil'])) {
			
			$urunno = guvenlik($_POST['urunno']);

			$urun_eski_sira = guvenlik($_POST['urun_eski_sira']);

			$kaycaklaricek = $db->query("SELECT * FROM fiyatlar WHERE sira > '{$urun_eski_sira}' AND silik = '0'", PDO::FETCH_ASSOC);

			if ( $kaycaklaricek->rowCount() ){

				foreach( $kaycaklaricek as $kuc ){

					$kayan_urun_id = $kuc['fiyatid'];

					$kayan_urun_sira = $kuc['sira'];

					$kayan_urun_sira--;

					$sira_guncelle = $db->prepare("UPDATE fiyatlar SET sira = ? WHERE fiyatid = ?"); 

					$kaymaguncelle = $sira_guncelle->execute(array($kayan_urun_sira,$kayan_urun_id));

				}

			}

			$query = $db->prepare("UPDATE fiyatlar SET silik = ? WHERE urunno = ?"); 

			$guncelle = $query->execute(array('1',$urunno));

			header("Location:fiyatlar.php");

			exit();

		}

		// Ürünlere alt satır ekleme

		if (isset($_POST['satirekle'])) {
			
			$urunno = guvenlik($_POST['urunno']);

			$kod = uppercase_tr(guvenlik($_POST['kod']));

			$model = uppercase_tr(guvenlik($_POST['model']));

			$adetmetre = uppercase_tr(guvenlik($_POST['adetmetre']));

			$fiyat = uppercase_tr(guvenlik($_POST['fiyat']));		

			$resim1 = guvenlik($_POST['resim1']);

			$resim2 = guvenlik($_POST['resim2']);

			$aciklama = guvenlik($_POST['aciklama']);

			$sira = guvenlik($_POST['sira']);

			echo "Ürün no : ".$urunno."<br/>Kod : ".$kod."<br/>Model : ".$model."<br/>Adetmetre : ".$adetmetre."<br/>Fiyat : ".$fiyat."<br/>Resim 1 : ".$resim1."<br/>Resim 2 : ".$resim2."<br/>Açıklama : ".$aciklama."<br/>Sıra : ".$sira; 

			$query = $db->prepare("INSERT INTO fiyatlar SET urunno = ?, kod = ?, model = ?, adetmetre = ?, fiyat = ?, resim1 = ?, resim2 = ?, aciklama = ?, saniye = ?, silik = ?, sira = ?");

			$insert = $query->execute(array($urunno,$kod,$model,$adetmetre,$fiyat,$resim1,$resim2,$aciklama,time(),'0',$sira));	

			header("Location:fiyatlar.php");

      exit();

		}

		if (isset($_POST['satirsil'])) {
			
			$fiyatid = guvenlik($_POST['fiyatid']);

			$query = $db->prepare("UPDATE fiyatlar SET silik = ? WHERE fiyatid = ?"); 

			$guncelle = $query->execute(array('1',$fiyatid));

			header("Location:fiyatlar.php");

			exit();

		}

		if (isset($_POST['fiyatgoster'])) {
			$query = $db->prepare("UPDATE sirketler SET fiyatlistesi = ? WHERE sirketid = ?"); 
			$guncelle = $query->execute(array(0,$user->company_id));
			header("Location:fiyatlar.php");
			exit();
		}

		if (isset($_POST['fiyatgizle'])) {
			$query = $db->prepare("UPDATE sirketler SET fiyatlistesi = ? WHERE sirketid = ?"); 
			$guncelle = $query->execute(array(1,$user->company_id));
			header("Location:fiyatlar.php");
			exit();
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

    <div class="container">
    	
    	<div class="div4">

    			<div class="row">
    				<div class="col-md-6 col-12"><h4 style="font-weight: bold;">Ürün Ekleme Formu</h4></div>
    				<div class="col-md-6 col-12">
    					<form action="" method="POST">
    						<?php if($sirketfiyatlistesi == 0){ ?>
    							<button type="submit" name="fiyatgizle" class="btn btn-danger btn-block btn-sm">Fiyatları Gizle</button>
    						<?php }else{ ?> 
    							<button type="submit" name="fiyatgoster" class="btn btn-success btn-block btn-sm">Fiyatları Göster</button>
    						<?php } ?>
    					</form>
    				</div>
    			</div>

    			<hr/>

    		<form action="" method="POST" enctype="multipart/form-data">
    	
		    	<div class="row">
		    		
		    		<div class="col-1"><small>ÜRÜN NO : </small></div>

		    		<div class="col-2"><input type="text" name="urunno" class="form-control form-control-sm" placeholder="ÜRÜN NO" style="width: 100%;"></div>

		    		<div class="col-1"><small>KOD</small></div>

		    		<div class="col-2"><small>MODEL</small></div>

		    		<div class="col-2"><small>ADET / METRE</small></div>

		    		<div class="col-1"><small>FİYAT</small></div>

		    	</div>

		    	<div class="row" style="margin-top: 1%; padding: 1%;">
		    		
		    		<div class="col-3" style="padding: 2% 1% 1% 2%; border: 1px solid #bebebe; border-radius: 5px;">

		    			Teknik resim ekleyin.

		    			<input type="file" name="uploadfile1" style="margin: 8% 0% 2% 0%;">

		    		</div>

		    		<div class="col-6">

		    			<div class="row">
		    				
		    				<div class="col-2" style="padding: 0px 1px 0px 1px;"><input type="text" name="kod1" class="form-control form-control-sm" style="margin: 0px;" placeholder="KOD"></div>

				    		<div class="col-4" style="padding: 0px 1px 0px 1px;"><input type="text" name="model1" class="form-control form-control-sm" style="margin: 0px;" placeholder="MODEL"></div>

				    		<div class="col-4" style="padding: 0px 1px 0px 1px;"><input type="text" name="adetmetre1" class="form-control form-control-sm" style="margin: 0px;" placeholder="ADET / METRE"></div>

				    		<div class="col-2" style="padding: 0px 1px 0px 1px;"><input type="text" name="fiyat1" class="form-control form-control-sm" style="margin: 0px;" placeholder="FİYAT"></div>

		    			</div>		

		    			<div class="row">
		    				
		    				<div class="col-2" style="padding: 0px 1px 0px 1px;"><input type="text" name="kod2" class="form-control form-control-sm" style="margin: 0px;" placeholder="KOD"></div>

				    		<div class="col-4" style="padding: 0px 1px 0px 1px;"><input type="text" name="model2" class="form-control form-control-sm" style="margin: 0px;" placeholder="MODEL"></div>

				    		<div class="col-4" style="padding: 0px 1px 0px 1px;"><input type="text" name="adetmetre2" class="form-control form-control-sm" style="margin: 0px;" placeholder="ADET / METRE"></div>

				    		<div class="col-2" style="padding: 0px 1px 0px 1px;"><input type="text" name="fiyat2" class="form-control form-control-sm" style="margin: 0px;" placeholder="FİYAT"></div>

		    			</div>		

		    			<div class="row">
		    				
		    				<div class="col-2" style="padding: 0px 1px 0px 1px;"><input type="text" name="kod3" class="form-control form-control-sm" style="margin: 0px;" placeholder="KOD"></div>

				    		<div class="col-4" style="padding: 0px 1px 0px 1px;"><input type="text" name="model3" class="form-control form-control-sm" style="margin: 0px;" placeholder="MODEL"></div>

				    		<div class="col-4" style="padding: 0px 1px 0px 1px;"><input type="text" name="adetmetre3" class="form-control form-control-sm" style="margin: 0px;" placeholder="ADET / METRE"></div>

				    		<div class="col-2" style="padding: 0px 1px 0px 1px;"><input type="text" name="fiyat3" class="form-control form-control-sm" style="margin: 0px;" placeholder="FİYAT"></div>

		    			</div>		

		    			<div class="row">
		    				
		    				<div class="col-2" style="padding: 0px 1px 0px 1px;"><input type="text" name="kod4" class="form-control form-control-sm" style="margin: 0px;" placeholder="KOD"></div>

				    		<div class="col-4" style="padding: 0px 1px 0px 1px;"><input type="text" name="model4" class="form-control form-control-sm" style="margin: 0px;" placeholder="MODEL"></div>

				    		<div class="col-4" style="padding: 0px 1px 0px 1px;"><input type="text" name="adetmetre4" class="form-control form-control-sm" style="margin: 0px;" placeholder="ADET / METRE"></div>

				    		<div class="col-2" style="padding: 0px 1px 0px 1px;"><input type="text" name="fiyat4" class="form-control form-control-sm" style="margin: 0px;" placeholder="FİYAT"></div>

		    			</div>		

		    			<div class="row">
		    				
		    				<div class="col-2" style="padding: 0px 1px 0px 1px;"><input type="text" name="kod5" class="form-control form-control-sm" style="margin: 0px;" placeholder="KOD"></div>

				    		<div class="col-4" style="padding: 0px 1px 0px 1px;"><input type="text" name="model5" class="form-control form-control-sm" style="margin: 0px;" placeholder="MODEL"></div>

				    		<div class="col-4" style="padding: 0px 1px 0px 1px;"><input type="text" name="adetmetre5" class="form-control form-control-sm" style="margin: 0px;" placeholder="ADET / METRE"></div>

				    		<div class="col-2" style="padding: 0px 1px 0px 1px;"><input type="text" name="fiyat5" class="form-control form-control-sm" style="margin: 0px;" placeholder="FİYAT"></div>

		    			</div>		

		    			<div class="row">
		    				
		    				<div class="col-2" style="padding: 0px 1px 0px 1px;"><input type="text" name="kod6" class="form-control form-control-sm" style="margin: 0px;" placeholder="KOD"></div>

				    		<div class="col-4" style="padding: 0px 1px 0px 1px;"><input type="text" name="model6" class="form-control form-control-sm" style="margin: 0px;" placeholder="MODEL"></div>

				    		<div class="col-4" style="padding: 0px 1px 0px 1px;"><input type="text" name="adetmetre6" class="form-control form-control-sm" style="margin: 0px;" placeholder="ADET / METRE"></div>

				    		<div class="col-2" style="padding: 0px 1px 0px 1px;"><input type="text" name="fiyat6" class="form-control form-control-sm" style="margin: 0px;" placeholder="FİYAT"></div>

		    			</div>		    				

		    		</div>	    		

		    		<div class="col-3" style="padding: 2% 1% 1% 2%; border: 1px solid #bebebe; border-radius: 5px;">

		    			Üç boyutlu modeli ekleyin.

		    			<input type="file" name="uploadfile2" style="margin: 8% 0% 2% 0%;">

		    		</div>

		    	</div>

		    	<div class="row" style="margin-top: 5px;">
		    		
		    		<div class="col-2" style="text-align: center;">
		    			
		    			<b>Açıklama : </b>

		    		</div>

		    		<div class="col-10">

		    			<input type="text" name="aciklama" class="form-control form-control-sm" placeholder="Buraya ürünle alakalı açıklamalarınızı girebilirsiniz.">

		    		</div>

		    	</div>

		    	<div class="row" style="margin-top: 5px;">
		    		
		    		<div class="col-6">
		    			
		    			<button type="submit" name="fiyatekle" class="btn btn-primary btn-block btn-sm">Kaydet</button>

		    		</div>

		    		<div class="col-6">
		    			
		    			<a href="fiyatlistesi.php" target="_blank"><button type="button" class="btn btn-info btn-block btn-sm">Fiyat Listesi</button></a>

		    		</div>

		    	</div>	 

		    </form>   	

	    </div>

	    <hr/>

	    <div class="div4" style="padding: 0px;">
	    	
	    	<?php

				$hafizaurunno = "";

	    	$query = $db->query("SELECT * FROM fiyatlar WHERE silik = '0' ORDER BY sira ASC", PDO::FETCH_ASSOC);

				if ( $query->rowCount() ){

					foreach( $query as $row ){

						$urunno = guvenlik($row['urunno']);
						$resim1 = guvenlik($row['resim1']);
						$resim2 = guvenlik($row['resim2']);
						$aciklama = guvenlik($row['aciklama']);
						$sira = guvenlik($row['sira']);

			?>

						<?php if($hafizaurunno != $urunno){ ?>

						<form action="" method="POST" enctype="multipart/form-data">

							<div class="row" style="background-color: dodgerblue; color: white; margin: 0px; padding-top: 7px; padding-bottom: 7px;">
					    		
					    		<div class="col-2">

					    			<div class="row">
			    		
							    		<div class="col-6" style="padding: 0px 0px 0px 5px;"><b>ÜRÜN NO : </b></div>

							    		<div class="col-6" style="padding: 0px;"><input type="text" name="urunno" value="<?= $urunno; ?>" class="form-control form-control-sm"></div>

							    	</div>

					    		</div>

					    		<div class="col-8">

					    			<div class="row">
					    				
					    				<div class="col-2"><b>KOD</b></div>

							    		<div class="col-4"><b>MODEL</b></div>

							    		<div class="col-4"><b>ADET / METRE</b></div>

							    		<div class="col-2"><b>FİYAT</b></div>

					    			</div>		

					    		</div>	

					    		<div class="col-2"></div>

					    	</div>

					    	<div class="row">
					    		
					    		<div class="col-2">

					    			<img src="img/fiyatlar/<?= $resim1; ?>" style="width: 100%; height: auto;"><br/>

					    			<input type="file" name="uploadfile1" style="margin-bottom: 10px;">

					    			<input type="hidden" name="resim1" value="<?= $resim1; ?>">

					    		</div>

					    		<div class="col-8">

					    		<?php

					    			$satir = 0;

					    			$icsorgu = $db->query("SELECT * FROM fiyatlar WHERE urunno = '{$urunno}' AND silik = '0' ORDER BY fiyatid ASC", PDO::FETCH_ASSOC);

									if ( $icsorgu->rowCount() ){

										foreach( $icsorgu as $orw ){

											$satir++;
											$fiyatid = guvenlik($orw['fiyatid']);
											$kod = guvenlik($orw['kod']);
											$model = guvenlik($orw['model']);
											$adetmetre = guvenlik($orw['adetmetre']);
											$fiyat = guvenlik($orw['fiyat']);
											$sira = guvenlik($orw['sira']);

					    		?>

					    			<input type="hidden" name="fiyatid<?= $satir; ?>" value="<?= $fiyatid; ?>">

					    			<div class="row" style="margin-top: 5px;">
					    				
					    				<div class="col-2" style="padding: 0px 1px 0px 5px;"><input type="text" name="kod<?= $satir; ?>" value="<?= $kod; ?>" class="form-control form-control-sm"></div>

							    		<div class="col-4" style="padding: 0px 1px 0px 5px;"><input type="text" name="model<?= $satir; ?>" value="<?= $model; ?>" class="form-control form-control-sm"></div>

							    		<div class="col-3" style="padding: 0px 1px 0px 5px;"><input type="text" name="adetmetre<?= $satir; ?>" value="<?= $adetmetre; ?>" class="form-control form-control-sm"></div>

							    		<div class="col-2" style="padding: 0px 1px 0px 5px;"><input type="text" name="fiyat<?= $satir; ?>" value="<?= $fiyat; ?>" class="form-control form-control-sm"></div>

							    		<div class="col-1">

							    			<input type="hidden" name="fiyatid" value="<?= $fiyatid; ?>">

					    					<button type="submit" name="satirsil" style="border-style:none; background-color:white;"><i class="fas fa-minus" style="color:red;"></i></button>

					    				</div>

					    			</div>	

					    		<?php } } ?>

					    			<!-- Ürün için tekli satırlara ilave yapma kısmı -->

					    			<div class="row">
					    				
					    				<div class="col-2" style="padding: 0px 1px 0px 5px;"><input type="text" name="kod<?= ($satir+1); ?>" placeholder="Kod" class="form-control form-control-sm"></div>

					    				<div class="col-4" style="padding: 0px 1px 0px 5px;"><input type="text" name="model<?= ($satir+1); ?>" placeholder="Model" class="form-control form-control-sm"></div>

					    				<div class="col-3" style="padding: 0px 1px 0px 5px;"><input type="text" name="adetmetre<?= ($satir+1); ?>" placeholder="Adet / Metre" class="form-control form-control-sm"></div>

					    				<div class="col-2" style="padding: 0px 1px 0px 5px;"><input type="text" name="fiyat<?= ($satir+1); ?>" placeholder="Fiyat" class="form-control form-control-sm"></div>

					    				<div class="col-1">

					    					<input type="hidden" name="urunno" value="<?= $urunno; ?>">

					    					<input type="hidden" name="aciklama" value="<?= $aciklama; ?>">

					    					<input type="hidden" name="sira" value="<?= $sira; ?>">

					    					<button type="submit" name="satirekle" style="border-style:none; background-color:white;"><i class="fas fa-plus" style="color:green;"></i></button>

					    				</div>

					    			</div>

					    			<input type="hidden" name="satirsayisi" value="<?= ($satir+1); ?>">

					    			<div class="row" style="margin-top: 5px;">
					    		
							    		<div class="col-2"><b>Açıklama : </b></div>

							    		<div class="col-10"><input type="text" name="aciklama" value="<?= $aciklama; ?>" class="form-control form-control-sm"></div>

							    	</div>	

							    	<div class="row" style="margin-top: 5px;">							    		

							    		<div class="col-4"><button type="submit" name='guncelle' class="btn btn-success btn-block btn-sm">Güncelle</button></div>

							    		<div class="col-4">
							    			
							    		<input type="hidden" name="urun_eski_sira" value="<?= $sira; ?>">

											<select class="form-control form-control-sm" id="exampleFormControlSelect1" name="urun_yeni_sira">

												<?php

													for ($i=1; $i <= $sonsira; $i++) { 
														
														if ($sira == $i) {

															echo '<option selected value='.$i.'>Seçili Sıra Numarası : '.$i.'</option>';
															
														}else{

															echo '<option value='.$i.'>'.$i.'</option>';

														}

													}

												?>
										    </select>

							    		</div>

							    		<div class="col-4">

							    			<button type="submit" name="sil" class="btn btn-secondary btn-block btn-sm">Sil</button>

							    		</div>

							    	</div>

					    		</div>	

					    		<div class="col-2">

					    			<img src="img/fiyatlar/<?= $resim2; ?>" style="width: 100%; height: auto;">

					    			<input type="file" name="uploadfile2" style="margin-bottom: 10px;">

					    			<input type="hidden" name="resim2" value="<?= $resim2; ?>">

					    		</div>

					    	</div>

					    </form>

				    	<hr/>	

			<?php

						}

						$hafizaurunno = $urunno;

					}

				}

	    	?>

	    </div>

    </div>

    <?php include 'template/script.php'; ?>

</body>
</html>