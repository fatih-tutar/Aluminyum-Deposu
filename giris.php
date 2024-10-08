<?php 

	include 'fonksiyonlar/bagla.php';

	if ($girdi == 1) {
		
		header("Location:index.php");

		exit();

	}

	if (isset($_POST['giris'])) {
		
		$uye_adi = guvenlik($_POST['uye_adi']);

		$sifre = guvenlik($_POST['sifre']);

		$sifreli = md5($sifre);

		if (empty($uye_adi) === true) {

			$hata = '<div class="alert alert-danger" role="alert">E-posta kısmını boş bıraktınız.</div>';

		}elseif(empty($sifre) === true){

			$hata = '<div class="alert alert-danger" role="alert">Şifre kısmını boş bıraktınız.</div>';
		}

		elseif(giris($uye_adi,$sifreli) === false){

			$hata = '<div class="alert alert-danger" role="alert">E-posta veya şifreyi yanlış girdiniz.</div>';
		
		}elseif(pasifmi($uye_adi) == '1'){

			$hata = '<div class="alert alert-danger" role="alert">Üyeliğiniz pasifleştirilmiştir.</div>';

		}else{

			if (is_numeric(giris($uye_adi,$sifreli)) === true) {
				
				$_SESSION['uye_id'] = giris($uye_adi,$sifreli);

				header("Location: index.php");

				exit();

			}
			
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

		<div class="container">

			<br/><br/><br/><br/>

			<div class="row">

				<div class="col-xl-4 col-lg-4 col-md-3 col-sm-2 col-12"></div>
				
				<div class="col-xl-4 col-lg-4 col-md-6 col-sm-8 col-12">

					<div class="div2">

						<?php echo $hata; ?>
					
						<form action="" method="POST">

							<h2 style="text-align:center;">Kullanıcı Girişi</h2>

							<div class="input-group mb-3">
								
								<input type="text" class="form-control" placeholder="Kullanıcı Adı" aria-label="uye_adi" aria-describedby="basic-addon1" name="uye_adi" style="text-align: center; font-weight: bold; border:2px grey solid;">
							
							</div>

							<div class="input-group mb-3">
								
								<input type="password" class="form-control" placeholder="Şifre" aria-label="sifre" aria-describedby="basic-addon1" name="sifre" style="text-align: center; font-weight: bold; border:2px grey solid;">
							
							</div>

							<div class="input-group mb-3">
								
								<button type="submit" class="btn btn-danger btn-block" name="giris">Giriş Yap</button>

							</div>
						
						</form>

					</div>

				</div>

				<div class="col-xl-4 col-lg-4 col-md-3 col-sm-2 col-12"></div>

			</div>

		</div>

		<?php include 'template/script.php'; ?>

	</body>

</html>

