<?php 

	include 'fonksiyonlar/bagla.php';

	if ($girdi != 1) {
		
		header("Location:giris.php");

		exit();

	}elseif ($girdi == 1) {

		if($uye_tipi == '0' || $uye_tipi == '1'){

			header("Location:index.php");

			exit();

		}

	if($uye_tipi != '3'){

		if (isset($_POST['aciklamaguncelle'])) {
			
			$sirketaciklama = yollaf($_POST['sirketaciklama']);

			$query = $db->prepare("UPDATE sirketler SET sirketaciklama = ? WHERE sirketid = ?"); 

			$guncelle = $query->execute(array($sirketaciklama,$uye_sirket));

			header("Location:yonetim.php");

			exit();

		}

		if (isset($_POST['logoguncelle'])) {

			$allow = array('pdf');

            $temp = explode(".", $_FILES['uploadfile']['name']);

            $dosyaadi = $temp[0];

            $extension = end($temp);

            $randomsayi = rand(0,10000);;

            $upload_file = $dosyaadi.$randomsayi.".".$extension;

            move_uploaded_file($_FILES['uploadfile']['tmp_name'], "img/file/".$upload_file);

            $query = $db->prepare("UPDATE sirketler SET sirketlogo = ? WHERE sirketid = ?"); 

			$guncelle = $query->execute(array($upload_file,$uye_sirket));

			header("Location:yonetim.php");

			exit();

		}

		if (isset($_POST['bilgileriguncelle'])) {
			
			$kullanici_id = guvenlik($_POST['kullanici_id']);

			$kullanici_adi = guvenlik($_POST['kullanici_adi']);

			$kullanici_pasiflik = 0;

			if(isset($_POST['pasiflik'])){ $kullanici_pasiflik = 1; }

			$kullanici_yetkileri = '0,0,0,0,0,0,0,0,0,0,0,0,0,0,0';

			$kullanici_yetkileri_arrayi = explode(",", $kullanici_yetkileri);

			if(isset($_POST['yetkialis'])){ $kullanici_yetkileri_arrayi[0] = 1; }

			if(isset($_POST['yetkifabrika'])){ $kullanici_yetkileri_arrayi[1] = 1; }

			if(isset($_POST['yetkiteklif'])){ $kullanici_yetkileri_arrayi[2] = 1; }

			if(isset($_POST['yetkisiparis'])){ $kullanici_yetkileri_arrayi[3] = 1; }

			if(isset($_POST['yetkiduzenleme'])){ $kullanici_yetkileri_arrayi[4] = 1; }

			if(isset($_POST['yetkiislemlerigorme'])){ $kullanici_yetkileri_arrayi[5] = 1; }

			if(isset($_POST['gelengidenigorme'])){ $kullanici_yetkileri_arrayi[6] = 1; }

			if(isset($_POST['yetkisatis'])){ $kullanici_yetkileri_arrayi[7] = 1; }

			if(isset($_POST['toplamgorme'])){ $kullanici_yetkileri_arrayi[8] = 1; }

			if(isset($_POST['ziyaretyetki'])){ $kullanici_yetkileri_arrayi[9] = 1; }

			if(isset($_POST['sevkiyatyetki'])){ $kullanici_yetkileri_arrayi[10] = 1; }

			if(isset($_POST['yetkiadet'])){ $kullanici_yetkileri_arrayi[11] = 1; }

			if(isset($_POST['yetkipalet'])){ $kullanici_yetkileri_arrayi[12] = 1; }

			if(isset($_POST['yetkialkop'])){ $kullanici_yetkileri_arrayi[13] = 1; }

			if(isset($_POST['ofisyetki'])){ $kullanici_yetkileri_arrayi[14] = 1; }

			$kullanici_yetkileri = implode(",", $kullanici_yetkileri_arrayi);

			$query = $db->prepare("UPDATE uyeler SET uye_adi = ?, uye_yetkiler = ?, pasiflik = ? WHERE uye_id = ?"); 

			$guncelle = $query->execute(array($kullanici_adi,$kullanici_yetkileri,$kullanici_pasiflik,$kullanici_id));

			header("Location:yonetim.php");

			exit();

		}

		if (isset($_POST['kullanicisil'])) {
			
			$kullanici_id = guvenlik($_POST['kullanici_id']);

			$query = $db->prepare("UPDATE uyeler SET uye_silik = ? WHERE uye_id = ?"); 

			$guncelle = $query->execute(array('1',$kullanici_id));

			header("Location:yonetim.php");

			exit();

		}

		if (isset($_POST['uye_ekle'])) {
			
			$yeni_uye_adi = guvenlik($_POST['yeni_uye_adi']);

			$yeni_uye_mail = guvenlik($_POST['yeni_uye_mail']);

			$yeni_uye_sifre = "81dc9bdb52d04dc20036dbd8313ed055";

			$yeni_uye_yetki = "0,0,0,0,0,0,0,0,0,0,0,0,0,0,0";

			if (empty($yeni_uye_adi)) {
				
				$hata = '<div class="alert alert-danger" style="text-align:center; font-weight:bolder;">Kullanıcı adı kısmını boş bıraktınız.</div>';

			}elseif (uye_adi_var_mi($yeni_uye_adi) == '1') {
				
				$hata = '<div class="alert alert-danger" style="text-align:center; font-weight:bolder;">Bu kullanıcı adı kullanılıyor.</div>';				

			}else{

				$query = $db->prepare("INSERT INTO uyeler SET uye_adi = ?, uye_mail = ?, uye_sifre = ?, uye_firma = ?, uye_tipi = ?, uye_yetkiler = ?, uye_silik = ?");

				$insert = $query->execute(array($yeni_uye_adi,$yeni_uye_mail,$yeni_uye_sifre,$uye_firma,'0',$yeni_uye_yetki,'0'));

				header("Location:yonetim.php");

				exit();

			}
			
		}

		if (isset($_POST['yedekal'])) {
			
			yedekal();

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

		<div class="container-fluid mb-5">

			<div class="row">
				
				<div class="col-md-12">
					
					<?php echo $hata; ?>

				</div>

			</div>
			
			<div class="row">

				<div class="col-md-3 col-12"><!-- OKUMALARIN LİSTELENDİĞİ SAĞ TARAFTAKİ DİV -->

					<div class="div4">

						<form action="" method="POST">

							<h4>Yeni Kullanıcı Ekleme</h4>
								
							<div>
								
								<input type="text" name="yeni_uye_adi" placeholder="Kullanıcı Adı Giriniz" class="form-control" style="margin-bottom: 10px;">

							</div>
							
							<div>
								
								<input type="text" name="yeni_uye_mail" placeholder="Kullanıcının E-Posta Adresini Giriniz" class="form-control" style="margin-bottom: 10px;">

							</div>
							
							<div>
								
								<button type="submit" class="btn btn-primary btn-block" style="margin-bottom: 10px;" name="uye_ekle">Kullanıcı Ekle</button>

							</div>
								
							<div class="alert alert-info">
								
								Buraya ekleyeceğiniz kişinin şifresi varsayılan olarak 1234 olarak atanır. Kişinin oturum açtığında şifresini değiştirmesi tavsiye edilir.

							</div>

						</form>

					</div>					

				</div>

				<div class="col-md-3 col-12">
					
					<div class="div4">
						
						<h4>Künye</h4>

						<form action="" method="POST" enctype="multipart/form-data">
							
							<textarea name="sirketaciklama" class="form-control" rows="5"><?php echo $sirketaciklama; ?></textarea><br/>

							<button type="submit" class="btn btn-warning btn-sm" name="aciklamaguncelle">Kaydet</button>

						</form>

					</div>

				</div>

				<div class="col-md-3 col-12">

					<div class="div4">
						
						<h4>Logo</h4>

						<form action="" method="POST" enctype="multipart/form-data">
							
							<input type="file" name="uploadfile" style="margin-bottom: 10px;"><br/>

							<button type="submit" class="btn btn-warning btn-sm" name="logoguncelle">Güncelle</button>

						</form>

					</div>

					<div class="div4" style="text-align: center;">
						
						<form action="" method="POST">

							<button name="yedekal" class="btn btn-info btn-block" type="submit" style="margin-bottom: 7px;">

								Veritabanı Yedeğini Al

							</button>

						</form>

						<a href="islemler.php">
							
							<button class="btn btn-secondary btn-block" style="margin-bottom: 7px;">İşlem Kayıtlarına Bak</button>

						</a>

					</div>

				</div>

				<div class="col-md-3 col-12">
					<div class="div4">	
						<h4>Kullanıcılar</h4>
						<div class="row">
							<?php	
								$query = $db->query("SELECT * FROM uyeler WHERE uye_firma = '$uye_firma' AND uye_tipi != '2' AND uye_silik = '0' ORDER BY uye_adi ASC", PDO::FETCH_ASSOC);
								if ( $query->rowCount() ){
									foreach( $query as $row ){
										$kullanici_id = $row['uye_id'];
										$kullanici_adi = $row['uye_adi'];	
							?>
										<div class="col-6 my-2">
											<a href="profil.php?id=<?= $kullanici_id ?>"><?= $kullanici_adi ?></a>								
										</div>
							<?php
									}
								}
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php include 'template/script.php'; ?>
	</body>
</html>