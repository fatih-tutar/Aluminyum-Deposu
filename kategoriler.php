<?php 

	include 'functions/init.php';

	if (!isLoggedIn()) {
		
		header("Location:login.php");

		exit();

	}elseif (isLoggedIn()) {

		if($user->type == '0'){

			header("Location:index.php");

			exit();

		}

		if (isset($_POST['kategorisil'])) {
			
			$kategori_id = guvenlik($_POST['kategori_id']);

			if (kategoridolumu($kategori_id) == '1') {
				
				$error = '<br/><div class="alert alert-danger" role="alert">Silmek istediğiniz kategoride kayıtlı ürünler var. O ürünleri silmeden kategoriyi silemezsiniz.</a></div>';

			}else{

				$sil = $db->prepare("UPDATE kategori SET silik = ? WHERE kategori_id = ?");

				$delete = $sil->execute(array('1',$kategori_id));

				header("Location:kategoriler.php");

				exit();

			}

		}

		if (isset($_POST['kategoriekle'])) {

			$sutunlar = '1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1';

			$sutunlararray = explode(",",$sutunlar);
			
			$kategori_adi = guvenlik($_POST['kategori_adi']);

			$kategori_tipi = guvenlik($_POST['kategori_tipi']);

			if($kategori_tipi == '0'){

				if(!isset($_POST['sutunadet'])){ $sutunlararray[0] = 0; }

				if(!isset($_POST['sutunbirimkg'])){ $sutunlararray[1] = 0; }

				if(!isset($_POST['sutuntoplam'])){ $sutunlararray[2] = 0; }

				if(!isset($_POST['sutunalis'])){ $sutunlararray[3] = 0; }

				if(!isset($_POST['sutunsatis'])){ $sutunlararray[4] = 0; }

				if(!isset($_POST['sutunfabrika'])){ $sutunlararray[5] = 0; }

				if(!isset($_POST['sutunteklifbutonu'])){ $sutunlararray[6] = 0; }

				if(!isset($_POST['sutunsiparisbutonu'])){ $sutunlararray[7] = 0; }

				if(!isset($_POST['sutunduzenlebutonu'])){ $sutunlararray[8] = 0; }

				if(!isset($_POST['sutunsiparisadedi'])){ $sutunlararray[9] = 0; }

				if(!isset($_POST['sutunuyariadedi'])){ $sutunlararray[10] = 0; }

				if(!isset($_POST['sutunsipariskilo'])){ $sutunlararray[11] = 0; }

				if(!isset($_POST['sutunboyolcusu'])){ $sutunlararray[12] = 0; }

				if(!isset($_POST['sutunmusteriismi'])){ $sutunlararray[13] = 0; }

				if(!isset($_POST['sutuntarih'])){ $sutunlararray[14] = 0; }

				if(!isset($_POST['sutuntermin'])){ $sutunlararray[15] = 0; }

				if(!isset($_POST['sutunmanuelsatis'])){ $sutunlararray[16] = 0; }

				if(!isset($_POST['sutunurunkodu'])){ $sutunlararray[17] = 0; }

				if(!isset($_POST['sutundepoadet'])){ $sutunlararray[18] = 0; }

				if(!isset($_POST['sutundepouyariadet'])){ $sutunlararray[19] = 0; }

				if(!isset($_POST['sutunraf'])){ $sutunlararray[20] = 0; }

				if(!isset($_POST['sutunsevkiyatbutonu'])){ $sutunlararray[21] = 0; }

				if(!isset($_POST['sutunpalet'])){ $sutunlararray[22] = 0; }

				$sutunlar = implode(",",$sutunlararray);

			}else if($kategori_tipi == '1'){

				$sutunlar = '';

			}

			$allow = array('pdf');

            $temp = explode(".", $_FILES['uploadfile']['name']);

            $dosyaadi = $temp[0];

            $extension = end($temp);

            $randomsayi = rand(0,10000);

            $upload_file = $dosyaadi.$randomsayi.".".$extension;

            move_uploaded_file($_FILES['uploadfile']['tmp_name'], "img/kategoriler/".$upload_file);

			$query = $db->prepare("INSERT INTO kategori SET kategori_adi = ?, kategori_tipi = ?, kategori_ust = ?, resim = ?, sutunlar = ?, sirketid = ?, silik = ?");

			$insert = $query->execute(array($kategori_adi,$kategori_tipi,'0',$upload_file,$sutunlar,$user->company_id,'0'));

			header("Location:kategoriler.php");

			exit();

		}

		if (isset($_POST['urunekle'])) {
			
			$kategori_iki = guvenlik($_POST['kategori_iki']);

			$katbircek = $db->query("SELECT * FROM kategori WHERE kategori_id = '{$kategori_iki}' AND sirketid = '{$user->company_id}'")->fetch(PDO::FETCH_ASSOC);

			$kategori_bir = $katbircek['kategori_ust'];

			$urun_adi = guvenlik($_POST['urun_adi']);

			$sonuruncek = $db->query("SELECT * FROM urun WHERE kategori_iki = '{$kategori_iki}' AND kategori_bir = '{$kategori_bir}' AND sirketid = '{$user->company_id}' ORDER BY urun_sira DESC LIMIT 1", PDO::FETCH_ASSOC);

			if ( $sonuruncek->rowCount() ){

				foreach( $sonuruncek as $suc ){

					$sonurunsirasi = $suc['urun_sira'];

				}

			}

			$sonurunsirasi++;

			$query = $db->prepare("INSERT INTO urun SET kategori_bir = ?, kategori_iki = ?, urun_kodu = ?, urun_adi = ?, urun_adet = ?, urun_palet = ?, urun_depo_adet = ?, urun_raf = ?, urun_birimkg = ?, urun_boy_olcusu = ?, urun_alis = ?, urun_fabrika = ?, urun_stok = ?, urun_uyari_stok_adedi = ?, urun_depo_uyari_adet = ?, urun_sira = ?, musteri_ismi = ?, tarih = ?, termin = ?, satis = ?, sirketid = ?, silik = ?");

			$insert = $query->execute(array($kategori_bir,$kategori_iki,'',$urun_adi,'','','','','','','','','','','',$sonurunsirasi,'','','','',$user->company_id,'0'));

			header("Location:kategoriler.php");

			exit();

		}

		if (isset($_POST['kategoriduzenle'])) {
			
			$kategori_id = guvenlik($_POST['kategori_id']);

			$kategori_adi = guvenlik($_POST['kategori_adi']);

			$kategori_tipi = guvenlik($_POST['kategori_tipi']);

			$kategori_ust = guvenlik($_POST['kategori_ust']);

			$eskiresim = guvenlik($_POST['eskiresim']);

			if ($kategori_tipi == 0) {
				
				$kategori_ust = 0;

			}

			$sutunlar = '1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1';

			$sutunlararray = explode(",",$sutunlar);

			if(!isset($_POST['sutunadet'])){ $sutunlararray[0] = 0; }

			if(!isset($_POST['sutunbirimkg'])){ $sutunlararray[1] = 0; }

			if(!isset($_POST['sutuntoplam'])){ $sutunlararray[2] = 0; }

			if(!isset($_POST['sutunalis'])){ $sutunlararray[3] = 0; }

			if(!isset($_POST['sutunsatis'])){ $sutunlararray[4] = 0; }

			if(!isset($_POST['sutunfabrika'])){ $sutunlararray[5] = 0; }

			if(!isset($_POST['sutunteklifbutonu'])){ $sutunlararray[6] = 0; }

			if(!isset($_POST['sutunsiparisbutonu'])){ $sutunlararray[7] = 0; }

			if(!isset($_POST['sutunduzenlebutonu'])){ $sutunlararray[8] = 0; }

			if(!isset($_POST['sutunsiparisadedi'])){ $sutunlararray[9] = 0; }

			if(!isset($_POST['sutunuyariadedi'])){ $sutunlararray[10] = 0; }

			if(!isset($_POST['sutunsipariskilo'])){ $sutunlararray[11] = 0; }

			if(!isset($_POST['sutunboyolcusu'])){ $sutunlararray[12] = 0; }

			if(!isset($_POST['sutunmusteriismi'])){ $sutunlararray[13] = 0; }

			if(!isset($_POST['sutuntarih'])){ $sutunlararray[14] = 0; }

			if(!isset($_POST['sutuntermin'])){ $sutunlararray[15] = 0; }

			if(!isset($_POST['sutunmanuelsatis'])){ $sutunlararray[16] = 0; }

			if(!isset($_POST['sutunurunkodu'])){ $sutunlararray[17] = 0; }

			if(!isset($_POST['sutundepoadet'])){ $sutunlararray[18] = 0; }

			if(!isset($_POST['sutundepouyariadet'])){ $sutunlararray[19] = 0; }

			if(!isset($_POST['sutunraf'])){ $sutunlararray[20] = 0; }

			if(!isset($_POST['sutunsevkiyatbutonu'])){ $sutunlararray[21] = 0; }

			if(!isset($_POST['sutunpalet'])){ $sutunlararray[22] = 0; }

			$sutunlar = implode(",",$sutunlararray);

			$allow = array('pdf');

            $temp = explode(".", $_FILES['uploadfile']['name']);

            $dosyaadi = $temp[0];

            $extension = end($temp);

            $randomsayi = rand(0,10000);

            if (empty($dosyaadi)) {
            	
            	$upload_file = $eskiresim;

            }else{

            	$upload_file = $dosyaadi.$randomsayi.".".$extension;

            }

            move_uploaded_file($_FILES['uploadfile']['tmp_name'], "img/kategoriler/".$upload_file);

			$query = $db->prepare("UPDATE kategori SET kategori_adi = ?, kategori_tipi = ?, kategori_ust = ?, resim = ?, sutunlar = ? WHERE kategori_id = ?"); 

			$guncelle = $query->execute(array($kategori_adi,$kategori_tipi,$kategori_ust,$upload_file,$sutunlar,$kategori_id));

			header("Location:kategoriler.php");

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

		<div class="container-fluid">

			<div class="row">
				
				<div class="col-md-12">
					
					<?= $error; ?>

				</div>

			</div>

			<div class="row">

				<div class="col-md-3 col-12">

					<div class="div4">

						<form action="" method="POST">

							<h4>Ürün Ekleme</h4>

							<div class="row form-group">
								
								<div class="col-12">
									
									<select class="form-control" name="kategori_iki">

										<option selected>Lütfen Bir Kategori Seçiniz</option>
										
										<?php

											$query = $db->query("SELECT * FROM kategori WHERE kategori_tipi = '1' AND sirketid = '{$user->company_id}' AND silik = '0' ORDER BY kategori_adi ASC", PDO::FETCH_ASSOC);

											if ( $query->rowCount() ){

												foreach( $query as $row ){

													$kategori_id = $row['kategori_id'];

													$kategori_adi = $row['kategori_adi'];

													$kategori_ust = $row['kategori_ust'];

													$ustadcek = $db->query("SELECT * FROM kategori WHERE kategori_id = '{$kategori_ust}' AND sirketid = '{$user->company_id}'  AND silik = '0'")->fetch(PDO::FETCH_ASSOC);

                                                    if ($ustadcek && is_array($ustadcek)) {
                                                        $ustkategoriadi = $ustadcek['kategori_adi'] ?? null;
                                                    } else {
                                                        $ustkategoriadi = null;
                                                    }

										?>

													<option value="<?= $kategori_id; ?>"><?= $kategori_adi." (".$ustkategoriadi.")"; ?></option>

										<?php

												}

											}

										?>

									</select>

								</div>

							</div>

							<div class="row form-group">
								
								<div class="col-12">
									
									<input type="text" name="urun_adi" placeholder="Lütfen Ürün Adını Giriniz" class="form-control">

								</div>

							</div>

							<div class="row form-group">
								
								<div class="col-12">
									
									<button type="submit" class="btn btn-warning btn-block" name="urunekle">Ürün Ekle</button>

								</div>

							</div>

						</form>

					</div>
					
					<div class="div4">

						<form action="" method="POST" enctype="multipart/form-data">

							<h4>Alt Kategori Ekleme</h4>

							<div class="row form-group">
								
								<div class="col-12">
									
									<input type="text" name="kategori_adi" placeholder="Lütfen Kategori Adını Giriniz" class="form-control">

								</div>

							</div>

							<div class="row form-group">
								
								<div class="col-12">
									
									<input type="file" name="uploadfile" style="margin-bottom: 10px;">

								</div>

							</div>

							<div class="row form-group">
								
								<div class="col-12">

									<input type="hidden" name="kategori_tipi" value="1"/>
									
									<button type="submit" class="btn btn-success btn-block" name="kategoriekle">Kategori Ekle</button>

								</div>

							</div>

						</form>

					</div>

					<div class="div4">

						<form action="" method="POST" enctype="multipart/form-data">

							<h4>Üst Kategori Ekleme</h4>

							<div class="row form-group">
								
								<div class="col-12">
									
									<input type="text" name="kategori_adi" placeholder="Lütfen Kategori Adını Giriniz" class="form-control">

								</div>

							</div>

							<div class="row form-group">
								
								<div class="col-12">
									
									<input type="file" name="uploadfile" style="margin-bottom: 10px;">

								</div>

							</div>

							<hr/>

							<h5><b>Sütunlar</b></h5><small>(Gereksiz sütunlardaki işaretleri kaldırınız.)</small><br/>

							<hr/>

							<b>Göstergeler</b>

							<div class="form-check">

								<input type="checkbox" class="form-check-input" id="exampleCheck0" name="sutunurunkodu" checked>
								
								<label class="form-check-label" for="exampleCheck0">Ürün Kodu<small>(1 birim)</small></label>
							
							</div>

							<div class="form-check">

								<input type="checkbox" class="form-check-input" id="sutunAdetCheck" name="sutunadet" checked>
								
								<label class="form-check-label" for="sutunAdetCheck">Adet <small>(1 birim)</small></label>
							
							</div>

							<div class="form-check">

								<input type="checkbox" class="form-check-input" id="sutunPaletCheck" name="sutunpalet" checked>
								
								<label class="form-check-label" for="sutunPaletCheck">Palet <small>(1 birim)</small></label>
							
							</div>

							<div class="form-check">

								<input type="checkbox" class="form-check-input" id="sutunDepoAdetCheck" name="sutundepoadet" checked>
								
								<label class="form-check-label" for="sutunDepoAdetCheck">Depo Adet <small>(1 birim)</small></label>
							
							</div>

							<div class="form-check">

								<input type="checkbox" class="form-check-input" id="sutunrafCheck" name="sutunraf" checked>
								
								<label class="form-check-label" for="sutunrafCheck">Raf <small>(1 birim)</small></label>
							
							</div>

							<div class="form-check">

								<input type="checkbox" class="form-check-input" id="exampleCheck2" name="sutunbirimkg" checked>
								
								<label class="form-check-label" for="exampleCheck2">Birim Kg <small>(1 birim)</small></label>
							
							</div>

							<div class="form-check">

								<input type="checkbox" class="form-check-input" id="exampleCheck3" name="sutuntoplam" checked>
								
								<label class="form-check-label" for="exampleCheck3">Toplam <small>(1 birim)</small></label>
							
							</div>

							<div class="form-check">

								<input type="checkbox" class="form-check-input" id="exampleCheck4" name="sutunalis" checked>
								
								<label class="form-check-label" for="exampleCheck4">Alış <small>(1 birim)</small></label>
							
							</div>

							<div class="form-check">

								<input type="checkbox" class="form-check-input" id="exampleCheck5" name="sutunsatis" checked>
								
								<label class="form-check-label" for="exampleCheck5">Satış <small>(1 birim)</small></label>
							
							</div>

							<div class="form-check">

								<input type="checkbox" class="form-check-input" id="exampleCheck6" name="sutunfabrika" checked>
								
								<label class="form-check-label" for="exampleCheck6">Fabrika <small>(2 birim)</small></label>
							
							</div>

							<hr/>

							<b>Düzenleme Formu</b>

							<div class="form-check">

								<input type="checkbox" class="form-check-input" id="exampleCheck10" name="sutunsiparisadedi" checked>
								
								<label class="form-check-label" for="exampleCheck10">Sipariş Adedi<small>(1 birim)</small></label>
							
							</div>	

							<div class="form-check">

								<input type="checkbox" class="form-check-input" id="sutunuyariadedi" name="sutunuyariadedi" checked>
								
								<label class="form-check-label" for="sutunuyariadedi">Uyarı Adedi<small>(1 birim)</small></label>
							
							</div>
							
							<div class="form-check">

								<input type="checkbox" class="form-check-input" id="sutundepouyariadet" name="sutundepouyariadet" checked>
								
								<label class="form-check-label" for="sutundepouyariadet">Depo Uyarı Adedi<small>(1 birim)</small></label>
							
							</div>
							
							<div class="form-check">

								<input type="checkbox" class="form-check-input" id="sutunsipariskilo" name="sutunsipariskilo" checked>
								
								<label class="form-check-label" for="sutunsipariskilo">Sipariş Kilo<small>(1 birim)</small></label>
							
							</div>	

							<div class="form-check">

								<input type="checkbox" class="form-check-input" id="sutunboyolcusu" name="sutunboyolcusu" checked>
								
								<label class="form-check-label" for="sutunboyolcusu">Boy Ölçüsü<small>(1 birim)</small></label>
							
							</div>	

							<div class="form-check">

								<input type="checkbox" class="form-check-input" id="sutunmusteriismi" name="sutunmusteriismi" checked>
								
								<label class="form-check-label" for="sutunmusteriismi">Müşteri İsmi<small>(1 birim)</small></label>
							
							</div>	

							<div class="form-check">

								<input type="checkbox" class="form-check-input" id="sutuntarih" name="sutuntarih" checked>
								
								<label class="form-check-label" for="sutuntarih">Tarih<small>(1 birim)</small></label>
							
							</div>	

							<div class="form-check">

								<input type="checkbox" class="form-check-input" id="sutuntermin" name="sutuntermin" checked>
								
								<label class="form-check-label" for="sutuntermin">Termin<small>(1 birim)</small></label>
							
							</div>	

							<div class="form-check">

								<input type="checkbox" class="form-check-input" id="sutunmanuelsatis" name="sutunmanuelsatis" onchange="yuzdeinputuac('yuzdeinputu');" checked >
								
								<label class="form-check-label" for="sutunmanuelsatis">Manuel Satış<small>(1 birim)</small></label>
							
							</div>	

							<div class="row form-group">
								
								<div class="col-12">

									<div id="yuzdeinputu" style="display: none;">
									
										<input type="text" name="karyuzdesi" class="form-control" placeholder="Kâr yüzdenizi sadece sayı ile yazınız.">

									</div>

								</div>

							</div>

							<hr/>

							<b>Butonlar</b>

							<div class="form-check">

								<input type="checkbox" class="form-check-input" id="exampleCheck7" name="sutunteklifbutonu" checked>
								
								<label class="form-check-label" for="exampleCheck7">Teklif Butonu <small>(1 birim)</small></label>
							
							</div>

							<div class="form-check">

								<input type="checkbox" class="form-check-input" id="exampleCheck8" name="sutunsiparisbutonu" checked>
								
								<label class="form-check-label" for="exampleCheck8">Sipariş Butonu <small>(1 birim)</small></label>
							
							</div>

							<div class="form-check">

								<input type="checkbox" class="form-check-input" id="sevkiyatButonuCheckCreate" name="sutunsevkiyatbutonu" checked>
								
								<label class="form-check-label" for="sevkiyatButonuCheckCreate">Sevkiyat Butonu <small>(1 birim)</small></label>
							
							</div>	

							<div class="form-check">

								<input type="checkbox" class="form-check-input" id="exampleCheck9" name="sutunduzenlebutonu" checked>
								
								<label class="form-check-label" for="exampleCheck9">Düzenle Butonu <small>(1 birim)</small></label>
							
							</div>	
							
							<br/>

							<div class="row form-group">
								
								<div class="col-12">

									<input type="hidden" name="kategori_tipi" value="0"/>
									
									<button type="submit" class="btn btn-info btn-block" name="kategoriekle">Kategori Ekle</button>

								</div>

							</div>

						</form>

					</div>

				</div>
				
				<div class="col-md-9 col-12">
					
					<div class="div4">
				
						<h4>Kategorileri Düzenleme</h4>

						<hr style="margin: 5px;" />

						<div class="row" style="margin-top:15px;">
							
							<div class="col-md-6">
								
								<h5><b>Üst Kategoriler</b></h5>

								<hr style="margin: 5px;" />

						<?php

							$query = $db->query("SELECT * FROM kategori WHERE sirketid = '{$user->company_id}' AND kategori_tipi = '0' AND silik = '0' ORDER BY kategori_adi ASC", PDO::FETCH_ASSOC);

							if ( $query->rowCount() ){
								foreach( $query as $row ){
									$kategori_id = guvenlik($row['kategori_id']);
									$kategori_adi = guvenlik($row['kategori_adi']);
									$kategori_tipi = guvenlik($row['kategori_tipi']);
									$kategori_ust = guvenlik($row['kategori_ust']);
									$karyuzdesi = guvenlik($row['karyuzdesi']);
									$resim = guvenlik($row['resim']);
									$sutunlar = guvenlik($row['sutunlar']);
									$sutunlaripatlat = explode(",", $sutunlar);
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
									$sutunurunkoduizni = $sutunlaripatlat[17];
									$sutundepoadetizni = $sutunlaripatlat[18];
									$sutundepouyariizni = $sutunlaripatlat[19];
									$sutunrafizni = $sutunlaripatlat[20];
									$sutunsevkiyatbutonuizni = $sutunlaripatlat[21];
									$sutunpaletizni = $sutunlaripatlat[22];

						?>			

									<div class="row" style="padding: 10px;">
										
										<div class="col-12">
											
											<div class="row" style="margin-bottom: 3px;">
												
												<div class="col-12">
													
													<a href="#" onclick="return false" onmousedown="javascript:ackapa('kategoriduzenlemeformu<?= $kategori_id; ?>');">

														<div><?= $kategori_adi; ?></div>

													</a>

												</div>

											</div>

											<div id="kategoriduzenlemeformu<?= $kategori_id; ?>" style="display:none; background-color: #e6ecf0; padding: 10px;">
												
												<form action="" method="POST" enctype="multipart/form-data">

													<div class="row" style="margin-bottom: 3px;">
														
														<div class="col-12"><input type="text" name="kategori_adi" value="<?= $kategori_adi; ?>" class="form-control"></div>

													</div>

													<div class="row" style="margin-bottom: 3px;">

														<div class="col-12">
																
															<select class="form-control" name="kategori_tipi">

																<?php if($kategori_tipi == '0'){?>

																	<option selected value="0">Üst Kategori</option>

																	<option value="1">Alt Kategori</option>

																<?php }else{ ?>

																	<option value="0">Üst Kategori</option>

																	<option selected value="1">Alt Kategori</option>

																<?php } ?>									

															</select>

														</div>

													</div>

													<div class="row" style="margin-bottom: 3px;">

														<div class="col-12">

															<?php if($kategori_tipi == 1){ ?>
															
															<select class="form-control" name="kategori_ust">

																<option value="0">Kategori Seçiniz</option>

																<?php

																	$ustkatcek = $db->query("SELECT * FROM kategori WHERE kategori_tipi = '0' AND sirketid = '{$user->company_id}' AND silik = '0' ORDER BY kategori_adi ASC", PDO::FETCH_ASSOC);

																	if ( $ustkatcek->rowCount() ){

																		foreach( $ustkatcek as $ukc ){

																			$ust_kategori_id = $ukc['kategori_id'];

																			$ust_kategori_adi = $ukc['kategori_adi'];

																?>
																				<option <?= $kategori_ust == $ust_kategori_id ? 'selected' : '' ?> value="<?= $ust_kategori_id; ?>"><?= $ust_kategori_adi; ?></option>
																<?php
																		}

																	}

																?>								

															</select>

															<?php } ?>

														</div>

													</div>

													<div style="padding: 3px;">

														<hr/>

														<b>Göstergeler</b>

														<div class="form-check">

															<input type="checkbox" class="form-check-input" id="exampleCheck0ug" name="sutunurunkodu" <?= $sutunurunkoduizni == 1 ? 'checked' : '' ?> >

															<label class="form-check-label" for="exampleCheck0ug">Ürün Kodu<small>(1 birim)</small></label>
														
														</div>

														<div class="form-check">

															<input type="checkbox" class="form-check-input" id="checkAdet" name="sutunadet" <?= $sutunadetizni == 1 ? 'checked' : '' ?> >

															<label class="form-check-label" for="checkAdet">Adet <small>(1 birim)</small></label>
														
														</div>

														<div class="form-check">

															<input type="checkbox" class="form-check-input" id="checkPalet" name="sutunpalet" <?= $sutunpaletizni == 1 ? 'checked' : '' ?> >

															<label class="form-check-label" for="checkPalet">Palet <small>(1 birim)</small></label>
														
														</div>

														<div class="form-check">

															<input type="checkbox" class="form-check-input" id="checkDepoAdet" name="sutundepoadet" <?= $sutundepoadetizni == 1 ? 'checked' : '' ?> >

															<label class="form-check-label" for="checkDepoAdet">Depo Adet <small>(1 birim)</small></label>
														
														</div>

														<div class="form-check">

															<input type="checkbox" class="form-check-input" id="sutunRafCheckUpdate" name="sutunraf" <?= $sutunrafizni == 1 ? 'checked' : '' ?> >

															<label class="form-check-label" for="sutunRafCheckUpdate">Raf <small>(1 birim)</small></label>
														
														</div>

														<div class="form-check">

															<input type="checkbox" class="form-check-input" id="exampleCheck2" name="sutunbirimkg" <?= $sutunbirimkgizni == 1 ? 'checked' : '' ?> >

															<label class="form-check-label" for="exampleCheck2">Birim Kg <small>(1 birim)</small></label>
														
														</div>

														<div class="form-check">

															<input type="checkbox" class="form-check-input" id="exampleCheck3" name="sutuntoplam" <?= $sutuntoplamizni == 1 ? 'checked' : '' ?> >

															<label class="form-check-label" for="exampleCheck3">Toplam <small>(1 birim)</small></label>
														
														</div>

														<div class="form-check">

															<input type="checkbox" class="form-check-input" id="exampleCheck4" name="sutunalis" <?= $sutunalisizni == 1 ? 'checked' : '' ?> >

															<label class="form-check-label" for="exampleCheck4">Alış <small>(1 birim)</small></label>
														
														</div>

														<div class="form-check">

															<input type="checkbox" class="form-check-input" id="exampleCheck5" name="sutunsatis" <?= $sutunsatisizni == 1 ? 'checked' : '' ?> >

															<label class="form-check-label" for="exampleCheck5">Satış <small>(1 birim)</small></label>
														
														</div>

														<div class="form-check">

															<input type="checkbox" class="form-check-input" id="exampleCheck6" name="sutunfabrika" <?= $sutunfabrikaizni == 1 ? 'checked' : '' ?> >

															<label class="form-check-label" for="exampleCheck6">Fabrika <small>(1 birim)</small></label>
														
														</div>

														<hr/>

														<b>Düzenleme Formu</b>

														<div class="form-check">

															<input type="checkbox" class="form-check-input" id="exampleCheck10" name="sutunsiparisadedi" <?= $sutunsiparisadediizni == 1 ? 'checked' : '' ?> >

															<label class="form-check-label" for="exampleCheck10">Sipariş Adedi<small>(1 birim)</small></label>
														
														</div>	

														<div class="form-check">

															<input type="checkbox" class="form-check-input" id="sutunuyariadedi" name="sutunuyariadedi" <?= $sutunuyariadediizni == 1 ? 'checked' : '' ?> >

															<label class="form-check-label" for="sutunuyariadedi">Uyarı Adedi<small>(1 birim)</small></label>
														
														</div>	

														<div class="form-check">

															<input type="checkbox" class="form-check-input" id="sutundepouyariadet" name="sutundepouyariadet" <?= $sutundepouyariizni == 1 ? 'checked' : '' ?> >

															<label class="form-check-label" for="sutundepouyariadet">Depo Uyarı Adedi<small>(1 birim)</small></label>
														
														</div>	
														
														<div class="form-check">

															<input type="checkbox" class="form-check-input" id="sutunsipariskilo" name="sutunsipariskilo" <?= $sutunsipariskiloizni == 1 ? 'checked' : '' ?> >

															<label class="form-check-label" for="sutunsipariskilo">Sipariş Kilo<small>(1 birim)</small></label>
														
														</div>	

														<div class="form-check">

															<input type="checkbox" class="form-check-input" id="sutunboyolcusu" name="sutunboyolcusu" <?= $sutunboyolcusuizni == 1 ? 'checked' : '' ?> >

															<label class="form-check-label" for="sutunboyolcusu">Boy Ölçüsü<small>(1 birim)</small></label>
														
														</div>	

														<div class="form-check">

															<input type="checkbox" class="form-check-input" id="sutunmusteriismi" name="sutunmusteriismi" <?= $sutunmusteriismiizni == 1 ? 'checked' : '' ?> >

															<label class="form-check-label" for="sutunmusteriismi">Müşteri İsmi<small>(1 birim)</small></label>
														
														</div>	

														<div class="form-check">

															<input type="checkbox" class="form-check-input" id="sutuntarih" name="sutuntarih" <?= $sutuntarihizni == 1 ? 'checked' : '' ?> >

															<label class="form-check-label" for="sutuntarih">Tarih<small>(1 birim)</small></label>
														
														</div>	

														<div class="form-check">

															<input type="checkbox" class="form-check-input" id="sutuntermin" name="sutuntermin" <?= $sutunterminizni == 1 ? 'checked' : '' ?> >

															<label class="form-check-label" for="sutuntermin">Termin<small>(1 birim)</small></label>
														
														</div>	

														<div class="form-check">

															<input type="checkbox" class="form-check-input" id="sutunmanuelsatis2" name="sutunmanuelsatis" onchange="yuzdeinputuac('yuzdeinputu<?= $kategori_id; ?>');" c<?= $sutunmanuelsatisizni == 1 ? 'hecked ' : '' ?> >

															<label class="form-check-label" for="sutunmanuelsatis2">Manuel Satış<small>(1 birim)</small></label>
														
														</div>	

														<div class="row form-group">
															
															<div class="col-12">

																<?php if($sutunmanuelsatisizni == 1){ ?>

																<div id="yuzdeinputu<?= $kategori_id; ?>" style="display: none;">

																<?php }else{ ?>

																<div id="yuzdeinputu<?= $kategori_id; ?>">

																<?php } ?>
																
																	<input type="text" name="karyuzdesi" class="form-control" placeholder="Kâr yüzdenizi sadece sayı ile yazınız." value="<?= $karyuzdesi; ?>">

																</div>

															</div>

														</div>

														<hr/>

														<b>Butonlar</b>

														<div class="form-check">

															<input type="checkbox" class="form-check-input" id="exampleCheck7" name="sutunteklifbutonu" <?= $sutunteklifbutonuizni == 1 ? 'checked' : '' ?> >

															<label class="form-check-label" for="exampleCheck7">Teklif Butonu <small>(1 birim)</small></label>
														
														</div>

														<div class="form-check">

															<input type="checkbox" class="form-check-input" id="exampleCheck8" name="sutunsiparisbutonu" <?= $sutunsiparisbutonuizni == 1 ? 'checked' : '' ?> >

															<label class="form-check-label" for="exampleCheck8">Sipariş Butonu <small>(1 birim)</small></label>
														
														</div>

														<div class="form-check">

															<input type="checkbox" class="form-check-input" id="sevkiyatButonuCheckEdit" name="sutunsevkiyatbutonu" <?= $sutunsevkiyatbutonuizni == 1 ? 'checked' : '' ?> >

															<label class="form-check-label" for="sevkiyatButonuCheckEdit">Sevkiyat Butonu <small>(1 birim)</small></label>
														
														</div>

														<div class="form-check">

															<input type="checkbox" class="form-check-input" id="exampleCheck9" name="sutunduzenlebutonu" <?= $sutunduzenlebutonuizni == 1 ? 'checked' : '' ?> >

															<label class="form-check-label" for="exampleCheck9">Düzenle Butonu <small>(1 birim)</small></label>
														
														</div>

														<hr/>

														<div class="form-group">
															
															<input type="file" name="uploadfile" style="margin-bottom: 10px;">

														</div>

													</div>

													<div class="row" style="margin-bottom: 3px;">

														<div class="col-12" style="text-align: right;">

															<input type="hidden" name="kategori_id" value="<?= $kategori_id; ?>">

															<input type="hidden" name="eskiresim" value="<?= $resim; ?>">

															<button type="submit" name="kategoriduzenle" class="btn btn-warning btn-sm btn-block">Kaydet</button>

														</div>	

													</div>

												</form>

												<form action="" method="POST">

													<div class="row" style="margin-bottom: 3px;">

														<div class="col-12">

															<input type="hidden" name="kategori_id" value="<?= $kategori_id; ?>">
														
															<button type="submit" name="kategorisil" class="btn btn-danger btn-sm btn-block">Sil</button>

														</div>

													</div>

												</form>

											</div>

										</div>

									</div>

									<hr style="margin: 5px;" />						

						<?php

								}

							}

						?>

							</div>

							<div class="col-md-6">
								
								<h5><b>Alt Kategoriler</b></h5>
						
								<hr style="margin: 5px;" />

						<?php

							$query = $db->query("SELECT * FROM kategori WHERE sirketid = '{$user->company_id}' AND kategori_tipi = '1' AND silik = '0' ORDER BY kategori_adi ASC", PDO::FETCH_ASSOC);

							if ( $query->rowCount() ){

								foreach( $query as $row ){

									$kategori_id = guvenlik($row['kategori_id']);

									$kategori_adi = guvenlik($row['kategori_adi']);

									$kategori_tipi = guvenlik($row['kategori_tipi']);

									$kategori_ust = guvenlik($row['kategori_ust']);

									$sutunlar = guvenlik($row['sutunlar']);

									$sutunlaripatlat = explode(",", $sutunlar);

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

									$sutunurunkoduizni = $sutunlaripatlat[17];

									$sutundepoadetizni = $sutunlaripatlat[18];

									$sutundepouyariizni = $sutunlaripatlat[19];

									$sutunrafizni = $sutunlaripatlat[20];
									
									$sutunsevkiyatbutonuizni = $sutunlaripatlat[21];

									$sutunpaletizni = $sutunlaripatlat[22];

						?>			

									<div class="row" style="padding: 10px;">
										
										<div class="col-12">
											
											<div class="row" style="margin-bottom: 3px;">
												
												<div class="col-12">
													
													<a href="#" onclick="return false" onmousedown="javascript:ackapa('kategoriduzenlemeformu<?= $kategori_id; ?>');">

														<div><?= $kategori_adi; ?></div>

													</a>

												</div>

											</div>

											<div id="kategoriduzenlemeformu<?= $kategori_id; ?>" style="display:none; background-color: #e6ecf0; padding: 10px;">
												
												<form action="" method="POST" enctype="multipart/form-data">

													<div class="row" style="margin-bottom: 3px;">
														
														<div class="col-12"><input type="text" name="kategori_adi" value="<?= $kategori_adi; ?>" class="form-control"></div>

													</div>

													<div class="row" style="margin-bottom: 3px;">

														<div class="col-12">
																
															<select class="form-control" name="kategori_tipi">

																<?php if($kategori_tipi == '0'){?>

																	<option selected value="0">Üst Kategori</option>

																	<option value="1">Alt Kategori</option>

																<?php }else{ ?>

																	<option value="0">Üst Kategori</option>

																	<option selected value="1">Alt Kategori</option>

																<?php } ?>									

															</select>

														</div>

													</div>

													<div class="row" style="margin-bottom: 3px;">

														<div class="col-12">

															<?php if($kategori_tipi == 1){ ?>
															
															<select class="form-control" name="kategori_ust">

																<option value="0">Kategori Seçiniz</option>

																<?php

																	$ustkatcek = $db->query("SELECT * FROM kategori WHERE kategori_tipi = '0' AND sirketid = '{$user->company_id}' AND silik = '0' ORDER BY kategori_adi ASC", PDO::FETCH_ASSOC);

																	if ( $ustkatcek->rowCount() ){

																		foreach( $ustkatcek as $ukc ){

																			$ust_kategori_id = $ukc['kategori_id'];

																			$ust_kategori_adi = $ukc['kategori_adi'];

																			if($kategori_ust == $ust_kategori_id){ ?>

																				<option selected value="<?= $ust_kategori_id; ?>"><?= $ust_kategori_adi; ?></option>

																			<?php }else{ ?> 

																				<option value="<?= $ust_kategori_id; ?>"><?= $ust_kategori_adi; ?></option>

																			<?php } 

																		}

																	}

																?>								

															</select>

															<?php } ?>

														</div>

													</div>

													<div style="padding: 3px;">

														<hr/>

														<b>Göstergeler</b>

														<div class="form-check">

															<input type="checkbox" class="form-check-input" id="exampleCheck0" name="sutunurunkodu" <?= $sutunurunkoduizni == 1 ? 'checked' : '' ?> >

															<label class="form-check-label" for="exampleCheck0">Ürün Kodu<small>(1 birim)</small></label>
														
														</div>

														<div class="form-check">

															<input type="checkbox" class="form-check-input" id="adetControl" name="sutunadet" <?= $sutunadetizni == 1 ? 'checked' : '' ?> >

															<label class="form-check-label" for="adetControl">Adet <small>(1 birim)</small></label>
														
														</div>

														<div class="form-check">

															<input type="checkbox" class="form-check-input" id="paletControl" name="sutunpalet" <?= $sutunpaletizni == 1 ? 'checked' : '' ?> >

															<label class="form-check-label" for="paletControl">Palet <small>(1 birim)</small></label>
														
														</div>

														<div class="form-check">

															<input type="checkbox" class="form-check-input" id="depoAdetControl" name="sutundepoadet" <?= $sutundepoadetizni == 1 ? 'checked' : '' ?> >

															<label class="form-check-label" for="depoAdetControl">Depo Adet <small>(1 birim)</small></label>
														
														</div>

														<div class="form-check">

															<input type="checkbox" class="form-check-input" id="sutunRafCheckEdit" name="sutunraf" <?= $sutunrafizni == 1 ? 'checked' : '' ?> >

															<label class="form-check-label" for="sutunRafCheckEdit">Raf <small>(1 birim)</small></label>
														
														</div>

														<div class="form-check">

															<input type="checkbox" class="form-check-input" id="exampleCheck2" name="sutunbirimkg" <?= $sutunbirimkgizni == 1 ? 'checked' : '' ?> >

															<label class="form-check-label" for="exampleCheck2">Birim Kg <small>(1 birim)</small></label>
														
														</div>

														<div class="form-check">

															<input type="checkbox" class="form-check-input" id="exampleCheck3" name="sutuntoplam" <?= $sutuntoplamizni == 1 ? 'checked' : '' ?> >

															<label class="form-check-label" for="exampleCheck3">Toplam <small>(1 birim)</small></label>
														
														</div>

														<div class="form-check">

															<input type="checkbox" class="form-check-input" id="exampleCheck4" name="sutunalis" <?= $sutunalisizni == 1 ? 'checked' : '' ?> >

															<label class="form-check-label" for="exampleCheck4">Alış <small>(1 birim)</small></label>
														
														</div>

														<div class="form-check">

															<input type="checkbox" class="form-check-input" id="exampleCheck5" name="sutunsatis" <?= $sutunsatisizni == 1 ? 'checked' : '' ?> >

															<label class="form-check-label" for="exampleCheck5">Satış <small>(1 birim)</small></label>
														
														</div>

														<div class="form-check">

															<input type="checkbox" class="form-check-input" id="exampleCheck6" name="sutunfabrika" <?= $sutunfabrikaizni == 1 ? 'checked' : '' ?> >

															<label class="form-check-label" for="exampleCheck6">Fabrika <small>(2 birim)</small></label>
														
														</div>

														<hr/>

														<b>Düzenleme Formu</b>

														<div class="form-check">

															<input type="checkbox" class="form-check-input" id="exampleCheck10" name="sutunsiparisadedi" <?= $sutunsiparisadediizni == 1 ? 'checked' : '' ?> >

															<label class="form-check-label" for="exampleCheck10">Sipariş Adedi<small>(1 birim)</small></label>
														
														</div>	

														<div class="form-check">

															<input type="checkbox" class="form-check-input" id="sutunuyariadedi" name="sutunuyariadedi" <?= $sutunuyariadediizni == 1 ? 'checked' : '' ?> >

															<label class="form-check-label" for="sutunuyariadedi">Uyarı Adedi<small>(1 birim)</small></label>
														
														</div>
														
														<div class="form-check">

															<input type="checkbox" class="form-check-input" id="sutundepouyariadet" name="sutundepouyariadet" <?= $sutundepouyariizni == 1 ? 'checked' : '' ?> >

															<label class="form-check-label" for="sutundepouyariadet">Depo Uyarı Adedi<small>(1 birim)</small></label>
														
														</div>
														
														<div class="form-check">

															<input type="checkbox" class="form-check-input" id="sutunsipariskilo" name="sutunsipariskilo" <?= $sutunsipariskiloizni == 1 ? 'checked' : '' ?> >

															<label class="form-check-label" for="sutunsipariskilo">Sipariş Kilo<small>(1 birim)</small></label>
														
														</div>	

														<div class="form-check">

															<input type="checkbox" class="form-check-input" id="sutunboyolcusu" name="sutunboyolcusu" <?= $sutunboyolcusuizni == 1 ? 'checked' : '' ?> >

															<label class="form-check-label" for="sutunboyolcusu">Boy Ölçüsü<small>(1 birim)</small></label>
														
														</div>	

														<div class="form-check">

															<input type="checkbox" class="form-check-input" id="sutunmusteriismi" name="sutunmusteriismi" <?= $sutunmusteriismiizni == 1 ? 'checked' : '' ?> >

															<label class="form-check-label" for="sutunmusteriismi">Müşteri İsmi<small>(1 birim)</small></label>
														
														</div>	

														<div class="form-check">

															<input type="checkbox" class="form-check-input" id="sutuntarih" name="sutuntarih" <?= $sutuntarihizni == 1 ? 'checked' : '' ?> >

															<label class="form-check-label" for="sutuntarih">Tarih<small>(1 birim)</small></label>
														
														</div>	

														<div class="form-check">

															<input type="checkbox" class="form-check-input" id="sutuntermin" name="sutuntermin" <?= $sutunterminizni == 1 ? 'checked' : '' ?> >

															<label class="form-check-label" for="sutuntermin">Termin<small>(1 birim)</small></label>
														
														</div>	

														<div class="form-check">

															<?php if($sutunmanuelsatisizni == 1){ ?><input type="checkbox" class="form-check-input" id="sutunmanuelsatis3" name="sutunmanuelsatis" onchange="yuzdeinputuac('yuzdeinputu3');">

															<?php }else{ ?><input type="checkbox" class="form-check-input" id="sutunmanuelsatis3" name="sutunmanuelsatis" onchange="yuzdeinputuac('yuzdeinputu3');" checked ><?php } ?>
															
															<label class="form-check-label" for="sutunmanuelsatis3">Manuel Satış<small>(1 birim)</small></label>
														
														</div>	

														<div class="row form-group">
															
															<div class="col-12">

																<div id="yuzdeinputu3" style="display: none;">
																
																	<input type="text" name="karyuzdesi" class="form-control" placeholder="Kâr yüzdenizi sadece sayı ile yazınız.">

																</div>

															</div>

														</div>

														<hr/>

														<b>Butonlar</b>

														<div class="form-check">

															<input type="checkbox" class="form-check-input" id="exampleCheck7" name="sutunteklifbutonu" <?= $sutunteklifbutonuizni == 1 ? 'checked' : '' ?> >

															<label class="form-check-label" for="exampleCheck7">Teklif Butonu <small>(1 birim)</small></label>
														
														</div>

														<div class="form-check">

															<input type="checkbox" class="form-check-input" id="exampleCheck8" name="sutunsiparisbutonu" <?= $sutunsiparisbutonuizni == 1 ? 'checked' : '' ?> >

															<label class="form-check-label" for="exampleCheck8">Sipariş Butonu <small>(1 birim)</small></label>
														
														</div>

														<div class="form-check">

															<input type="checkbox" class="form-check-input" id="sevkiyatButonuCheckUpdate" name="sutunsevkiyatbutonu" <?= $sutunsevkiyatbutonuizni == 1 ? 'checked' : '' ?> >

															<label class="form-check-label" for="sevkiyatButonuCheckUpdate">Sevkiyat Butonu <small>(1 birim)</small></label>
														
														</div>

														<div class="form-check">

															<input type="checkbox" class="form-check-input" id="exampleCheck9" name="sutunduzenlebutonu" <?= $sutunduzenlebutonuizni == 1 ? 'checked' : '' ?> >

															<label class="form-check-label" for="exampleCheck9">Düzenle Butonu <small>(1 birim)</small></label>
														
														</div>

														<hr/>

														<div class="form-group">
															
															<input type="file" name="uploadfile" style="margin-bottom: 10px;">

														</div>

													</div>

													<div class="row" style="margin-bottom: 3px;">

														<div class="col-12" style="text-align: right;">

															<input type="hidden" name="kategori_id" value="<?= $kategori_id; ?>">

															<button type="submit" name="kategoriduzenle" class="btn btn-warning btn-sm btn-block">Kaydet</button>

														</div>	

													</div>

												</form>

												<form action="" method="POST">

													<div class="row" style="margin-bottom: 3px;">

														<div class="col-12">

															<input type="hidden" name="kategori_id" value="<?= $kategori_id; ?>">
														
															<button type="submit" name="kategorisil" class="btn btn-danger btn-sm btn-block">Sil</button>

														</div>

													</div>

												</form>

											</div>

										</div>

									</div>

									<hr style="margin: 5px;" />						

						<?php

								}

							}

						?>


							</div>

						</div>
						
						
					</div>

				</div>

			</div>

		</div>

		<br/><br/><br/><br/><br/><br/>

		<?php include 'template/script.php'; ?>

	</body>

</html>