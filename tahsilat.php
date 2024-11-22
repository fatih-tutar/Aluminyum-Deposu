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

		if (isset($_POST['firmabilgileriguncelle'])) {
			
			$firmaid = guvenlik($_POST['firmaid']);

			$firmaadi = guvenlik($_POST['firmaadi']);

			$firmatel = guvenlik($_POST['firmatel']);

			$firmaeposta = guvenlik($_POST['firmaeposta']);

			$firmaadres = guvenlik($_POST['firmaadres']);

			$query = $db->prepare("UPDATE firmalar SET firmaadi = ?, firmatel = ?, firmaeposta = ?, firmaadres = ? WHERE firmaid = ?"); 

			$guncelle = $query->execute(array($firmaadi, $firmatel, $firmaeposta, $firmaadres, $firmaid));

			header("Location:firmalar.php");

			exit();

		}

		if (isset($_POST['firmasil'])) {

			$firmaid = guvenlik($_POST['firmaid']);

			if (firmakullanimdami($firmaid) == '1') {
			
				$hata = '<br/><div class="alert alert-danger" role="alert">Bu firmanın kayıtlı olduğu bir ürün, teklif veya teklif formu var o yüzden silemiyoruz.</a></div>';

			}else{
			
				$sil = $db->prepare("UPDATE firmalar SET silik = ? WHERE firmaid = ?");

				$delete = $sil->execute(array('1',$firmaid));

				header("Location:firmalar.php");

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

			header("Location:firmalar.php");

			exit();

		}

		if (isset($_POST['firma_ekleme_formu'])) {
			
			$firmaadi = guvenlik($_POST['firmaadi']);

			$firmatel = guvenlik($_POST['firmatel']);

			$firmaeposta = guvenlik($_POST['firmaeposta']);

			$firmaadres = guvenlik($_POST['firmaadres']);

			$query = $db->prepare("INSERT INTO firmalar SET firmaadi = ?, firmatel = ?, firmaeposta = ?, firmaadres = ?, sirketid = ?, silik = ?");

			$insert = $query->execute(array($firmaadi, $firmatel, $firmaeposta, $firmaadres, $uye_sirket,'0'));

			header("Location:firmalar.php");

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

						<a href="#" onclick="return false" onmousedown="javascript:ackapa('formdivi');"><h5><i class="fas fa-angle-double-down"></i>&nbsp;&nbsp;&nbsp;&nbsp;<b>Tahsilat Kayıt Formu</b>&nbsp;&nbsp;&nbsp;&nbsp;<i class="fas fa-angle-double-down"></i></h5></a>

						<div id="formdivi" style="display: none;">
						
							<form action="" method="POST">
									
								<div>
									
									<select class="form-control" style="margin-bottom: 10px;" id="exampleFormControlSelect1" name="firmaid">

	                                    <option selected value="0">Firma Seçiniz</option>

	                                    <?php

	                                    $fabrika = $db->query("SELECT * FROM firmalar WHERE sirketid = '{$uye_sirket}' ORDER BY firmaadi ASC", PDO::FETCH_ASSOC);

	                                    if ( $fabrika->rowCount() ){

	                                        foreach( $fabrika as $fbrk ){

	                                            $firmaid = $fbrk['firmaid'];

	                                            $firmaadi = $fbrk['firmaadi'];

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

					$query = $db->query("SELECT * FROM firmalar WHERE sirketid = '{$uye_sirket}' ORDER BY firmaadi ASC", PDO::FETCH_ASSOC);

					if ( $query->rowCount() ){

						foreach( $query as $row ){

							$firmaid = $row['firmaid'];

							$firmaadi = $row['firmaadi'];

							$firmatel = $row['firmatel'];

							$firmaeposta = $row['firmaeposta'];

							$firmaadres = $row['firmaadres'];

				?>

							<div class="row">
								
								<div class="col-md-3 col-12" style="margin-top: 7px;">
									
									<a href="#" onclick="return false" onmousedown="javascript:ackapa('tekliflerdivi<?php echo $firmaid; ?>');">

										<?php echo $firmaadi;?>
											
									</a>

								</div>

								<div class="col-md-2 col-12" style="margin-top: 7px;"><?php echo $firmatel; ?></div>

								<div class="col-md-2 col-12" style="margin-top: 7px;"><?php echo $firmaeposta; ?></div>

								<div class="col-md-3 col-12" style="margin-top: 7px;"><?php echo $firmaadres; ?></div>

								<div class="col-md-1 col-6" style="margin-top: 7px;">

									<a href="#" onclick="return false" onmousedown="javascript:ackapa('duzenlemedivi<?php echo $firmaid; ?>');"><button class="btn btn-primary btn-sm btn-block">Düzenle</button></a>
									
								</div>

								<div class="col-md-1 col-6" style="margin-top: 7px;">

									<a href="#" onclick="return false" onmousedown="javascript:ackapa('silmedivi<?php echo $firmaid; ?>');"><button class="btn btn-danger btn-sm btn-block">Sil</button></a>
									
								</div>

							</div>

							<div id="silmedivi<?php echo $firmaid; ?>" style="display: none; text-align: right; margin-top: 15px;">
										
								<form action="" method="POST">

									<input type="hidden" name="firmaid" value="<?php echo $firmaid; ?>">

									Silmek istediğinize emin misiniz?&nbsp;&nbsp;&nbsp;

									<button class="btn btn-success btn-sm" name="firmasil" type="submit">Evet</button>&nbsp;&nbsp;&nbsp;

									<a href="#" onclick="return false" onmousedown="javascript:ackapa('silmedivi<?php echo $firmaid; ?>');"><button class="btn btn-danger btn-sm">Hayır</button></a>

								</form>

							</div>

							<div id="duzenlemedivi<?php echo $firmaid; ?>" style="display: none;">

								<form action="" method="POST">

									<div class="row">
										
										<div class="col-md-3 col-12" style="margin-top: 7px;">
											
											<input type="hidden" name="firmaid" value="<?php echo $firmaid; ?>">
										
											<input type="text" name="firmaadi" class="form-control form-control-sm" value="<?php echo $firmaadi; ?>">

										</div>

										<div class="col-md-2 col-12" style="margin-top: 7px;">
											
											<input type="text" name="firmatel" class="form-control form-control-sm" value="<?php echo $firmatel; ?>">

										</div>

										<div class="col-md-2 col-12" style="margin-top: 7px;">
											
											<input type="text" name="firmaeposta" class="form-control form-control-sm" value="<?php echo $firmaeposta; ?>">

										</div>

										<div class="col-md-3 col-12" style="margin-top: 7px;">

											<textarea name="firmaadres" class="form-control form-control-sm" rows="3" style="margin-bottom: 10px;"><?php echo $firmaadres; ?></textarea>

										</div>

										<div class="col-md-1 col-12" style="margin-top: 7px;">
											
											<button class="btn btn-warning btn-sm btn-block" type="submit" name="firmabilgileriguncelle">Güncelle</button>

										</div>

									</div>

								</form>

							</div>

							<div id="tekliflerdivi<?php echo $firmaid; ?>" style="display: none;">

								<hr/>

								<div class="row">
									
									<div class="col-6"><h5><b style="line-height: 40px;">Teklifler</b></h5></div>

									<div class="col-6"><a href="teklif.php?id=<?php echo $firmaid; ?>" target="_blank"><button class="btn btn-primary btn-sm">Teklif Formuna Git</button></a></div>

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

									$tklfcek = $db->query("SELECT * FROM teklif WHERE tverilenfirma = '{$firmaid}' AND formda = '0' AND sirketid = '{$uye_sirket}' AND silik = '0' ORDER BY teklifid DESC", PDO::FETCH_ASSOC);

									if ( $tklfcek->rowCount() ){

										foreach( $tklfcek as $tklfrow ){

											$teklifid = $tklfrow['teklifid'];

											$tekliflersiralamasi++;

											$turunid = $tklfrow['turunid'];

											$urunbilgicek = $db->query("SELECT * FROM urun WHERE urun_id = '{$turunid}' AND sirketid = '{$uye_sirket}'")->fetch(PDO::FETCH_ASSOC);

											$urun_adi = $urunbilgicek['urun_adi'];

											$urun_birimkg = $urunbilgicek['urun_birimkg'];

											$katbilcek = $db->query("SELECT * FROM urun WHERE urun_id = '{$turunid}' AND sirketid = '{$uye_sirket}'")->fetch(PDO::FETCH_ASSOC);

											$kategori_bir = $katbilcek['kategori_bir'];

											$katadcek = $db->query("SELECT * FROM kategori WHERE kategori_id = '{$kategori_bir}' AND sirketid = '{$uye_sirket}'")->fetch(PDO::FETCH_ASSOC);

											$kategori_bir_adi = $katadcek['kategori_adi'];

											$kategori_iki = $katbilcek['kategori_iki'];

											$katadcek = $db->query("SELECT * FROM kategori WHERE kategori_id = '{$kategori_iki}' AND sirketid = '{$uye_sirket}'")->fetch(PDO::FETCH_ASSOC);

											$kategori_iki_adi = $katadcek['kategori_adi'];

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

												<div class="col-4 d-block d-sm-none" style="margin-top: 7px;"><b style="color: red;">Firma</b></div>
												
												<div class="col-md-3 col-8" style="margin-top: 7px;"><?php echo $tekliflersiralamasi.". ".$tverilenfirmaadi; ?></div>

												<div class="col-4 d-block d-sm-none" style="margin-top: 7px;"><b style="color: red;">Ürün</b></div>

												<div class="col-md-3 col-8" style="margin-top: 7px;"><?php echo $urun_adi." ".$kategori_iki_adi." ".$kategori_bir_adi; ?></div>

												<div class="col-4 d-block d-sm-none" style="margin-top: 7px;"><b style="color: red;">Adet</b></div>

												<div class="col-md-1 col-8" style="margin-top: 7px;"><?php echo $tadet; ?></div>

												<div class="col-4 d-block d-sm-none" style="margin-top: 7px;"><b style="color: red;">Satış</b></div>

												<div class="col-md-1 col-8" style="margin-top: 7px;"><?php echo $tsatisfiyati." TL"; ?></div>

												<div class="col-4 d-block d-sm-none" style="margin-top: 7px;"><b style="color: red;">Toplam</b></div>

												<div class="col-md-2 col-8" style="margin-top: 7px;"><?php echo $toplam_fiyat." TL"; ?></div>

												<div class="col-4 d-block d-sm-none" style="margin-top: 7px;"><b style="color: red;">Tarih</b></div>

												<div class="col-md-1 col-8" style="margin-top: 7px;"><?php echo $ttarih; ?></div>

												<div class="col-md-1 col-12" style="margin-top: 7px; text-align: right;">
													
													<form action="" method="POST">

														<input type="hidden" name="teklifid" value="<?php echo $teklifid; ?>">
														
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

							$teklifformlarinicek = $db->query("SELECT * FROM teklifformlari WHERE firmaid = '{$firmaid}' AND sirketid = '{$uye_sirket}' AND silik = '0' ORDER BY tformid DESC", PDO::FETCH_ASSOC);

							if ( $teklifformlarinicek->rowCount() ){

								foreach( $teklifformlarinicek as $tfb ){

									$tformid = $tfb['tformid'];

									$tekliflistesi = $tfb['tekliflistesi'];

									$teklifsaniye = $tfb['saniye'];

									$tekliftarihi = date("d-m-Y H:i:s", $teklifsaniye);

						?>

									<div class="alert alert-warning">

										<div class="row">
											
											<div class="col-6"><button class="btn btn-warning" onclick="return false" onmousedown="javascript:ackapa('teklifformdivi<?php echo $tformid; ?>');"><?php echo $tekliftarihi." Tarihli Teklif Formundaki Ürünler<br/>"; ?></button></div>

											<div class="col-6" style="text-align: right;"><form action="" method="POST"><input type="hidden" name="tformid" value="<?php echo $tformid; ?>"><input type="hidden" name="tekliflistesi" value="<?php echo $tekliflistesi; ?>"><button class="btn btn-danger" type="submit" name="teklifformunusil">Bu teklif formunu sil</button></form></div>
										
										</div>																		

										<div id="teklifformdivi<?php echo $tformid; ?>" style="display: none; padding: 10px;">

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
												
												$tklfrow = $db->query("SELECT * FROM teklif WHERE teklifid = '{$value}' AND sirketid = '{$uye_sirket}' AND silik = '0'")->fetch(PDO::FETCH_ASSOC);

												$tekliflersiralamasi++;

												$teklifid = $tklfrow['teklifid'];

												$turunid = $tklfrow['turunid'];

												$urunbilgicek = $db->query("SELECT * FROM urun WHERE urun_id = '{$turunid}' AND sirketid = '{$uye_sirket}'")->fetch(PDO::FETCH_ASSOC);

												$urun_adi = $urunbilgicek['urun_adi'];

												$urun_birimkg = $urunbilgicek['urun_birimkg'];

												$katbilcek = $db->query("SELECT * FROM urun WHERE urun_id = '{$turunid}' AND sirketid = '{$uye_sirket}'")->fetch(PDO::FETCH_ASSOC);

												$kategori_bir = $katbilcek['kategori_bir'];

												$katadcek = $db->query("SELECT * FROM kategori WHERE kategori_id = '{$kategori_bir}' AND sirketid = '{$uye_sirket}'")->fetch(PDO::FETCH_ASSOC);

												$kategori_bir_adi = $katadcek['kategori_adi'];

												$kategori_iki = $katbilcek['kategori_iki'];

												$katadcek = $db->query("SELECT * FROM kategori WHERE kategori_id = '{$kategori_iki}' AND sirketid = '{$uye_sirket}'")->fetch(PDO::FETCH_ASSOC);

												$kategori_iki_adi = $katadcek['kategori_adi'];

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
													
													<div class="col-3"><?php echo $tekliflersiralamasi.". ".$tverilenfirmaadi; ?></div>

													<div class="col-3"><?php echo $urun_adi." ".$kategori_iki_adi." ".$kategori_bir_adi; ?></div>

													<div class="col-1"><?php echo $tadet; ?></div>

													<div class="col-1"><?php echo $tsatisfiyati." TL"; ?></div>

													<div class="col-2"><?php echo $toplam_fiyat." TL"; ?></div>

													<div class="col-1"><?php echo $ttarih; ?></div>

													<div class="col-1" style="text-align: right;">
															
														<form action="" method="POST">

															<input type="hidden" name="teklifformid" value="<?php echo $tformid; ?>">

															<input type="hidden" name="tekliflistesi" value="<?php echo $tekliflistesi; ?>">

															<input type="hidden" name="teklifkey" value="<?php echo $key; ?>">

															<input type="hidden" name="teklifid" value="<?php echo $teklifid; ?>">
															
															<button type="submit" class="btn btn-danger btn-sm" name="formluteklifsil" style="margin-bottom: 5px;">Sil</button>

														</form>

													</div>

												</div>

									<?php

											} ?>

											<a href="teklifformu.php?id=<?php echo $tformid; ?>" target="_blank"><button class="btn btn-warning" style="margin-top: 10px;">Teklif formuna gitmek için tıklayınız.</button></a>

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