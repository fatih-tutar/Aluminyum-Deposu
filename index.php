<?php 

	include 'fonksiyonlar/bagla.php';

	if ($girdi != 1) {
		
		header("Location:giris.php");

		exit();

	}elseif ($girdi == 1) {

		if(isset($_GET['mt']) === true && empty($_GET['mt']) === false){ 

			$mt = guvenlik($_GET['mt']); 

			if ($mt == '1') {
				
				$kalinlik = guvenlik($_GET['k']);

				$en = guvenlik($_GET['e']);

				$boy = guvenlik($_GET['b']);

				$adet = guvenlik($_GET['a']);

				$toplam1 = guvenlik($_GET['toplam']);

			}

			if ($mt == '2') {
				
				$amm = guvenlik($_GET['a']);

				$bmm = guvenlik($_GET['b']);

				$etkalinligi = guvenlik($_GET['e']);

				$boy = guvenlik($_GET['boy']);

				$adet = guvenlik($_GET['adet']);

				$toplam2 = guvenlik($_GET['toplam']);

			}

			if ($mt == '3') {
				
				$cap = guvenlik($_GET['c']);

				$uzunluk = guvenlik($_GET['u']);

				$adet = guvenlik($_GET['a']);

				$toplam3 = guvenlik($_GET['toplam']);

			}

			
			if ($mt == '4') {
				
				$amm = guvenlik($_GET['a']);

				$bmm = guvenlik($_GET['b']);

				$etkalinligi = guvenlik($_GET['e']);

				$boy = guvenlik($_GET['boy']);

				$adet = guvenlik($_GET['adet']);

				$toplam4 = guvenlik($_GET['toplam']);

			}

			if ($mt == '5') {
				
				$iccap = guvenlik($_GET['iccap']);

				$discap = guvenlik($_GET['discap']);

				$etkalinligi = guvenlik($_GET['e']);

				$boy = guvenlik($_GET['boy']);

				$adet = guvenlik($_GET['adet']);

				$toplam5 = guvenlik($_GET['toplam']);

			}

		}

		if (isset($_POST['hesapla'])) {
			
			$dolarkuru = guvenlik($_POST['dolarkuru']);

			$lme = guvenlik($_POST['lme']);

			$iscilik = guvenlik($_POST['iscilik']);

			$toplam = ($lme + $iscilik) * $dolarkuru;

			header("Location:index.php?fiyat=".$toplam);

			exit();
 
		}

		if (isset($_POST['levhahesapla'])) {

			$malzemetipi = guvenlik($_POST['malzemetipi']);
			
			$kalinlik = guvenlik($_POST['kalinlik']);

			$en = guvenlik($_POST['en']);

			$boy = guvenlik($_POST['boy']);

			$adet = guvenlik($_POST['adet']);

			$toplam = $kalinlik * $en * $boy * $adet * 0.000001 * 2.81;

			header("Location:index.php?mt=".$malzemetipi."&k=".$kalinlik."&e=".$en."&b=".$boy."&a=".$adet."&toplam=".$toplam);

			exit();

		}

		if (isset($_POST['kosebenthesapla'])) {

			$malzemetipi = guvenlik($_POST['malzemetipi']);
			
			$a = guvenlik($_POST['a']);

			$b = guvenlik($_POST['b']);

			$etkalinligi = guvenlik($_POST['etkalinligi']);

			$boy = guvenlik($_POST['boy']);

			$adet = guvenlik($_POST['adet']);

			$toplam = $adet * 2.71 * $etkalinligi * $boy * ($a + $b - $etkalinligi) * 0.000001;

			header("Location:index.php?mt=".$malzemetipi."&a=".$a."&b=".$b."&e=".$etkalinligi."&boy=".$boy."&adet=".$adet."&toplam=".$toplam);

			exit();

		}

		if (isset($_POST['cubukhesapla'])) {

			$malzemetipi = guvenlik($_POST['malzemetipi']);
			
			$cap = guvenlik($_POST['cap']);

			$uzunluk = guvenlik($_POST['uzunluk']);

			$adet = guvenlik($_POST['adet']);

			$toplam = 3.14 * (($cap / 2) * ($cap / 2) * $uzunluk * $adet * (0.000001) * 2.71);

			header("Location:index.php?mt=".$malzemetipi."&c=".$cap."&u=".$uzunluk."&a=".$adet."&toplam=".$toplam);

			exit();

		}

		if (isset($_POST['kutuhesapla'])) {

			$malzemetipi = guvenlik($_POST['malzemetipi']);
			
			$a = guvenlik($_POST['a']);

			$b = guvenlik($_POST['b']);

			$etkalinligi = guvenlik($_POST['etkalinligi']);

			$boy = guvenlik($_POST['boy']);

			$adet = guvenlik($_POST['adet']);

			$toplam = (2 * $etkalinligi * ($a + $b) - (4 * $etkalinligi * $etkalinligi)) * $boy * $adet * 2.71 * 0.000001;

			header("Location:index.php?mt=".$malzemetipi."&a=".$a."&b=".$b."&e=".$etkalinligi."&boy=".$boy."&adet=".$adet."&toplam=".$toplam);

			exit();

		}

		if (isset($_POST['boruhesapla'])) {

			$malzemetipi = guvenlik($_POST['malzemetipi']);
			
			$iccap = guvenlik($_POST['iccap']);

			$discap = guvenlik($_POST['discap']);

			$etkalinligi = guvenlik($_POST['etkalinligi']);

			if (empty($iccap) === true && empty($etkalinligi) === false) {
				
				$iccap = $discap - (2 * $etkalinligi);

			}

			$boy = guvenlik($_POST['boy']);

			$adet = guvenlik($_POST['adet']);

			$toplam = 3.14 * ((($discap / 2) * ($discap / 2)) - (($iccap / 2) * ($iccap / 2))) * $boy * $adet * 2.71 * 0.000001;

			header("Location:index.php?mt=".$malzemetipi."&iccap=".$iccap."&discap=".$discap."&e=".$etkalinligi."&boy=".$boy."&adet=".$adet."&toplam=".$toplam);

			exit();

		}

		if(isset($_POST['plan_duzenle'])){

            $plan_id = guvenlik($_POST['plan_id']);

            $plan = guvenlik($_POST['plan']);

            $plan_tarihi = guvenlik($_POST['plan_tarihi']);

			$plan_tarihi = strtotime($plan_tarihi);

            $plan_tekrar = guvenlik($_POST['plan_tekrar']);
            
            $plan_durum = guvenlik($_POST['plan_durum']);

            $query = $db->prepare("UPDATE plan SET plan = ?, plan_tarihi = ?, plan_tekrar = ?, plan_durum = ? WHERE plan_id = ?"); 

            $guncelle = $query->execute(array($plan,$plan_tarihi,$plan_tekrar,$plan_durum,$plan_id));

            header("Location: plan.php");

            exit();

        }

        if(isset($_POST['plan_sil'])){

            $plan_id = guvenlik($_POST['plan_id']);

            $query = $db->prepare("UPDATE plan SET plan_silik = ? WHERE plan_id = ?"); 

            $guncelle = $query->execute(array('1',$plan_id));

            header("Location: plan.php");

            exit();

        }

	}

?>

<!DOCTYPE html>

<html>

	<head>

		<title>Alüminyum Deposu</title>

		<?php include 'template/head.php'; ?>

		<style type="text/css">
			
			.gorsel-container {
			    width:100%;
			    overflow:hidden;
			    margin:0;
			    height:170px;
			}

			.gorsel-container img {
			    display:block;
			    width:100%;
			    margin:-20px 20;
			}

		</style>

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
				
				<div class="col-xl-8 col-lg-8 col-md-12 col-sm-12 col-12">
					
					<div class="row">

						<?php

							$a = 0;

							$renkstringi = "#03045e,#023e8a,#0077b6,#0096c7,#00b4d8,#48cae4,#03045e";

							$renkarrayi = explode(",", $renkstringi);

							$query = $db->query("SELECT * FROM kategori WHERE kategori_tipi = '0' AND sirketid = '{$uye_sirket}'", PDO::FETCH_ASSOC);

							if ( $query->rowCount() ){

								foreach( $query as $row ){

									$kategori_id = $row['kategori_id'];

									$kategori_adi = $row['kategori_adi'];

									$resim = "img/kategoriler/".$row['resim'];

						?>

									<div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">

										<a href="kategori.php?id=<?php echo $kategori_id; ?>">

											<img src="<?php echo $resim; ?>" class="img-thumbnail" style="width: 100%; height: auto; padding: 20px;">

											<button class="btn btn-dark btn-sm btn-block" style="font-size: 20px; background-color: black;"><?php echo $kategori_adi; ?></button>

											<!--<div style="border-radius: 50px; background-color: <?php echo $renkarrayi[$a]; ?>; color: white; font-weight: bolder; text-align: center; height: 90%; padding: 30% 20% 30% 20%; font-size: 25px;">
												
												<?php echo $kategori_adi; ?>

											</div>-->
																		
										</a>

										<br/>

									</div>

						<?php

									$a++;

								}

							}

						?>

					</div>

					<hr style="margin:30px;" />

					<div class="row" style="padding-bottom:1rem;">

						<div class="col-md-4 col-4" style="text-align:center; padding: 0px 1px 0px 1px;">

							<a href="tekliflistesi.php" target="_blank">

								<button class="btn btn-primary" style="border-radius: 50px; width: 100%; height: 100px; color: white; font-weight: bolder; text-align: center; font-size: 15px;">
									
									ÜRÜN SORGULAMA LİSTESİ

								</button>
															
							</a>

						</div>

						<div class="col-md-4 col-4" style="text-align:center; padding: 0px 1px 0px 1px;">

							<a href="kaliplistesi.php" target="_blank">

								<button class="btn btn-warning" style="border-radius: 50px; width: 100%; height: 100px; color: white; font-weight: bolder; text-align: center; font-size: 15px;">
									
									KALIP SORGULAMA EKRANI

								</button>
															
							</a>

						</div>

						<div class="col-md-4 col-4" style="text-align:center; padding: 0px 1px 0px 1px;">
							
							<a href="fiyatlistesi.php" target="_blank">

								<button class="btn btn-success" style="border-radius: 50px; width: 100%; height: 100px; color: white; font-weight: bolder; text-align: center; font-size: 15px;">
									
									AYDINLATMA FİYAT LİSTESİ

								</button>
															
							</a>

						</div>

					</div>

				</div>

				<div class="col-xl-4 col-lg-4 col-md-12 col-sm-12 col-12">
							
					<div class="div4" style="margin-top: 0px;">

						<h5 style="text-align: center;"><b>Fiyat Hesaplama</b></h5>
			
						<div class="div5">
					
							<?php

								$icerik = file_get_contents("https://www.tcmb.gov.tr/kurlar/today.xml");
							    
							    $baslik = ara("<ForexSelling>", "</ForexSelling>", $icerik);
							    
							    $dolarsatis = $baslik[0];

							    //$string = file_get_contents("https://www.lme.com/api/trading-data/fifteen-minutes-metal-block?datasourceIds=48b1eb21-2c1c-4606-a031-2e0e48804557&datasourceIds=30884874-b778-48ec-bdb2-a0a1d98de5ab&datasourceIds=53f6374a-165d-446a-b9f6-b08bbd2e46a3&datasourceIds=9632206e-db22-407f-892c-ac0fb7735b2e&datasourceIds=61f12b51-04e8-4269-987b-3d4516b20f41&datasourceIds=2908ddcb-e514-4265-9ad9-f0d27561cf52");
								
								//$json_a = json_decode($string, true);

								$lme = 0;

								$url = 'https://www.bloomberght.com/emtia/aliminyum';
								$ch = curl_init($url);
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
								$html = curl_exec($ch);
								curl_close($ch);
								$dom = new DOMDocument();
								@$dom->loadHTML($html);
								$xpath = new DOMXPath($dom);
								$h1List = $xpath->query('//h1');
								foreach ($h1List as $index => $item) {
									if($index == 0){
										$content = $item->nodeValue;
										$lmeArray = explode(" ", $content);
										$number = $lmeArray[72];
										$number = str_replace(".", "", $number);
										$number = str_replace(",", ".", $number);
										$number = floatval($number);
										$roundedNumber = intval($number);
										$lme1 = $roundedNumber + 1;
									}
								}

								$lme = $lme1;

								// $url = 'https://www.bloomberght.com/emtia/aliminyum3m';
								// $ch = curl_init($url);
								// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
								// $html = curl_exec($ch);
								// curl_close($ch);
								// $dom = new DOMDocument();
								// @$dom->loadHTML($html);
								// $xpath = new DOMXPath($dom);
								// $h1List = $xpath->query('//h1');
								// foreach ($h1List as $index => $item) {
								// 	if($index == 0){
								// 		$content = $item->nodeValue;
								// 		$lmeArray = explode(" ", $content);
								// 		$number = $lmeArray[74];
								// 		$number = str_replace(".", "", $number);
								// 		$number = str_replace(",", ".", $number);
								// 		$number = floatval($number);
								// 		$roundedNumber = intval($number);
								// 		$lme2 = $roundedNumber + 1;
								// 	}
								// }

								// if($lme2 > $lme1){ $lme = $lme2; }else{$lme = $lme1;}

							?>

							<div class="row">
								
								<div class="col-xl-6 col-lg-12"><?php echo "<b>Dolar : </b>".$dolarsatis." TL"; ?></div>

								<div class="col-xl-6 col-lg-12"><?php echo "<b>LME : </b>".$lme." $"; ?></div>

							</div>

						</div>

						<div class="div5">
							
							<h5><b>Hesaplama</b></h5>

							<form action="" method="POST">

								<div class="row" style="margin-bottom: 5px;">
									
									<div class="col-3">Dolar</div>

									<div class="col-9"><input type="text" class="form-control" name="dolarkuru" value="<?php echo $dolarsatis; ?>"></div>

								</div>

								<div class="row" style="margin-bottom: 5px;">
									
									<div class="col-3">LME</div>

									<div class="col-9"><input type="text" class="form-control" name="lme" value="<?php echo $lme; ?>"></div>

								</div>

								<div class="row" style="margin-bottom: 5px;">
									
									<div class="col-3">İşçilik</div>

									<div class="col-9"><input type="text" class="form-control" name="iscilik" placeholder="İşçilik Giriniz."></div>

								</div>

								<button type="submit" name="hesapla" class="btn btn-primary btn-block btn-sm" style="background-color:black;">Hesapla</button>

							</form>

						</div>

				<?php

					if (isset($_GET['fiyat']) === true && empty($_GET['fiyat']) === false) {
						
				?>

						<div class="div5">
							
							<h5><b>Fiyat</b></h5>

							<?php echo $_GET['fiyat']." TL"; ?>

						</div>

				<?php

					}

				?>

						<h5 style="text-align: center;"><b>ANLIK FİYATLAMA</b></h5>

						<div class="div5">
							
						<?php

							$query = $db->query("SELECT * FROM fabrikalar WHERE sirketid = '{$uye_sirket}' ORDER BY fabrika_adi ASC", PDO::FETCH_ASSOC);

							if ( $query->rowCount() ){

								foreach( $query as $row ){

									$id++;

									$fabrika_id = guvenlik($row['fabrika_id']);

									$fabrika_adi = guvenlik($row['fabrika_adi']);

									$fabrikaiscilik = guvenlik($row['fabrikaiscilik']);

									$fiyat = ($lme + $fabrikaiscilik) * $dolarsatis / 1000;

									$fiyat2=floor($fiyat*100/100*102)/100;

									$fiyat1=floor($fiyat*100/100*101)/100;

									$fiyat=floor($fiyat*100)/100;

									if($fabrikaiscilik != 0){ 

						?>

									<div class="row">
										
										<div class="col-md-5 col-5" style="font-size: 17px; font-weight: bold; border-right: 2px solid black;"><?php echo $fabrika_adi; ?></div>

										<div class="col-md-3 col-3" style="font-size: 17px; font-weight: bold;"><?php echo $fiyat." TL"; ?></div>
										<div class="col-md-3 col-3" style="font-size: 17px; font-weight: bold;"><?php echo $fiyat2." TL"; ?></div>

									</div><hr style="margin: 1px; border: 1px solid black;" />


						<?php } } } ?>

						</div>

					</div>

					<div class="div4">
						
						<h5 style="text-align: center;"><b>Ağırlık Hesaplama</b></h5>

						<select name="malzemetipi" id="selectkutuID" class="form-control" onchange="degergoster();" style="margin-bottom: 5px;">

							<option value="0">Malzeme Tipini Seçiniz</option>
							
							<option value="1">Alüminyum Levha</option>

							<option value="2">Alüminyum Köşebent</option>

							<option value="3">Alüminyum Çubuk</option>

							<option value="4">Alüminyum Kutu</option>

							<option value="5">Alüminyum Boru</option>

						</select>

						<?php if(isset($mt) && $mt == '1'){ ?><div id="malzeme1"><?php }else{ ?><div id="malzeme1" style="display: none;"><?php } ?>
							
							<hr/>
							
							<h6 style="margin-top: 5px; text-align: center;"><b>Alüminyum Levha Bilgilerini Doldurunuz</b></h6>

							<form action="" method="POST">

								<input type="hidden" name="malzemetipi" value="1">

								<div class="row"><div class="col-3"><b>Kalıklık</b></div><div class="col-9"><?php if(isset($_GET['k'])){ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="kalinlik" value="<?php echo $kalinlik ?>"><?php }else{ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="kalinlik" placeholder="KALINLIK"><?php } ?></div></div>

								<div class="row"><div class="col-3"><b>En</b></div><div class="col-9"><?php if(isset($_GET['e'])){ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="en" value="<?php echo $en; ?>"><?php }else{ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="en" placeholder="EN"><?php } ?></div></div>

								<div class="row"><div class="col-3"><b>Boy</b></div><div class="col-9"><?php if(isset($_GET['b'])){ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="boy" value="<?php echo $boy; ?>"><?php }else{ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="boy" placeholder="BOY"><?php } ?></div></div>

								<div class="row"><div class="col-3"><b>Adet</b></div><div class="col-9"><?php if(isset($_GET['a'])){ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="adet" value="<?php echo $adet; ?>"><?php }else{ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="adet" placeholder="ADET"><?php } ?></div></div>
								
								<button type="submit" class="btn btn-primary btn-block btn-sm" style="margin-bottom: 5px; background-color: black;" name="levhahesapla">Hesapla</button>

							</form>

							<?php if(isset($toplam1)){?><h5 style="text-align: center; margin-top: 20px;"><b>Sonuç : <ins><?php echo $toplam1." KG"; ?></ins></b></h5><?php } ?>

						</div>

						<?php if(isset($mt) && $mt == '2'){ ?><div id="malzeme2"><?php }else{ ?><div id="malzeme2" style="display: none;"><?php } ?>
							
							<hr/>
							
							<h6 style="margin-top: 5px; text-align: center;"><b>Alüminyum Köşebent Bilgilerini Doldurunuz</b></h6>

							<form action="" method="POST">

								<input type="hidden" name="malzemetipi" value="2">

								<div class="row"><div class="col-4"><b>A (mm)</b></div><div class="col-8"><?php if(isset($_GET['a'])){ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="a" value="<?php echo $amm ?>"><?php }else{ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="a" placeholder="A (mm)"><?php } ?></div></div>

								<div class="row"><div class="col-4"><b>B (mm)</b></div><div class="col-8"><?php if(isset($_GET['b'])){ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="b" value="<?php echo $bmm; ?>"><?php }else{ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="b" placeholder="B (mm)"><?php } ?></div></div>

								<div class="row"><div class="col-4"><b>Et Kalınlığı</b></div><div class="col-8"><?php if(isset($_GET['e'])){ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="etkalinligi" value="<?php echo $etkalinligi; ?>"><?php }else{ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="etkalinligi" placeholder="ET KALINLIĞI"><?php } ?></div></div>

								<div class="row"><div class="col-4"><b>Boy</b></div><div class="col-8"><?php if(isset($_GET['boy'])){ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="boy" value="<?php echo $boy; ?>"><?php }else{ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="boy" placeholder="BOY"><?php } ?></div></div>

								<div class="row"><div class="col-4"><b>Adet</b></div><div class="col-8"><?php if(isset($_GET['adet'])){ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="adet" value="<?php echo $adet; ?>"><?php }else{ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="adet" placeholder="ADET"><?php } ?></div></div>
								
								<button type="submit" class="btn btn-primary btn-block btn-sm" style="margin-bottom: 5px;" name="kosebenthesapla">Hesapla</button>

							</form>

							<?php if(isset($toplam2)){?><h5 style="text-align: center; margin-top: 20px;"><b>Sonuç : <ins><?php echo $toplam2." KG"; ?></ins></b></h5><?php } ?>

						</div>

						<?php if(isset($mt) && $mt == '3'){ ?><div id="malzeme3"><?php }else{ ?><div id="malzeme3" style="display: none;"><?php } ?>
							
							<hr/>
							
							<h6 style="margin-top: 5px; text-align: center;"><b>Alüminyum Çubuk Bilgilerini Doldurunuz</b></h6>

							<form action="" method="POST">

								<input type="hidden" name="malzemetipi" value="3">

								<div class="row"><div class="col-4"><b>Çap</b></div><div class="col-8"><?php if(isset($_GET['c'])){ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="cap" value="<?php echo $cap ?>"><?php }else{ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="cap" placeholder="ÇAP"><?php } ?></div></div>

								<div class="row"><div class="col-4"><b>Uzunluk</b></div><div class="col-8"><?php if(isset($_GET['u'])){ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="uzunluk" value="<?php echo $uzunluk; ?>"><?php }else{ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="uzunluk" placeholder="UZUNLUK"><?php } ?></div></div>

								<div class="row"><div class="col-4"><b>Adet</b></div><div class="col-8"><?php if(isset($_GET['a'])){ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="adet" value="<?php echo $adet; ?>"><?php }else{ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="adet" placeholder="ADET"><?php } ?></div></div>
								
								<button type="submit" class="btn btn-primary btn-block btn-sm" style="margin-bottom: 5px;" name="cubukhesapla">Hesapla</button>

							</form>

							<?php if(isset($toplam3)){?><h5 style="text-align: center; margin-top: 20px;"><b>Sonuç : <ins><?php echo $toplam3." KG"; ?></ins></b></h5><?php } ?>

						</div>

						<?php if(isset($mt) && $mt == '4'){ ?><div id="malzeme4"><?php }else{ ?><div id="malzeme4" style="display: none;"><?php } ?>
							
							<hr/>
							
							<h6 style="margin-top: 5px; text-align: center;"><b>Alüminyum Kutu Bilgilerini Doldurunuz</b></h6>

							<form action="" method="POST">

								<input type="hidden" name="malzemetipi" value="4">

								<div class="row"><div class="col-4"><b>A (mm)</b></div><div class="col-8"><?php if(isset($_GET['a'])){ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="a" value="<?php echo $amm ?>"><?php }else{ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="a" placeholder="A (mm)"><?php } ?></div></div>

								<div class="row"><div class="col-4"><b>B (mm)</b></div><div class="col-8"><?php if(isset($_GET['b'])){ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="b" value="<?php echo $bmm; ?>"><?php }else{ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="b" placeholder="B (mm)"><?php } ?></div></div>

								<div class="row"><div class="col-4"><b>Et Kalınlığı</b></div><div class="col-8"><?php if(isset($_GET['e'])){ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="etkalinligi" value="<?php echo $etkalinligi; ?>"><?php }else{ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="etkalinligi" placeholder="ET KALINLIĞI"><?php } ?></div></div>

								<div class="row"><div class="col-4"><b>Boy</b></div><div class="col-8"><?php if(isset($_GET['boy'])){ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="boy" value="<?php echo $boy; ?>"><?php }else{ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="boy" placeholder="BOY"><?php } ?></div></div>

								<div class="row"><div class="col-4"><b>Adet</b></div><div class="col-8"><?php if(isset($_GET['adet'])){ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="adet" value="<?php echo $adet; ?>"><?php }else{ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="adet" placeholder="ADET"><?php } ?></div></div>
								
								<button type="submit" class="btn btn-primary btn-block btn-sm" style="margin-bottom: 5px;" name="kutuhesapla">Hesapla</button>

							</form>

							<?php if(isset($toplam4)){?><h5 style="text-align: center; margin-top: 20px;"><b>Sonuç : <ins><?php echo $toplam4." KG"; ?></ins></b></h5><?php } ?>

						</div>

						<?php if(isset($mt) && $mt == '5'){ ?><div id="malzeme5"><?php }else{ ?><div id="malzeme5" style="display: none;"><?php } ?>
							
							<hr/>
							
							<h6 style="margin-top: 5px; text-align: center;"><b>Alüminyum Boru Bilgilerini Doldurunuz</b></h6>

							<form action="" method="POST">

								<input type="hidden" name="malzemetipi" value="5">

								<div class="row"><div class="col-4"><b>İç Çap</b></div><div class="col-8"><?php if(isset($_GET['iccap'])){ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="iccap" value="<?php echo $iccap ?>"><?php }else{ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="iccap" placeholder="İç Çap"><?php } ?></div></div>

								<div class="row"><div class="col-4"><b>Dış Çap</b></div><div class="col-8"><?php if(isset($_GET['discap'])){ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="discap" value="<?php echo $discap; ?>"><?php }else{ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="discap" placeholder="Dış Çap"><?php } ?></div></div>

								<div class="row"><div class="col-4"><b>Et Kalınlığı</b></div><div class="col-8"><?php if(isset($_GET['e'])){ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="etkalinligi" value="<?php echo $etkalinligi; ?>"><?php }else{ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="etkalinligi" placeholder="ET KALINLIĞI"><?php } ?></div></div>

								<div class="row"><div class="col-4"><b>Boy</b></div><div class="col-8"><?php if(isset($_GET['boy'])){ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="boy" value="<?php echo $boy; ?>"><?php }else{ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="boy" placeholder="BOY"><?php } ?></div></div>

								<div class="row"><div class="col-4"><b>Adet</b></div><div class="col-8"><?php if(isset($_GET['adet'])){ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="adet" value="<?php echo $adet; ?>"><?php }else{ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="adet" placeholder="ADET"><?php } ?></div></div>
								
								<button type="submit" class="btn btn-primary btn-block btn-sm" style="margin-bottom: 5px;" name="boruhesapla">Hesapla</button>

							</form>

							<?php if(isset($toplam5)){?><h5 style="text-align: center; margin-top: 20px;"><b>Sonuç : <ins><?php echo $toplam5." KG"; ?></ins></b></h5><?php } ?>

						</div>

					</div>

					<div class="div4">
						
						<b>Toplam Ürün Tonaj</b> : <?php echo $uye_adi; ?>

					</div>

				</div>

			</div>

			<div style="margin-top:30px;">

				<div class="row">

					<div class="col-12" style="background-color:white; padding-top:10px;"><h3>Gelecek 30 Günlük İşler</h3></div>

				</div>

				<?php

					$i = 0;
					
					$plan_cek = $db->query("SELECT * FROM plan WHERE plan_silik = '0' AND plan_durum = '0' ORDER BY plan_tarihi ASC", PDO::FETCH_ASSOC);

					if ( $plan_cek->rowCount() ){
					
						foreach( $plan_cek as $plancek ){

							$i++;
					
							$plan_id = guvenlik($plancek['plan_id']);

							$plan = guvenlik($plancek['plan']);

							$plan_tarihi = guvenlik($plancek['plan_tarihi']);

							$gecmis = 0;

							if($plan_tarihi < $su_an){ $gecmis = 1; }

							$plan_tarihi = date("d-m-Y",$plan_tarihi);

							$plan_tekrar = guvenlik($plancek['plan_tekrar']);

							$plan_durum = guvenlik($plancek['plan_durum']);

				?>

						<div><form action="" method="POST">

						<?php if($gecmis == '0'){ ?>
							
							<div class="row mb-1" style="background-color:#52c0c0; padding:20px 10px; margin:-10px;">

						<?php }else{ ?>
							
							<div class="row mb-1" style="background-color:#ad3f3f; padding:20px 10px; margin:-10px;">

						<?php } ?>

								<div class="col-md-2">

									<input type="hidden" name="plan_id" value="<?php echo $plan_id; ?>">
										
									<input type="text" id="tarih<?php echo $plan_id; ?>" name="plan_tarihi" value="<?php echo $plan_tarihi; ?>" class="form-control form-control-sm" style="border-style:none; font-size:1.1rem;">

								</div>

								<div class="col-md-4"><input type="text" name="plan" class="form-control form-control-sm" placeholder="İş planına eklenecek görev" value="<?php echo $plan; ?>" style="border-style:none; font-size:1.1rem;"></div>

								<div class="col-md-3">

									<div class="row">
										<div class="col-md-6">
											<select name="plan_tekrar" id="plan_tekrar" class="form-control form-control-sm" style="border-style:none; font-size:1.1rem;">

											<?php if($plan_tekrar == '0'){ ?>

												<option value="0" selected>Tekrarsız</option>
												<option value="1">Aylık Tekrarlı</option>
												
											<?php }else{ ?>

												<option value="0">Tekrarsız</option>
												<option value="1" selected>Aylık Tekrarlı</option>
												
											<?php } ?>

											</select>
										</div>
										<div class="col-md-6">
											<select name="plan_durum" id="plan_durum" class="form-control form-control-sm" style="border-style:none; font-size:1.1rem;">

											<?php if($plan_durum == '0'){ ?>

												<option value="0" selected>Sırada</option>
												<option value="1">Tamamlandı</option>
												
											<?php }else{ ?>

												<option value="0">Sırada</option>
												<option value="1" selected>Tamamlandı</option>
												
											<?php } ?>

											</select>
										</div>
									</div>
								</div>

								<div class="col-md-1 col-6"><button type="submit" class="btn btn-primary btn-block btn-sm" name="plan_duzenle" >Düzenle</button></div>

								<div class="col-md-2 col-6">

									<div id="sildivi<?php echo $plan_id; ?>">

										<a href="#" onclick="return false" onmousedown="javascript:ackapa2('silmeonaydivi<?php echo $plan_id; ?>','sildivi<?php echo $plan_id; ?>');">
									
											<button class="btn btn-danger btn-block btn-sm">Sil</button>
										
										</a>

									</div>

									<div id="silmeonaydivi<?php echo $plan_id; ?>" style="display:none;">
								
										<div class="row">

											<div class="col-md-6">

												<button type="submit" name="plan_sil" class="btn btn-success btn-sm btn-block">Evet</button>

											</div>
											
											<div class="col-md-6">

												<a href="#" onclick="return false" onmousedown="javascript:ackapa2('sildivi<?php echo $plan_id; ?>','silmeonaydivi<?php echo $plan_id; ?>');">
										
													<button class="btn btn-danger btn-block btn-sm">Hayır</button>
												
												</a>

											</div>

										</div>

									</div>
								
								</div>

							</div>

						</form></div>

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