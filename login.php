<?php 

	include 'functions/init.php';

	if (isLoggedIn()) {
		
		header("Location:index.php");

		exit();

	}

	if (isset($_POST['giris'])) {
		$name = guvenlik($_POST['name']);
		$sifre = guvenlik($_POST['sifre']);
		$sifreli = md5($sifre);
		if (empty($name) === true) {
			$error = '<div class="alert alert-danger" role="alert">E-posta kısmını boş bıraktınız.</div>';
		}elseif(empty($sifre) === true){
			$error = '<div class="alert alert-danger" role="alert">Şifre kısmını boş bıraktınız.</div>';
		}elseif(giris($name,$sifreli) === false){
			$error = '<div class="alert alert-danger" role="alert">E-posta veya şifreyi yanlış girdiniz.</div>';
		}elseif(pasifmi($name) == '1'){
			$error = '<div class="alert alert-danger" role="alert">Üyeliğiniz pasifleştirilmiştir.</div>';
		}else{
			if (is_numeric(giris($name,$sifreli)) === true) {
				$_SESSION['user_id'] = giris($name,$sifreli);
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

                    <div class="card p-4 shadow-lg">
                        <?= $error; ?>
                        <h4 class="text-center mb-3">Giriş Yap</h4>
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Kullanıcı Adı</label>
                                <input type="text" class="form-control" id="username" name="name" placeholder="Kullanıcı adınızı girin">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Şifre</label>
                                <input type="password" class="form-control" id="password" name="sifre" placeholder="Şifrenizi girin">
                            </div>
                            <button type="submit" class="btn btn-primary w-100" name="giris">Giriş Yap</button>
                        </form>
                    </div>
				</div>

				<div class="col-xl-4 col-lg-4 col-md-3 col-sm-2 col-12"></div>

			</div>

		</div>

		<?php include 'template/script.php'; ?>

	</body>

</html>

