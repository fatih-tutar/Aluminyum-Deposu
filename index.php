<?php 

	include 'fonksiyonlar/bagla.php';

	if ($girdi != 1) {
		
		header("Location:giris.php");

		exit();

	}elseif ($girdi == 1) {

		if (isset($_POST['hesapla'])) {
			
			$dolarPost = guvenlik($_POST['dolarkuru']);

			$lmePost = guvenlik($_POST['lme']);

			$iscilik = guvenlik($_POST['iscilik']);

			$toplam = ($lmePost + $iscilik) * $dolarPost;

			header("Location:index.php?fiyat=".$toplam);

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
			if(!empty($kilolar) && !is_numeric($kilolar)){
				$hata = '<br/><div class="alert alert-danger" role="alert">Kilo kısmına sadece sayısal bir değer girebilirsiniz.</div>'; 
			}
			if(empty($kilolar)){
				for($i = 0; $i < $malzemeAdeti; $i++){
					if(!empty(guvenlik($_POST['kilo_'.$i]))){ 
						if(is_numeric($_POST['kilo_'.$i])){
							if($i == 0){
								$kilolar = guvenlik($_POST['kilo_'.$i]);
							}else{
								$kilolar = $kilolar.",".guvenlik($_POST['kilo_'.$i]);
							}
						}else{
							$hata = '<br/><div class="alert alert-danger" role="alert">Kilo kısmına sadece sayısal bir değer girebilirsiniz.</div>'; 
						}
					}
				}	
			}
			if(!empty($kilolar)){
				$query = $db->prepare("UPDATE sevkiyat SET kilolar = ?, durum = ?, hazirlayan = ? WHERE id = ?");
				$update = $query->execute(array($kilolar,'1',$uye_id,$sevkiyatID));
			}else{
				$hata = '<br/><div class="alert alert-danger" role="alert">Ürünlere tek tek veya toplam olarak kilo girmelisiniz.</div>'; 
			}
			if(!$hata){
				header("Location: index.php");
				exit();
			}
		}

		if(isset($_POST['sevkiyatsil'])){
			$sevkiyatID = guvenlik($_POST['sevkiyatID']);
			$query = $db->prepare("UPDATE sevkiyat SET silik = ? WHERE id = ?");
			$update = $query->execute(array('1',$sevkiyatID));
			header("Location: index.php");
			exit();
		}

		if(isset($_POST['faturahazir'])){
			$sevkiyatID = guvenlik($_POST['sevkiyatID']);
			$query = $db->prepare("UPDATE sevkiyat SET durum = ?, faturaci = ? WHERE id = ?");
			$update = $query->execute(array('2',$uye_id,$sevkiyatID));
			header("Location: index.php");
			exit();
		}

		if(isset($_POST['alinanagerial'])){
			$sevkiyatID = guvenlik($_POST['sevkiyatID']);
			$query = $db->prepare("UPDATE sevkiyat SET durum = ? WHERE id = ?");
			$update = $query->execute(array('0',$sevkiyatID));
			header("Location: index.php");
			exit();
		}

		if(isset($_POST['arsivegonder'])){
			$sevkiyatID = guvenlik($_POST['sevkiyatID']);
			$query = $db->prepare("UPDATE sevkiyat SET durum = ? WHERE id = ?");
			$update = $query->execute(array('3',$sevkiyatID));
			header("Location: index.php");
			exit();
		}

		if(isset($_POST['hazirlananagerial'])){
			$sevkiyatID = guvenlik($_POST['sevkiyatID']);
			$query = $db->prepare("UPDATE sevkiyat SET durum = ? WHERE id = ?");
			$update = $query->execute(array('1',$sevkiyatID));
			header("Location: index.php");
			exit();
		}

		if(isset($_POST['sevkiyattanurunsil'])){
			$malzemeIndex = guvenlik($_POST['sevkiyattanurunsil']); 
			$sevkiyatID = guvenlik($_POST['sevkiyatID']);
			$sevkiyat = getSevkiyatInfo($sevkiyatID);
			
			$sevkiyatUrunler = $sevkiyat['urunler'];
			$urunArray = explode(",",$sevkiyatUrunler);
			unset($urunArray[$malzemeIndex]);
			$sevkiyatUrunler = implode(",",array_values($urunArray));
			
			$sevkiyatAdetler = $sevkiyat['adetler'];
			$adetArray = explode(",",$sevkiyatAdetler);
			unset($adetArray[$malzemeIndex]);
			$sevkiyatAdetler = implode(",",array_values($adetArray));

			$sevkiyatKilolar = $sevkiyat['kilolar'];
			$kiloArray = explode(",",$sevkiyatKilolar);
			unset($kiloArray[$malzemeIndex]);
			$sevkiyatKilolar = implode(",",array_values($kiloArray));

			$sevkiyatFiyatlar = $sevkiyat['fiyatlar'];
			$fiyatArray = explode("-",$sevkiyatFiyatlar);
			unset($fiyatArray[$malzemeIndex]);
			$sevkiyatFiyatlar = implode("-",array_values($fiyatArray));

			$query = $db->prepare("UPDATE sevkiyat SET urunler = ?, adetler = ?, kilolar = ?, fiyatlar  = ? WHERE id = ?");
			$update = $query->execute(array($sevkiyatUrunler,$sevkiyatAdetler,$sevkiyatKilolar,$sevkiyatFiyatlar,$sevkiyatID));
			header("Location: index.php");
			exit();
		}

		if (isset($_POST['sevkiyatkaydet'])) {
			$urun = $_POST['urun'];
			$adet = guvenlik($_POST['adet']);
			$fiyat = guvenlik($_POST['fiyat']);
			$sevkTipi =  guvenlik($_POST['sevk_tipi']);
			$aciklama =  guvenlik($_POST['aciklama']);
			$firma = guvenlik($_POST['firma']);
			if(empty($urun)){
				$hata = '<br/><div class="alert alert-danger" role="alert">Müşteri sipariş formu için lütfen bir ürün seçiniz.</div>'; 
			}else if(empty($firma)){
                $hata = '<br/><div class="alert alert-danger" role="alert">Müşteri sipariş formu için lütfen bir firma seçiniz.</div>';
            }else if(empty($adet)){
				$hata = '<br/><div class="alert alert-danger" role="alert">Müşteri sipariş formu için lütfen bir adet belirtiniz.</div>'; 
			}else if(empty($fiyat)){
				$hata = '<br/><div class="alert alert-danger" role="alert">Müşteri sipariş formu için lütfen bir fiyat yazınız.</div>'; 
			}else if($sevkTipi === "null") {
                $hata = '<br/><div class="alert alert-danger" role="alert">Müşteri sipariş formu için lütfen bir sevk tipi seçiniz.</div>';
			}else{
				$urunArray = explode("/",$urun);
				$urun = trim($urunArray[0]);
				$kategori_iki = trim($urunArray[1]);
				$kategori_bir = trim($urunArray[2]);
				$urunId = getUrunID($urun,$kategori_iki,$kategori_bir);
				$firmaId = getFirmaID($firma);
				$sevkiyatList = $db->query("SELECT * FROM sevkiyat WHERE firma_id = '{$firmaId}' AND durum = '0' AND silik = '0' AND sirket_id = '{$uye_sirket}' ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
				if($sevkiyatList){
					$urunler = guvenlik($sevkiyatList['urunler']);
					$adetler = guvenlik($sevkiyatList['adetler']);
					$fiyatlar = guvenlik($sevkiyatList['fiyatlar']);
					$urunler = $urunler.",".$urunId;
					$adetler = $adetler.",".$adet;
					$fiyatlar = $fiyatlar."-".$fiyat;
					$query = $db->prepare("UPDATE sevkiyat SET urunler = ?, adetler = ?, fiyatlar = ? WHERE firma_id = ? AND durum = ? AND silik = ? AND sirket_id = ?");
					$update = $query->execute(array($urunler, $adetler, $fiyatlar, $firmaId, '0', '0', $uye_sirket));
				}else{
					$query = $db->prepare("INSERT INTO sevkiyat SET urunler = ?, firma_id = ?, adetler = ?, kilolar = ?, fiyatlar = ?, olusturan = ?, hazirlayan = ?, sevk_tipi = ?, aciklama = ?, durum = ?, silik = ?, saniye = ?, sirket_id = ?");
					$insert = $query->execute(array($urunId,$firmaId,$adet,'',$fiyat,$uye_id,'',$sevkTipi,$aciklama,'0','0',$su_an, $uye_sirket));
				}
				header("Location:index.php");
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
					<div class="row mx-1">
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