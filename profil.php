<?php 

	include 'fonksiyonlar/bagla.php'; 

	if ($girdi != '1') {
		
		header("Location:giris.php");

		exit();

	}elseif($girdi == '1'){

		$profil_id = guvenlik($_GET['id']);

		if ($profil_id != $uye_id) {
			
			header("Location:index.php");

			exit();

		}

		if (isset($_POST['bilgilerimiguncelle'])) {
			
			$uye_adi = guvenlik($_POST['uye_adi']);

			$uye_mail = guvenlik($_POST['uye_mail']);

			if (empty($uye_adi) === true) {
				
				$hata = '<br/><div class="alert alert-danger" role="alert">Kullanıcı adınızı boş bıraktınız.</div>';

			}elseif (empty($uye_mail) === true) {
				
				$hata = '<br/><div class="alert alert-danger" role="alert">E-posta kısmını boş bıraktınız.</div>';

			}else{

				$query = $db->prepare("UPDATE uyeler SET uye_adi = ?, uye_mail = ? WHERE uye_id = ?"); 

				$guncelle = $query->execute(array($uye_adi,$uye_mail,$uye_id));

				header("Location:profil.php?id=".$uye_id."&guncellendi");

				exit();

			}

		}

		if (isset($_POST['sifreyidegistir'])) {
			
			$eski_sifre = guvenlik($_POST['eski_sifre']);

			$yeni_sifre = guvenlik($_POST['yeni_sifre']);

			$sifre_tekrar = guvenlik($_POST['sifre_tekrar']);

			$md5lisifre = md5($yeni_sifre);

			if (empty($eski_sifre) === true || empty($yeni_sifre) === true || empty($sifre_tekrar) === true) {
				
				$hata = '<br/><div class="alert alert-danger" role="alert" style="text-align:center;">Formda boş bırakılan alanlar var lütfen kontrol ediniz.</div>';

			}elseif ($uye_sifre != md5($eski_sifre)) {
				
				$hata = '<br/><div class="alert alert-danger" role="alert" style="text-align:center;">Mevcut şifrenizi yanlış girdiniz. Lütfen tekrar deneyiniz.</div>';

			}elseif ($yeni_sifre != $sifre_tekrar) {
				
				$hata = '<br/><div class="alert alert-danger" role="alert" style="text-align:center;">Yeni şifreniz ve tekrarı birbiriyle örtüşmüyor. Lütfen tekrar deneyiniz.</div>';

			}else{

				$query = $db->prepare("UPDATE uyeler SET uye_sifre = ? WHERE uye_id = ?"); 

				$guncelle = $query->execute(array($md5lisifre,$uye_id));

				header("Location:profil.php?id=".$uye_id."&guncellendi");

				exit();

			}			

		}

		if (isset($_GET['guncellendi'])) {
			
			$hata = '<br/><div class="alert alert-success" role="alert" style="text-align:center;">Bilgileriniz başarıyla güncellendi.</div>';

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

    <?php echo $hata; ?>

    <div class="container">
    	
    	<div class="row">
    		
    		<div class="col-md-4 col-12">
    			
    			<div class="div4">

    				<h5 style="margin-top: 10px;"><b>Kullanıcı Bilgileri Güncelleme</b></h5>
    	
			    	<form action="" method="POST">
			    		
			    		<input type="text" class="form-control" style="margin-bottom: 10px;" name="uye_adi" value="<?php echo $uye_adi; ?>">

			    		<input type="text" class="form-control" style="margin-bottom: 10px;" name="uye_mail" value="<?php echo $uye_mail; ?>">

			    		<button type="submit" class="btn btn-primary btn-block" name="bilgilerimiguncelle">Güncelle</button>

			    	</form>

			    </div>

    		</div>

    		<div class="col-md-4 col-12">

    			<div class="div4">
    			
    				<h5 style="margin-top: 10px;"><b>Şifre Değiştir</b></h5>

			    	<form action="" method="POST">

			    		<input type="password" class="form-control" style="margin-bottom: 10px;" name="eski_sifre" placeholder="Mevcut şifreyi giriniz.">

			    		<input type="password" class="form-control" style="margin-bottom: 10px;" name="yeni_sifre" placeholder="Yeni şifreyi giriniz.">

			    		<input type="password" class="form-control" style="margin-bottom: 10px;" name="sifre_tekrar" placeholder="Yeni şifreyi tekrar ediniz.">

			    		<button type="submit" class="btn btn-primary btn-block" name="sifreyidegistir">Bilgileri Güncelle</button>

			    	</form>

			    </div>

    		</div>

    	</div>

    </div>

    <?php include 'template/script.php'; ?>

</body>
</html>