<?php

	include 'fonksiyonlar/bagla.php';

	if($uye_tipi == '0'){

		header("Location:index.php");

		exit();

	}

	$firmaid = guvenlik($_GET['id']);

	$firmabilgicek = $db->query("SELECT * FROM firmalar WHERE firmaid = '{$firmaid}' ORDER BY firmaid ASC LIMIT 1")->fetch(PDO::FETCH_ASSOC);

	$firmaadi = $firmabilgicek['firmaadi'];

	$firmatel = $firmabilgicek['firmatel'];

if($uye_tipi != '3'){

	if (isset($_POST['formkaydet'])) {

		$tekliflistesi = guvenlik($_POST['tekliflistesi']);

		$tekliflistesipatlat = explode(",", $tekliflistesi);

		foreach ($tekliflistesipatlat as $key => $value) {
				
			$query = $db->prepare("UPDATE teklif SET formda = ? WHERE teklifid = ?"); 

			$guncelle = $query->execute(array('1',$value));

		}
		
		$query = $db->prepare("INSERT INTO teklifformlari SET tekliflistesi = ?, firmaid = ?, saniye = ?, sirketid = ?, silik = ?");

		$insert = $query->execute(array($tekliflistesi,$firmaid,$su_an,$uye_sirket,'0'));

		header("Location:firmalar.php");

		exit();

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
				
				<?= $user->name."<br/>".$uye_mail."<br/>".$uye_tel; ?>

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

			$tekliflistesi = "";

			$formtoplamkilo = 0;

			$query = $db->query("SELECT * FROM teklif WHERE tverilenfirma = '{$firmaid}' AND formda = '0' AND silik = '0'", PDO::FETCH_ASSOC);

			if ( $query->rowCount() ){

				foreach( $query as $row ){

					$a++;

					$teklifid = $row['teklifid'];

					if(empty($tekliflistesi)){

						$tekliflistesi = $teklifid;

					}else{

						$tekliflistesi = $tekliflistesi.",".$teklifid;

					}

					$tadet = $row['tadet'];

					$tsaniye = $row['tsaniye'];

					$siparisboy = $row['siparisboy'];

					$tsatisfiyati = $row['tsatisfiyati'];

					$teklifintarihi = date("d-m-Y",$tsaniye);

					$turunid = $row['turunid'];

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

					$formtoplamkilo += $toplamkilo;

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

			}

		?>

		<hr style="border:2px black solid; margin: 0px;" />

		<div class="row" style="margin-top: 10px;">

			<div class="col-md-8" style="text-align:right;">
				<b style="font-size:18px;"><?= "Toplam Kilo : ".$formtoplamkilo." KG"; ?></b>
			</div>
			
			<div class="col-md-4" style="text-align:right;">
				
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

		<div class="row">
			
			<div class="col-6">

				<form action="" method="POST">

					<input type="hidden" name="tekliflistesi" value="<?= $tekliflistesi; ?>">
					
					<button type="submit" name="formkaydet" class="btn btn-warning btn-lg btn-block">Formu Kaydet</button>

				</form>

			</div>

			<div class="col-6">
				
				<a href="teklifyazdir.php?id=<?= $firmaid; ?>" target="_blank">
					
					<button type="submit" class="btn btn-danger btn-lg btn-block">Yazdırma Sayfasına Git</button>

				</a>

			</div>

		</div>

	</div>

	<?php include 'template/script.php'; ?>

	<br/><br/><br/><br/><br/><br/><br/><br/><br/>

</body>
</html>