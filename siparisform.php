<?php

	include 'fonksiyonlar/bagla.php';

	if($girdi == '0'){

		header("Location:index.php");

		exit();

	}else{

		if($uye_tipi == '0'){

			header("Location:index.php");

			exit();

		}else{

			$formid = guvenlik($_GET['id']);

			$formbilgileri = $db->query("SELECT * FROM siparisformlari WHERE formid = '{$formid}' AND silik = '0'")->fetch(PDO::FETCH_ASSOC);

			$hazirlayankisi = $formbilgileri['hazirlayankisi'];

			$ilgilikisi = $formbilgileri['ilgilikisi'];

			$siparisler = $formbilgileri['siparisler'];

			$siparisleripatlat = explode(",", $siparisler);

			$fabrikaid = $formbilgileri['fabrikaid'];

			$saniye = $formbilgileri['saniye'];

			$tarih = date("d-m-Y", $saniye);

		}

	}

?>

<!DOCTYPE html>
<html>
<head>
	<title></title>
	<?php include 'template/head.php'; ?>

	</script>
</head>
<body>
	<div class="container" style="background: white;">
		
		<div class="row">
			
			<div class="col-md-4" style="text-align: center; padding: 5px;"><img src="img/doga.jpg" style="width: 170px; height: auto;"></div>

			<div class="col-md-8" style="padding: 30px 0px 30px 0px; text-align: center;"><p style="color:green; font-size: 18px; font-weight: bolder;">Gerçekten ihtiyacınız yoksa bu mesajı kağıda basmayınız.</p></div>

		</div>

		<div class="row">
			
			<div class="col-md-4" style="text-align: center;"><img src="img/file/<?php echo $sirketlogo; ?>" style="width: 370px; height: auto;"></div>

			<div class="col-md-8" style="text-align: center; padding: 0px 30px 0px 30px;">

				<p style="font-size: 15px;">
			
					<?php $sirketaciklama = str_replace("\n", "<br/>", $sirketaciklama); echo $sirketaciklama; ?>

				</p>

			</div>

		</div>

		<div class="row">
			
			<div class="col-md-12" style="text-align: center; padding: 20px;">
				
				<b>MALZEME TALEP FORMU</b>

			</div>

		</div>

		<div class="row" style="padding: 20px;">
			
			<div class="col-md-3">
				
				<b>Hazırlayan Kişi : </b>

			</div>

			<div class="col-md-4">
				
				<?php echo $hazirlayankisi; ?>

			</div>

			<div class="col-md-2">
				
				<b>Tarih :</b>

			</div>

			<div class="col-md-3">
				
				<?php echo $siparistarih; ?>

			</div>

		</div>

		<div class="row" style="padding: 20px;">
			
			<div class="col-md-3">
				
				<b>Talep Edilen Fabrika : </b>

			</div>

			<div class="col-md-4">
				
				<?php echo $urun_fabrika_adi; ?>

			</div>

			<div class="col-md-2">
				
				<b>İlgili Kişi : </b>

			</div>

			<div class="col-md-3">
				
				<?php echo $ilgilikisi; ?>

			</div>

		</div>

		<div class="row" style="padding: 20px;">
			
			<div class="col-md-1"><b>S.No</b></div>

			<div class="col-md-3"><b>Malzemenin Cinsi</b></div>

			<div class="col-md-2"><b>Boy</b></div>

			<div class="col-md-2"><b>Miktar Adet</b></div>			

		</div>

		<?php

			$a = 0;

			foreach ($siparisleripatlat as $key => $value) {
				
				$siparisbilgileri = $db->query("SELECT * FROM siparis WHERE siparis_id = '{$value}' AND silik = '0'")->fetch(PDO::FETCH_ASSOC);

				$a++;

				$siparis_id = $siparisbilgileri['siparis_id'];

				$urun_adi = $siparisbilgileri['urun_adi'];

				$urun_siparis_aded = $siparisbilgileri['urun_siparis_aded'];

				$siparissaniye = $siparisbilgileri['siparissaniye'];

				$siparisboy = $siparisbilgileri['siparisboy'];

				$urun_siparis_aded = $siparisbilgileri['urun_siparis_aded'];

				$siparisintarihi = date("d-m-Y",$siparissaniye);

				$urun_id = $siparisbilgileri['urun_id'];

				$katbilcek = $db->query("SELECT * FROM urun WHERE urun_id = '{$urun_id}'")->fetch(PDO::FETCH_ASSOC);

				$kategori_bir = $katbilcek['kategori_bir'];

				$katadcek = $db->query("SELECT * FROM kategori WHERE kategori_id = '{$kategori_bir}'")->fetch(PDO::FETCH_ASSOC);

				$kategori_bir_adi = $katadcek['kategori_adi'];

				$kategori_iki = $katbilcek['kategori_iki'];

				$katadcek = $db->query("SELECT * FROM kategori WHERE kategori_id = '{$kategori_iki}'")->fetch(PDO::FETCH_ASSOC);

				$kategori_iki_adi = $katadcek['kategori_adi'];

			?>

				<hr style="border:1px black solid;" />

				<div class="row" style="padding: 20px;">
		
					<div class="col-md-1"><?php echo $a; ?></div>

					<div class="col-md-3"><?php echo $urun_adi." ".$kategori_iki_adi; ?></div>

					<div class="col-2"><?php echo $siparisboy." boyunda "; ?></div>

					<div class="col-2"><?php echo $urun_siparis_aded." adet "; ?></div>

					<div class="col-2"><?php echo $kategori_bir_adi; ?></div>

				</div>

			<?php

			}

		?>

		<div class="row" style="padding: 20px;">
			
			<div class="col-md-12">
				
				<p>
					
					* Aksi Belirtmedikçe Paketlemede REKLAMSIZ  VE ŞEFFAF Ambalaj Kullanılacaktır<br/>

					* Özel Boydaki Siparişlerimiz İçin Hassas Boy Kesim Bedeli Tarafımıza Ayrıca Bildirirmelidir Aksi Taktirde Tutar Kabul Edılmeyecektir.<br/>

					* Eloksallı Ürünlerde  Mikron 10-12 Altına Düşmemeli Ve Paketlemesinde Cizilmeye Karşı Önlem Alınmalıdır.<br/>

					* Kesim Dahil Tüm Ölçülerde Özel Tolerans Belirtilmedikçe EN-755-9 Standartları na Uygun Üretim Yapılmalıdır.<br/>

					* Hatalı Ürünler 15  İş Günü İçinde Tespit Edilip İade Edilecektir,Doğacak Olan Maddi Manevi Zarar Tarafınıza Yansıtılıcaktır.<br/>

					* Firmamızın Araçları Haricinde Nakliye Esnasında Yaşanacak Sorun Ve Sıkıntılarından OSMANLI ALM.SAN.TİC.LTD  Sorumlu Değildir.<br/>									

				</p>

			</div>

		</div>

	</div>

	<?php include 'template/script.php'; ?>

</body>

</html>