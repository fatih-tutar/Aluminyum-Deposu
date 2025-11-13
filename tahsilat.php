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

	if($user->type != '3'){

		if (isset($_POST['firmabilgileriguncelle'])) {
			
			$firmaid = guvenlik($_POST['firmaid']);

			$firmaadi = guvenlik($_POST['firmaadi']);

			$firmatel = guvenlik($_POST['firmatel']);

			$firmaeposta = guvenlik($_POST['firmaeposta']);

			$firmaadres = guvenlik($_POST['firmaadres']);

			$query = $db->prepare("UPDATE clients SET name = ?, phone = ?, email = ?, address = ? WHERE id = ?");

			$guncelle = $query->execute(array($firmaadi, $firmatel, $firmaeposta, $firmaadres, $firmaid));

			header("Location:client.php");

			exit();

		}

		if (isset($_POST['firmasil'])) {

			$firmaid = guvenlik($_POST['firmaid']);

			if (isCompanyInUse($firmaid) == '1') {
			
				$error = '<br/><div class="alert alert-danger" role="alert">Bu firmanın kayıtlı olduğu bir ürün, teklif veya teklif formu var o yüzden silemiyoruz.</a></div>';

			}else{
			
				$sil = $db->prepare("UPDATE clients SET is_deleted = ? WHERE id = ?");

				$delete = $sil->execute(array('1',$firmaid));

				header("Location:client.php");

				exit();

			}

		}

		if (isset($_POST['teklifformunusil'])) {
			
			$tformid = guvenlik($_POST['tformid']);

			$tekliflistesi = guvenlik($_POST['tekliflistesi']);

			$silmekicinpatlat = explode(",", $tekliflistesi);

			foreach ($silmekicinpatlat as $key => $value) {

				$sil = $db->prepare("UPDATE teklif SET silik = ? WHERE teklifid = ?"); 

				$delete = $query->execute(array('1',$value));

			}

			$silici = $db->prepare("UPDATE teklifformlari SET silik = ? WHERE tformid = ?"); 

			$deleter = $query->execute(array('1',$tformid));

			header("Location:client.php");

			exit();

		}

		if (isset($_POST['firma_ekleme_formu'])) {
			
			$firmaadi = guvenlik($_POST['firmaadi']);

			$firmatel = guvenlik($_POST['firmatel']);

			$firmaeposta = guvenlik($_POST['firmaeposta']);

			$firmaadres = guvenlik($_POST['firmaadres']);

			$query = $db->prepare("INSERT INTO clients SET name = ?, phone = ?, email = ?, address = ?, company_id = ?, is_deleted = ?");

			$insert = $query->execute(array($firmaadi, $firmatel, $firmaeposta, $firmaadres, $user->company_id,'0'));

			header("Location:client.php");

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
					
					<?= $error; ?>

				</div>

			</div>
				
			<div class="row">
				
				<div class="col-md-3 col-12">

					<div class="div4" style="padding-top: 20px; text-align: center;">

						<a href="#" onclick="return false" onmousedown="javascript:ackapa('formdivi');"><h5><i class="fas fa-angle-double-down"></i>&nbsp;&nbsp;&nbsp;&nbsp;<b>Tahsilat Kayıt Formu</b>&nbsp;&nbsp;&nbsp;&nbsp;<i class="fas fa-angle-double-down"></i></h5></a>

						<div id="formdivi" style="display: none;">
						
							<form action="" method="POST">
									
								<div>
									
									<select class="form-control" style="margin-bottom: 10px;" id="exampleFormControlSelect1" name="firmaid">

	                                    <option selected value="0">Firma Seçiniz</option>

	                                    <?php

	                                    $fabrika = $db->query("SELECT * FROM clients WHERE company_id = '{$user->company_id}' ORDER BY name ASC", PDO::FETCH_ASSOC);

	                                    if ( $fabrika->rowCount() ){

	                                        foreach( $fabrika as $fbrk ){

	                                            $firmaid = $fbrk[ 'id']; 

	                                            $firmaadi = $fbrk['name']; 

	                                            echo "<option value='".$firmaid."'>".$firmaadi."</option>";

	                                        }

	                                    }

	                                    ?>
	                               
	                                </select>       

								</div>

								<div><input type="text" name="firmatel" class="form-control" style="margin-bottom: 10px;" placeholder="Telefon numarasını giriniz"></div>

								<div><input type="text" name="firmaeposta" class="form-control" style="margin-bottom: 10px;" placeholder="E-posta adresini yazınız."></div>

								<div><textarea name="firmaadres" class="form-control" rows="3" placeholder="Firma adresini yazınız." style="margin-bottom: 10px;"></textarea></div>

								<div><button type="submit" class="btn btn-primary btn-block" name="firma_ekleme_formu">Firma Ekle</button></div>

							</form>

						</div> 	

					</div>

				</div>

			</div>
					
			<div class="div4">

				<div class="d-none d-sm-block">

					<div class="row" style="margin-top: 10px;">
						
						<div class="col-md-3"><h5><b>Firma Adı</b></h5></div>

						<div class="col-md-1"><h5><b>Tutar</b></h5></div>

						<div class="col-md-2"><h5><b>Telefon</b></h5></div>

						<div class="col-md-2"><h5><b>E-posta</b></h5></div>

						<div class="col-md-3"><h5><b>Adres</b></h5></div>

					</div>

					<hr/>

				</div>
				
				<?php 

					$query = $db->query("SELECT * FROM clients WHERE company_id = '{$user->company_id}' ORDER BY name ASC", PDO::FETCH_ASSOC);

					if ( $query->rowCount() ){

						foreach( $query as $row ){

							$firmaid = $row[ 'id']; 

							$firmaadi = $row['name']; 

							$firmatel = $row[ 'phone']; 

							$firmaeposta = $row[ 'email']; 

							$firmaadres = $row[ 'address']; 

				?>

							<div class="row">
								
								<div class="col-md-3 col-12" style="margin-top: 7px;">
									
									<a href="#" onclick="return false" onmousedown="javascript:ackapa('tekliflerdivi<?= $firmaid; ?>');">

										<?= $firmaadi;?>
											
									</a>

								</div>

								<div class="col-md-2 col-12" style="margin-top: 7px;"><?= $firmatel; ?></div>

								<div class="col-md-2 col-12" style="margin-top: 7px;"><?= $firmaeposta; ?></div>

								<div class="col-md-3 col-12" style="margin-top: 7px;"><?= $firmaadres; ?></div>

								<div class="col-md-1 col-6" style="margin-top: 7px;">

									<a href="#" onclick="return false" onmousedown="javascript:ackapa('duzenlemedivi<?= $firmaid; ?>');"><button class="btn btn-primary btn-sm btn-block">Düzenle</button></a>
									
								</div>

								<div class="col-md-1 col-6" style="margin-top: 7px;">

									<a href="#" onclick="return false" onmousedown="javascript:ackapa('silmedivi<?= $firmaid; ?>');"><button class="btn btn-danger btn-sm btn-block">Sil</button></a>
									
								</div>

							</div>

							<div id="silmedivi<?= $firmaid; ?>" style="display: none; text-align: right; margin-top: 15px;">
										
								<form action="" method="POST">

									<input type="hidden" name="firmaid" value="<?= $firmaid; ?>">

									Silmek istediğinize emin misiniz?&nbsp;&nbsp;&nbsp;

									<button class="btn btn-success btn-sm" name="firmasil" type="submit">Evet</button>&nbsp;&nbsp;&nbsp;

									<a href="#" onclick="return false" onmousedown="javascript:ackapa('silmedivi<?= $firmaid; ?>');"><button class="btn btn-danger btn-sm">Hayır</button></a>

								</form>

							</div>

							<div id="duzenlemedivi<?= $firmaid; ?>" style="display: none;">

								<form action="" method="POST">

									<div class="row">
										
										<div class="col-md-3 col-12" style="margin-top: 7px;">
											
											<input type="hidden" name="firmaid" value="<?= $firmaid; ?>">
										
											<input type="text" name="firmaadi" class="form-control form-control-sm" value="<?= $firmaadi; ?>">

										</div>

										<div class="col-md-2 col-12" style="margin-top: 7px;">
											
											<input type="text" name="firmatel" class="form-control form-control-sm" value="<?= $firmatel; ?>">

										</div>

										<div class="col-md-2 col-12" style="margin-top: 7px;">
											
											<input type="text" name="firmaeposta" class="form-control form-control-sm" value="<?= $firmaeposta; ?>">

										</div>

										<div class="col-md-3 col-12" style="margin-top: 7px;">

											<textarea name="firmaadres" class="form-control form-control-sm" rows="3" style="margin-bottom: 10px;"><?= $firmaadres; ?></textarea>

										</div>

										<div class="col-md-1 col-12" style="margin-top: 7px;">
											
											<button class="btn btn-warning btn-sm btn-block" type="submit" name="firmabilgileriguncelle">Güncelle</button>

										</div>

									</div>

								</form>

							</div>

							<div id="tekliflerdivi<?= $firmaid; ?>" style="display: none;">

								<hr/>

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

											$katadcek = $db->query("SELECT * FROM categories WHERE id = '{$kategori_bir}' AND company_id = '{$user->company_id}'")->fetch(PDO::FETCH_ASSOC);

											$kategori_bir_adi = $katadcek['name'];

											$kategori_iki = $katbilcek['kategori_iki'];

											$katadcek = $db->query("SELECT * FROM categories WHERE id = '{$kategori_iki}' AND company_id = '{$user->company_id}'")->fetch(PDO::FETCH_ASSOC);

											$kategori_iki_adi = $katadcek['name'];

											$tverilenfirmaid = $tklfrow['tverilenfirma'];

											$firmabilgi = $db->query("SELECT * FROM clients WHERE id = '{$tverilenfirmaid}'")->fetch(PDO::FETCH_ASSOC);

											$tverilenfirmaadi = $firmabilgi['name']; 

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

								<hr/><h5><b style="line-height: 40px;">Teklif Formları</b></h5>

						<?php

							$teklifformlarinicek = $db->query("SELECT * FROM teklifformlari WHERE firmaid = '{$firmaid}' AND sirketid = '{$user->company_id}' AND silik = '0' ORDER BY tformid DESC", PDO::FETCH_ASSOC);

							if ( $teklifformlarinicek->rowCount() ){

								foreach( $teklifformlarinicek as $tfb ){

									$tformid = $tfb['tformid'];

									$tekliflistesi = $tfb['tekliflistesi'];

									$teklifsaniye = $tfb['saniye'];

									$tekliftarihi = date("d-m-Y H:i:s", $teklifsaniye);

						?>

									<div class="alert alert-warning">

										<div class="row">
											
											<div class="col-6"><button class="btn btn-warning" onclick="return false" onmousedown="javascript:ackapa('teklifformdivi<?= $tformid; ?>');"><?= $tekliftarihi." Tarihli Teklif Formundaki Ürünler<br/>"; ?></button></div>

											<div class="col-6" style="text-align: right;"><form action="" method="POST"><input type="hidden" name="tformid" value="<?= $tformid; ?>"><input type="hidden" name="tekliflistesi" value="<?= $tekliflistesi; ?>"><button class="btn btn-danger" type="submit" name="teklifformunusil">Bu teklif formunu sil</button></form></div>
										
										</div>																		

										<div id="teklifformdivi<?= $tformid; ?>" style="display: none; padding: 10px;">

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

												$urunbilgicek = $db->query("SELECT * FROM urun WHERE urun_id = '{$turunid}' AND sirketid = '{$user->company_id}'")->fetch(PDO::FETCH_ASSOC);

												$urun_adi = $urunbilgicek['urun_adi'];

												$urun_birimkg = $urunbilgicek['urun_birimkg'];

												$katbilcek = $db->query("SELECT * FROM urun WHERE urun_id = '{$turunid}' AND sirketid = '{$user->company_id}'")->fetch(PDO::FETCH_ASSOC);

												$kategori_bir = $katbilcek['kategori_bir'];

												$katadcek = $db->query("SELECT * FROM categories WHERE id = '{$kategori_bir}' AND company_id = '{$user->company_id}'")->fetch(PDO::FETCH_ASSOC);

												$kategori_bir_adi = $katadcek['name'];

												$kategori_iki = $katbilcek['kategori_iki'];

												$katadcek = $db->query("SELECT * FROM categories WHERE id = '{$kategori_iki}' AND company_id = '{$user->company_id}'")->fetch(PDO::FETCH_ASSOC);

												$kategori_iki_adi = $katadcek['name'];

												$tverilenfirmaid = $tklfrow['tverilenfirma'];

												$firmabilgi = $db->query("SELECT * FROM clients WHERE id = '{$tverilenfirmaid}'")->fetch(PDO::FETCH_ASSOC);

												$tverilenfirmaadi = $firmabilgi['name']; 

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

											} ?>

											<a href="teklifformu.php?id=<?= $tformid; ?>" target="_blank"><button class="btn btn-warning" style="margin-top: 10px;">Teklif formuna gitmek için tıklayınız.</button></a>

									<?php	}else{ echo "Bu teklif formunda ürün bulunmamaktadır."; }

									?>

										</div>

									</div>

						<?php

								}

							}

						?>
							

							</div>

							<hr/>

				<?php

						}

					}

				?>

			</div>

		</div>

		<br/><br/><br/><br/><br/><br/>

		<?php include 'template/script.php'; ?>

	</body>

</html>