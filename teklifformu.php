<?php

	include 'fonksiyonlar/bagla.php';

	if($uye_tipi == '0'){

		header("Location:index.php");

		exit();

	}

	$tformid = guvenlik($_GET['id']);

	$query = $db->query("SELECT * FROM teklifformlari WHERE tformid = '{$tformid}' AND silik = '0' ORDER BY tformid ASC LIMIT 1")->fetch(PDO::FETCH_ASSOC);

	$firmaid = $query['firmaid'];

	$firmabilgicek = $db->query("SELECT * FROM firmalar WHERE firmaid = '{$firmaid}' ORDER BY firmaid ASC LIMIT 1")->fetch(PDO::FETCH_ASSOC);

	$firmaadi = $firmabilgicek['firmaadi'];

	$firmatel = $firmabilgicek['firmatel'];

	$tekliflistesi = $query['tekliflistesi'];

	$tekliflistesinipatlat = explode(",", $tekliflistesi);

?>

<!DOCTYPE html>
<html>
<head>
	<title></title>
	<?php include 'template/head.php'; ?>

	</script>
</head>
<body>
	<div class="container-fluid pt-5" style="background: white;">

		<div class="row">
			
			<div class="col-md-4" style="text-align: center;"><img src="img/file/<?= $sirketlogo; ?>" style="width: 370px; height: auto;"></div>

			<div class="col-md-8" style="text-align: center; padding: 0px 30px 0px 30px;">

				<p style="font-size: 15px;">
			
					<?php $sirketaciklama = str_replace("\n", "<br/>", $sirketaciklama); echo $sirketaciklama; ?>

				</p>

			</div>

		</div>

		<div class="row">

			<div class="col-3"></div>
			
			<div class="col-md-6" style="text-align: center; padding: 20px;">
				
				<h4><b>TEKLİF FORMU</b></h4>

			</div>

			<div class="col-3" style="padding-top: 20px;"><?= "Tarih : ".$tarihf2; ?></div>

		</div>

		<div class="row" style="padding: 20px;">
			
			<div class="col-md-2">
				
				<b>Firma Adı :</b>

			</div>

			<div class="col-md-5">
				
				<?= $firmaadi."<br/><br/>".$firmatel; ?>

			</div>

			<div class="col-5" style="text-align: center;">
				
				Müşteri Onay (Kaşe İmza Tarih)

			</div>

		</div>

		<div class="row" style="padding: 20px;">
			
			<div class="col-md-2"><br/>
				
				<b>Siparişi Yazan : </b>

			</div>

			<div class="col-md-5"><br/>

				<?= $uye_adi." / ".$uye_mail; ?>

			</div>

			<div class="col-5" style="text-align: center; padding: 0px; margin: 0px;">
				
				<div style="border: 2px dashed black; width: 400px; height: 80px;"></div>

			</div>

		</div>

		<div class="row" style="padding: 20px;">

			<div class="col-md-3"><b>Ürün Adı</b></div>

			<div class="col-md-1"><b>Adet</b></div>

			<div class="col-md-1"><b>Cinsi</b></div>	

			<div class="col-md-2" style="text-align: right;"><b>Bir Boy (6m) Kg</b></div>	

			<div class="col-md-1"><b>Toplam</b></div>	

			<div class="col-md-2" style="text-align: right;"><b>Bir Kg Fiyatı</b></div>

			<div class="col-md-2"><b>Tutar</b></div>		

		</div>

		<?php

			$a = 0;

			$toplamtutar = 0;

			foreach ($tekliflistesinipatlat as $key => $value) {

				$teklifbilgileri = $db->query("SELECT * FROM teklif WHERE teklifid = '{$value}' AND silik = '0'")->fetch(PDO::FETCH_ASSOC);

				$a++;

				$tadet = $teklifbilgileri['tadet'];

				$tsaniye = $teklifbilgileri['tsaniye'];

				$siparisboy = $teklifbilgileri['siparisboy'];

				$tsatisfiyati = $teklifbilgileri['tsatisfiyati'];

				$teklifintarihi = date("d-m-Y",$tsaniye);

				$turunid = $teklifbilgileri['turunid'];

				$urunbilgi = $db->query("SELECT * FROM urun WHERE urun_id = '{$turunid}'")->fetch(PDO::FETCH_ASSOC);

				$urun_adi = $urunbilgi['urun_adi'];

				$urun_birimkg = $urunbilgi['urun_birimkg'];

				$kategori_bir = $urunbilgi['kategori_bir'];

				$katadcek = $db->query("SELECT * FROM kategori WHERE kategori_id = '{$kategori_bir}'")->fetch(PDO::FETCH_ASSOC);

				$kategori_bir_adi = $katadcek['kategori_adi'];

				$kategori_iki = $urunbilgi['kategori_iki'];

				$katadcek = $db->query("SELECT * FROM kategori WHERE kategori_id = '{$kategori_iki}'")->fetch(PDO::FETCH_ASSOC);

				$kategori_iki_adi = $katadcek['kategori_adi'];

				$toplamkilo = $tadet * $urun_birimkg;

				$tutar = $toplamkilo * $tsatisfiyati;

				$toplamtutar = $toplamtutar + $tutar;

			?>

				<hr style="border:2px black solid; margin: 0px;" />

				<div class="row" style="padding: 20px;">

					<div class="col-3"><?= $a.". ".$urun_adi." ".$kategori_iki_adi; ?></div>

					<div class="col-1"><?= $tadet." Boy "; ?></div>

					<div class="col-2"><?= $kategori_bir_adi; ?></div>

					<div class="col-1"><?= $urun_birimkg." Kg"; ?></div>

					<div class="col-2"><?= $toplamkilo." Kg"; ?></div>

					<div class="col-1"><?= $tsatisfiyati." TL"; ?></div>

					<div class="col-2"><?= $tutar." TL + KDV"; ?></div>

				</div>

			<?php

			}

		?>

		<hr style="border:2px black solid; margin: 0px;" />

		<div class="row">
			
			<div class="col-12" style="text-align: right; padding-right: 50px; margin: 10px;">
				
				<b style="font-size: 20px;"><?= "Toplam Tutar : ".(number_format($toplamtutar, 0, ',', '.'))." + KDV"; ?></b>

			</div>

		</div>	

		<hr/>

		<div class="row">

			<div class="col-3">ÖDEME BİLGİLERİ :  NAKİT</div>

			<div class="col-4">GARANTİ BANKASI ÇAĞLAYAN ŞUBESİ</div>

			<div class="col-5">HESAP ADI : OSMANLI ALÜMİNYUM SAN TİC LTD ŞTİ TL HESABI</div>

		</div>

		<div class="row">

			<div class="col-3">ŞUBE KODU : 403</div>

			<div class="col-4">HESAPNO : 6298512</div>

			<div class="col-5">IBAN:TR73 0006 2000 4030 0006 2985 12</div>

		</div>

		<hr/>

		<div class="row">
			
			<div class="col-12">
				
				   <input type="text" class="form-control" name="" style="border-style: none;" value="TESLİMAT  BİLGİLERİ : ONAY SONRASI 1  İŞ GÜNÜ TESLİM      (  TEKLİF GEÇERLİLİK SÜRESİ 5 İŞ GÜNÜDÜR  )">	
				   
			</div>

		</div>

		<hr/>

		<div class="row">
			
			<div class="col-md-12">
				
				<p>
					
					*   Ürün mt/gr biriminde yaklaşık gramaj alınmış olup, kg' lar +/- tolerans dâhilindedir.<br/>
					
					*   Yeni düzenlenen kanuna göre 2.000 TL üzeri alımlarımızda tevkifatlı fatura kesilecektir.<br/>
					
					*   özel  siparişlerinizde  % 20 ön ödeme alınır ( boyalı ürünlerde )<br/>
					
					*   Sipariş formlarının teyit edilerek geri gönderilmesi gerekmektedir. Kaşe-imza yoluyla onayı gelmeyen siparişler sevk edilmeyecektir. Onaylanmış siparişlere ait mesuliyet alıcı firmaya aittir.<br/>
					
					*   Kesilmiş profiller iade alınmaz. İadesi söz konusu olan malzemeler orijinal ambalajında ve orijinal boyda olmalıdır.<br/>
					
					*   Taşıma bedelleri (ambar/kargo/nakliye)alıcı firmaya aittir. Osmanlı  Alüminyum doğacak sorunlardan sorumlu değildir.<br/>
									
				</p>

			</div>

		</div>

	</div>

	<?php include 'template/script.php'; ?>
</body>
</html>