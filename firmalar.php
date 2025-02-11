<?php 

	include 'fonksiyonlar/bagla.php';

	if ($girdi != 1) {
		
		header("Location:giris.php");

		exit();

	}elseif ($girdi == 1) {

		if($user->type == '0'){

			header("Location:index.php");

			exit();

		}

	if($user->type != '3'){

		if (isset($_POST['firmabilgileriguncelle'])) {

			$siraid = guvenlik($_POST['siraid']);
			
			$firmaid = guvenlik($_POST['firmaid']);

			$firmaadi = guvenlik($_POST['firmaadi']);

			$firmatel = guvenlik($_POST['firmatel']);

			$firmaeposta = guvenlik($_POST['firmaeposta']);

			$firmaadres = guvenlik($_POST['firmaadres']);

			$query = $db->prepare("UPDATE firmalar SET firmaadi = ?, firmatel = ?, firmaeposta = ?, firmaadres = ? WHERE firmaid = ?"); 

			$guncelle = $query->execute(array($firmaadi, $firmatel, $firmaeposta, $firmaadres, $firmaid));

			header("Location:firmalar.php#".($siraid - 2));

			exit();

		}

		if (isset($_POST['firmasil'])) {

			$siraid = guvenlik($_POST['siraid']);

			$firmaid = guvenlik($_POST['firmaid']);

			if (firmakullanimdami($firmaid) == '1') {
			
				$hata = '<br/><div class="alert alert-danger" role="alert">Bu firmanın kayıtlı olduğu bir ürün, teklif veya teklif formu var o yüzden silemiyoruz.</a></div>';

			}else{
			
				$sil = $db->prepare("UPDATE firmalar SET silik = ? WHERE firmaid = ?");

				$delete = $sil->execute(array('1',$firmaid));

				header("Location:firmalar.php#".($siraid - 2));

				exit();

			}

		}

		if (isset($_POST['teklifformunusil'])) {

			$siraid = guvenlik($_POST['siraid']);
			
			$tformid = guvenlik($_POST['tformid']);

			$tekliflistesi = guvenlik($_POST['tekliflistesi']);

			$silmekicinpatlat = explode(",", $tekliflistesi);

			foreach ($silmekicinpatlat as $key => $value) {

				$sil = $db->prepare("UPDATE teklif SET silik = ? WHERE teklifid = ?"); 

				$delete = $query->execute(array('1',$value));

			}

			$silici = $db->prepare("UPDATE teklifformlari SET silik = ? WHERE tformid = ?"); 

			$deleter = $query->execute(array('1',$tformid));

			header("Location:firmalar.php#".($siraid - 2));

			exit();

		}

		if (isset($_POST['firma_ekleme_formu'])) {
			
			$firmaadi = guvenlik($_POST['firmaadi']);

			$firmatel = guvenlik($_POST['firmatel']);

			$firmaeposta = guvenlik($_POST['firmaeposta']);

			$firmaadres = guvenlik($_POST['firmaadres']);

			$firmaalacak = 0;

			$firmaalacaktarih = 0;

			$query = $db->prepare("INSERT INTO firmalar SET firmaadi = ?, firmatel = ?, firmaeposta = ?, firmaadres = ?, firmaalacak = ?, firmaalacaktarih = ?, sirketid = ?, silik = ?");

			$insert = $query->execute(array($firmaadi, $firmatel, $firmaeposta, $firmaadres, $firmaalacak, $firmaalacaktarih, $user->company_id,'0'));

			header("Location:firmalar.php");

			exit();

		}

		if (isset($_POST['arandi'])) {

			$siraid = guvenlik($_POST['siraid']);

			$firmaid = guvenlik($_POST['firmaid']);
			
			$firmaalacak = guvenlik($_POST['tutar']);

			$firmaalacaktarih = guvenlik($_POST['vefo_tarih']);

			$firmaalacaktarih = strtotime($firmaalacaktarih);

			$query = $db->prepare("UPDATE firmalar SET firmaalacak = ?, firmaalacaktarih = ? WHERE firmaid = ?"); 

			$guncelle = $query->execute(array($firmaalacak, $firmaalacaktarih, $firmaid));

			header("Location:firmalar.php#".($siraid - 2));

			exit();

		}

		if (isset($_POST['kaydet'])) {

			$siraid = guvenlik($_POST['siraid']);

			$firmaid = guvenlik($_POST['firmaid']);
			
			$firmaalacak = guvenlik($_POST['tutar']);

			$firmaalacaktarih = 0;

			$query = $db->prepare("UPDATE firmalar SET firmaalacak = ?, firmaalacaktarih = ? WHERE firmaid = ?"); 

			$guncelle = $query->execute(array($firmaalacak, $firmaalacaktarih, $firmaid));

			header("Location:firmalar.php#".($siraid - 2));

			exit();

		}

		if (isset($_POST['tahsilattamamlandi'])) {

			$siraid = guvenlik($_POST['siraid']);

			$firmaid = guvenlik($_POST['firmaid']);
			
			$firmaalacak = 0;

			$firmaalacaktarih = 0;

			$query = $db->prepare("UPDATE firmalar SET firmaalacak = ?, firmaalacaktarih = ? WHERE firmaid = ?"); 

			$guncelle = $query->execute(array($firmaalacak, $firmaalacaktarih, $firmaid));

			header("Location:firmalar.php#".($siraid - 2));

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
					
					<?= $hata; ?>

				</div>

			</div>
				
			<div class="row">
				
				<div class="col-md-3 col-12">

					<div class="div4" style="padding-top: 20px; text-align: center;">

						<a href="#" onclick="return false" onmousedown="javascript:ackapa('formdivi');"><h5><i class="fas fa-angle-double-down"></i>&nbsp;&nbsp;&nbsp;&nbsp;<b>Firma Ekleme Formu</b>&nbsp;&nbsp;&nbsp;&nbsp;<i class="fas fa-angle-double-down"></i></h5></a>

						<div id="formdivi" style="display: none;">
						
							<form action="" method="POST">
									
								<div><input type="text" name="firmaadi" class="form-control" style="margin-bottom: 10px;" placeholder="Firma adını giriniz"></div>

								<div><input type="text" name="firmatel" class="form-control" style="margin-bottom: 10px;" placeholder="Telefon numarasını giriniz"></div>

								<div><input type="text" name="firmaeposta" class="form-control" style="margin-bottom: 10px;" placeholder="E-posta adresini yazınız."></div>

								<div><textarea name="firmaadres" class="form-control" rows="3" placeholder="Firma adresini yazınız." style="margin-bottom: 10px;"></textarea></div>

								<div><button type="submit" class="btn btn-primary btn-block" name="firma_ekleme_formu">Firma Ekle</button></div>

							</form>

						</div> 	

					</div>

				</div>

				<div class="col-md-9 col-12" style="text-align: right; padding-top: 15px;">
					
					<a href="firmalar.php"><button class="btn btn-sm btn-success">Tüm Liste</button></a>

					<a href="firmalar.php?odemeler"><button class="btn btn-sm btn-info">Tutarlılar</button></a>

					<a href="firmalar.php?arananlar"><button class="btn btn-sm btn-primary">Aranılanlar</button></a>
					
					<a href="firmalar.php?tahsilatigecenler"><button class="btn btn-sm btn-danger">Gecikenler</button></a>

				</div>

			</div>
					
			<div class="div4">

				<div class="d-none d-sm-block">

					<div class="row" style="margin-top: 10px;">
						
						<div class="col-md-3"><button class="btn btn-primary"><h5><b style="color: white;">Firma Adı</b></h5></button></div>

						<div class="col-md-2">

							<button class="btn btn-primary"><h5><b style="color: white;">Telefon</b></h5></button>

						</div>

						<div class="col-md-2"><button class="btn btn-primary"><h5><b style="color: white;">Tutar</b></h5></button></div>

						<div class="col-md-2"><button class="btn btn-primary"><h5><b style="color: white;">Ödeme Tarihi</b></h5></button></div>

					</div>

					<hr/>

				</div>
				
				<?php 

					$id = 0;

					$toplamfirmaalacak = 0;

					if(!isset($_GET['s'])){
						$i = 0;
					}else{
						$s = guvenlik($_GET['s']) * 20;
						$i = $s -20;
					}

					if(isset($_GET['arananlar'])){

						$query = $db->query("SELECT * FROM firmalar WHERE sirketid = '{$user->company_id}' AND firmaalacak != 0 AND firmaalacaktarih != '0' AND firmaalacaktarih > '{$bugununsaniyesi}' ORDER BY firmaadi ASC", PDO::FETCH_ASSOC);

					}elseif (isset($_GET['odemeler'])) {

						$query = $db->query("SELECT * FROM firmalar WHERE sirketid = '{$user->company_id}' AND firmaalacak != 0 ORDER BY firmaadi ASC", PDO::FETCH_ASSOC);
						
					}elseif (isset($_GET['tahsilatigecenler'])) {

						$query = $db->query("SELECT * FROM firmalar WHERE sirketid = '{$user->company_id}' AND firmaalacak != 0 AND firmaalacaktarih != '0' AND firmaalacaktarih <= '{$bugununsaniyesi}' ORDER BY firmaadi ASC", PDO::FETCH_ASSOC);
						
					}else{

						$query = $db->query("SELECT * FROM firmalar WHERE sirketid = '{$user->company_id}' AND silik = '0' ORDER BY firmaadi ASC LIMIT $i,20", PDO::FETCH_ASSOC);

					}					

					if ( $query->rowCount() ){

						foreach( $query as $row ){

							$id++;

							$firmaid = guvenlik($row['firmaid']);

							$firmaadi = guvenlik($row['firmaadi']);

							$firmatel = guvenlik($row['firmatel']);

							$firmaeposta = guvenlik($row['firmaeposta']);

							$firmaadres = guvenlik($row['firmaadres']);

							$firmaalacak = guvenlik($row['firmaalacak']);

							$toplamfirmaalacak = $toplamfirmaalacak + $firmaalacak;

							$firmaalacaktarih = guvenlik($row['firmaalacaktarih']);

							$firmaalacaktarihv2 = date("d-m-Y",$firmaalacaktarih);

							if($firmaalacaktarih == 0){

									echo '<form action="" method="POST"><div class="row" style="margin:0px; padding:5px;">';							

							}elseif($firmaalacaktarih > $bugununsaniyesi){

								echo '<form action="" method="POST"><div class="row alert btn-primary" style="margin:0px; padding:5px;">';

							}else{

								echo '<form action="" method="POST"><div class="row btn-danger" style="margin:0px; padding:5px;">';

							}

				?>
								
									<div class="col-md-3 col-12" style="margin-top: 7px;">
										
										<a href="#" id="<?= $id; ?>" onclick="return false" onmousedown="javascript:ackapa('tekliflerdivi<?= $firmaid; ?>');">

											<b><?= $firmaadi;?></b>
												
										</a>

									</div>

									<div class="col-md-2" style="margin-top: 7px;">

										<b><?= $firmatel; ?></b>									

									</div>

									<div class="col-md-2" style="margin-top: 7px;">
										
										<div class="row">
											
											<div class="col-md-9">
												
												<?php if($firmaalacak == 0){?><input type="text" name="tutar" class="form-control" placeholder="Tutar giriniz." style="margin-bottom: 5px;"><?php }else{ ?><input type="text" name="tutar" class="form-control" value="<?= $firmaalacak; ?>" style="margin-bottom: 5px;"><?php } ?>

											</div>

											<div class="col-md-3">
												
												<button class="btn btn-dark btn-sm" type="submit" name="kaydet"><i class="fas fa-save"></i></button>

											</div>

										</div>

									</div>	

									<div class="col-md-2" style="margin-top: 7px;">
										
										<div class="row">

											<div class="col-md-9">
												
												<?php if($firmaalacaktarih == 0){?>
													
													<input type="text" id="tarih<?= $id; ?>" name="vefo_tarih" value="<?= "Tarih seçiniz."; ?>" class="form-control form-control-sm">
													
												<?php }else{ ?>
													
													<input type="text" id="tarih<?= $id; ?>" name="vefo_tarih" value="<?= $firmaalacaktarihv2; ?>" class="form-control form-control-sm">
													
												<?php } ?>

											</div>

											<div class="col-md-3">

												<input type="hidden" id="tarih-db" name="vefat_tarih">

												<input type="hidden" name="siraid" value="<?= $id; ?>">

												<input type="hidden" name="firmaid" value="<?= $firmaid; ?>">
												
												<button class="btn btn-dark btn-sm" type="submit" name="arandi"><i class="fas fa-phone"></i></button>												

											</div>	

										</div>

									</div>	

									<div class="col-md-1 col-6" style="margin-top: 7px;">

										<button class="btn btn-success btn-sm btn-block" type="submit" name="tahsilattamamlandi">Temizle</button>																		
										
									</div>		

									<div class="col-md-1 col-6" style="margin-top: 7px;">

										<a href="#" onclick="return false" onmousedown="javascript:ackapa('duzenlemedivi<?= $firmaid; ?>');"><button class="btn btn-warning btn-sm btn-block">Düzenle</button></a>
										
									</div>

									<div class="col-md-1 col-6" style="margin-top: 7px;">

										<a href="#" onclick="return false" onmousedown="javascript:ackapa('silmedivi<?= $firmaid; ?>');"><button class="btn btn-secondary btn-sm btn-block">Sil</button></a>
										
									</div>

								</div>

							</form>

							<div id="silmedivi<?= $firmaid; ?>" class="alert alert-danger" style="display: none; text-align: right; margin-top: 15px;">
										
								<form action="" method="POST">

									<input type="hidden" name="firmaid" value="<?= $firmaid; ?>">

									<input type="hidden" name="siraid" value="<?= $id; ?>">

									Silmek istediğinize emin misiniz?&nbsp;&nbsp;&nbsp;

									<button class="btn btn-success btn-sm" name="firmasil" type="submit">Evet</button>&nbsp;&nbsp;&nbsp;

									<a href="#" onclick="return false" onmousedown="javascript:ackapa('silmedivi<?= $firmaid; ?>');"><button class="btn btn-danger btn-sm">Hayır</button></a>

								</form>

							</div>

							<div id="duzenlemedivi<?= $firmaid; ?>" style="display: none; position: fixed; top: 20%; left: 20%; z-index: 1; " class="div2">			

								<div class="row">
									
									<div class="col-md-8 col-8">
										
										<h5><b><?= $firmaadi; ?></b></h5>

									</div>

									<div class="col-md-4 col-4" style="text-align: right;">
										
										<a href="#" onclick="return false" onmousedown="javascript:ackapa('duzenlemedivi<?= $firmaid; ?>');"><span style="font-size: 24px;"><i class="fas fa-times"></i></span></a>

									</div>

								</div>			

								<div class="alert-primary" style="padding: 10px;">

									<h5><b>Bilgi Düzenleme Formu</b></h5>

									<form action="" method="POST">

										<div class="row">
											
											<div class="col-md-5 col-12" style="margin-top: 5px;">

												<b>Firma Adı</b>
												
												<input type="hidden" name="firmaid" value="<?= $firmaid; ?>">
											
												<input type="text" name="firmaadi" class="form-control" value="<?= $firmaadi; ?>">

											</div>

											<div class="col-md-3 col-12" style="margin-top: 5px;">

												<b>Telefon</b><br/>
												
												<input type="text" name="firmatel" class="form-control" value="<?= $firmatel; ?>">

											</div>

											<div class="col-md-4 col-12" style="margin-top: 5px;">

												<b>E-posta</b><br/>
												
												<input type="text" name="firmaeposta" class="form-control" value="<?= $firmaeposta; ?>">

											</div>

										</div>

										<div class="row">
											
											<div class="col-md-12 col-12" style="margin-top: 5px;">

												<b>Adres</b><br/>

												<textarea name="firmaadres" class="form-control" rows="1"><?= $firmaadres; ?></textarea>

											</div>

										</div>

										<div class="row">
											
											<div class="col-md-12 col-12"  style="margin-top: 5px;">

												<input type="hidden" name="siraid" value="<?= $id; ?>">
												
												<button class="btn btn-primary" type="submit" name="firmabilgileriguncelle">Güncelle</button>

											</div>

										</div>

									</form>

								</div>

							</div>

							<div id="tekliflerdivi<?= $firmaid; ?>" class="div2" style="display: none; margin: 0px -20px 0px -20px;">

								<div class="alert alert-primary">
									
									<div class="row">
									
										<div class="col-6"><h5><b style="line-height: 40px;">Teklifler</b></h5></div>

										<div class="col-6"><a href="teklif.php?id=<?= $firmaid; ?>" target="_blank"><button class="btn btn-primary btn-sm">Teklif Formuna Git</button></a></div>

									</div>	

									<div class="d-none d-sm-block">

										<div class="row">
																
											<div class="col-3"><b>Firma Adı</b></div>

											<div class="col-3"><b>Ürün Adı</b></div>

											<div class="col-1"><b>Adet</b></div>

											<div class="col-1"><b>Satış Fiyatı</b></div>

											<div class="col-2"><b>Toplam</b></div>

											<div class="col-1"><b>Tarih</b></div>

										</div>

									</div>															

									<?php

										$tekliflersiralamasi = 0;

										$tklfcek = $db->query("SELECT * FROM teklif WHERE tverilenfirma = '{$firmaid}' AND formda = '0' AND sirketid = '{$user->company_id}' AND silik = '0' ORDER BY teklifid DESC", PDO::FETCH_ASSOC);

										if ( $tklfcek->rowCount() ){

											foreach( $tklfcek as $tklfrow ){

												$teklifid = $tklfrow['teklifid'];

												$tekliflersiralamasi++;

												$turunid = $tklfrow['turunid'];

												$urunbilgicek = $db->query("SELECT * FROM urun WHERE urun_id = '{$turunid}' AND sirketid = '{$user->company_id}'")->fetch(PDO::FETCH_ASSOC);

												$urun_adi = $urunbilgicek['urun_adi'];

												$urun_birimkg = $urunbilgicek['urun_birimkg'];

												$katbilcek = $db->query("SELECT * FROM urun WHERE urun_id = '{$turunid}' AND sirketid = '{$user->company_id}'")->fetch(PDO::FETCH_ASSOC);

												$kategori_bir = $katbilcek['kategori_bir'];

												$katadcek = $db->query("SELECT * FROM kategori WHERE kategori_id = '{$kategori_bir}' AND sirketid = '{$user->company_id}'")->fetch(PDO::FETCH_ASSOC);

												$kategori_bir_adi = $katadcek['kategori_adi'];

												$kategori_iki = $katbilcek['kategori_iki'];

												$katadcek = $db->query("SELECT * FROM kategori WHERE kategori_id = '{$kategori_iki}' AND sirketid = '{$user->company_id}'")->fetch(PDO::FETCH_ASSOC);

												$kategori_iki_adi = $katadcek['kategori_adi'];

												$tverilenfirmaid = $tklfrow['tverilenfirma'];

												$firmabilgi = $db->query("SELECT * FROM firmalar WHERE firmaid = '{$tverilenfirmaid}' AND sirketid = '{$user->company_id}'")->fetch(PDO::FETCH_ASSOC);

												$tverilenfirmaadi = $firmabilgi['firmaadi'];

												$tadet = $tklfrow['tadet'];

												$tsatisfiyati = $tklfrow['tsatisfiyati'];

												$tsaniye = $tklfrow['tsaniye'];

												$ttarih = date("d-m-Y",$tsaniye);

												$toplam_fiyat = $tadet * $urun_birimkg * $tsatisfiyati;

									?>

												<div class="row">

													<div class="col-4 d-block d-sm-none" style="margin-top: 7px;"><b style="color: red;">Firma</b></div>
													
													<div class="col-md-3 col-8" style="margin-top: 7px;"><?= $tekliflersiralamasi.". ".$tverilenfirmaadi; ?></div>

													<div class="col-4 d-block d-sm-none" style="margin-top: 7px;"><b style="color: red;">Ürün</b></div>

													<div class="col-md-3 col-8" style="margin-top: 7px;"><?= $urun_adi." ".$kategori_iki_adi." ".$kategori_bir_adi; ?></div>

													<div class="col-4 d-block d-sm-none" style="margin-top: 7px;"><b style="color: red;">Adet</b></div>

													<div class="col-md-1 col-8" style="margin-top: 7px;"><?= $tadet; ?></div>

													<div class="col-4 d-block d-sm-none" style="margin-top: 7px;"><b style="color: red;">Satış</b></div>

													<div class="col-md-1 col-8" style="margin-top: 7px;"><?= $tsatisfiyati." TL"; ?></div>

													<div class="col-4 d-block d-sm-none" style="margin-top: 7px;"><b style="color: red;">Toplam</b></div>

													<div class="col-md-2 col-8" style="margin-top: 7px;"><?= $toplam_fiyat." TL"; ?></div>

													<div class="col-4 d-block d-sm-none" style="margin-top: 7px;"><b style="color: red;">Tarih</b></div>

													<div class="col-md-1 col-8" style="margin-top: 7px;"><?= $ttarih; ?></div>

													<div class="col-md-1 col-12" style="margin-top: 7px; text-align: right;">
														
														<form action="" method="POST">

															<input type="hidden" name="teklifid" value="<?= $teklifid; ?>">
															
															<button type="submit" class="btn btn-danger btn-sm btn-block" name="teklifsil" style="margin-bottom: 5px;">Sil</button>

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
									
									<h5><b style="line-height: 40px;">Teklif Formları</b></h5>

							<?php

								$teklifformlarinicek = $db->query("SELECT * FROM teklifformlari WHERE firmaid = '{$firmaid}' AND sirketid = '{$user->company_id}' AND silik = '0' ORDER BY tformid DESC LIMIT 1", PDO::FETCH_ASSOC);

									if ( $teklifformlarinicek->rowCount() ){

									foreach( $teklifformlarinicek as $tfb ){

										$tformid = $tfb['tformid'];

										$tekliflistesi = $tfb['tekliflistesi'];

										$teklifsaniye = $tfb['saniye'];

										$tekliftarihi = date("d-m-Y H:i:s", $teklifsaniye);

							?>

											<div class="row" style="margin-bottom: 3px;">
												
												<div class="col-10"><a onclick="return false" onmousedown="javascript:ackapa('teklifformdivi<?= $tformid; ?>');"><?= $tekliftarihi." Tarihli Teklif Formundaki Ürünler<br/>"; ?></a></div>

												<div class="col-1" style="text-align: right;"><a href="teklifformu.php?id=<?= $tformid; ?>" target="_blank"><button class="btn btn-warning btn-sm btn-block">Göster</button></a></div>

												<div class="col-1" style="text-align: right;"><form action="" method="POST"><input type="hidden" name="siraid" value="<?= $id; ?>"><input type="hidden" name="tformid" value="<?= $tformid; ?>"><input type="hidden" name="tekliflistesi" value="<?= $tekliflistesi; ?>"><button class="btn btn-danger btn-sm btn-block" type="submit" name="teklifformunusil">Sil</button></form></div>
											
											</div>																		

											<div id="teklifformdivi<?= $tformid; ?>" style="display: none; padding: 10px;">

												<hr/>

									<?php 	if (!empty($tekliflistesi)) { ?>

												<div class="row">
																
													<div class="col-3"><b>Firma Adı</b></div>

													<div class="col-3"><b>Ürün Adı</b></div>

													<div class="col-1"><b>Adet</b></div>

													<div class="col-1"><b>Satış Fiyatı</b></div>

													<div class="col-2"><b>Toplam</b></div>

													<div class="col-1"><b>Tarih</b></div>

												</div>

										<?php																		

												$tekliflersiralamasi = 0;

												$teklifleripatlat = explode(",", $tekliflistesi);

												foreach ($teklifleripatlat as $key => $value) {
													
													$tklfrow = $db->query("SELECT * FROM teklif WHERE teklifid = '{$value}' AND sirketid = '{$user->company_id}' AND silik = '0'")->fetch(PDO::FETCH_ASSOC);

													$tekliflersiralamasi++;

													$teklifid = $tklfrow['teklifid'];

													$turunid = $tklfrow['turunid'];

													$urunbilgicek = $db->query("SELECT * FROM urun WHERE urun_id = '{$turunid}' AND silik = '0' AND sirketid = '{$user->company_id}'")->fetch(PDO::FETCH_ASSOC);

													if($urunbilgicek) {
													
														$urun_adi = $urunbilgicek['urun_adi'];

														$urun_birimkg = $urunbilgicek['urun_birimkg'];

														$katbilcek = $db->query("SELECT * FROM urun WHERE urun_id = '{$turunid}' AND sirketid = '{$user->company_id}'")->fetch(PDO::FETCH_ASSOC);

														$kategori_bir = $katbilcek['kategori_bir'];

														$katadcek = $db->query("SELECT * FROM kategori WHERE kategori_id = '{$kategori_bir}' AND sirketid = '{$user->company_id}'")->fetch(PDO::FETCH_ASSOC);

														$kategori_bir_adi = $katadcek['kategori_adi'];

														$kategori_iki = $katbilcek['kategori_iki'];

														$katadcek = $db->query("SELECT * FROM kategori WHERE kategori_id = '{$kategori_iki}' AND sirketid = '{$user->company_id}'")->fetch(PDO::FETCH_ASSOC);

														$kategori_iki_adi = $katadcek['kategori_adi'];

														$tverilenfirmaid = $tklfrow['tverilenfirma'];

														$firmabilgi = $db->query("SELECT * FROM firmalar WHERE firmaid = '{$tverilenfirmaid}' AND sirketid = '{$user->company_id}'")->fetch(PDO::FETCH_ASSOC);

														$tverilenfirmaadi = $firmabilgi['firmaadi'];

														$tadet = $tklfrow['tadet'];

														$tsatisfiyati = $tklfrow['tsatisfiyati'];

														$tsaniye = $tklfrow['tsaniye'];

														$ttarih = date("d-m-Y",$tsaniye);

														$toplam_fiyat = $tadet * $urun_birimkg * $tsatisfiyati;

										?>

														<div class="row">
															
															<div class="col-3"><?= $tekliflersiralamasi.". ".$tverilenfirmaadi; ?></div>

															<div class="col-3"><?= $urun_adi." ".$kategori_iki_adi." ".$kategori_bir_adi; ?></div>

															<div class="col-1"><?= $tadet; ?></div>

															<div class="col-1"><?= $tsatisfiyati." TL"; ?></div>

															<div class="col-2"><?= $toplam_fiyat." TL"; ?></div>

															<div class="col-1"><?= $ttarih; ?></div>

															<div class="col-1" style="text-align: right;">
																	
																<form action="" method="POST">

																	<input type="hidden" name="teklifformid" value="<?= $tformid; ?>">

																	<input type="hidden" name="tekliflistesi" value="<?= $tekliflistesi; ?>">

																	<input type="hidden" name="teklifkey" value="<?= $key; ?>">

																	<input type="hidden" name="teklifid" value="<?= $teklifid; ?>">
																	
																	<button type="submit" class="btn btn-danger btn-sm" name="formluteklifsil" style="margin-bottom: 5px;">Sil</button>

																</form>

															</div>

														</div>

										<?php
													}

												} ?>

										<?php	}else{ echo "Bu teklif formunda ürün bulunmamaktadır."; }

										?>

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

					<div class="col-md-12" style="padding-top: 20px; padding-bottom: 10px; text-align: center;"><b style="font-size: 20px;">Toplam Tahsilat Tutarı : <?= $toplamfirmaalacak; ?> TL</b></div>

				</div>

				<hr/>
					
				<nav aria-label="Page navigation example">
  					<ul class="pagination justify-content-center">
					<?php
                    $totalCount = $db->query("SELECT COUNT(*) FROM firmalar WHERE sirketid = '{$user->company_id}' AND silik = '0'")->fetchColumn();
                    $sayfaSayisi = ceil($totalCount / 20);
                    for ($i=1; $i <= $sayfaSayisi; $i++) {
						echo '<li class="page-item"><a class="page-link" href="firmalar.php?s='.$i.'">'.$i.'</a></li>'; 
					} ?>
				  </ul>
			 	</nav>		

			</div>

		</div>

		<br/><br/><br/><br/><br/><br/>

		<?php include 'template/script.php'; ?>

	</body>

</html>