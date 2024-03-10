<?php 

	include 'fonksiyonlar/bagla.php';

	if ($girdi != 1) {
		
		header("Location:giris.php");

		exit();

	}elseif ($girdi == 1) {

		if($uye_tipi == '0' || $uye_tipi == '3'){

			header("Location:index.php");

			exit();

		}

		if (isset($_POST['kategorisil'])) {
			
			$kategori_id = guvenlik($_POST['kategori_id']);

			if (kategoridolumu($kategori_id) == '1') {
				
				$hata = '<br/><div class="alert alert-danger" role="alert">Silmek istediğiniz kategoride kayıtlı ürünler var. O ürünleri silmeden kategoriyi silemezsiniz.</a></div>';

			}else{

				$sil = $db->prepare("DELETE FROM kategori WHERE kategori_id = ?");

				$delete = $sil->execute(array($kategori_id));

				header("Location:kategoriler.php");

				exit();

			}

		}

		if (isset($_POST['kategoriekle'])) {

			$sutunlar = '1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1';

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

			$query = $db->prepare("INSERT INTO kategori SET kategori_adi = ?, kategori_tipi = ?, kategori_ust = ?, resim = ?, sutunlar = ?, sirketid = ?");

			$insert = $query->execute(array($kategori_adi,$kategori_tipi,'0',$upload_file,$sutunlar,$uye_sirket));

			header("Location:kategoriler.php");

			exit();

		}

		if (isset($_POST['urunekle'])) {
			
			$kategori_iki = guvenlik($_POST['kategori_iki']);

			$katbircek = $db->query("SELECT * FROM kategori WHERE kategori_id = '{$kategori_iki}' AND sirketid = '{$uye_sirket}'")->fetch(PDO::FETCH_ASSOC);

			$kategori_bir = $katbircek['kategori_ust'];

			$urun_adi = guvenlik($_POST['urun_adi']);

			$sonuruncek = $db->query("SELECT * FROM urun WHERE kategori_iki = '{$kategori_iki}' AND kategori_bir = '{$kategori_bir}' AND sirketid = '{$uye_sirket}' ORDER BY urun_sira DESC LIMIT 1", PDO::FETCH_ASSOC);

			if ( $sonuruncek->rowCount() ){

				foreach( $sonuruncek as $suc ){

					$sonurunsirasi = $suc['urun_sira'];

				}

			}

			$sonurunsirasi++;

			$query = $db->prepare("INSERT INTO urun SET kategori_bir = ?, kategori_iki = ?, urun_adi = ?, urun_sira = ?, urun_birimkg = ?, urun_boy_olcusu = ?, urun_alis = ?, satis = ?, urun_fabrika = ?, urun_stok = ?, urun_uyari_stok_adedi = ?, tarih = ?, termin = ?, musteri_ismi = ?, sirketid = ?");

			$insert = $query->execute(array($kategori_bir,$kategori_iki,$urun_adi,$sonurunsirasi,'','','','','','','','','','',$uye_sirket));

			header("Location:kategoriler.php");

			exit();

		}

		if (isset($_POST['kategoriduzenle'])) {
			
			$kategori_id = guvenlik($_POST['kategori_id']);

			$kategori_adi = guvenlik($_POST['kategori_adi']);

			$kategori_tipi = guvenlik($_POST['kategori_tipi']);

			$kategori_ust = guvenlik($_POST['kategori_ust']);

			if ($kategori_tipi == 0) {
				
				$kategori_ust = 0;

			}

			$query = $db->prepare("UPDATE kategori SET kategori_adi = ?, kategori_tipi = ?, kategori_ust = ? WHERE kategori_id = ?"); 

			$guncelle = $query->execute(array($kategori_adi,$kategori_tipi,$kategori_ust,$kategori_id));

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
					
					<?php echo $hata; ?>

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

											$query = $db->query("SELECT * FROM kategori WHERE kategori_tipi = '1' AND sirketid = '{$uye_sirket}' ORDER BY kategori_adi ASC", PDO::FETCH_ASSOC);

											if ( $query->rowCount() ){

												foreach( $query as $row ){

													$kategori_id = $row['kategori_id'];

													$kategori_adi = $row['kategori_adi'];

													$kategori_ust = $row['kategori_ust'];

													$ustadcek = $db->query("SELECT * FROM kategori WHERE kategori_id = '{$kategori_ust}' AND sirketid = '{$uye_sirket}'")->fetch(PDO::FETCH_ASSOC);

													$ustkategoriadi = $ustadcek['kategori_adi'];

										?>

													<option value="<?php echo $kategori_id; ?>"><?php echo $kategori_adi." (".$ustkategoriadi.")"; ?></option>

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

								<input type="checkbox" class="form-check-input" id="exampleCheck1" name="sutunadet" checked>
								
								<label class="form-check-label" for="exampleCheck1">Adet <small>(1 birim)</small></label>
							
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

								<input type="checkbox" class="form-check-input" id="sutunmanuelsatis" name="sutunmanuelsatis" onchange="yuzdeinputuac();" checked >
								
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
				
						<h4>Kategorileri Düzenleme</h4><hr/>

						<div class="d-none d-sm-block">

							<div class="row">
								
								<div class="col-4"><b>Kategori Adı</b></div>

								<div class="col-3"><b>Kategori Tipi</b></div>

								<div class="col-3"><b>Üst Kategori</b></div>

								<div class="col-2"></div>

							</div>

						</div>

						<hr style="margin: 5px;" />

			<?php

				$query = $db->query("SELECT * FROM kategori WHERE sirketid = '{$uye_sirket}'", PDO::FETCH_ASSOC);

				if ( $query->rowCount() ){

					foreach( $query as $row ){

						$kategori_id = $row['kategori_id'];

						$kategori_adi = $row['kategori_adi'];

						$kategori_tipi = $row['kategori_tipi'];

						$kategori_ust = $row['kategori_ust'];

			?>			

							<div class="row">

								<div class="col-md-11 col-12">
									
									<form action="" method="POST">

										<div class="row">

											<div class="col-4 d-block d-sm-none">Adı : </div>
											
											<div class="col-md-4 col-8"><input type="text" name="kategori_adi" value="<?php echo $kategori_adi; ?>" class="form-control"></div>

											<div class="col-4 d-block d-sm-none">Tipi : </div>

											<div class="col-md-3 col-8">
													
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

											<div class="col-4 d-block d-sm-none">Üst Kat. : </div>

											<div class="col-md-3 col-8">

												<?php if($kategori_tipi == 1){ ?>
												
												<select class="form-control" name="kategori_ust">

													<option value="0">Kategori Seçiniz</option>

													<?php

														$ustkatcek = $db->query("SELECT * FROM kategori WHERE kategori_tipi = '0' AND sirketid = '{$uye_sirket}' ORDER BY kategori_adi ASC", PDO::FETCH_ASSOC);

														if ( $ustkatcek->rowCount() ){

															foreach( $ustkatcek as $ukc ){

																$ust_kategori_id = $ukc['kategori_id'];

																$ust_kategori_adi = $ukc['kategori_adi'];

																if($kategori_ust == $ust_kategori_id){ ?>

																	<option selected value="<?php echo $ust_kategori_id; ?>"><?php echo $ust_kategori_adi; ?></option>

																<?php }else{ ?> 

																	<option value="<?php echo $ust_kategori_id; ?>"><?php echo $ust_kategori_adi; ?></option>

																<?php } 

															}

														}

													?>								

												</select>

												<?php } ?>

											</div>

											<div class="col-md-2 col-12" style="text-align: right;">

												<input type="hidden" name="kategori_id" value="<?php echo $kategori_id; ?>">

												<button type="submit" name="kategoriduzenle" class="btn btn-warning btn-sm btn-block">Kaydet</button>

											</div>	

										</div>

									</form>

								</div>

								<div class="col-md-1 col-12">

									<form action="" method="POST">

										<input type="hidden" name="kategori_id" value="<?php echo $kategori_id; ?>">
									
										<button type="submit" name="kategorisil" class="btn btn-danger btn-sm btn-block">Sil</button>

									</form>

								</div>

							</div><hr style="margin: 5px;" />

			<?php

					}

				}

			?>

					</div>

				</div>

			</div>

		</div>

		<br/><br/><br/><br/><br/><br/>

		<?php include 'template/script.php'; ?>

	</body>

</html>