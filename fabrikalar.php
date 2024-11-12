<?php 

	include 'fonksiyonlar/bagla.php';

	if ($girdi != 1) {
		
		header("Location:giris.php");

		exit();

	}elseif ($girdi == 1) {

		if($uye_tipi == '0'){

			header("Location:index.php");

			exit();

		}

	if($uye_tipi != '3'){

		if (isset($_POST['fabrikabilgileriguncelle'])) {
			
			$fabrika_id = guvenlik($_POST['fabrika_id']);

			$fabrika_adi = guvenlik($_POST['fabrika_adi']);

			$fabrikatel = guvenlik($_POST['fabrikatel']);

			$fabrikaeposta = guvenlik($_POST['fabrikaeposta']);

			$fabrikaiscilik = guvenlik($_POST['fabrikaiscilik']);

			$fabrikaadres = guvenlik($_POST['fabrikaadres']);

			$query = $db->prepare("UPDATE fabrikalar SET fabrika_adi = ?, fabrikatel = ?, fabrikaeposta = ?, fabrikaiscilik = ?, fabrikaadres = ? WHERE fabrika_id = ?"); 

			$guncelle = $query->execute(array($fabrika_adi, $fabrikatel, $fabrikaeposta, $fabrikaiscilik, $fabrikaadres, $fabrika_id));

			$siraid = guvenlik($_POST['siraid']);

			header("Location:fabrikalar.php#".($siraid - 2));

			exit();

		}

		if (isset($_POST['fabrikasil'])) {

			$fabrika_id = guvenlik($_POST['fabrika_id']);

			if (fabrikakullanimdami($fabrika_id) == '1') {
			
				$hata = '<br/><div class="alert alert-danger" role="alert">Bu fabrikanın kayıtlı olduğu bir ürün, sipariş veya sipariş formu var o yüzden silemiyoruz.</div>';

			}else{
			
				$sil = $db->prepare("DELETE FROM fabrikalar WHERE fabrika_id = ?");

				$delete = $sil->execute(array($fabrika_id));

				$siraid = guvenlik($_POST['siraid']);

				header("Location:fabrikalar.php#".($siraid - 2));

				exit();

			}

		}

		if (isset($_POST['siparisformunusil'])) {
			
			$formid = guvenlik($_POST['formid']);

			$siparisler = guvenlik($_POST['siparisler']);

			$silmekicinpatlat = explode(",", $siparisler);

			foreach ($silmekicinpatlat as $key => $value) {

				$query = $db->prepare("UPDATE siparis SET silik = ? WHERE siparis_id = ?"); 

				$guncelle = $query->execute(array('1',$value));

			}

			$silici = $db->prepare("UPDATE siparisformlari SET silik = ? WHERE formid = ?"); 

			$deleter = $query->execute(array('1',$formid));

			$siraid = guvenlik($_POST['siraid']);

			header("Location:fabrikalar.php#".($siraid - 2));

			exit();

		}

		if (isset($_POST['fabrika_ekleme_formu'])) {
			
			$fabrika_adi = guvenlik($_POST['fabrika_adi']);

			$fabrikatel = guvenlik($_POST['fabrikatel']);

			$fabrikaeposta = guvenlik($_POST['fabrikaeposta']);

			$fabrikaadres = guvenlik($_POST['fabrikaadres']);

			$fabrikaalacak = 0;

			$fabrikaalacaktarih = 0;

			$query = $db->prepare("INSERT INTO fabrikalar SET fabrika_adi = ?, fabrikatel = ?, fabrikaeposta = ?, fabrikaadres = ?, fabrikaalacak = ?, fabrikaalacaktarih = ?, sirketid = ?");

			$insert = $query->execute(array($fabrika_adi, $fabrikatel, $fabrikaeposta, $fabrikaadres, $fabrikaalacak, $fabrikaalacaktarih, $uye_sirket));

			header("Location:fabrikalar.php");

			exit();

		}

		if (isset($_POST['siparissil'])) {
			
			$siparis_id = guvenlik($_POST['siparis_id']);

			$query = $db->prepare("UPDATE siparis SET silik = ? WHERE siparis_id = ?"); 

			$guncelle = $query->execute(array('1',$siparis_id));

			$siraid = guvenlik($_POST['siraid']);

			header("Location:fabrikalar.php#".($siraid - 2));

			exit();

		}

		if (isset($_POST['formlusiparissil'])) {
			
			$siparis_id = guvenlik($_POST['siparis_id']);

			$siparisformid = guvenlik($_POST['siparisformid']);

			$siparisler = guvenlik($_POST['siparisler']);

			$sipariskey = guvenlik($_POST['sipariskey']);

			$siparislerinipatlat = explode(",", $siparisler);

			unset($siparislerinipatlat[$sipariskey]);

			$siparisler = implode(",", $siparislerinipatlat);

			$query = $db->prepare("UPDATE siparisformlari SET siparisler = ? WHERE formid = ?"); 

			$guncelle = $query->execute(array($siparisler,$siparisformid));

			$sil = $db->prepare("UPDATE siparis SET silik = ? WHERE siparis_id = ?"); 

			$delete = $query->execute(array('1',$siparis_id));

			$siraid = guvenlik($_POST['siraid']);

			header("Location:fabrikalar.php#".($siraid - 2));

			exit();

		}

		if (isset($_POST['arandi'])) {

			$fabrikaid = guvenlik($_POST['fabrikaid']);
			
			$fabrikaalacak = guvenlik($_POST['tutar']);

			$fabrikaalacaktarih = guvenlik($_POST['vefo_tarih']);

			$fabrikaalacaktarih = strtotime($fabrikaalacaktarih);

			$query = $db->prepare("UPDATE fabrikalar SET fabrikaalacak = ?, fabrikaalacaktarih = ? WHERE fabrika_id = ?"); 

			$guncelle = $query->execute(array($fabrikaalacak, $fabrikaalacaktarih, $fabrikaid));

			$siraid = guvenlik($_POST['siraid']);

			header("Location:fabrikalar.php#".($siraid - 2));

			exit();

		}

		if (isset($_POST['kaydet'])) {

			$fabrikaid = guvenlik($_POST['fabrikaid']);
			
			$fabrikaalacak = guvenlik($_POST['tutar']);

			$fabrikaalacaktarih = 0;

			$query = $db->prepare("UPDATE fabrikalar SET fabrikaalacak = ?, fabrikaalacaktarih = ? WHERE fabrika_id = ?"); 

			$guncelle = $query->execute(array($fabrikaalacak, $fabrikaalacaktarih, $fabrikaid));

			$siraid = guvenlik($_POST['siraid']);

			header("Location:fabrikalar.php#".($siraid - 2));

			exit();

		}

		if (isset($_POST['tahsilattamamlandi'])) {

			$fabrikaid = guvenlik($_POST['fabrikaid']);
			
			$fabrikaalacak = 0;

			$fabrikaalacaktarih = 0;

			$query = $db->prepare("UPDATE fabrikalar SET fabrikaalacak = ?, fabrikaalacaktarih = ? WHERE fabrika_id = ?"); 

			$guncelle = $query->execute(array($fabrikaalacak, $fabrikaalacaktarih, $fabrikaid));

			$siraid = guvenlik($_POST['siraid']);

			header("Location:fabrikalar.php#".($siraid - 2));

			exit();

		}

		if(isset($_POST['siparisleregit'])){

			$fabrikaid = guvenlik($_POST['fabrikaid']);

			header("Location:fabrikasiparis.php?id=".$fabrikaid);

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

			<div class="row">
				
				<div class="col-md-12">
					
					<?php echo $hata; ?>

				</div>

			</div>

			<div class="row">
				
				<div class="col-md-3 col-12">
					
					<div class="div4" style="padding-top: 20px; text-align: center;">

						<a href="#" onclick="return false" onmousedown="javascript:ackapa('formdivi');"><h5><i class="fas fa-angle-double-down"></i>&nbsp;&nbsp;&nbsp;&nbsp;<b>Fabrika Ekleme Formu</b>&nbsp;&nbsp;&nbsp;&nbsp;<i class="fas fa-angle-double-down"></i></h5></a>

						<div id="formdivi" style="display: none;">
						
							<form action="" method="POST">
									
								<div><input type="text" name="fabrika_adi" class="form-control" style="margin-bottom: 10px;" placeholder="Fabrika Adı"></div>

								<div><input type="text" name="fabrikatel" class="form-control" style="margin-bottom: 10px;" placeholder="Fabrika Telefonu"></div>

								<div><input type="text" name="fabrikaeposta" class="form-control" style="margin-bottom: 10px;" placeholder="Fabrika E-posta Adresi"></div>

								<div><button type="submit" class="btn btn-primary btn-block" name="fabrika_ekleme_formu">Fabrika Ekle</button></div>

							</form>

						</div>

					</div>

				</div>

				<div class="col-md-9 col-12" style="text-align: right; padding-top: 15px;">
					
					<a href="fabrikalar.php"><button class="btn btn-sm btn-success">Tüm Liste</button></a>

					<a href="fabrikalar.php?odemeler"><button class="btn btn-sm btn-info">Tutarlılar</button></a>

					<a href="fabrikalar.php?arananlar"><button class="btn btn-sm btn-primary">Aranılanlar</button></a>
					
					<a href="fabrikalar.php?tahsilatigecenler"><button class="btn btn-sm btn-danger">Gecikenler</button></a>

				</div>

			</div>
					
			<div class="div4">

				<div class="d-none d-sm-block">

					<div class="row" style="margin-top: 10px;">
						
						<div class="col-md-3"><button class="btn btn-info"><h5><b style="color: white;">Fabrika Adı</b></h5></button></div>

						<div class="col-md-2">

							<button class="btn btn-info"><h5><b style="color: white;">Telefon</b></h5></button>

						</div>

						<div class="col-md-2"><button class="btn btn-info"><h5><b style="color: white;">Tutar</b></h5></button></div>

						<div class="col-md-2"><button class="btn btn-info"><h5><b style="color: white;">Ödeme Tarihi</b></h5></button></div>

					</div>

					<hr/>

				</div>
				
				<?php 

					$id = 0;

					$toplamfabrikaborc = 0;

					if(isset($_GET['arananlar'])){

						$query = $db->query("SELECT * FROM fabrikalar WHERE sirketid = '{$uye_sirket}' AND fabrikaalacak != 0 AND fabrikaalacaktarih != '0' AND fabrikaalacaktarih > '{$bugununsaniyesi}' ORDER BY fabrika_adi ASC", PDO::FETCH_ASSOC);

					}elseif (isset($_GET['odemeler'])) {

						$query = $db->query("SELECT * FROM fabrikalar WHERE sirketid = '{$uye_sirket}' AND fabrikaalacak != 0 ORDER BY fabrika_adi ASC", PDO::FETCH_ASSOC);
						
					}elseif (isset($_GET['tahsilatigecenler'])) {

						$query = $db->query("SELECT * FROM fabrikalar WHERE sirketid = '{$uye_sirket}' AND fabrikaalacak != 0 AND fabrikaalacaktarih != '0' AND fabrikaalacaktarih <= '{$bugununsaniyesi}' ORDER BY fabrika_adi ASC", PDO::FETCH_ASSOC);
						
					}else{

						$query = $db->query("SELECT * FROM fabrikalar WHERE sirketid = '{$uye_sirket}' ORDER BY fabrika_adi ASC", PDO::FETCH_ASSOC);

					}	

					if ( $query->rowCount() ){

						foreach( $query as $row ){

							$id++;

							$fabrika_id = guvenlik($row['fabrika_id']);

							$fabrika_adi = guvenlik($row['fabrika_adi']);

							$fabrikatel = guvenlik($row['fabrikatel']);

							$fabrikaeposta = guvenlik($row['fabrikaeposta']);

							$fabrikaiscilik = guvenlik($row['fabrikaiscilik']);

							$fabrikaadres = guvenlik($row['fabrikaadres']);

							$fabrikaalacak = guvenlik($row['fabrikaalacak']);

							$toplamfabrikaborc = $toplamfabrikaborc + $fabrikaalacak;

							$fabrikaalacaktarih = guvenlik($row['fabrikaalacaktarih']);

							$fabrikaalacaktarihv2 = date("d-m-Y",$fabrikaalacaktarih);

				?>

							<div class="row">

								<div class="col-md-3 col-12" style="margin-top: 7px;">
									
									<a href="#" onclick="return false" onmousedown="javascript:ackapa('siparislerdivi<?php echo $fabrika_id; ?>');">

										<b><?php echo $fabrika_adi;?></b>
											
									</a>

								</div>

								<div class="col-md-2" style="margin-top: 7px;">

									<b><?php echo $fabrikatel; ?></b>									

								</div>

								<div class="col-md-3" style="margin-top: 7px;">

									<?php

									if($fabrikaalacaktarih == 0){

										echo '<form action="" method="POST"><div class="row" style="margin:0px; padding:5px;">';							

									}elseif($fabrikaalacaktarih > $bugununsaniyesi){

										echo '<form action="" method="POST"><div class="row btn-primary" style="margin:0px; padding:5px;">';

									}else{

										echo '<form action="" method="POST"><div class="row btn-danger" style="margin:0px; padding:5px;">';

									}

									?>
											
											<div class="col-md-4">
												
												<?php if($fabrikaalacak == 0){?><input type="text" name="tutar" class="form-control" placeholder="Tutar giriniz." style="margin-bottom: 5px;"><?php }else{ ?><input type="text" name="tutar" class="form-control" value="<?php echo $fabrikaalacak; ?>" style="margin-bottom: 5px;"><?php } ?>

											</div>

											<div class="col-md-2">
												
												<button class="btn btn-dark btn-sm" type="submit" name="kaydet"><i class="fas fa-save"></i></button>

											</div>

											<div class="col-md-4">
												
												<?php if($fabrikaalacaktarih == 0){?><input type="text" id="tarih<?php echo $id; ?>" name="vefo_tarih" value="<?php echo "Tarih seçiniz."; ?>" class="form-control form-control-sm"><?php }else{ ?><input type="text" id="tarih<?php echo $id; ?>" name="vefo_tarih" value="<?php echo $fabrikaalacaktarihv2; ?>" class="form-control form-control-sm"><?php } ?>

											</div>

											<div class="col-md-2">

												<input type="hidden" id="tarih-db" name="vefat_tarih">

												<input type="hidden" name="fabrikaid" value="<?php echo $fabrika_id; ?>">

												<input type="hidden" name="siraid" value="<?php echo $id; ?>">
												
												<button class="btn btn-dark btn-sm" type="submit" name="arandi"><i class="fas fa-phone"></i></button>												

											</div>	

										</div>

									</form>	

								</div>	

								<div class="col-md-1 col-6" style="margin-top: 7px;">

									<button class="btn btn-success btn-sm btn-block" type="submit" name="tahsilattamamlandi">Temizle</button>																		
									
								</div>

								<div class="col-md-1 col-6" style="margin-top: 7px;">

									<a href="fabrikasiparis.php?id=<?php echo $fabrika_id; ?>"><button class="btn btn-info btn-sm btn-block">Siparişler</button></a>												
									
								</div>		

								<div class="col-md-1 col-6" style="margin-top: 7px;">

									<a href="#" onclick="return false" onmousedown="javascript:ackapa('duzenlemedivi<?php echo $fabrika_id; ?>');"><button class="btn btn-warning btn-sm btn-block">Düzenle</button></a>
									
								</div>

								<div class="col-md-1 col-6" style="margin-top: 7px;">

									<a href="#" onclick="return false" onmousedown="javascript:ackapa('silmedivi<?php echo $fabrika_id; ?>');"><button class="btn btn-secondary btn-sm btn-block">Sil</button></a>
									
								</div>

							</div>

							<div id="silmedivi<?php echo $fabrika_id; ?>" class="alert alert-danger" style="display: none; text-align: right; margin-top: 15px;">												

								<form action="" method="POST">

									<input type="hidden" name="fabrika_id" value="<?php echo $fabrika_id; ?>">

									<input type="hidden" name="siraid" value="<?php echo $id; ?>">

									Silmek istediğinize emin misiniz?&nbsp;&nbsp;&nbsp;

									<button class="btn btn-success btn-sm" name="fabrikasil" type="submit">Evet</button>&nbsp;&nbsp;&nbsp;

									<a href="#" onclick="return false" onmousedown="javascript:ackapa('silmedivi<?php echo $fabrika_id; ?>');"><button class="btn btn-danger btn-sm">Hayır</button></a>

								</form>

							</div>

							<div id="duzenlemedivi<?php echo $fabrika_id; ?>" style="display: none; position: fixed; top: 20%; left: 20%; z-index: 1; " class="div2">			

								<div class="row">
									
									<div class="col-md-8 col-8">
										
										<h5><b><?php echo $fabrika_adi; ?></b></h5>

									</div>

									<div class="col-md-4 col-4" style="text-align: right;">
										
										<a href="#" onclick="return false" onmousedown="javascript:ackapa('duzenlemedivi<?php echo $fabrika_id; ?>');"><span style="font-size: 24px;"><i class="fas fa-times"></i></span></a>

									</div>

								</div>			

								<div class="alert-primary" style="padding: 10px;">

									<h5><b>Bilgi Düzenleme Formu</b></h5>

									<form action="" method="POST">

										<div class="row">
											
											<div class="col-md-5 col-12" style="margin-top: 5px;">

												<b>Fabrika Adı</b>
												
												<input type="hidden" name="fabrika_id" value="<?php echo $fabrika_id; ?>">
											
												<input type="text" name="fabrika_adi" class="form-control" value="<?php echo $fabrika_adi; ?>">

											</div>

											<div class="col-md-3 col-12" style="margin-top: 5px;">

												<b>Telefon</b><br/>
												
												<input type="text" name="fabrikatel" class="form-control" value="<?php echo $fabrikatel; ?>">

											</div>

											<div class="col-md-4 col-12" style="margin-top: 5px;">

												<b>E-posta</b><br/>
												
												<input type="text" name="fabrikaeposta" class="form-control" value="<?php echo $fabrikaeposta; ?>">

											</div>

										</div>

										<div class="row">

											<div class="col-md-4 col-12" style="margin-top: 5px;">

												<b>İşçilik</b><br/>
												
												<input type="text" name="fabrikaiscilik" class="form-control" placeholder="Sadece sayı giriniz." value="<?php echo $fabrikaiscilik; ?>">

											</div>

										</div>

										<div class="row">
											
											<div class="col-md-12 col-12" style="margin-top: 5px;">

												<b>Adres</b><br/>

												<textarea name="fabrikaadres" class="form-control" rows="1"><?php echo $fabrikaadres; ?></textarea>

											</div>

										</div>

										<div class="row">
											
											<div class="col-md-12 col-12"  style="margin-top: 5px;">

												<input type="hidden" name="siraid" value="<?php echo $id; ?>">
												
												<button class="btn btn-primary" type="submit" name="fabrikabilgileriguncelle">Güncelle</button>

											</div>

										</div>

									</form>

								</div>

							</div>

							<div id="siparislerdivi<?php echo $fabrika_id; ?>" class="div2" style="display: none; margin: 0px -20px 0px -20px;">

								<div class="alert alert-primary">

									<div class="row">
										
										<div class="col-6" style="text-align: left;"><h5><b style="line-height: 40px;">Siparişler</b></h5></div>

										<div class="col-6" style="text-align: right;"><a href="pdf.php?id=<?php echo $fabrika_id; ?>" target="_blank"><button class="btn btn-primary btn-sm">Sipariş Formuna Git</button></a></div>

									</div>																

									<?php

										$sipariscek = $db->query("SELECT * FROM siparis WHERE urun_fabrika_id = '{$fabrika_id}' AND formda = '0' AND sirketid = '{$uye_sirket}' AND silik = '0'", PDO::FETCH_ASSOC);

										if ( $sipariscek->rowCount() ){

											foreach( $sipariscek as $row ){

												$siparis_id = $row['siparis_id'];

												$urun_id = $row['urun_id'];

												$urunbilgicek = $db->query("SELECT * FROM urun WHERE urun_id = '{$urun_id}' AND sirketid = '{$uye_sirket}'")->fetch(PDO::FETCH_ASSOC);

												$urun_adi = $urunbilgicek['urun_adi'];

												$katbilcek = $db->query("SELECT * FROM urun WHERE urun_id = '{$urun_id}' AND sirketid = '{$uye_sirket}'")->fetch(PDO::FETCH_ASSOC);

												$kategori_bir = $katbilcek['kategori_bir'];

												$katadcek = $db->query("SELECT * FROM kategori WHERE kategori_id = '{$kategori_bir}' AND sirketid = '{$uye_sirket}'")->fetch(PDO::FETCH_ASSOC);

												$kategori_bir_adi = $katadcek['kategori_adi'];

												$kategori_iki = $katbilcek['kategori_iki'];

												$katadcek = $db->query("SELECT * FROM kategori WHERE kategori_id = '{$kategori_iki}' AND sirketid = '{$uye_sirket}'")->fetch(PDO::FETCH_ASSOC);

												$kategori_iki_adi = $katadcek['kategori_adi'];

												$siparis_id = $row['siparis_id'];

												$hazirlayankisi = $row['hazirlayankisi'];

												$urun_siparis_aded = $row['urun_siparis_aded'];

												$urun_fabrika_id = $row['urun_fabrika_id'];

												$ilgilikisi = $row['ilgilikisi'];

												$urun_id = $row['urun_id'];

												$siparissaniye = $row['siparissaniye'];

												$siparistarih = date("d-m-Y", $siparissaniye);

												$fabrikaadcek = $db->query("SELECT * FROM fabrikalar WHERE fabrika_id = '{$urun_fabrika_id}' AND sirketid = '{$uye_sirket}'")->fetch(PDO::FETCH_ASSOC);

												$urun_fabrika_adi = $fabrikaadcek['fabrika_adi'];

									?>

												<div class="row">

													<div class="col-4 d-block d-sm-none" style="margin-top: 7px;"><b style="color: red;">Hazırlayan</b></div>
													
													<div class="col-md-2 col-8" style="margin-top: 7px;"><?php echo $hazirlayankisi; ?></div>

													<div class="col-4 d-block d-sm-none" style="margin-top: 7px;"><b style="color: red;">Fabrika</b></div>

													<div class="col-md-2 col-12" style="margin-top: 7px;"><?php echo $urun_fabrika_adi; ?></div>

													<div class="col-4 d-block d-sm-none" style="margin-top: 7px;"><b style="color: red;">İlgili</b></div>

													<div class="col-md-2 col-8" style="margin-top: 7px;"><?php echo $ilgilikisi; ?></div>

													<div class="col-4 d-block d-sm-none" style="margin-top: 7px;"><b style="color: red;">Ürün</b></div>

													<div class="col-md-3 col-8" style="margin-top: 7px;"><?php echo $urun_adi." ".$kategori_iki_adi." ".$kategori_bir_adi; ?></div>

													<div class="col-4 d-block d-sm-none" style="margin-top: 7px;"><b style="color: red;">Adet</b></div>

													<div class="col-md-1 col-8" style="margin-top: 7px;"><?php echo $urun_siparis_aded; ?></div>

													<div class="col-4 d-block d-sm-none" style="margin-top: 7px;"><b style="color: red;">Tarih</b></div>

													<div class="col-md-1 col-8" style="margin-top: 7px;"><?php echo $siparistarih; ?></div>

													<div class="col-md-1 col-12" style="margin-top: 7px; text-align: right;">
														
														<form action="" method="POST">

															<input type="hidden" name="siparis_id" value="<?php echo $siparis_id; ?>">

															<input type="hidden" name="siraid" value="<?php echo $id; ?>">
															
															<button type="submit" class="btn btn-danger btn-sm btn-block" name="siparissil" style="margin-bottom: 5px;">Sil</button>

														</form>

													</div>

												</div>

									<?php

											}

										}

									?>

								</div>

								<hr/>

								<div class="alert alert-info">

									<h5><b style="line-height: 40px;">Sipariş Formları</b></h5>

									<?php

									$formcek = $db->query("SELECT * FROM siparisformlari WHERE fabrikaid = '{$fabrika_id}' AND sirketid = '{$uye_sirket}' AND silik = '0' ORDER BY saniye DESC LIMIT 10", PDO::FETCH_ASSOC);

									if ( $formcek->rowCount() ){

										foreach( $formcek as $frm ){

											$formid = $frm['formid'];

											$siparisler = $frm['siparisler'];

											$fabrikaid = $frm['fabrikaid'];

											$formsaniye = $frm['saniye'];

											$formtarih = date("d-m-Y H:i:s",$formsaniye);

									?>

											<div class="row" style="margin-bottom: 3px;">
												
												<div class="col-10"><a onclick="return false" onmousedown="javascript:ackapa('formdivi<?php echo $formid; ?>');"><?php echo $formtarih." Tarihli Sipariş Formundaki Ürünler<br/>"; ?></a></div>

												<div class="col-1" style="text-align: right;"><a href="siparisform.php?id=<?php echo $formid; ?>" target="_blank"><button class="btn btn-warning btn-sm btn-block">Göster</button></a></div>

												<div class="col-1" style="text-align: right;"><form action="" method="POST"><input type="hidden" name="formid" value="<?php echo $formid; ?>"><input type="hidden" name="siparisler" value="<?php echo $siparisler; ?>"><input type="hidden" name="siraid" value="<?php echo $id; ?>"><button type="submit" name="siparisformunusil" class="btn btn-danger btn-sm btn-block">Sil</button></form></div>

											</div>

											<div id="formdivi<?php echo $formid; ?>" style="display: none; padding: 10px;">

												<hr/>

								<?php if(!empty($siparisler)){ ?>

												<div class="d-none d-sm-block">
												
													<div class="row">
														
														<div class="col-2"><b>Hazırlayan Kişi</b></div>

														<div class="col-2"><b>Talep Edilen Fabrika</b></div>

														<div class="col-2"><b>İlgili Kişi</b></div>

														<div class="col-3"><b>Ürün Adı</b></div>

														<div class="col-1"><b>Miktar</b></div>

														<div class="col-2"><b>Tarih</b></div>

													</div>

												</div>
											
												<?php

													$siparisleripatlat = explode(",", $siparisler);

													foreach ($siparisleripatlat as $key => $value) {
														
														$siparisbilgisi = $db->query("SELECT * FROM siparis WHERE siparis_id = '{$value}' AND sirketid = '{$uye_sirket}' AND silik = '0'")->fetch(PDO::FETCH_ASSOC);
													
														$siparis_id = $siparisbilgisi['siparis_id'] ?? null;

														$urun_id = $siparisbilgisi['urun_id'] ?? null;

														$urunbilgicek = $db->query("SELECT * FROM urun WHERE urun_id = '{$urun_id}' AND sirketid = '{$uye_sirket}'")->fetch(PDO::FETCH_ASSOC);

														$urun_adi = $urunbilgicek['urun_adi'] ?? null;

														$katbilcek = $db->query("SELECT * FROM urun WHERE urun_id = '{$urun_id}' AND sirketid = '{$uye_sirket}'")->fetch(PDO::FETCH_ASSOC);

														$kategori_bir = $katbilcek['kategori_bir'] ?? null;

														$katadcek = $db->query("SELECT * FROM kategori WHERE kategori_id = '{$kategori_bir}' AND sirketid = '{$uye_sirket}'")->fetch(PDO::FETCH_ASSOC);

														$kategori_bir_adi = $katadcek['kategori_adi'] ?? null;

														$kategori_iki = $katbilcek['kategori_iki'] ?? null;

														$katadcek = $db->query("SELECT * FROM kategori WHERE kategori_id = '{$kategori_iki}' AND sirketid = '{$uye_sirket}'")->fetch(PDO::FETCH_ASSOC);

														$kategori_iki_adi = $katadcek['kategori_adi'] ?? null;

														$siparis_id = $siparisbilgisi['siparis_id'] ?? null;

														$hazirlayankisi = $siparisbilgisi['hazirlayankisi'] ?? null;

														$urun_siparis_aded = $siparisbilgisi['urun_siparis_aded'] ?? null;

														$urun_fabrika_id = $siparisbilgisi['urun_fabrika_id'] ?? null;

														$ilgilikisi = $siparisbilgisi['ilgilikisi'] ?? null;

														$urun_id = $siparisbilgisi['urun_id'] ?? null;

														$siparissaniye = $siparisbilgisi['siparissaniye'] ?? null;

														$siparistarih = date("d-m-Y", $siparissaniye);

														$fabrikaadcek = $db->query("SELECT * FROM fabrikalar WHERE fabrika_id = '{$urun_fabrika_id}' AND sirketid = '{$uye_sirket}'")->fetch(PDO::FETCH_ASSOC);

														$urun_fabrika_adi = $fabrikaadcek['fabrika_adi'];

												?>

														<div class="row">

															<div class="col-4 d-block d-sm-none" style="margin-top: 7px;"><b style="color: red;">Hazırlayan</b></div>
															
															<div class="col-md-2 col-8" style="margin-top: 7px;"><?php echo $hazirlayankisi; ?></div>

															<div class="col-4 d-block d-sm-none" style="margin-top: 7px;"><b style="color: red;">Fabrika</b></div>

															<div class="col-md-2 col-8" style="margin-top: 7px;"><?php echo $urun_fabrika_adi; ?></div>

															<div class="col-4 d-block d-sm-none" style="margin-top: 7px;"><b style="color: red;">İlgili</b></div>

															<div class="col-md-2 col-8" style="margin-top: 7px;"><?php echo $ilgilikisi; ?></div>

															<div class="col-4 d-block d-sm-none" style="margin-top: 7px;"><b style="color: red;">Ürün</b></div>

															<div class="col-md-3 col-8" style="margin-top: 7px;"><?php echo $urun_adi." ".$kategori_iki_adi." ".$kategori_bir_adi; ?></div>

															<div class="col-4 d-block d-sm-none" style="margin-top: 7px;"><b style="color: red;">Adet</b></div>

															<div class="col-md-1 col-8" style="margin-top: 7px;"><?php echo $urun_siparis_aded; ?></div>

															<div class="col-4 d-block d-sm-none" style="margin-top: 7px;"><b style="color: red;">Tarih</b></div>

															<div class="col-md-1 col-8" style="margin-top: 7px;"><?php echo $siparistarih; ?></div>

															<div class="col-md-1 col-12" style="margin-top: 7px; text-align: right;">
													
																<form action="" method="POST">

																	<input type="hidden" name="siparisformid" value="<?php echo $formid; ?>">

																	<input type="hidden" name="siparisler" value="<?php echo $siparisler; ?>">

																	<input type="hidden" name="sipariskey" value="<?php echo $key; ?>">

																	<input type="hidden" name="siparis_id" value="<?php echo $siparis_id; ?>">

																	<input type="hidden" name="siraid" value="<?php echo $id; ?>">
																	
																	<button type="submit" class="btn btn-danger btn-sm btn-block" name="formlusiparissil" style="margin-bottom: 5px;">Sil</button>

																</form>

															</div>

														</div>

												<?php

													}

												?>												

											<?php }else{ echo "Bu sipariş formunda ürün bulunmamaktadır."; } ?>

												<hr/>

											</div>

									<?php

										}

									}

									?>

								</div>

							</div>

							<hr style="margin: 0px; border: 2px solid black;" />

				<?php

						}

					}

				?>

				<div class="row">

					<div class="col-md-12" style="padding-top: 20px; padding-bottom: 10px; text-align: center;"><b style="font-size: 20px;">Toplam Borç Tutarı : <?php echo $toplamfabrikaborc; ?> TL</b></div>

				</div>

			</div>

		</div>

		<br/><br/><br/><br/><br/><br/>

		<?php include 'template/script.php'; ?>

	</body>

</html>