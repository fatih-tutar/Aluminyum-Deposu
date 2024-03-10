<?php 

	include 'fonksiyonlar/bagla.php'; 

	if ($girdi != '1') {
		
		header("Location:giris.php");

		exit();

	}else{

		$kategori_id = guvenlik($_GET['id']);

		$ustkategoriyicek = $db->query("SELECT * FROM kategori WHERE kategori_id = '{$kategori_id}' AND sirketid = '{$uye_sirket}'")->fetch(PDO::FETCH_ASSOC);

		$ust_kategori_id = guvenlik($ustkategoriyicek['kategori_ust']);

		$ustbilgicek = $db->query("SELECT * FROM kategori WHERE kategori_id = '{$ust_kategori_id}' AND sirketid = '{$uye_sirket}'")->fetch(PDO::FETCH_ASSOC);

		$ust_kategori_adi = guvenlik($ustbilgicek['kategori_adi']);

		$ust_kategori_kar_yuzdesi = guvenlik($ustbilgicek['karyuzdesi']);

		$ust_kategori_sutunlar = guvenlik($ustbilgicek['sutunlar']);

		$sutunlaripatlat = explode(",", $ust_kategori_sutunlar);

		$sutunadetizni = $sutunlaripatlat[0];

		$sutunbirimkgizni = $sutunlaripatlat[1];

		$sutuntoplamizni = $sutunlaripatlat[2];

		$sutunalisizni = $sutunlaripatlat[3];

		$sutunsatisizni = $sutunlaripatlat[4];

		$sutunfabrikaizni = $sutunlaripatlat[5];

		$sutunteklifbutonuizni = $sutunlaripatlat[6];

		$sutunsiparisbutonuizni = $sutunlaripatlat[7];

		$sutunduzenlebutonuizni = $sutunlaripatlat[8];

		$sutunsiparisadediizni = $sutunlaripatlat[9];

		$sutunuyariadediizni = $sutunlaripatlat[10];

		$sutunsipariskiloizni = $sutunlaripatlat[11];

		$sutunboyolcusuizni = $sutunlaripatlat[12];

		$sutunmusteriismiizni = $sutunlaripatlat[13];

		$sutuntarihizni = $sutunlaripatlat[14];

		$sutunterminizni = $sutunlaripatlat[15];

		$sutunmanuelsatisizni = $sutunlaripatlat[16];

		$a = guvenlik($_GET['a']);

		$b = guvenlik($_GET['b']);

		if (isset($_POST['urunsil'])) {
			
			$urun_id = guvenlik($_POST['urun_id']);

			$urun_adet = guvenlik($_POST['urun_adet']);

			$urun_sira = guvenlik($_POST['urun_sira']);

			if ($urun_adet != 0) {

				header("Location:urunler.php?id=".$kategori_id."&u=".$urun_id."&urunsilinemez");

				exit();

			}elseif ($urun_adet == 0) {

				$siralar = $db->query("SELECT * FROM urun WHERE urun_sira > '{$urun_sira}' AND kategori_iki = '{$kategori_id}' AND sirketid = '{$uye_sirket}' ORDER BY urun_sira ASC", PDO::FETCH_ASSOC);

				if ( $siralar->rowCount() ){

					foreach( $siralar as $sc ){

						$sira_urun_id = $sc['urun_id'];

						$urun_sira = $sc['urun_sira'];

						$yenisira = $urun_sira - 1;

						$query = $db->prepare("UPDATE urun SET urun_sira = ? WHERE urun_id = ?"); 

						$guncelle = $query->execute(array($yenisira,$sira_urun_id));

					}

				}
				
				$sil = $db->prepare("DELETE FROM urun WHERE urun_id = ?");

				$delete = $sil->execute(array($urun_id));

				header("Location:urunler.php?id=".$kategori_id."&urunsilindi#".$urun_id);

				exit();

			}

		}

		if (isset($_POST['teklifkaydet'])) {

			$turunid = guvenlik($_POST['turunid']);
			
			$tekliffirma = guvenlik($_POST['tekliffirma']);

			$firmaidcek = $db->query("SELECT * FROM firmalar WHERE firmaadi = '{$tekliffirma}' AND sirketid = '{$uye_sirket}'")->fetch(PDO::FETCH_ASSOC);

			$tekliffirma = $firmaidcek['firmaid'];

			$teklifadet =  guvenlik($_POST['teklifadet']);

			$teklifsatisfiyat = guvenlik($_POST['teklifsatisfiyat']);

			$query = $db->prepare("INSERT INTO teklif SET turunid = ?, tverilenfirma = ?, tadet = ?, tsatisfiyati = ?, tsaniye = ?, formda = ?, sirketid = ?, silik = ?");

			$insert = $query->execute(array($turunid,$tekliffirma,$teklifadet,$teklifsatisfiyat,$su_an,'0',$uye_sirket,'0'));

			header("Location:urunler.php?id=".$kategori_id."&u=".$turunid."&teklifeklendi#".$turunid);

			exit();

		}

		if (isset($_POST['guncellemeformu'])) {

			$urun_id = guvenlik($_POST['urun_id']);

			$urun_adi = guvenlik($_POST['urun_adi']);

			$urun_adet = guvenlik($_POST['urun_adet']);

			$eskiadeticek = $db->query("SELECT * FROM urun WHERE urun_id = '{$urun_id}'")->fetch(PDO::FETCH_ASSOC); 

			$eskiadet = $eskiadeticek['urun_adet'];

			$urun_birimkg = guvenlik($_POST['urun_birimkg']);

			$urun_boy_olcusu = guvenlik($_POST['urun_boy_olcusu']);

			$urun_alis = guvenlik($_POST['urun_alis']);

			$satis = guvenlik($_POST['satis']);

			$urun_fabrika = guvenlik($_POST['urun_fabrika']);

			$urun_stok = guvenlik($_POST['urun_stok']);

			$musteri_ismi = guvenlik($_POST['musteri_ismi']);

			$tarih = guvenlik($_POST['tarih']);

			$termin = guvenlik($_POST['termin']);

			$urun_uyari_stok_adedi = guvenlik($_POST['urun_uyari_stok_adedi']);

			$urun_eski_sira = guvenlik($_POST['urun_eski_sira']);

			$urun_yeni_sira = guvenlik($_POST['urun_yeni_sira']);

			if ($urun_eski_sira < $urun_yeni_sira) {
				
				$kayacakurunsayisi = $urun_yeni_sira - $urun_eski_sira;

				$kaycaklaricek = $db->query("SELECT * FROM urun WHERE kategori_iki = '{$kategori_id}' AND sirketid = '{$uye_sirket}' ORDER BY urun_sira ASC LIMIT $urun_eski_sira,$kayacakurunsayisi", PDO::FETCH_ASSOC);

				if ( $kaycaklaricek->rowCount() ){

					foreach( $kaycaklaricek as $kuc ){

						$kayan_urun_id = $kuc['urun_id'];

						$kayan_urun_sira = $kuc['urun_sira'];

						$kayan_urun_sira--;

						$sira_guncelle = $db->prepare("UPDATE urun SET urun_sira = ? WHERE urun_id = ?"); 

						$kaymaguncelle = $sira_guncelle->execute(array($kayan_urun_sira,$kayan_urun_id));

					}

				}

			}elseif ($urun_eski_sira > $urun_yeni_sira) {
				
				$kayacakurunsayisi = $urun_eski_sira - $urun_yeni_sira; 

				$bionce = $urun_yeni_sira - 1;

				$kaycaklaricek = $db->query("SELECT * FROM urun WHERE kategori_iki = '{$kategori_id}' AND sirketid = '{$uye_sirket}' ORDER BY urun_sira ASC LIMIT $bionce,$kayacakurunsayisi", PDO::FETCH_ASSOC);

				if ( $kaycaklaricek->rowCount() ){

					foreach( $kaycaklaricek as $kuc ){

						$kayan_urun_id = $kuc['urun_id'];

						$kayan_urun_sira = $kuc['urun_sira'];

						$kayan_urun_sira++;

						$sadece = $db->prepare("UPDATE urun SET urun_sira = ? WHERE urun_id = ?"); 

						$benzeme = $sadece->execute(array($kayan_urun_sira,$kayan_urun_id));

					}

				}

			}

			$query = $db->query("SELECT * FROM firmalar WHERE sirketid = '{$uye_sirket}' ORDER BY firmaadi ASC", PDO::FETCH_ASSOC);

			if ( $query->rowCount() ){

				foreach( $query as $row ){

					$firmaid = $row['firmaid'];

				}

			}

			$query = $db->prepare("UPDATE urun SET urun_adi = ?, urun_adet = ?, urun_birimkg = ?, urun_boy_olcusu = ?, urun_alis = ?, satis = ?, urun_fabrika = ?, urun_stok = ?, musteri_ismi = ?,urun_uyari_stok_adedi = ?, urun_sira = ?, tarih = ?, termin = ? WHERE urun_id = ?"); 

			$guncelle = $query->execute(array($urun_adi,$urun_adet,$urun_birimkg,$urun_boy_olcusu,$urun_alis,$satis,$urun_fabrika,$urun_stok,$musteri_ismi,$urun_uyari_stok_adedi,$urun_yeni_sira,$tarih,$termin,$urun_id));

			if ($urun_adet != $eskiadet) {
				
				$islem = $db->prepare("INSERT INTO islemler SET yapanid = ?, urunid = ?, eskiadet = ?, yeniadet = ?, saniye = ?, sirketid = ?");

				$islemiekle = $islem->execute(array($uye_id,$urun_id,$eskiadet,$urun_adet,$su_an,$uye_sirket));

			}

			header("Location:urunler.php?id=".$kategori_id."&u=".$urun_id."&guncellendi#".$urun_id);

			exit();

		}

		if (isset($_POST['siparisformu'])) {

			$siparisboy = guvenlik($_POST['siparisboy']);
			
			$hazirlayankisi = guvenlik($_POST['hazirlayankisi']);

			$urun_fabrika = guvenlik($_POST['urun_fabrika']);

			$ilgilikisi = guvenlik($_POST['ilgilikisi']);

			$urun_adi = guvenlik($_POST['urun_adi']);

			$urun_stok = guvenlik($_POST['urun_stok']);

			$urun_id = guvenlik($_POST['urun_id']);

			$termin = guvenlik($_POST['termin']);

			$terminsaniye = strtotime($termin);

			$uruninfo = $db->query("SELECT * FROM urun WHERE urun_id = '{$urun_id}' AND sirketid = '{$uye_sirket}'")->fetch(PDO::FETCH_ASSOC);

			$urun_adi = $uruninfo['urun_adi'];

			$query = $db->prepare("INSERT INTO siparis SET terminsaniye = ?, siparisboy = ?, hazirlayankisi = ?, urun_fabrika_id = ?, ilgilikisi = ?, urun_id = ?, urun_adi = ?, urun_siparis_aded = ?, taslak = ?, siparissaniye = ?, formda = ?, sirketid = ?, silik = ?");

			$insert = $query->execute(array($terminsaniye,$siparisboy,$hazirlayankisi,$urun_fabrika,$ilgilikisi,$urun_id,$urun_adi,$urun_stok,'1',$su_an,'0',$uye_sirket,'0'));

			header("Location:urunler.php?id=".$kategori_id."&u=".$urun_id."&sipariseklendi#".$urun_id);

			exit();

		}

		if (isset($_POST['siparisalindi'])) {
			
			$siparis_id = guvenlik($_POST['siparis_id']);

			$urun_id = guvenlik($_POST['urun_id']);

			$eklenenadet = guvenlik($_POST['eklenenadet']);

			$urun_adet = guvenlik($_POST['urun_adet']);

			$urun_adet = $urun_adet + $eklenenadet;

			$query = $db->prepare("UPDATE urun SET urun_adet = ? WHERE urun_id = ?"); 

			$guncelle = $query->execute(array($urun_adet,$urun_id));

			$query = $db->prepare("UPDATE siparis SET taslak = ? WHERE siparis_id = ?"); 

			$guncelle = $query->execute(array('0',$siparis_id));

			header("Location:urunler.php?id=".$kategori_id."&u=".$urun_id."&siparisalindi#".$urun_id);

			exit();

		}

		if (isset($_GET['guncellendi'])) {
			
			$hata = '<br/><div class="alert alert-success" role="alert">İlgili ürüne ait bilgiler başarıyla güncellendi.</a></div>';

		}

		if (isset($_GET['sipariseklendi'])) {
			
			$hata = '<br/><div class="alert alert-success" role="alert"><a href="fabrikalar.php" target="m_blank">Siparişin başarıyla eklendi. Buraya tıklayarak yönetim sayfasından sipariş formlarına ulaşabilirsin.</a></div>';

		}

		if (isset($_GET['teklifeklendi'])) {
			
			$hata = '<br/><div class="alert alert-success" role="alert"><a href="firmalar.php" target="m_blank">Teklifin başarıyla eklendi. Buraya tıklayarak yönetim sayfasından teklif formlarına ulaşabilirsin.</a></div>';

		}

		if (isset($_GET['siparisalindi'])) {
			
			$hata = '<br/><div class="alert alert-success" role="alert"><a href="fabrikalar.php" target="m_blank">Sipariş alındı ve sipariş kaydı geçmiş siparişlere eklendi.</a></div>';

		}

		if (isset($_GET['urunsilinemez'])) {
			
			$hata = '<br/><div class="alert alert-danger" role="alert">İlgili ürüne ait stokda malzeme bulunduğudan kaydını silemezsiniz.</a></div>';

		}

		if (isset($_GET['urunsilindi'])) {
			
			$hata = '<br/><div class="alert alert-success" role="alert">Ürünün stokta kaydı olmadığından emin olunmuş ve ürün başarıyla silinmiştir.</div>';

		}

	}

?>

<!DOCTYPE html>

<html>

  <head>

    <title>Stok Programım</title>

    <?php include 'template/head.php'; ?>

  </head>

  <body>

    <?php include 'template/banner.php' ?>

    <div class="container-fluid" style="padding: 0px;">

    	<div style="background-color: white; padding: 15px 15px 50px 15px; margin: 0px;">

    		<div class="d-block d-sm-none">

    			<a href="kategori.php?id=<?php echo $ust_kategori_id; ?>" style="color: white;">
    			
	    			<button class="btn btn-primary btn-sm btn-block">
	    		
			    		<i class="fas fa-backward"></i>&nbsp;&nbsp;&nbsp;<?php echo $ust_kategori_adi; ?>

			    	</button>

		    	</a>

    		</div>
    	
	    	<div class="d-none d-sm-block">

				<div class="row">
					
					<div class="col-md-2 col-2"><b>Ürün Adı</b></div>

					<?php if($sutunadetizni == '1'){?><div class="col-md-1 col-1" style="text-align: center;"><b>Adet</b></div><?php } ?>

					<?php if($sutunbirimkgizni == '1'){?><div class="col-md-1 col-1" style="text-align: center;"><b>Birim Kg</b></div><?php } ?>

					<?php if($sutunsipariskiloizni == '1'){?><div class="col-md-1 col-1" style="text-align: center;"><b>Sipariş Kilo</b></div><?php } ?>

					<?php if($sutunboyolcusuizni == '1'){?><div class="col-md-1 col-1" style="text-align: center;"><b>Boy Ölçüsü</b></div><?php } ?>

					<?php if($sutuntoplamizni == '1'){?><div class="col-md-1 col-1" style="text-align: center;"><b>Toplam</b></div><?php } ?>

					<?php if($sutunalisizni == '1' && $uye_alis_yetkisi == '1'){?><div class="col-md-1 col-1"><b>Alış</b></div><?php  } ?>

					<?php if($sutunsatisizni == '1'){?><div class="col-md-1 col-1"><b>Satış</b></div><?php } ?>

					<?php if($sutunfabrikaizni == '1' && $uye_fabrika_yetkisi == '1'){?><div class="col-md-2 col-2"><b>Fabrika</b></div><?php } ?>	

					<?php if($sutunmusteriismiizni == '1'){?><div class="col-md-1 col-1"><b>Müşteri</b></div><?php } ?>	

					<div class="col-md-2 col-2" style="text-align: right;">

						<a href="kategori.php?id=<?php echo $ust_kategori_id; ?>" style="color: white;">
						
							<button class="btn btn-primary btn-sm btn-block">
	    		
					    		<i class="fas fa-backward"></i>&nbsp;&nbsp;&nbsp;<?php echo $ust_kategori_adi; ?>

					    	</button>

				    	</a>

					</div>						

				</div>

			</div>

			<hr style="border: 1px solid black;" />

			<?php

				$toplam_urun_kg = 0;

				$urunlistesira = 0;

				$urun = $db->query("SELECT * FROM urun WHERE kategori_iki = '{$kategori_id}' AND sirketid = '{$uye_sirket}' ORDER BY urun_sira ASC", PDO::FETCH_ASSOC);

				if ( $urun->rowCount() ){

					foreach( $urun as $orw ){

						$urunlistesira++;

						$terminigecikmismi = 0;

						$urun_id = $orw['urun_id'];	

						$terminlericekme = $db->query("SELECT * FROM siparis WHERE urun_id = '{$urun_id}' AND taslak = '1'", PDO::FETCH_ASSOC);

						if ( $terminlericekme->rowCount() ){

							foreach( $terminlericekme as $row ){

								$terminsaniye = $row['terminsaniye'];

								if($bugununsaniyesi > $terminsaniye){

									$terminigecikmismi = 1;

								}

							}

						}

						$urun_kategori_bir = $orw['kategori_bir'];																		

						$urun_adi = $orw['urun_adi'];

						$urun_adet = $orw['urun_adet'];

						$urun_birimkg = $orw['urun_birimkg'];

						$urun_boy_olcusu = $orw['urun_boy_olcusu'];

						$urun_alis = $orw['urun_alis'];

						$urun_satis = $urun_alis * ($ust_kategori_kar_yuzdesi + 100) / 100;

						$urun_satis = round($urun_satis, 2);

						$satis = $orw['satis'];

						if($sutunmanuelsatisizni == '1'){ $urun_satis = $satis; }

						$urun_fabrika = $orw['urun_fabrika'];

						$u_fabrika = $db->query("SELECT * FROM fabrikalar WHERE fabrika_id = '{$urun_fabrika}' AND sirketid = '{$uye_sirket}'")->fetch(PDO::FETCH_ASSOC);

						$urun_fabrika_adi = $u_fabrika['fabrika_adi'];

						$carpim = $urun_adet * $urun_birimkg;

						$toplam_urun_kg = $toplam_urun_kg + $carpim;

						$urun_stok = $orw['urun_stok'];

						$urun_uyari_stok_adedi = $orw['urun_uyari_stok_adedi'];

						$urun_sira = $orw['urun_sira'];

						$musteri_ismi = $orw['musteri_ismi'];

						$tarih = $orw['tarih'];

						$termin = $orw['termin'];

						if (empty($tarih)) {$tarih = $tarihf2;}

						if (empty($termin)) {$termin = $tarihf2;}

			?>

						<div class="row">

							<?php if($sutunterminizni == 1 && $termin < $tarihf2){ ?>

								<div class="col-4 d-block d-sm-none"><b style="color: red;">Ürün Adı :</b></div>

								<div class="col-md-2 col-8">

									<a id="<?php echo $urun_id; ?>" href="islemler.php?id=<?php echo $urun_id; ?>" target="_blank"><b style="color: red;"><?php echo "<small>".$urunlistesira.".</small> ".$urun_adi; ?></b></a>

								</div>

							<?php }elseif($urun_adet < $urun_uyari_stok_adedi){ ?>

								<div class="col-4 d-block d-sm-none"><b style="color: red;">Ürün Adı :</b></div>

								<div class="col-md-2 col-8">

									<a id="<?php echo $urun_id; ?>" href="islemler.php?id=<?php echo $urun_id; ?>" target="_blank"><b style="color: red;"><?php echo "<small>".$urunlistesira.".</small> ".$urun_adi; ?></b></a>

								</div>

							<?php }else{ ?>

								<div class="col-4 d-block d-sm-none">Ürün Adı : </div>

								<div class="col-md-2 col-8">

									<a id="<?php echo $urun_id; ?>" href="islemler.php?id=<?php echo $urun_id; ?>" target="_blank"><b><?php echo "<small>".$urunlistesira.".</small> ".$urun_adi; ?></b></a>

								</div>

							<?php } ?>		

							<?php if($sutunadetizni == '1'){?>								

							<div class="col-4 d-block d-sm-none">Adet : </div>

							<div class="col-md-1 col-8" style="text-align: center;"><button class="btn btn-warning btn-sm btn-block"><b><?php echo $urun_adet; ?></b></button></div>

							<?php } ?>

							<?php if($sutunbirimkgizni == '1'){?>

								<div class="col-4 d-block d-sm-none">Birim Kg : </div>

								<div class="col-md-1 col-8"><small><?php echo $urun_birimkg." kg"; ?></small></div>

							<?php } ?>

							<?php if($sutunsipariskiloizni == '1'){ ?>

								<div class="col-4 d-block d-sm-none">Sipariş Kilo : </div>

								<div class="col-md-1 col-8" style="text-align: center;"><button class="btn btn-danger btn-sm btn-block"><b><?php echo $urun_birimkg." kg"; ?></b></button></div>

							<?php } ?>

							<?php if($sutunboyolcusuizni == '1'){ ?>

								<div class="col-4 d-block d-sm-none">Boy Ölçüsü : </div>

								<div class="col-md-1 col-8"><?php echo $urun_boy_olcusu; ?></div>

							<?php } ?>

							<?php if($sutuntoplamizni == '1'){?>

								<div class="col-4 d-block d-sm-none">Toplam : </div>

								<div class="col-md-1 col-8"><small><?php echo $carpim." kg"; ?></small></div>

							<?php } ?>

							<?php if($sutunalisizni == '1' && $uye_alis_yetkisi == '1'){ ?>

								<div class="col-4 d-block d-sm-none">Alış : </div>

								<div class="col-md-1 col-8"><b><?php echo $urun_alis." TL"; ?></b></div>

							<?php } ?>

							<?php if($sutunsatisizni == '1'){?>

							<div class="col-4 d-block d-sm-none">Satış : </div>

							<div class="col-md-1 col-8"><b><?php echo $urun_satis." TL"; ?></b></div>

							<?php } ?>

							<?php if($sutunfabrikaizni == '1' && $uye_fabrika_yetkisi == '1'){?>

								<div class="col-4 d-block d-sm-none">Fabrika : </div>

								<div class="col-md-2 col-8"><b><?php echo $urun_fabrika_adi; ?></b></div>

							<?php } ?>

							<?php if($sutunmusteriismiizni == '1'){?>

								<div class="col-4 d-block d-sm-none">Müşteri : </div>

								<div class="col-md-1 col-8"><b><?php echo $musteri_ismi; ?></b></div>

							<?php } ?>

							<?php if($sutunteklifbutonuizni == '1' && $uye_teklif_yetkisi == '1'){ ?>

								<div class="col-md-1 col-4">
								
									<a href="#" onclick="return false" onmousedown="javascript:ackapa3v2('teklifdivi<?php echo $urun_id; ?>','siparisdiv<?php echo $urun_id; ?>','editdiv<?php echo $urun_id; ?>');"><button class="btn btn-warning btn-sm btn-block">Teklif</button></a>

								</div>

							<?php } ?>

							<?php if($sutunsiparisbutonuizni == '1' && $uye_siparis_yetkisi == '1'){ ?>

								<div class="col-md-1 col-4">
									
									<a href="#" id="btn1" onclick="return false" onmousedown="javascript:ackapa3v2('siparisdiv<?php echo $urun_id; ?>','teklifdivi<?php echo $urun_id; ?>','editdiv<?php echo $urun_id; ?>');"><?php if($terminigecikmismi == 0){ ?><button class="btn btn-info btn-sm btn-block"><b>Sipariş</b></button><?php }else{ ?><button class="btn btn-danger btn-sm btn-block"><b>Sipariş</b></button><?php } ?></a>

								</div>

							<?php } ?>

							<?php if($sutunduzenlebutonuizni == '1' && $uye_duzenleme_yetkisi == '1'){ ?>

								<div class="col-md-1 col-4">

									<a href="#" id="btn1" onclick="return false" onmousedown="javascript:ackapa3v2('editdiv<?php echo $urun_id; ?>','siparisdiv<?php echo $urun_id; ?>','teklifdivi<?php echo $urun_id; ?>');"><button class="btn btn-success btn-sm btn-block"><b>Düzenle</b></button></a>

								</div>

							<?php } ?>

						</div>

						<?php if($uye_teklif_yetkisi == '1'){?>

					<?php if (isset($_GET['teklifeklendi']) && $_GET['u'] == $urun_id) { ?>

						<div id="teklifdivi<?php echo $urun_id; ?>" class="div2">
						
					<?php }else{ ?>

						<div id="teklifdivi<?php echo $urun_id; ?>" style="display: none;" class="div2">

					<?php } ?>

							<div class="alert alert-warning">

								<h5><b style="line-height: 40px;">Teklif Formu</b></h5>
							
								<form action="" method="POST">
									
									<div class="row">

										<div class="col-md-3 col-12 search-box">

											<b>Teklif Verilen Firma</b>
											
											<input autofocus="autofocus" name="tekliffirma" id="firmainputu" type="text" class="form-control" autocomplete="off" placeholder="Firma Adı"/>
	    
	        								<ul class="list-group liveresult" id="firmasonuc" style="position: absolute; z-index: 1;"></ul>

										</div>
										
										<div class="col-md-2 col-12">

											<b>Adet</b>
											
											<input type="text" class="form-control" name="teklifadet" placeholder="Teklif Adeti Giriniz.">

										</div>

										<div class="col-md-2 col-12">

											<b>Kg Satış Fiyatı</b> (TL)
											
											<input type="text" class="form-control" name="teklifsatisfiyat" value="<?php echo $urun_satis; ?>">

										</div>

										<div class="col-md-2 col-12">

											<br/>

											<input type="hidden" name="turunid" value="<?php echo $urun_id; ?>">
											
											<button class="btn btn-warning" name="teklifkaydet">Teklif Formuna Ekle</button>

										</div>

									</div>

								</form>

							</div>

							<hr/>

							<div class="alert alert-success">
								
								<h5><b style="line-height: 40px;">Teklif Listesi</b></h5>

								<div class="d-none d-sm-block">

									<div class="row">
										
										<div class="col-3"><b>Firma Adı</b></div>

										<div class="col-1"><b>Adet</b></div>

										<div class="col-2"><b>Satış Fiyatı</b></div>

										<div class="col-2"><b>Toplam Fiyat</b></div>

										<div class="col-1"><b>Tarih</b></div>

									</div>

								</div>

					<?php

						$tekliflersiralamasi = 0;

						$tklfcek = $db->query("SELECT * FROM teklif WHERE turunid = '{$urun_id}' AND sirketid = '{$uye_sirket}' AND silik = '0' ORDER BY teklifid DESC", PDO::FETCH_ASSOC);

						if ( $tklfcek->rowCount() ){

							foreach( $tklfcek as $tklfrow ){

								$tekliflersiralamasi++;

								$tverilenfirmaid = $tklfrow['tverilenfirma'];

								$firmabilgi = $db->query("SELECT * FROM firmalar WHERE firmaid = '{$tverilenfirmaid}' AND sirketid = '{$uye_sirket}'")->fetch(PDO::FETCH_ASSOC);

								$tverilenfirmaadi = $firmabilgi['firmaadi'];

								$tadet = $tklfrow['tadet'];

								$tsatisfiyati = $tklfrow['tsatisfiyati'];

								$tsaniye = $tklfrow['tsaniye'];

								$ttarih = date("d-m-Y",$tsaniye);

								$toplam_fiyat = $tadet * $urun_birimkg * $tsatisfiyati;

					?>

								<div class="row">
									
									<div class="col-4 d-block d-sm-none">Firma Adı : </div>

									<div class="col-md-3 col-8"><?php echo $tekliflersiralamasi.". ".$tverilenfirmaadi; ?></div>

									<div class="col-4 d-block d-sm-none">Adet : </div>
									
									<div class="col-md-1 col-8"><?php echo $tadet; ?></div>

									<div class="col-4 d-block d-sm-none">Satış Fiyatı : </div>

									<div class="col-md-2 col-8"><?php echo $tsatisfiyati." TL"; ?></div>

									<div class="col-4 d-block d-sm-none">Toplam Fiyat : </div>

									<div class="col-md-2 col-8"><?php echo $toplam_fiyat." TL"; ?></div>

									<div class="col-4 d-block d-sm-none">Tarih : </div>

									<div class="col-md-1 col-8"><?php echo $ttarih; ?></div>

								</div>

					<?php

							}

						}

					?>

							</div>

						</div>

					<?php if ((isset($_GET['guncellendi']) || isset($_GET['urunsilinemez'])) && $_GET['u'] == $urun_id) { ?>

						<div class="div2" id="editdiv<?php echo $urun_id; ?>" >
					
					<?php }else{ ?>

						<div class="div2" style="display: none;" id="editdiv<?php echo $urun_id; ?>" >

					<?php } ?>	

							<div class="alert alert-success">

								<h5><b style="line-height: 40px;">Düzenleme Formu</b></h5>

								<form action="" method="POST">

									<div class="row">

										<input type="hidden" name="urun_id" value="<?php echo $urun_id; ?>">
									
										<div class="col-md-2 col-12"><b>Ürün Adı</b><input type="text" class="form-control" name="urun_adi" value="<?php echo $urun_adi; ?>"></div>

										<?php if($sutunadetizni == '1'){?><div class="col-md-1 col-12"><b>Adet</b><input type="text" class="form-control" name="urun_adet" value="<?php echo $urun_adet; ?>"></div><?php } ?>	

										<?php if($sutunbirimkgizni == '1'){?><div class="col-md-1 col-12"><b>Birim Kg</b><input type="text" class="form-control" name="urun_birimkg" value="<?php echo $urun_birimkg; ?>"></div><?php } ?>

										<?php if($sutunsipariskiloizni == '1'){?><div class="col-md-1 col-12"><b>Sipariş Kilo</b><input type="text" class="form-control" name="urun_birimkg" value="<?php echo $urun_birimkg; ?>"></div><?php } ?>

										<?php if($sutunboyolcusuizni == '1'){?><div class="col-md-1 col-12"><b>Boy Ölçüsü</b><input type="text" class="form-control" name="urun_boy_olcusu" value="<?php echo $urun_boy_olcusu; ?>"></div><?php } ?>			

										<?php if($sutunalisizni == '1' && $uye_alis_yetkisi == '1'){?><div class="col-md-1 col-12"><b>Alış</b><input type="text" class="form-control" name="urun_alis" value="<?php echo $urun_alis; ?>"></div><?php } ?>

										<?php if($sutunmanuelsatisizni == '1'){?><div class="col-md-1 col-12"><b>Satış</b><input type="text" class="form-control" name="satis" value="<?php echo $urun_satis; ?>"></div><?php } ?>

										<?php if($sutunfabrikaizni == '1' && $uye_fabrika_yetkisi == '1'){?>

										<div class="col-md-2 col-12"><b>Fabrika</b>
											
											<select class="form-control" id="exampleFormControlSelect1" name="urun_fabrika">

										    	<?php

										    	if ($urun_fabrika == 0) {
										    		
										    		echo "<option selected value='0'>Fabrika Seçiniz</option>";

										    	}else{

										    		echo "<option selected value='".$urun_fabrika."'>".$urun_fabrika_adi."</option>";

										    	}

										    	$fabrika = $db->query("SELECT * FROM fabrikalar WHERE sirketid = '{$uye_sirket}' ORDER BY fabrika_adi ASC", PDO::FETCH_ASSOC);

												if ( $fabrika->rowCount() ){

													foreach( $fabrika as $fbrk ){

														$fabrika_id = $fbrk['fabrika_id'];

														$fabrika_adi = $fbrk['fabrika_adi'];

														echo "<option value='".$fabrika_id."'>".$fabrika_adi."</option>";

													}

												}

										    	?>
										   
										    </select>

										</div>

										<?php } ?>

										<?php if($sutunsiparisadediizni == '1'){?><div class="col-md-2 col-12"><b>Sipariş Adedi</b><input type="text" class="form-control" name="urun_stok" value="<?php echo $urun_stok; ?>"></div><?php } ?>

										<?php if($sutunuyariadediizni == '1'){?><div class="col-md-1 col-12"><b>Uyarı Adedi</b><input type="text" class="form-control" name="urun_uyari_stok_adedi" value="<?php echo $urun_uyari_stok_adedi; ?>"></div><?php } ?>

										<div class="col-md-1 col-12">

											<b>Liste Sıra</b>

											<input type="hidden" name="urun_eski_sira" value="<?php echo $urun_sira; ?>">

											<select class="form-control" id="exampleFormControlSelect1" name="urun_yeni_sira">
												<?php

													$sirayicek = $db->query("SELECT * FROM urun WHERE kategori_iki = '{$kategori_id}' AND sirketid = '{$uye_sirket}' ORDER BY urun_sira ASC", PDO::FETCH_ASSOC);

													if ( $sirayicek->rowCount() ){

														foreach( $sirayicek as $sc ){

															$siralama_urun_sira = $sc['urun_sira'];

															if ($urun_sira == $siralama_urun_sira) {

																echo '<option selected value='.$siralama_urun_sira.'>'.$siralama_urun_sira.'</option>';
																
															}else{

																echo '<option value='.$siralama_urun_sira.'>'.$siralama_urun_sira.'</option>';

															}

														}

													}

												?>
										    </select>

										</div>

										<div class="col-md-1 col-12"><br/><button type="submit" class="btn btn-info btn-block" name="guncellemeformu">Güncelle</button></div>

									</div>

									<br/>

									<div class="row">

										<?php if($sutunmusteriismiizni == '1'){?><div class="col-md-2 col-12"><b>Müşteri İsmi</b><input type="text" class="form-control" name="musteri_ismi" value="<?php echo $musteri_ismi; ?>"></div><?php } ?>

										<?php if($sutuntarihizni == '1'){?><div class="col-md-2 col-12"><b>Tarih</b><input type="text" id="tarih<?php echo ($urunlistesira+500); ?>" name="tarih" value="<?php echo $tarih; ?>" class="form-control form-control-sm"></div><?php } ?>
										
										<?php if($sutunterminizni == '1'){?><div class="col-md-2 col-12"><b>Termin</b><input type="text" id="tarih<?php echo $urunlistesira; ?>" name="termin" value="<?php echo $termin; ?>" class="form-control form-control-sm"></div><?php } ?>
									
									</div>

								</form>

							</div>

							<div style="text-align: right;">

								<form action="" method="POST">

									<input type="hidden" name="urun_id" value="<?php echo $urun_id; ?>">

									<input type="hidden" name="urun_adet" value="<?php echo $urun_adet; ?>">

									<input type="hidden" name="urun_sira" value="<?php echo $urun_sira; ?>">
										
									<button type="submit" name="urunsil" class="btn btn-danger">Ürünü Sil</button>

								</form>

							</div>

						</div>

						<?php if ((isset($_GET['siparisalindi']) || isset($_GET['sipariseklendi'])) && $_GET['u'] == $urun_id) { ?>

							<div id="siparisdiv<?php echo $urun_id; ?>" class="div2">
							
						<?php }else{ ?>

							<div style="display: none;" id="siparisdiv<?php echo $urun_id; ?>" class="div2">

						<?php } ?>

							<form action="" method="POST">

								<div class="alert alert-info">

								<h5><b style="line-height: 40px;">Sipariş Formu</b></h5>

								<div class="row">

									<input type="hidden" name="urun_id" value="<?php echo $urun_id; ?>">

									<div class="col-md-2 col-12">

										<b>Hazırlayan Kişi</b><br/>

										<select name="hazirlayankisi" class="form-control">

											<option selected>Hazırlayan Kişiyi Seçiniz</option>
											
											<?php

												$calisanlaricek = $db->query("SELECT * FROM uyeler WHERE uye_firma = '{$uye_sirket}' ORDER BY uye_adi ASC", PDO::FETCH_ASSOC);

												if ( $calisanlaricek->rowCount() ){

													foreach( $calisanlaricek as $cc ){

														$hazirlayanadi = $cc['uye_adi'];

											?>

														<option value="<?php echo $hazirlayanadi; ?>"><?php echo $hazirlayanadi; ?></option>

											<?php

													}

												}

											?>

										</select>

									</div>
								
									<div class="col-md-2 col-12">

										<b>Talep Edilen Fabrika</b><br/>
										
										<select class="form-control" id="exampleFormControlSelect1" name="urun_fabrika">

									    	<?php

									    	if ($urun_fabrika == 0) {
									    		
									    		echo "<option selected value='0'>Fabrika Seçiniz</option>";

									    	}else{

									    		echo "<option selected value='".$urun_fabrika."'>".$urun_fabrika_adi."</option>";

									    	}

									    	$fabrika = $db->query("SELECT * FROM fabrikalar WHERE sirketid = '{$uye_sirket}'", PDO::FETCH_ASSOC);

											if ( $fabrika->rowCount() ){

												foreach( $fabrika as $fbrk ){

													$fabrika_id = $fbrk['fabrika_id'];

													$fabrika_adi = $fbrk['fabrika_adi'];

													echo "<option value='".$fabrika_id."'>".$fabrika_adi."</option>";

												}

											}

									    	?>
									   
									    </select>

									</div>

									<div class="col-md-2 col-12"><b>İlgili Kişi</b><br/><input type="text" class="form-control" name="ilgilikisi" placeholder="İlgili Kişinin İsmini Yazınız"></div>

									<div class="col-md-1 col-12"><b>Miktar</b><br/><input type="text" class="form-control" name="urun_stok" value="<?php echo $urun_stok; ?>"></div>

									<div class="col-md-1 col-12"><b>Boy</b><br/><input type="text" name="siparisboy" value="6 metre" class="form-control"></div>

									<div class="col-md-4 col-12">
										
										<div class="row">
											
											<div class="col-md-5 col-12"><b>Termin</b><br/><input type="text" name="termin" value="<?php echo $tarihf2; ?>" id="tarih<?php echo $urunlistesira; ?>" class="form-control"></div>
			
											<div class="col-md-7 col-12" style="padding-top: 25px;"><button type="submit" class="btn btn-info btn-sm" name="siparisformu">Sipariş Listesine Ekle</button></div>

										</div>

									</div>									

								</div>

								</div>

							</form>	

							<hr/>

							<div class="alert alert-danger">

								<h5><b style="line-height: 40px;">Sipariş Listesi</b></h5>

								<div class="d-none d-sm-block">

									<div class="row">
													
										<div class="col-2"><b>Hazırlayan Kişi</b></div>

										<div class="col-2"><b>Talep Edilen Fabrika</b></div>

										<div class="col-2"><b>İlgili Kişi</b></div>

										<div class="col-1"><b>Miktar</b></div>

										<div class="col-1"><b>Tarih</b></div>

										<div class="col-1"><b>Termin</b></div>

									</div>

								</div>
							
								<?php

									$sipariscek = $db->query("SELECT * FROM siparis WHERE urun_id = '{$urun_id}' AND taslak = '1' AND sirketid = '{$uye_sirket}' AND silik = '0'", PDO::FETCH_ASSOC);

									if ( $sipariscek->rowCount() ){

										foreach( $sipariscek as $row ){

											$siparis_id = $row['siparis_id'];

											$hazirlayankisi = $row['hazirlayankisi'];

											$urun_siparis_aded = $row['urun_siparis_aded'];

											$urun_fabrika_id = $row['urun_fabrika_id'];

											$ilgilikisi = $row['ilgilikisi'];

											$siparissaniye = $row['siparissaniye'];

											$terminsaniye = $row['terminsaniye'];

											$siparistarih = date("d-m-Y", $siparissaniye);

											$termintarih = date("d-m-Y", $terminsaniye);

											$fabrikaadcek = $db->query("SELECT * FROM fabrikalar WHERE fabrika_id = '{$urun_fabrika_id}' AND sirketid = '{$uye_sirket}'")->fetch(PDO::FETCH_ASSOC);

											$urun_fabrika_adi = $fabrikaadcek['fabrika_adi'];

								?>

											<form action="" method="POST">

												<div class="row" style="margin: 2px;">

													<div class="col-4 d-block d-sm-none">Hazırlayan :</div>
													
													<div class="col-md-2 col-8"><?php echo $hazirlayankisi; ?></div>

													<div class="col-4 d-block d-sm-none">Fabrika :</div>

													<div class="col-md-2 col-8"><?php echo $urun_fabrika_adi; ?></div>

													<div class="col-4 d-block d-sm-none">İlgili Kişi :</div>

													<div class="col-md-2 col-8"><?php echo $ilgilikisi; ?></div>

													<div class="col-4 d-block d-sm-none">Miktar :</div>

													<div class="col-md-1 col-8"><input type="text" name="eklenenadet" class="form-control form-control-sm" value="<?php echo $urun_siparis_aded; ?>"></div>

													<div class="col-4 d-block d-sm-none">Tarih :</div>

													<div class="col-md-1 col-8"><?php echo $siparistarih; ?></div>

													<div class="col-4 d-block d-sm-none">Termin :</div>

													<div class="col-md-1 col-8"><?php echo $termintarih; ?></div>

													<div class="col-md-2 col-12">
														
														<input type="hidden" name="siparis_id" value="<?php echo $siparis_id; ?>">

														<input type="hidden" name="urun_id" value="<?php echo $urun_id; ?>">

														<input type="hidden" name="urun_adet" value="<?php echo $urun_adet; ?>">
														
														<button type="submit" name="siparisalindi" class="btn btn-danger btn-sm">Sipariş Alındı</button>

													</div>

												</div>

											</form>

								<?php

										}

									}

								?>

							</div>

							<hr/>

							<div class="alert alert-success">

								<h5><b style="line-height: 40px;">Geçmiş Siparişler</b></h5>

								<div class="d-none d-sm-block">

									<div class="row">
												
										<div class="col-2"><b>Hazırlayan Kişi</b></div>

										<div class="col-2"><b>Talep Edilen Fabrika</b></div>

										<div class="col-2"><b>İlgili Kişi</b></div>

										<div class="col-2"><b>Miktar</b></div>

										<div class="col-2"><b>Tarih</b></div>

									</div>

								</div>
							
								<?php

									$sipariscek = $db->query("SELECT * FROM siparis WHERE urun_id = '{$urun_id}' AND taslak = '0' AND sirketid = '{$uye_sirket}' AND silik = '0'", PDO::FETCH_ASSOC);

									if ( $sipariscek->rowCount() ){

										foreach( $sipariscek as $row ){

											$hazirlayankisi = $row['hazirlayankisi'];

											$urun_siparis_aded = $row['urun_siparis_aded'];

											$urun_fabrika_id = $row['urun_fabrika_id'];

											$ilgilikisi = $row['ilgilikisi'];

											$siparissaniye = $row['siparissaniye'];

											$siparistarih = date("d-m-Y", $siparissaniye);

											$fabrikaadcek = $db->query("SELECT * FROM fabrikalar WHERE fabrika_id = '{$urun_fabrika_id}' AND sirketid = '{$uye_sirket}'")->fetch(PDO::FETCH_ASSOC);

											$urun_fabrika_adi = $fabrikaadcek['fabrika_adi'];

								?>

											<div class="row">

												<div class="col-4 d-block d-sm-none">Hazırlyan</div>
												
												<div class="col-md-2 col-8"><?php echo $hazirlayankisi; ?></div>

												<div class="col-4 d-block d-sm-none">Fabrika</div>

												<div class="col-md-2 col-8"><?php echo $urun_fabrika_adi; ?></div>

												<div class="col-4 d-block d-sm-none">İlgili Kişi</div>

												<div class="col-md-2 col-8"><?php echo $ilgilikisi; ?></div>

												<div class="col-4 d-block d-sm-none">Adet</div>

												<div class="col-md-2 col-8"><?php echo $urun_siparis_aded; ?></div>

												<div class="col-4 d-block d-sm-none">Adet</div>

												<div class="col-md-2 col-8"><?php echo $siparistarih; ?></div>

											</div>

								<?php

										}

									}

								?>

							</div>
						
						</div>

						<?php } ?>

						<hr style="border: 1px solid black;" />

			<?php

					}

				}

			?>

			<div class="row">

				<div class="col-md-12"><b style="font-size: 20px;">Toplam Ürün : <?php echo $toplam_urun_kg; ?> Kg</b></div>

			</div>

		</div>

    </div>

    <?php include 'template/script.php'; ?>

</body>
</html>