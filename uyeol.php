<?php 

	include 'fonksiyonlar/bagla.php';

	header("Location:giris.php");

	if (isLoggedIn()) {
		
		header("Location:index.php");

		exit();

	}else{

		if (isset($_POST['uyeol'])) {
			
			$uyeadi = guvenlik($_POST['uyeadi']);

			$eposta = guvenlik($_POST['eposta']);

			$sifre = guvenlik($_POST['sifre']);

			$sifretekrar = guvenlik($_POST['sifretekrar']);

			$yeni_uye_yetki = "0,0,0,0,0,0";

			if (empty($uyeadi)) {
				
				$hata = '<div class="alert alert-danger" style="text-align:center; font-weight:bolder;">Kullanıcı adı kısmını boş bıraktınız.</div>';

			}elseif (empty($eposta)) {
			
				$hata = '<div class="alert alert-danger" style="text-align:center; font-weight:bolder;">E-posta kısmını boş bıraktınız.</div>';

			}elseif (empty($sifre)) {
				
				$hata = '<div class="alert alert-danger" style="text-align:center; font-weight:bolder;">Şifre kısmını boş bıraktınız.</div>';

			}elseif (empty($sifretekrar)) {
				
				$hata = '<div class="alert alert-danger" style="text-align:center; font-weight:bolder;">Şifre tekrarı kısmını boş bıraktınız.</div>';

			}elseif ($sifretekrar != $sifre) {
				
				$hata = '<div class="alert alert-danger" style="text-align:center; font-weight:bolder;">Şifre ve şifre tekrarı birbiriyle uyuşmuyor.</div>';				

			}elseif (checkUserById($uyeadi) == '1') {
				
				$hata = '<div class="alert alert-danger" style="text-align:center; font-weight:bolder;">Bu kullanıcı adı kullanılıyor.</div>';				

			}else{

				$sifre = md5($sifre);

				$query = $db->prepare("INSERT INTO users SET name = ?, email = ?, password = ?, company_id = ?, type = ?, permissions = ?, is_deleted =?");

				$insert = $query->execute(array($uyeadi,$eposta,$sifre,'0','0',$yeni_uye_yetki,'1'));

				header("Location:uyeol.php?ut"); // ut = ÜYELİK TAMAMLANDI

				exit();

			}

		}

		if (isset($_GET['ut'])) {
			
			$hata = '<div class="alert alert-success" style="text-align:center; font-weight:bolder;">Üyeliğiniz başarıyla oluşturuldu. Site yönetiminin onayından sonra stok programınızı kullanmaya başlayabilirsiniz.</div>';

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

		<?= $hata; ?>

		<div class="container">

			<br/><br/><br/><br/>

			<div class="row">

				<div class="col-xl-4 col-lg-3 col-md-3 col-sm-2 col-1"></div>
				
				<div class="col-xl-4 col-lg-6 col-md-6 col-sm-8 col-10">

					<div class="div2"  style="padding: 40px 40px 25px 40px;">
					
						<form action="" method="POST">

							<div class="input-group mb-3">
								
								<input type="text" class="form-control" placeholder="Kullanıcı Adı" aria-label="uyeadi" aria-describedby="basic-addon1" name="uyeadi" style="text-align: center; font-weight: bold; border:2px grey solid;">
							
							</div>

							<div class="input-group mb-3">
								
								<input type="text" class="form-control" placeholder="E-posta" aria-label="eposta" aria-describedby="basic-addon1" name="eposta" style="text-align: center; font-weight: bold; border:2px grey solid;">
							
							</div>

							<div class="input-group mb-3">
								
								<input type="password" class="form-control" placeholder="Şifre" aria-label="sifre" aria-describedby="basic-addon1" name="sifre" style="text-align: center; font-weight: bold; border:2px grey solid;">
							
							</div>

							<div class="input-group mb-3">
								
								<input type="password" class="form-control" placeholder="Şifre Tekrarı" aria-label="sifre" aria-describedby="basic-addon1" name="sifretekrar" style="text-align: center; font-weight: bold; border:2px grey solid;">
							
							</div>

							<div class="input-group mb-3">
								
								<button type="submit" class="btn btn-danger btn-block" name="uyeol">Üye Ol</button>

							</div>
						
						</form>

					</div>

				</div>

				<div class="col-xl-4 col-lg-3 col-md-3 col-sm-2 col-1"></div>

			</div>

		</div>

		<?php include 'template/script.php'; ?>

	</body>

</html>

