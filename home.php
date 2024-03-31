<?php 

	include 'fonksiyonlar/bagla.php';

	if ($girdi != 1) {
		
		header("Location:giris.php");

		exit();

	}elseif ($girdi == 1) {

		if(isset($_GET['mt']) === true && empty($_GET['mt']) === false){ 

			$mt = guvenlik($_GET['mt']); 

			if ($mt == '1') {
				
				$kalinlik = guvenlik($_GET['k']);

				$en = guvenlik($_GET['e']);

				$boy = guvenlik($_GET['b']);

				$adet = guvenlik($_GET['a']);

				$toplam1 = guvenlik($_GET['toplam']);

			}

			if ($mt == '2') {
				
				$amm = guvenlik($_GET['a']);

				$bmm = guvenlik($_GET['b']);

				$etkalinligi = guvenlik($_GET['e']);

				$boy = guvenlik($_GET['boy']);

				$adet = guvenlik($_GET['adet']);

				$toplam2 = guvenlik($_GET['toplam']);

			}

			if ($mt == '3') {
				
				$cap = guvenlik($_GET['c']);

				$uzunluk = guvenlik($_GET['u']);

				$adet = guvenlik($_GET['a']);

				$toplam3 = guvenlik($_GET['toplam']);

			}

			
			if ($mt == '4') {
				
				$amm = guvenlik($_GET['a']);

				$bmm = guvenlik($_GET['b']);

				$etkalinligi = guvenlik($_GET['e']);

				$boy = guvenlik($_GET['boy']);

				$adet = guvenlik($_GET['adet']);

				$toplam4 = guvenlik($_GET['toplam']);

			}

			if ($mt == '5') {
				
				$iccap = guvenlik($_GET['iccap']);

				$discap = guvenlik($_GET['discap']);

				$etkalinligi = guvenlik($_GET['e']);

				$boy = guvenlik($_GET['boy']);

				$adet = guvenlik($_GET['adet']);

				$toplam5 = guvenlik($_GET['toplam']);

			}

		}

		if (isset($_POST['hesapla'])) {
			
			$dolarPost = guvenlik($_POST['dolarkuru']);

			$lmePost = guvenlik($_POST['lme']);

			$iscilik = guvenlik($_POST['iscilik']);

			$toplam = ($lmePost + $iscilik) * $dolarPost;

			header("Location:index.php?fiyat=".$toplam);

			exit();
 
		}

		if (isset($_POST['levhahesapla'])) {

			$malzemetipi = guvenlik($_POST['malzemetipi']);
			
			$kalinlik = guvenlik($_POST['kalinlik']);

			$en = guvenlik($_POST['en']);

			$boy = guvenlik($_POST['boy']);

			$adet = guvenlik($_POST['adet']);

			$toplam = $kalinlik * $en * $boy * $adet * 0.000001 * 2.81;

			header("Location:index.php?mt=".$malzemetipi."&k=".$kalinlik."&e=".$en."&b=".$boy."&a=".$adet."&toplam=".$toplam);

			exit();

		}

		if (isset($_POST['kosebenthesapla'])) {

			$malzemetipi = guvenlik($_POST['malzemetipi']);
			
			$a = guvenlik($_POST['a']);

			$b = guvenlik($_POST['b']);

			$etkalinligi = guvenlik($_POST['etkalinligi']);

			$boy = guvenlik($_POST['boy']);

			$adet = guvenlik($_POST['adet']);

			$toplam = $adet * 2.71 * $etkalinligi * $boy * ($a + $b - $etkalinligi) * 0.000001;

			header("Location:index.php?mt=".$malzemetipi."&a=".$a."&b=".$b."&e=".$etkalinligi."&boy=".$boy."&adet=".$adet."&toplam=".$toplam);

			exit();

		}

		if (isset($_POST['cubukhesapla'])) {

			$malzemetipi = guvenlik($_POST['malzemetipi']);
			
			$cap = guvenlik($_POST['cap']);

			$uzunluk = guvenlik($_POST['uzunluk']);

			$adet = guvenlik($_POST['adet']);

			$toplam = 3.14 * (($cap / 2) * ($cap / 2) * $uzunluk * $adet * (0.000001) * 2.71);

			header("Location:index.php?mt=".$malzemetipi."&c=".$cap."&u=".$uzunluk."&a=".$adet."&toplam=".$toplam);

			exit();

		}

		if (isset($_POST['kutuhesapla'])) {

			$malzemetipi = guvenlik($_POST['malzemetipi']);
			
			$a = guvenlik($_POST['a']);

			$b = guvenlik($_POST['b']);

			$etkalinligi = guvenlik($_POST['etkalinligi']);

			$boy = guvenlik($_POST['boy']);

			$adet = guvenlik($_POST['adet']);

			$toplam = (2 * $etkalinligi * ($a + $b) - (4 * $etkalinligi * $etkalinligi)) * $boy * $adet * 2.71 * 0.000001;

			header("Location:index.php?mt=".$malzemetipi."&a=".$a."&b=".$b."&e=".$etkalinligi."&boy=".$boy."&adet=".$adet."&toplam=".$toplam);

			exit();

		}

		if (isset($_POST['boruhesapla'])) {

			$malzemetipi = guvenlik($_POST['malzemetipi']);
			
			$iccap = guvenlik($_POST['iccap']);

			$discap = guvenlik($_POST['discap']);

			$etkalinligi = guvenlik($_POST['etkalinligi']);

			if (empty($iccap) === true && empty($etkalinligi) === false) {
				
				$iccap = $discap - (2 * $etkalinligi);

			}

			$boy = guvenlik($_POST['boy']);

			$adet = guvenlik($_POST['adet']);

			$toplam = 3.14 * ((($discap / 2) * ($discap / 2)) - (($iccap / 2) * ($iccap / 2))) * $boy * $adet * 2.71 * 0.000001;

			header("Location:index.php?mt=".$malzemetipi."&iccap=".$iccap."&discap=".$discap."&e=".$etkalinligi."&boy=".$boy."&adet=".$adet."&toplam=".$toplam);

			exit();

		}

		if(isset($_POST['plan_duzenle'])){

            $plan_id = guvenlik($_POST['plan_id']);

            $plan = guvenlik($_POST['plan']);

            $plan_tarihi = guvenlik($_POST['plan_tarihi']);

			$plan_tarihi = strtotime($plan_tarihi);

            $plan_tekrar = guvenlik($_POST['plan_tekrar']);
            
            $plan_durum = guvenlik($_POST['plan_durum']);

            $query = $db->prepare("UPDATE plan SET plan = ?, plan_tarihi = ?, plan_tekrar = ?, plan_durum = ? WHERE plan_id = ?"); 

            $guncelle = $query->execute(array($plan,$plan_tarihi,$plan_tekrar,$plan_durum,$plan_id));

            header("Location: plan.php");

            exit();

        }

        if(isset($_POST['plan_sil'])){

            $plan_id = guvenlik($_POST['plan_id']);

            $query = $db->prepare("UPDATE plan SET plan_silik = ? WHERE plan_id = ?"); 

            $guncelle = $query->execute(array('1',$plan_id));

            header("Location: plan.php");

            exit();

        }

		if(isset($_POST['sevkiyathazir'])){
			$sevkiyatID = guvenlik($_POST['sevkiyatID']);
			$malzemeAdeti = guvenlik($_POST['malzemeAdeti']);
			$kilolar = guvenlik($_POST['kilolar']);
			if(empty($kilolar)){
				for($i = 0; $i < $malzemeAdeti; $i++){
					if(!empty(guvenlik($_POST['kilo_'.$i]))){ 
						if($i == 0){
							$kilolar = guvenlik($_POST['kilo_'.$i]);
						}else{
							$kilolar = $kilolar.",".guvenlik($_POST['kilo_'.$i]);
						}
					}
				}	
			}
			if(!empty($kilolar)){
				$query = $db->prepare("UPDATE sevkiyat SET kilolar = ?, durum = ?, hazirlayan = ? WHERE id = ?");
				$update = $query->execute(array($kilolar,'1',$uye_id,$sevkiyatID));
			}
			header("Location: home.php");
			exit();
		}

		if(isset($_POST['sevkiyatsil'])){
			$sevkiyatID = guvenlik($_POST['sevkiyatID']);
			$query = $db->prepare("UPDATE sevkiyat SET silik = ? WHERE id = ?");
			$update = $query->execute(array('1',$sevkiyatID));
			header("Location: home.php");
			exit();
		}

		if(isset($_POST['faturahazir'])){
			$sevkiyatID = guvenlik($_POST['sevkiyatID']);
			$query = $db->prepare("UPDATE sevkiyat SET durum = ?, faturaci = ? WHERE id = ?");
			$update = $query->execute(array('2',$uye_id,$sevkiyatID));
			header("Location: home.php");
			exit();
		}

		if(isset($_POST['alinanagerial'])){
			$sevkiyatID = guvenlik($_POST['sevkiyatID']);
			$query = $db->prepare("UPDATE sevkiyat SET durum = ? WHERE id = ?");
			$update = $query->execute(array('0',$sevkiyatID));
			header("Location: home.php");
			exit();
		}

		if(isset($_POST['arsivegonder'])){
			$sevkiyatID = guvenlik($_POST['sevkiyatID']);
			$query = $db->prepare("UPDATE sevkiyat SET durum = ? WHERE id = ?");
			$update = $query->execute(array('3',$sevkiyatID));
			header("Location: home.php");
			exit();
		}

		if(isset($_POST['hazirlananagerial'])){
			$sevkiyatID = guvenlik($_POST['sevkiyatID']);
			$query = $db->prepare("UPDATE sevkiyat SET durum = ? WHERE id = ?");
			$update = $query->execute(array('1',$sevkiyatID));
			header("Location: home.php");
			exit();
		}

		if (isset($_POST['sevkiyatkaydet'])) {

			$urun = $_POST['urun'];

			$urunArray = explode("/",$urun);

			$urun = trim($urunArray[0]);

			$urunId = getUrunID($urun);

			$adet = guvenlik($_POST['adet']);

			$fiyat = guvenlik($_POST['fiyat']);

			$sevkTipi =  guvenlik($_POST['sevk_tipi']);

			$aciklama =  guvenlik($_POST['aciklama']);

			$firma = guvenlik($_POST['firma']);

			$firmaId = getFirmaID($firma);

			$sevkiyatList = $db->query("SELECT * FROM sevkiyat WHERE firma_id = '{$firmaId}' AND durum = '0' AND sirket_id = '{$uye_sirket}' ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
			
			if($sevkiyatList){

				$urunler = guvenlik($sevkiyatList['urunler']);

				$adetler = guvenlik($sevkiyatList['adetler']);

				$fiyatlar = guvenlik($sevkiyatList['fiyatlar']);

				$urunler = $urunler.",".$urunId;

				$adetler = $adetler.",".$adet;

				$fiyatlar = $fiyatlar."-".$fiyat;

				$query = $db->prepare("UPDATE sevkiyat SET urunler = ?, adetler = ?, fiyatlar = ? WHERE firma_id = ? AND durum = ? AND sirket_id = ?"); 

				$update = $query->execute(array($urunler, $adetler, $fiyatlar, $firmaId, '0', $uye_sirket));

			}else{

				$query = $db->prepare("INSERT INTO sevkiyat SET urunler = ?, firma_id = ?, adetler = ?, kilolar = ?, fiyatlar = ?, olusturan = ?, hazirlayan = ?, sevk_tipi = ?, aciklama = ?, durum = ?, silik = ?, saniye = ?, sirket_id = ?");

				$insert = $query->execute(array($urunId,$firmaId,$adet,'',$fiyat,$uye_id,'',$sevkTipi,$aciklama,'0','0',$su_an, $uye_sirket));	

			}			

			header("Location:home.php");

			exit();

		}

	}

?>

<!DOCTYPE html>

<html>

	<head>

		<title>Alüminyum Deposu</title>

		<?php include 'template/head.php'; ?>

		<style type="text/css">
			.gorsel-container {
			    width:100%;
			    overflow:hidden;
			    margin:0;
			    height:170px;
			}

			.gorsel-container img {
			    display:block;
			    width:100%;
			    margin:-20px 20;
			}
			.sevkCardBlue{
				background-color: #17a2b8;
				border-radius: 5px;
				color: black;
				margin-bottom: 5px;
			}
			.sevkCardYellow{
				background-color: #ffc107;
				border-radius: 5px;
				color: black;
				margin-bottom: 5px;
			}
			.sevkCardGreen{
				background-color: #28a745;
				border-radius: 5px;
				color: black;
				margin-bottom: 5px;
			}
			.text-fiyat {
				font-size: 17px; 
				font-weight: bold;
			}
			@media (max-width:576px) {
				.text-fiyat {
					font-size: 15px; 
					font-weight: normal;
				}
			}
		</style>
	</head>
	<body>
		<?php include 'template/banner.php' ?>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<?php echo $hata; ?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-2 col-12">
					<?php include 'template/sidebar.php'; ?>
				</div>
				<div class="col-md-10">
					<div class="row">
						<?php include 'tools/anlikfiyatlama.php'; ?>
						<?php include 'tools/fiyathesaplama.php'; ?>
						<?php include 'tools/agirlikhesaplama.php'; ?>
					</div>
					<?php include 'tools/isplani.php'; ?>
					<?php include 'tools/sevkiyattakibi.php'; ?>
				</div>
			</div>	
		</div>

		<br/><br/><br/><br/><br/><br/>

		<?php include 'template/script.php'; ?>

	</body>

</html>