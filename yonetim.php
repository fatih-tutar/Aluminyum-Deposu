<?php 

	include 'functions/init.php';

	if (!isLoggedIn()) {
		
		header("Location:login.php");

		exit();

	}elseif (isLoggedIn()) {

		if($user->type == '0' || $user->type == '1'){

			header("Location:index.php");

			exit();

		}

        if($user->type != '3'){

            if (isset($_POST['aciklamaguncelle'])) {

                $description = yollaf($_POST['description']);

                $query = $db->prepare("UPDATE companies SET description = ? WHERE id = ?");

                $update = $query->execute(array($description,$user->company_id));

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

                $query = $db->prepare("UPDATE companies SET photo = ? WHERE id = ?");

                $guncelle = $query->execute(array($upload_file,$user->company_id));

                header("Location:yonetim.php");

                exit();

            }

            if (isset($_POST['kullanicisil'])) {

                $kullanici_id = guvenlik($_POST['kullanici_id']);

                $query = $db->prepare("UPDATE users SET is_deleted = ? WHERE id = ?");

                $guncelle = $query->execute(array('1',$kullanici_id));

                header("Location:yonetim.php");

                exit();

            }

            if (isset($_POST['uye_ekle'])) {

                $name = guvenlik($_POST['name']);

                $email = guvenlik($_POST['email']);

                $password = "81dc9bdb52d04dc20036dbd8313ed055";

                $yeni_uye_yetki = "0,0,0,0,0,0,0,0,0,0,0,0,0,0,0";

                if (empty($name)) {

                    $error = '<div class="alert alert-danger" style="text-align:center; font-weight:bolder;">Kullanıcı adı kısmını boş bıraktınız.</div>';

                }elseif (checkUserById($name) == '1') {

                    $error = '<div class="alert alert-danger" style="text-align:center; font-weight:bolder;">Bu kullanıcı adı kullanılıyor.</div>';

                }else{

                    $query = $db->prepare("INSERT INTO users SET name = ?, email = ?, password = ?, company_id = ?, type = ?, permissions = ?, is_deleted = ?");

                    $insert = $query->execute(array($name,$email,$password,$user->company_id,'0',$yeni_uye_yetki,'0'));

                    header("Location:yonetim.php");

                    exit();

                }

            }

            if (isset($_POST['yedekal'])) {

                yedekal();

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

	<style>
		.dikey-ortala {
			display: flex;
			align-items: center;
		}
	</style>

	<body>

		<?php include 'template/banner.php' ?>

		<div class="container-fluid mb-5">

			<div class="row">
				
				<div class="col-md-12">
					
					<?= $error; ?>

				</div>

			</div>
			
			<div class="row">

				<div class="col-md-9 col-12">
					
					<div class="div4">	
						<h4>Kullanıcılar</h4>
						<div class="row">
							<?php	
								$query = $db->query("SELECT * FROM users WHERE company_id = '$user->company_id' AND type != '2' AND is_deleted = '0' ORDER BY name ASC", PDO::FETCH_ASSOC);
								if ( $query->rowCount() ){
									foreach( $query as $row ){
										$kullanici_id = guvenlik($row['id']);
										$kullanici_adi = guvenlik($row['name']);
										$kullanici_unvan = guvenlik($row['title']);
							?>
										<div class="col-6 my-2">
											<a href="profil.php?id=<?= $kullanici_id ?>">
												<div class="dikey-ortala">
													<img src="img/pp.png" alt="" style="width:60px; border-radius:50%;">
													<div>
														<?= $kullanici_adi ?><br/><small><?= $kullanici_unvan ?></small>
													</div>
												</div>
											</a>								
										</div>
							<?php
									}
								}
							?>
						</div>
					</div>

					<div class="div4">

						<form action="" method="POST">

							<h4 class="pl-1 pt-1">Yeni Kullanıcı Ekleme</h4>
							<div class="row">
								<div class="col-md-5 col-12">
									<input type="text" name="name" placeholder="Kullanıcı Adı Giriniz" class="form-control" style="margin-bottom: 10px;">
								</div>
								<div class="col-md-5 col-12">
									<input type="text" name="email" placeholder="Kullanıcının E-Posta Adresini Giriniz" class="form-control" style="margin-bottom: 10px;">
								</div>					
								<div class="col-md-2 col-12">
									<button type="submit" class="btn btn-primary btn-block" style="margin-bottom: 10px;" name="uye_ekle">Ekle</button>
								</div>
								<div class="col-md-12 col-12">
									<div class="alert alert-info">				
										Yeni eklenen kişinin şifresi 1234 olarak atanır. Kişinin oturum açtığında şifresini değiştirmesi tavsiye edilir.
									</div>
								</div>
							</div>
						</form>
					</div>	
				</div>

				<div class="col-md-3 col-12">

					<div class="div4">
						
						<h4>Künye</h4>

						<form action="" method="POST" enctype="multipart/form-data">
							
							<textarea name="description" class="form-control" rows="5"><?= $company->description; ?></textarea><br/>

							<button type="submit" class="btn btn-warning btn-sm" name="aciklamaguncelle">Kaydet</button>

						</form>

					</div>

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
			</div>
		</div>
		<?php include 'template/script.php'; ?>
	</body>
</html>