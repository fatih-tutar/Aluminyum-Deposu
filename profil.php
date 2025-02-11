<?php 

	include 'fonksiyonlar/bagla.php'; 

	if ($girdi != '1') {
		
		header("Location:giris.php");

		exit();

	}elseif($girdi == '1'){

		$profil_id = guvenlik($_GET['id']);

		if ($profil_id != $user->id && $uye_tipi != 2) {
			
			header("Location:index.php");

			exit();

		}else{

			$profil = $db->query("SELECT * FROM uyeler WHERE id = '{$profil_id}'")->fetch(PDO::FETCH_ASSOC);
			$profil_yetkiler = guvenlik($profil['uye_yetkiler']);
			$profil_adi = guvenlik($profil['uye_adi']);
			$profil_mail = guvenlik($profil['uye_mail']);
			$profil_tel = guvenlik($profil['uye_tel']);
			$profil_tel_2 = guvenlik($profil['tel_2']);
			$profil_foto = guvenlik($profil['foto']);
			$unvan = guvenlik($profil['uye_unvan']);
			$adres = guvenlik($profil['adres']);
			$nufus_cuzdani = guvenlik($profil['nufus_cuzdani']);
			$is_basvuru_formu = guvenlik($profil['is_basvuru_formu']);
			$ikametgah_belgesi = guvenlik($profil['ikametgah_belgesi']);
			$saglik_raporu = guvenlik($profil['saglik_raporu']);
			$ise_giris_tarihi = guvenlik($profil['ise_giris_tarihi']);
			$profil_yetkileri_arrayi = explode(",", $profil_yetkiler);

		}

	if($uye_tipi != '3'){
		if (isset($_POST['bilgilerimiguncelle'])) {
			$uye_adi = guvenlik($_POST['uye_adi']);
			$uye_unvan = guvenlik($_POST['uye_unvan']);
			$uye_mail = guvenlik($_POST['uye_mail']);
			$uye_tel = guvenlik($_POST['uye_tel']);
			$tel_2 = guvenlik($_POST['tel_2']);
			$adres = guvenlik($_POST['adres']);
			$ise_giris_tarihi = guvenlik($_POST['ise_giris_tarihi']);
			if (!empty($_FILES['uploadfile']['name'])) {
				$temp = explode(".", $_FILES['uploadfile']['name']);
				$dosyaadi = $temp[0];
				$extension = end($temp);
				$randomsayi = rand(0,10000);;
				$upload_file = $dosyaadi.$randomsayi.".".$extension;
				move_uploaded_file($_FILES['uploadfile']['tmp_name'], "img/pp/".$upload_file);
			}else{
				$upload_file = $profil_foto;
			}
			if (!empty($_FILES['nufuscuzdani']['name'])) {
				$temp = explode(".", $_FILES['nufuscuzdani']['name']);
				$dosyaadi = $temp[0];
				$extension = end($temp);
				$randomsayi = rand(0,10000);;
				$nufuscuzdani = $dosyaadi.$randomsayi.".".$extension;
				move_uploaded_file($_FILES['nufuscuzdani']['tmp_name'], "img/belgeler/".$nufuscuzdani);
			}else{
				$nufuscuzdani = $nufus_cuzdani;
			}
			if (!empty($_FILES['isbasvuruformu']['name'])) {
				$temp = explode(".", $_FILES['isbasvuruformu']['name']);
				$dosyaadi = $temp[0];
				$extension = end($temp);
				$randomsayi = rand(0,10000);;
				$isbasvuruformu = $dosyaadi.$randomsayi.".".$extension;
				move_uploaded_file($_FILES['isbasvuruformu']['tmp_name'], "img/belgeler/".$isbasvuruformu);
			}else{
				$isbasvuruformu = $is_basvuru_formu;
			}
			if (!empty($_FILES['ikametgahbelgesi']['name'])) {
				$temp = explode(".", $_FILES['ikametgahbelgesi']['name']);
				$dosyaadi = $temp[0];
				$extension = end($temp);
				$randomsayi = rand(0,10000);;
				$ikametgahbelgesi = $dosyaadi.$randomsayi.".".$extension;
				move_uploaded_file($_FILES['ikametgahbelgesi']['tmp_name'], "img/belgeler/".$ikametgahbelgesi);
			}else{
				$ikametgahbelgesi = $ikametgah_belgesi;
			}
			if (!empty($_FILES['saglikraporu']['name'])) {
				$temp = explode(".", $_FILES['saglikraporu']['name']);
				$dosyaadi = $temp[0];
				$extension = end($temp);
				$randomsayi = rand(0,10000);;
				$saglikraporu = $dosyaadi.$randomsayi.".".$extension;
				move_uploaded_file($_FILES['saglikraporu']['tmp_name'], "img/belgeler/".$saglikraporu);
			}else{
				$saglikraporu = $saglik_raporu;
			}
			if (empty($uye_adi) === true) {				
				$hata = '<br/><div class="alert alert-danger" role="alert">Kullanıcı adınızı boş bıraktınız.</div>';
			}elseif (empty($uye_mail) === true) {
				$hata = '<br/><div class="alert alert-danger" role="alert">E-posta kısmını boş bıraktınız.</div>';
			}elseif (empty($uye_tel) === true) {
				$hata = '<br/><div class="alert alert-danger" role="alert">Telefon kısmını boş bıraktınız.</div>';
			}else{
				$query = $db->prepare("UPDATE uyeler SET uye_adi = ?, uye_mail = ?, uye_unvan = ?, uye_tel = ?, tel_2 = ?, ise_giris_tarihi = ?, adres = ?, foto = ?, nufus_cuzdani = ?, is_basvuru_formu = ?, ikametgah_belgesi = ?, saglik_raporu = ? WHERE id = ?");
				$guncelle = $query->execute(array($uye_adi, $uye_mail, $uye_unvan, $uye_tel, $tel_2, $ise_giris_tarihi, $adres, $upload_file, $nufuscuzdani, $isbasvuruformu, $ikametgahbelgesi, $saglikraporu, $profil_id));
				header("Location:profil.php?id=".$profil_id."&guncellendi");
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

			}elseif ($user->uye_sifre != md5($eski_sifre)) {
				
				$hata = '<br/><div class="alert alert-danger" role="alert" style="text-align:center;">Mevcut şifrenizi yanlış girdiniz. Lütfen tekrar deneyiniz.</div>';

			}elseif ($yeni_sifre != $sifre_tekrar) {
				
				$hata = '<br/><div class="alert alert-danger" role="alert" style="text-align:center;">Yeni şifreniz ve tekrarı birbiriyle örtüşmüyor. Lütfen tekrar deneyiniz.</div>';

			}else{

				$query = $db->prepare("UPDATE uyeler SET uye_sifre = ? WHERE id = ?");

				$guncelle = $query->execute(array($md5lisifre,$user->id));

				header("Location:profil.php?id=".$user->id."&guncellendi");

				exit();

			}			

		}

		if (isset($_GET['guncellendi'])) {
			
			$hata = '<br/><div class="alert alert-success" role="alert" style="text-align:center;">Bilgileriniz başarıyla güncellendi.</div>';

		}
		
		if (isset($_POST['yetkileriguncelle'])) {

			$kullanici_yetkileri = '0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0';

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

            if(isset($_POST['aracyetki'])){ $kullanici_yetkileri_arrayi[15] = 1; }

			$kullanici_yetkileri = implode(",", $kullanici_yetkileri_arrayi);

			$query = $db->prepare("UPDATE uyeler SET uye_yetkiler = ? WHERE id = ?");

			$guncelle = $query->execute(array($kullanici_yetkileri,$profil_id));

			header("Location:profil.php?id=".$profil_id);

			exit();

		}

	}}

?>

<!DOCTYPE html>

<html>

  <head>

    <title>Alüminyum Deposu</title>

    <?php include 'template/head.php'; ?>

	<style>
		.pp {
			width: 115px;
			margin-top:15px;
		}
	</style>

  </head>

  <body>

    <?php include 'template/banner.php' ?>

    <?= $hata; ?>

    <div class="container">

		<div class="div4" style="padding:10px;">
			<div class="row">
				<div class="col-md-2 col-12" style="display:flex; justify-content:center; align-items:center;">
					<img src="img/<?= empty($profil_foto) ? 'pp.png' : 'pp/'.$profil_foto ?>" alt="<?= $profil_adi ?> Profil Fotoğrafı" class="pp">
				</div>
				<div class="col-md-5 col-7 pt-3 pr-0">
					<h4><b><?= $profil_adi ?></b></h4>
					<h6><?= $unvan ?></h6>
					<h6><i class="fas fa-envelope mr-2"></i><?= $profil_mail ?></h6>
					<h6><i class="fas fa-mobile-alt mr-2" ></i><?= $profil_tel ?></h6>
					<h6><i class="fas fa-phone mr-2"></i><?= $profil_tel_2 ?></h6>
					<p class="mb-1"><i class="fas fa-map-marker mr-2"></i><?= $adres ?></p>
				</div>
				<div class="col-md-5 col-5 pt-3 pl-0" style="display:flex; justify-content:end; align-items:end;">
					<div style="text-align:right;">
						<h6>
							<i class="fas fa-calendar-alt mr-2"></i>
							<?= (new DateTime($ise_giris_tarihi))->format('d.m.Y') ?></h6>
						<h6>
							<i class="fas fa-id-card mr-2"></i>
							<?php if(empty($nufus_cuzdani)){ ?>
								Nüfus Cüzdanı Fotokopisi
							<?php }else{ ?>
								<a href="img/belgeler/<?= $nufus_cuzdani ?>" target="_blank">
									Nüfus Cüzdanı Fotokopisi
								</a>
							<?php } ?>
						</h6>
						<h6>
							<i class="fas fa-file-alt mr-2"></i>
							<?php if(empty($is_basvuru_formu)){ ?>
								İş Başvuru Formu
							<?php }else{ ?>
								<a href="img/belgeler/<?= $is_basvuru_formu ?>" target="_blank">
									İş Başvuru Formu
								</a>
							<?php } ?>
						</h6>
						<h6>
							<i class="fas fa-map-marker-alt mr-2"></i>
							<?php if(empty($ikametgah_belgesi)){ ?>
								İkâmetgâh Belgesi
							<?php }else{ ?>
								<a href="img/belgeler/<?= $ikametgah_belgesi ?>" target="_blank">
									İkâmetgâh Belgesi
								</a>
							<?php } ?>
						</h6>
						<h6>
							<i class="fas fa-file-medical mr-2"></i>
							<?php if(empty($saglik_raporu)){ ?>
								Sağlık Raporu
							<?php }else{ ?>
								<a href="img/belgeler/<?= $saglik_raporu ?>" target="_blank">
									Sağlık Raporu
								</a>
							<?php } ?>
						</h6>
					</div>
				</div>
			</div>
			<hr/>
			<div style="display:flex; justify-content:end;">
				<button class="btn btn-success btn-sm mr-2" onclick="return false" onmousedown="javascript:ackapa3('duzenlemedivi','sifredivi','yetkidivi');">Düzenle</button>
				<button class="btn btn-secondary btn-sm mr-2" onclick="return false" onmousedown="javascript:ackapa3('sifredivi','duzenlemedivi','yetkidivi');">Şifre Değiştir</button>
				<?php if($uye_tipi == 2) { ?>
					<button class="btn btn-warning btn-sm" onclick="return false" onmousedown="javascript:ackapa3('yetkidivi','sifredivi','duzenlemedivi');">Yetkiler</button>
				<?php } ?> 
			</div>
		</div>
    	
    	<div class="row">
    		
    		<div class="col-md-6 col-12">
    			
    			<div class="div4" id="duzenlemedivi" style="display:none;">
    	
			    	<form action="" method="POST" class="ml-1" enctype="multipart/form-data">
						<div class="row">
							<div class="col-md-6">
								<b>Fotoğraf</b>
								<input type="file" name="uploadfile" style="margin-bottom: 10px;"><br/>
								<b>Ad Soyad</b>
								<input type="text" class="form-control form-control-sm mb-1" placeholder="Kullanıcı Adı" name="uye_adi" value="<?= $profil_adi ?>">
								<b>Ünvan</b>
								<input type="text" class="form-control form-control-sm mb-1" placeholder="Ünvan" name="uye_unvan" value="<?= $unvan ?>">
								<b>E-posta Adresi</b>
								<input type="text" class="form-control form-control-sm mb-1" placeholder="E-posta Adresi" name="uye_mail" value="<?= $profil_mail ?>">
								<b>Telefon Numarası</b>
								<input type="text" class="form-control form-control-sm mb-1" placeholder="Telefon Numarası" name="uye_tel" value="<?= $profil_tel ?>">
								<b>İkinci Telefon Numarası</b>
								<input type="text" class="form-control form-control-sm mb-1" placeholder="İkinci Telefon Numarası" name="tel_2" value="<?= $profil_tel_2 ?>">
								<b>Adres</b>
								<textarea name="adres" id="adres" rows="3" class="form-control form-control-sm mb-1" placeholder="Adresinizi giriniz."><?= $adres ?></textarea>
							</div>
							<div class="col-md-6">
								<?php if($uye_tipi == 2){?>
									<b>İşe Giriş Tarihi</b>
									<input type="date" class="form-control form-control-sm mb-1" name="ise_giris_tarihi" value="<?= $ise_giris_tarihi ?>">	
								<?php } ?>
								<h6 class="mt-2"><b>Belgeler</b></h6>
								<a href="<?= empty($nufus_cuzdani) ? '#' : 'img/belgeler/'.$nufus_cuzdani ?>" target="_blank" class="btn btn-<?= empty($nufus_cuzdani) ? 'secondary' : 'primary' ?> btn-sm mb-1">Nüfus Cüzdanı Fotokopisi</a>
								<input type="file" name="nufuscuzdani" style="margin-bottom: 10px;"><br/>
								<a href="<?= empty($is_basvuru_formu) ? '#' : 'img/belgeler/'.$is_basvuru_formu ?>" target="_blank" class="btn btn-<?= empty($is_basvuru_formu) ? 'secondary' : 'primary' ?> btn-sm mb-1">İş Başvuru Formu</a>
								<input type="file" name="isbasvuruformu" style="margin-bottom: 10px;"><br/>
								<a href="<?= empty($ikametgah_belgesi) ? '#' : 'img/belgeler/'.$ikametgah_belgesi ?>" target="_blank" class="btn btn-<?= empty($ikametgah_belgesi) ? 'secondary' : 'primary' ?> btn-sm mb-1">İkametgah Belgesi</a>
								<input type="file" name="ikametgahbelgesi" style="margin-bottom: 10px;"><br/>
								<a href="<?= empty($saglik_raporu) ? '#' : 'img/belgeler/'.$saglik_raporu ?>" target="_blank" class="btn btn-<?= empty($saglik_raporu) ? 'secondary' : 'primary' ?> btn-sm mb-1">Sağlık Raporu</a>
								<input type="file" name="saglikraporu" style="margin-bottom: 10px;"><br/>
							</div>
						</div>
						<button type="submit" class="btn btn-success btn-block" name="bilgilerimiguncelle">Bilgileri Güncelle</button>
			    	</form>

			    </div>

    		</div>

    		<div class="col-md-6 col-12">

    			<div class="div4" id="sifredivi" style="display:none; padding:10px;">
    			
    				<h5 style="margin-top: 10px;"><b>Şifre Değiştir</b></h5>

			    	<form action="" method="POST">

			    		<input type="password" class="form-control" style="margin-bottom: 10px;" name="eski_sifre" placeholder="Mevcut şifreyi giriniz.">

			    		<input type="password" class="form-control" style="margin-bottom: 10px;" name="yeni_sifre" placeholder="Yeni şifreyi giriniz.">

			    		<input type="password" class="form-control" style="margin-bottom: 10px;" name="sifre_tekrar" placeholder="Yeni şifreyi tekrar ediniz.">

			    		<button type="submit" class="btn btn-primary btn-block" name="sifreyidegistir">Şifreyi Güncelle</button>

			    	</form>

			    </div>

    		</div>

    	</div>
		<?php if($uye_tipi == 2){ ?>
			<div class="div4 px-3" id="yetkidivi" style="display:none;">
				<h5 style="margin-top: 10px;"><b>Yetkiler</b></h5>
				<form action="" method="POST">
					<div class="row">
						<div class="col-md-2 col-4">
							<div class="form-group">
								<input type="checkbox" id="adetCheckbox" name="yetkiadet" <?= $profil_yetkileri_arrayi[11] == '1' ? 'checked' : '' ?>>
								<label for="adetCheckbox">Adet</label>
							</div>
						</div>
						<div class="col-md-2 col-4">
							<div class="form-group">
								<input type="checkbox" id="paletCheckbox" name="yetkipalet" <?= $profil_yetkileri_arrayi[12] == '1' ? 'checked' : '' ?>>
								<label for="paletCheckbox">Palet</label>
							</div>
						</div>
						<div class="col-md-2 col-4 px-0">
							<div class="form-group">
								<input type="checkbox" id="alkopCheckbox" name="yetkialkop" <?= $profil_yetkileri_arrayi[13] == '1' ? 'checked' : '' ?>>
								<label for="alkopCheckbox">Alkop</label>
							</div>
						</div>
						<div class="col-md-2 col-4">
							<div class="form-group">
								<input type="checkbox" id="alisCheckbox" name="yetkialis" <?= $profil_yetkileri_arrayi[0] == '1' ? 'checked' : '' ?>>
								<label for="alisCheckbox">Alış</label>
							</div>
						</div>
						<div class="col-md-2 col-4">
							<div class="form-group">
								<input type="checkbox" id="satisCheckbox" name="yetkisatis" <?= $profil_yetkileri_arrayi[7] == '1' ? 'checked' : '' ?>>
								<label for="satisCheckbox">Satış</label>
							</div>
						</div>
						<div class="col-md-2 col-4 px-0">
							<div class="form-group">
								<input type="checkbox" id="fabrikaCheckbox" name="yetkifabrika" <?= $profil_yetkileri_arrayi[1] == '1' ? 'checked' : '' ?>>
								<label for="fabrikaCheckbox">Fabrika</label>
							</div>
						</div>
						<div class="col-md-2 col-4">
							<div class="form-group">
								<input type="checkbox" id="teklifCheckbox" name="yetkiteklif" <?= $profil_yetkileri_arrayi[2] == '1' ? 'checked' : '' ?>>
								<label for="teklifCheckbox">Teklif</label>
							</div>
						</div>
						<div class="col-md-2 col-4">
							<div class="form-group">
								<input type="checkbox" id="siparisCheckbox" name="yetkisiparis" <?= $profil_yetkileri_arrayi[3] == '1' ? 'checked' : '' ?>>
								<label for="siparisCheckbox">Sipariş</label>
							</div>
						</div>
						<div class="col-md-2 col-4 px-0">
							<div class="form-group">
								<input type="checkbox" id="sevkiyatCheckbox" name="sevkiyatyetki" <?= $profil_yetkileri_arrayi[10] == '1' ? 'checked' : '' ?>>
								<label for="sevkiyatCheckbox">Sevkiyat</label>
							</div>
						</div>
						<div class="col-md-2 col-4">
							<div class="form-group">
								<input type="checkbox" id="duzenlemeCheckbox" name="yetkiduzenleme" <?= $profil_yetkileri_arrayi[4] == '1' ? 'checked' : '' ?>>
								<label for="duzenlemeCheckbox">Düzenleme</label>
							</div>
						</div>
						<div class="col-md-2 col-4">
							<div class="form-group">
								<input type="checkbox" id="islemlerCheckbox" name="yetkiislemlerigorme" <?= $profil_yetkileri_arrayi[5] == '1' ? 'checked' : '' ?>>
								<label for="islemlerCheckbox">İşlemler</label>
							</div>
						</div>
						<div class="col-md-2 col-4 px-0">
							<div class="form-group">
								<input type="checkbox" id="toplamCheckbox" name="toplamgorme" <?= $profil_yetkileri_arrayi[8] == '1' ? 'checked' : '' ?>>
								<label for="toplamCheckbox">Toplam Görme</label>
							</div>
						</div>
						<div class="col-md-2 col-4 pr-0">
							<div class="form-group">
								<input type="checkbox" id="gelengidenCheckbox" name="gelengidenigorme" <?= $profil_yetkileri_arrayi[6] == '1' ? 'checked' : '' ?>>
								<label for="gelengidenCheckbox">Gelen Giden</label>
							</div>
						</div>
						<div class="col-md-2 col-4">
							<div class="form-group">
								<input type="checkbox" id="ofisCheckbox" name="ofisyetki" <?= $profil_yetkileri_arrayi[14] == '1' ? 'checked' : '' ?>>
								<label for="ofisCheckbox">Ofis</label>
							</div>
						</div>
						<div class="col-md-2 col-4 px-0">
							<div class="form-group">
								<input type="checkbox" id="ziyaretCheckbox" name="ziyaretyetki" <?= $profil_yetkileri_arrayi[9] == '1' ? 'checked' : '' ?>>
								<label for="ziyaretCheckbox">Ziyaretler</label>
							</div>
						</div>
                        <div class="col-md-2 col-4">
                            <div class="form-group">
                                <input type="checkbox" id="aracCheckbox" name="aracyetki" <?= $profil_yetkileri_arrayi[15] == '1' ? 'checked' : '' ?>>
                                <label for="ziyaretCheckbox">Araçlar</label>
                            </div>
                        </div>
                        <div class="col-md-2 col-4">
                        </div>
                        <div class="col-md-2 col-4">
                        </div>
						<div class="col-md-2 col-4">
							<button type="submit" class="btn btn-warning btn-sm btn-block" name="yetkileriguncelle">Kaydet</button>
						</div>
						<div class="col-md-2 col-4" style="text-align:right;">						
							<button type="submit" class="btn btn-secondary btn-sm btn-block" name="pasiflestir">Pasifleştir</button>
						</div>
						<div class="col-md-2 col-4" style="text-align:right;">						
						<button type="submit" name="kullanicisil" class="btn btn-danger btn-sm btn-block" onclick="return confirmForm('Bu kullanıcıyı geri dönüşü olmayacak şekilde sileceksiniz, emin misiniz?');">Kullanıcıyı Sil</button>
						</div>
					</div>
				</form>
			</div>
		<?php } ?>

    </div>

	<br/><br/><br/><br/><br/><br/>

    <?php include 'template/script.php'; ?>

</body>
</html>