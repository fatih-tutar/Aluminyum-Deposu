<?php 

	include 'fonksiyonlar/bagla.php'; 

	if ($girdi != '1') {
		
		header("Location:giris.php");

		exit();

	}else{

		if($uye_gelen_giden_yetkisi != '1'){

			header("Location:index.php");

			exit();

		}else{

			$ozet = 0;

			if(isset($_GET['ozet']) && $_GET['ozet'] == 1 && empty($_GET['ozet']) === false){

				$ozet = 1;

			}

			//Bugünün kaydı var mı anlayabilmek için son kaydı çekiyoruz.

			$query = $db->query("SELECT * FROM gelengiden ORDER BY saniye DESC LIMIT 1", PDO::FETCH_ASSOC);

			if ( $query->rowCount() ){

				foreach( $query as $row ){

					$sontarih = guvenlik($row['tarih']);

					$date = new DateTime($sontarih);

					$hafizahafta = $date->format("W");

					$hafizaay = $date->format("m");

					$hafizayil = $date->format("Y");

				}

			}

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

		<br/><br/>

		<div class="row">
			
			<div class="col-md-4" style="text-align: center;"><img src="img/file/<?= $sirketlogo; ?>" style="width: 350px; height: auto;"></div>

			<div class="col-md-8" style="text-align: center; padding: 0px 30px 0px 30px;">

				<p style="font-size: 15px;">
			
					<?php $sirketaciklama = str_replace("\n", "<br/>", $sirketaciklama); echo $sirketaciklama; ?>

				</p>

			</div>

		</div>

		<div style="text-align:center; font-weight: bold; font-size: 32px;">Gelen / Giden Ürün Raporu <br/> (Haftalık - Aylık - Yıllık)</div>
		<hr/>
		<div class="d-none d-sm-block">
			<div class="row" style="font-weight:bold; font-size:24px;">			
				<div class="col-md-4">Hafta / Ay / Yıl</div>
				<div class="col-md-2">Mağaza Giden</div>
				<div class="col-md-2">Mağaza Gelen</div>
				<div class="col-md-2">Alkop Giden</div>
				<div class="col-md-2">Alkop Gelen</div>
			</div>
			<hr/>
		</div>
		<?php

			$haftalikGidenToplam = 0; 
			$aylikGidenToplam = 0; 
			$yillikGidenToplam = 0; 
			$haftalikGelenToplam = 0; 
			$aylikGelenToplam = 0; 
			$yillikGelenToplam = 0;
			$haftalikAlkopGidenToplam = 0; 
			$aylikAlkopGidenToplam = 0; 
			$yillikAlkopGidenToplam = 0; 
			$haftalikAlkopGelenToplam = 0; 
			$aylikAlkopGelenToplam = 0; 
			$yillikAlkopGelenToplam = 0;

			$query = $db->query("SELECT * FROM gelengiden WHERE silik = '0' ORDER BY saniye DESC", PDO::FETCH_ASSOC);

				if ( $query->rowCount() ){

					foreach( $query as $row ){

						$haftasayac++;
						$id = guvenlik($row['id']);
						$gelen = guvenlik($row['gelen']);
						$giden = guvenlik($row['giden']);
						$alkoGelen = guvenlik($row['alkop_gelen']);
						$alkopGiden = guvenlik($row['alkop_giden']);
						$tarih = guvenlik($row['tarih']);
						$saniye = guvenlik($row['saniye']);
						$date = new DateTime($tarih);
						$hafta = $date->format("W");
						$ay = $date->format("m"); 
						$yil = $date->format("Y");

						if($hafta != $hafizahafta){

			?>

							<div class="row" style="font-size:18px;">
								
								<div class="col-md-4 col-12"><?= $hafta.". Hafta Toplam"; ?></div>

								<div class="col-md-2 col-6"><?= $haftalikGidenToplam." <small>(Giden)</small>"; ?></div>

								<div class="col-md-2 col-6"><?= $haftalikGelenToplam." <small>(Gelen)</small>"; ?></div>

								<div class="col-md-2 col-6"><?= $haftalikAlkopGidenToplam." <small>(Giden)</small>"; ?></div>

								<div class="col-md-2 col-6"><?= $haftalikAlkopGelenToplam." <small>(Gelen)</small>"; ?></div>

							</div><hr/>

			<?php

							$haftalikGidenToplam = 0; 
							$haftalikGelenToplam = 0;
							$haftalikAlkopGidenToplam = 0; 
							$haftalikAlkopGelenToplam = 0;

							$hafizahafta = $hafta;

						}

						if($ay != $hafizaay){

			?>

							<div class="row" style="font-size:22px; font-weight: bold; color:red;">
									
								<div class="col-md-4 col-12"><?= ayAdi($ay+1)." ayı toplamı"; ?></div>

								<div class="col-md-2 col-6"><?= $aylikGidenToplam." <small>(Giden)</small>"; ?></div>

								<div class="col-md-2 col-6"><?= $aylikGelenToplam." <small>(Gelen)</small>"; ?></div>

								<div class="col-md-2 col-6"><?= $aylikAlkopGidenToplam." <small>(Giden)</small>"; ?></div>

								<div class="col-md-2 col-6"><?= $aylikAlkopGelenToplam." <small>(Gelen)</small>"; ?></div>

							</div><hr/>

			<?php

							$aylikGidenToplam = 0; 
							$aylikGelenToplam = 0;
							$aylikAlkopGidenToplam = 0; 
							$aylikAlkopGelenToplam = 0;

							$hafizaay = $ay;

						}

						if($yil != $hafizayil){

			?>

							<div class="row" style="font-size:24px; font-weight: bold; color:blue;">
									
								<div class="col-md-4 col-12">Yıllık Toplam</div>

								<div class="col-md-2 col-6"><?= $yillikGidenToplam." <small>(Giden)</small>"; ?></div>

								<div class="col-md-2 col-6"><?= $yillikGelenToplam." <small>(Gelen)</small>"; ?></div>

								<div class="col-md-2 col-6"><?= $yillikAlkopGidenToplam." <small>(Giden)</small>"; ?></div>

								<div class="col-md-2 col-6"><?= $yillikAlkopGelenToplam." <small>(Gelen)</small>"; ?></div>

							</div><hr/>

			<?php

							$yillikGidenToplam = 0; 
							$yillikGelenToplam = 0;
							$yillikAlkopGidenToplam = 0; 
							$yillikAlkopGelenToplam = 0;

							$hafizayil = $yil;

						}

						$haftalikGelenToplam += $gelen; 
						$aylikGelenToplam += $gelen; 
						$yillikGelenToplam += $gelen; 

						$haftalikGidenToplam += $giden; 
						$aylikGidenToplam += $giden; 
						$yillikGidenToplam += $giden; 

						$haftalikAlkopGelenToplam += $alkopGelen; 
						$aylikAlkopGelenToplam += $alkopGelen; 
						$yillikAlkopGelenToplam += $alkopGelen; 

						$haftalikAlkopGidenToplam += $alkopGiden; 
						$aylikAlkopGidenToplam += $alkopGiden; 
						$yillikAlkopGidenToplam += $alkopGiden; 

					}

				}

		?>

		<div class="row" style="font-size:18px;">
			
			<div class="col-md-4 col-12"><?= ($hafta-1).". Hafta Toplam"; ?></div>

			<div class="col-md-2 col-6"><?= $haftalikGidenToplam." <small>(Giden)</small>"; ?></div>

			<div class="col-md-2 col-6"><?= $haftalikGelenToplam." <small>(Gelen)</small>"; ?></div>

			<div class="col-md-2 col-6"><?= $haftalikAlkopGidenToplam." <small>(Giden)</small>"; ?></div>

			<div class="col-md-2 col-6"><?= $haftalikAlkopGelenToplam." <small>(Gelen)</small>"; ?></div>

		</div><hr/>

		<div class="row" style="font-size:22px; font-weight: bold; color:red;">
				
			<div class="col-md-4 col-12"><?= ayAdi($ay)." Ayı Toplamı"; ?></div>

			<div class="col-md-2 col-6"><?= $aylikAlkopGidenToplam." <small>(Giden)</small>"; ?></div>

			<div class="col-md-2 col-6"><?= $aylikAlkopGelenToplam." <small>(Gelen)</small>"; ?></div>

		</div><hr/>

		<div class="row" style="font-size:24px; font-weight: bold; color:blue;">
				
			<div class="col-md-4 col-12"><?= $yil." Yılı Toplamı" ?></div>

			<div class="col-md-2 col-6"><?= $yillikAlkopGidenToplam." <small>(Giden)</small>"; ?></div>

			<div class="col-md-2 col-6"><?= $yillikAlkopGelenToplam." <small>(Gelen)</small>"; ?></div>

		</div>

		<br/><br/>

	</div>

	<?php include 'template/script.php'; ?>

</body>
</html>