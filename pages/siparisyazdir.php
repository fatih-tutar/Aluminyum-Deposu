<?php

	require_once __DIR__.'/../config/init.php';

	if (!isLoggedIn()) {
		header("Location:/login");
		exit();
	}else{

		$urun_fabrika_id = guvenlik($_GET['id']);

		$query = $db->query("SELECT * FROM siparis WHERE urun_fabrika_id = '{$urun_fabrika_id}' AND silik = '0' ORDER BY siparis_id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);

		$hazirlayankisi = $query['hazirlayankisi'];

		$siparistarih = date("d-m-Y", time());

		$factoryInfo = $db->query("SELECT * FROM factories WHERE id = '{$urun_fabrika_id}'")->fetch(PDO::FETCH_ASSOC);

		$factoryId = $factoryInfo['id'];
		$urun_fabrika_adi = $factoryInfo['name'];

		$ilgilikisi = $query['ilgilikisi'];

		if (isset($_POST['formkaydet'])) {

			$siparislistesi = guvenlik($_POST['siparislistesi']);

			$siparislistesipatlat = explode(",", $siparislistesi);

			foreach ($siparislistesipatlat as $key => $value) {
					
				$query = $db->prepare("UPDATE siparis SET formda = ? WHERE siparis_id = ?"); 

				$guncelle = $query->execute(array('1',$value));

			}
			
			$query = $db->prepare("INSERT INTO siparisformlari SET siparisler = ?, fabrikaid = ?, saniye = ?, sirketid = ?, silik = ?");

			$insert = $query->execute(array($siparislistesi,$urun_fabrika_id,time(),$user->company_id,'0'));

			header("Location:/yonetim");

			exit();

		}

	}
?>

<!DOCTYPE html>
<html>
<head>
	<title></title>
	<?php include ROOT_PATH.'/template/head.php'; ?>

	</script>
</head>
<body>
	<div class="container-fluid" style="background: white;">
		
		<div class="row">
			
            <div class="col-md-4" style="text-align: center; padding: 5px;"><img src="files/img/doga.jpg" style="width: 170px; height: auto;"></div>

			<div class="col-md-8" style="padding: 30px 0px 30px 0px; text-align: center;"><p style="color:green; font-size: 18px; font-weight: bolder;">Gerçekten ihtiyacınız yoksa bu mesajı kağıda basmayınız.</p></div>

		</div>

		<div class="row">
			
            <div class="col-md-4" style="text-align: center;"><img src="files/company/<?= $company->photo; ?>" style="width: 370px; height: auto;"></div>

			<div class="col-md-8" style="text-align: center; padding: 0px 30px 0px 30px;">

				<p style="font-size: 15px;">
			
					<?= str_replace("\n", "<br/>", $company->description); ?>

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
				
				<?= $hazirlayankisi; ?>

			</div>

			<div class="col-md-2">
				
				<b>Tarih :</b>

			</div>

			<div class="col-md-3">
				
				<?= $siparistarih; ?>

			</div>

		</div>

		<div class="row" style="padding: 20px;">
			
			<div class="col-md-3">
				
				<b>Talep Edilen Fabrika : </b>

			</div>

			<div class="col-md-4">
				
				<?= $urun_fabrika_adi; ?>

			</div>

			<div class="col-md-2">
				
				<b>İlgili Kişi : </b>

			</div>

			<div class="col-md-3">
				
				<?= $ilgilikisi; ?>

			</div>

		</div>

		<div class="row" style="padding: 20px;">
			
			<div class="col-md-1 p-0"><b>S.No</b></div>
            <div class="col-md-1 p-0"><b>Kalıp</b></div>
			<div class="col-md-4 p-0"><b>Malzemenin Cinsi</b></div>
			<div class="col-md-1 p-0"><b>Boy</b></div>
			<div class="col-md-1 p-0"><b>Adet</b></div>
			<div class="col-md-2 p-0"><b>Paketleme Adedi</b></div>
            <div class="col-md-1 p-0"><b>Palet</b></div>
            <div class="col-md-1 p-0"><b>Tahmini Kg</b></div>

		</div>

		<?php

			$a = 0;

			$siparislistesi = "";

            $toplamkilo = 0;

			$query = $db->query("SELECT * FROM siparis WHERE urun_fabrika_id = '{$urun_fabrika_id}' AND formda = '0' AND silik = '0'", PDO::FETCH_ASSOC);

			if ( $query->rowCount() ){

				foreach( $query as $row ){

					$a++;

					$siparis_id = $row['siparis_id'];

					if(empty($siparislistesi)){

						$siparislistesi = $siparis_id;

					}else{

						$siparislistesi = $siparislistesi.",".$siparis_id;

					}
					$urun_adi = $row['urun_adi'];

					$urun_siparis_aded = $row['urun_siparis_aded'];

					$siparissaniye = $row['siparissaniye'];

					$siparisboy = $row['siparisboy'];

					$urun_siparis_aded = $row['urun_siparis_aded'];

					$siparisintarihi = date("d-m-Y",$siparissaniye);

					$urun_id = $row['urun_id'];

					$productInfo = $db->query("SELECT * FROM urun WHERE urun_id = '{$urun_id}'")->fetch(PDO::FETCH_ASSOC);

					$kategori_bir = $productInfo['kategori_bir'];

					$katadcek = $db->query("SELECT * FROM categories WHERE id = '{$kategori_bir}'")->fetch(PDO::FETCH_ASSOC);

					$kategori_bir_adi = $katadcek['name'];

					$kategori_iki = $productInfo['kategori_iki'];
					$packQuantity = $productInfo['pack_quantity'];
					$unitWeight = $productInfo['urun_birimkg'];

					$katadcek = $db->query("SELECT * FROM categories WHERE id = '{$kategori_iki}'")->fetch(PDO::FETCH_ASSOC);

					$kategori_iki_adi = $katadcek['name'];

                    $palet = $row['palet'];

                    $moldNumber = getMoldNumber($urun_id, $factoryId);

                    $kilo = $urun_siparis_aded * $unitWeight;

                    $toplamkilo += $kilo;

			?>

						<hr style="border:1px black solid;" />

						<div class="row" style="padding: 20px;">
				
							<div class="col-md-1 p-0"><?= $a; ?></div>
                            <div class="col-md-1 p-0"><?= $moldNumber->number ?? null; ?></div>
							<div class="col-md-4 p-0"><?= $urun_adi." ".$kategori_iki_adi.' / '.$kategori_bir_adi; ?></div>
							<div class="col-1 p-0"><?= $siparisboy; ?></div>
							<div class="col-1 p-0"><?= $urun_siparis_aded." adet "; ?></div>
							<div class="col-2 p-0"><?= $packQuantity == 0 ? '---------------------' : $packQuantity." adetli sarılsın "; ?></div>
                            <div class="col-1 p-0"><?= $palet == 0 ? '-------' : $palet." palet "; ?></div>
                            <div class="col-1 p-0"><?= number_format($kilo, 1, ',', '.')." KG"; ?></div>

						</div>

			<?php

				}

			}

		?>

        <hr/>

        <div class="row" style="padding: 20px;">

            <div class="col-md-9" style="text-align:right; font-size:18px;"></div>

            <div class="col-md-3" style="text-align:right; font-size:18px;"><b>Toplam Kilo : </b><b><?= number_format($toplamkilo, 1, ',', '.')." KG"; ?></b></div>

        </div>

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

	<?php include ROOT_PATH.'/template/script.php'; ?>

</body>

</html>