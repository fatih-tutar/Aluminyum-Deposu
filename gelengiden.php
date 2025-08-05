<?php 

	include 'functions/init.php';

	if (!isLoggedIn()) {
		
		header("Location:login.php");

		exit();

	}else{

		if($user->permissions->stock_flow != '1'){

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

		if($user->type != '3'){

			if(isset($_POST['gelengidenkayit'])){

				$gelen = guvenlik($_POST['gelen']);

				$gelen = str_replace(",",".",$gelen);

				$giden = guvenlik($_POST['giden']);

				$giden = str_replace(",",".",$giden);

				$alkop_gelen = guvenlik($_POST['alkop_gelen']);

				$alkop_gelen = str_replace(",",".",$alkop_gelen);

				$alkop_giden = guvenlik($_POST['alkop_giden']);

				$alkop_giden = str_replace(",",".",$alkop_giden);

				$tarih = guvenlik($_POST['tarih']);

				$saniye = strtotime($tarih);

				if(tarihvarmi($tarih) == 0){

					$query = $db->prepare("INSERT INTO gelengiden SET gelen = ?, giden = ?, alkop_gelen = ?, alkop_giden = ?, tarih = ?, saniye = ?, silik = ?");

					$insert = $query->execute(array($gelen,$giden,$alkop_gelen,$alkop_giden,$tarih,$saniye,'0'));

					header("Location:gelengiden.php");

					exit();

				}else{

					$error = '<br/><div class="alert alert-danger" role="alert">Bu tarihle zaten bir kayıt var onu düzenleyebilirsiniz.</div>';

				}

			}

			if (isset($_POST['gelengidenguncelle'])) {
				
				$id = guvenlik($_POST['id']);

				$gelen = guvenlik($_POST['gelen']);

				$gelen = str_replace(",",".",$gelen);

				$giden = guvenlik($_POST['giden']);

				$giden = str_replace(",",".",$giden);

				$alkop_gelen = guvenlik($_POST['alkop_gelen']);

				$alkop_gelen = str_replace(",",".",$alkop_gelen);

				$alkop_giden = guvenlik($_POST['alkop_giden']);

				$alkop_giden = str_replace(",",".",$alkop_giden);

				$query = $db->prepare("UPDATE gelengiden SET gelen = ?, giden = ?, alkop_gelen = ?, alkop_giden = ? WHERE id = ?"); 

				$guncelle = $query->execute(array($gelen,$giden,$alkop_gelen,$alkop_giden,$id));

				header("Location:gelengiden.php");

				exit();

			}

		}}

	}

?>

<!DOCTYPE html>

<html>

  <head>

    <title>Alüminyum Deposu</title>

    <?php include 'template/head.php'; ?>

	<style>
		.font-column-title {
			font-weight: bold; 
			font-size: 23px;
		}
	</style>

  </head>

  <body>

    <?php include 'template/banner.php' ?>

    <?= $error; ?>

    <div class="container-fluid">
    	
    	<div class="row">
    		
    		<div class="col-12">

				<div class="div4">

					<?php

						$magaza_tonaj = 0; $depo_tonaj = 0;
					
						$urunlergelsin = $db->query("SELECT * FROM urun", PDO::FETCH_ASSOC);

						if ( $urunlergelsin->rowCount() ){
						
							foreach( $urunlergelsin as $row ){
						
								$urun_adet = guvenlik($row['urun_adet']);
								$urun_palet = guvenlik($row['urun_palet']);
								$urun_depo_adet = guvenlik($row['urun_depo_adet']);
								$urun_birimkg = guvenlik($row['urun_birimkg']);

								$magaza_carpim = $urun_adet * $urun_birimkg;
								$depo_carpim = ($urun_depo_adet + $urun_palet) * $urun_birimkg;

								$magaza_tonaj += $magaza_carpim;
								$depo_tonaj += $depo_carpim;
						
							}
						
						}
					
					?>

					<h1><b>Çağlayan Toplam Tonaj : <span style="color:red;"><?= $magaza_tonaj." KG"; ?></span></b></h1>

					<h1><b>Alkop Toplam Tonaj : <span style="color:red;"><?= $depo_tonaj." KG"; ?><span></b></h1>

				</div>

    			<div class="div4">

	    				<div class="row">
	    					
	    					<div class="col-md-2 d-none d-sm-block font-column-title">Tarih</div>

	    					<div class="col-md-2 d-none d-sm-block font-column-title">Çağlayan Giden</div>

	    					<div class="col-md-2 d-none d-sm-block font-column-title">Çağlayan Gelen</div>

							<div class="col-md-2 d-none d-sm-block font-column-title">Alkop Giden</div>

	    					<div class="col-md-2 d-none d-sm-block font-column-title">Alkop Gelen</div>

	    					<div class="col-md-2 col-6">

								<div class="row">
									<div class="col-md-6 col-6 p-0">
									<?php if($ozet == 0){ ?>

										<a href="gelengiden.php?ozet=1"><button class="btn btn-info btn-block btn-sm">Özet Göster</button></a>

									<?php }else{ ?>

										<a href="gelengiden.php?ozet=0"><button class="btn btn-info btn-block btn-sm">Özeti Kapat</button></a>

									<?php } ?>
									</div>
									<div class="col-md-6 col-6">
										<a href="movement-report.php" target="_blank"><button class="btn btn-secondary btn-sm btn-block">Rapor</button></a>
									</div>
								</div>

	    					</div>

	    				</div>

	    				<hr/>
    			
	    			<form action="" method="POST">

	    				<div class="row">
    					
	    					<div class="col-md-2 col-12" style="font-weight: bold;">
								
									<input type="text" id="tarih" name="tarih" value="<?= $tarihf2; ?>" class="form-control form-control-lg">

									<input type="hidden" id="tarih-db" name="tarih2">

	    					</div>

	    					<div class="col-md-2 col-12" style="font-weight: bold;"><input type="text" name="giden" placeholder="SADECE SAYI GİRİNİZ." class="form-control form-control-lg"></div>

	    					<div class="col-md-2 col-12" style="font-weight: bold;"><input type="text" name="gelen" placeholder="SADECE SAYI GİRİNİZ." class="form-control form-control-lg"></div>

							<div class="col-md-2 col-12" style="font-weight: bold;"><input type="text" name="alkop_giden" placeholder="SADECE SAYI GİRİNİZ." class="form-control form-control-lg"></div>

	    					<div class="col-md-2 col-12" style="font-weight: bold;"><input type="text" name="alkop_gelen" placeholder="SADECE SAYI GİRİNİZ." class="form-control form-control-lg"></div>

	    					<div class="col-md-2 col-12"><button type="submit" name="gelengidenkayit" class="btn btn-success btn-block btn-sm">Bugünü Kaydet</button></div>

	    				</div>

	    			</form><hr style="margin:5px 0px 1px 0px;" />

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

									$id = guvenlik($row['id']);
									$gelen = guvenlik($row['gelen']);
									$giden = guvenlik($row['giden']);
									$alkopGelen = guvenlik($row['alkop_gelen']);
									$alkopGiden = guvenlik($row['alkop_giden']);
									$tarih = guvenlik($row['tarih']);
									$saniye = guvenlik($row['saniye']);
									$date = new DateTime($tarih);
									$hafta = $date->format("W");
									$ay = $date->format("m"); 
									$yil = $date->format("Y");

									if($hafta != $hafizahafta){

						?>

										<div class="alert alert-warning" style="margin-left: -10px; margin-right:-10px; color:orange; font-weight:bold; font-size: 22px;">

											<div class="row">
											
												<div class="col-md-2 col-12"><?= $hafta.". Hafta Toplam"; ?></div>

												<div class="col-md-2 col-6"><?= $haftalikGidenToplam." <small>(Giden)</small>"; ?></div>

												<div class="col-md-2 col-6"><?= $haftalikGelenToplam." <small>(Gelen)</small>"; ?></div>

												<div class="col-md-2 col-6"><?= $haftalikAlkopGidenToplam." <small>(Giden)</small>"; ?></div>

												<div class="col-md-2 col-6"><?= $haftalikAlkopGelenToplam." <small>(Gelen)</small>"; ?></div>

											</div>

										</div>

						<?php

										$haftalikGidenToplam = 0; 
										$haftalikGelenToplam = 0;
										$haftalikAlkopGidenToplam = 0; 
										$haftalikAlkopGelenToplam = 0;

										$hafizahafta = $hafta;

									}

									if($ay != $hafizaay){

						?>

										<div class="alert alert-danger" style="margin-left: -10px; margin-right:-10px; color:red; font-weight:bold; font-size: 24px;">
										
											<div class="row">
												
												<div class="col-md-2 col-12"><?= ayAdi($ay+1)." ayı toplamı"; ?></div>

												<div class="col-md-2 col-6"><?= $aylikGidenToplam." <small>(Giden)</small>"; ?></div>

												<div class="col-md-2 col-6"><?= $aylikGelenToplam." <small>(Gelen)</small>"; ?></div>

												<div class="col-md-2 col-6"><?= $aylikAlkopGidenToplam." <small>(Giden)</small>"; ?></div>

												<div class="col-md-2 col-6"><?= $aylikAlkopGelenToplam." <small>(Gelen)</small>"; ?></div>

											</div>

										</div>

						<?php
										$aylikGidenToplam = 0; 
										$aylikGelenToplam = 0;
										$aylikAlkopGidenToplam = 0; 
										$aylikAlkopGelenToplam = 0;

										$hafizaay = $ay;

									}

									if($yil != $hafizayil){

						?>

										<div class="alert alert-secondary" style="margin-left: -10px; margin-right:-10px; color:darkbrown; font-weight:bold; font-size: 26px;">

											<div class="row">
												
												<div class="col-md-2 col-12">Yıllık Toplam</div>

												<div class="col-md-2 col-6"><?= $yillikGidenToplam." <small>(Giden)</small>"; ?></div>

												<div class="col-md-2 col-6"><?= $yillikGelenToplam." <small>(Gelen)</small>"; ?></div>

												<div class="col-md-2 col-6"><?= $yillikAlkopGidenToplam." <small>(Giden)</small>"; ?></div>

												<div class="col-md-2 col-6"><?= $yillikAlkopGelenToplam." <small>(Gelen)</small>"; ?></div>

											</div>

										</div>

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

						?>

									<?php if($ozet == 0){ ?><div id="listedivi"><?php }else{ ?><div id="listedivi" style="display: none;"><?php } ?>

										<form action="" method="POST">

					    				<div class="row mb-1">
				    					
					    					<div class="col-md-2 col-12" style="font-weight: bold; font-size: 18px; padding: 1px 0px 5px 20px;"><?= $tarih; ?></div>

					    					<div class="col-md-2 col-12" style="font-weight: bold;"><input type="text" name="giden" value="<?= $giden; ?>" class="form-control form-control-lg"></div>

					    					<div class="col-md-2 col-12" style="font-weight: bold;"><input type="text" name="gelen" value="<?= $gelen; ?>" class="form-control form-control-lg"></div>

											<div class="col-md-2 col-12" style="font-weight: bold;"><input type="text" name="alkop_giden" value="<?= $alkopGiden; ?>" class="form-control form-control-lg"></div>

					    					<div class="col-md-2 col-12" style="font-weight: bold;"><input type="text" name="alkop_gelen" value="<?= $alkopGelen; ?>" class="form-control form-control-lg"></div>

					    					<div class="col-md-2 col-12">

					    						<input type="hidden" name="id" value="<?= $id; ?>">

					    						<button type="submit" name="gelengidenguncelle" class="btn btn-primary btn-block btn-lg">Düzenle</button></div>

					    				</div>

					    				<hr style="margin: 5px 0px 3px 0px;">

					    			</form>

					    		</div>

						<?php

								}

							}

	    			?>

	    				<div class="alert alert-warning" style="margin-left: -10px; margin-right:-10px; color:orange; font-weight:bold; font-size: 22px;">

								<div class="row">
								
									<div class="col-md-2 col-12"><?= ($hafta-1).". Hafta Toplam"; ?></div>

									<div class="col-md-2 col-6"><?= $haftalikGidenToplam." <small>(Giden)</small>"; ?></div>

									<div class="col-md-2 col-6"><?= $haftalikGelenToplam." <small>(Gelen)</small>"; ?></div>

									<div class="col-md-2 col-6"><?= $haftalikAlkopGidenToplam." <small>(Giden)</small>"; ?></div>

									<div class="col-md-2 col-6"><?= $haftalikAlkopGelenToplam." <small>(Gelen)</small>"; ?></div>

								</div>

							</div>

							<div class="alert alert-danger" style="margin-left: -10px; margin-right:-10px; color:red; font-weight:bold; font-size: 24px;">
										
								<div class="row">
									
									<div class="col-md-2 col-12"><?= ayAdi($ay)." ayı toplamı"; ?></div>

									<div class="col-md-2 col-6"><?= $aylikGidenToplam." <small>(Giden)</small>"; ?></div>

									<div class="col-md-2 col-6"><?= $aylikGelenToplam." <small>(Gelen)</small>"; ?></div>

									<div class="col-md-2 col-6"><?= $aylikAlkopGidenToplam." <small>(Giden)</small>"; ?></div>

									<div class="col-md-2 col-6"><?= $aylikAlkopGelenToplam." <small>(Gelen)</small>"; ?></div>

								</div>

							</div>

							<div class="alert alert-secondary" style="margin-left: -10px; margin-right:-10px; color:darkbrown; font-weight:bold; font-size: 26px;">

								<div class="row">
									
									<div class="col-md-2 col-12"><?= $yil." Yılı Toplamı" ?></div>

									<div class="col-md-2 col-6"><?= $yillikGidenToplam." <small>(Giden)</small>"; ?></div>

									<div class="col-md-2 col-6"><?= $yillikGelenToplam." <small>(Gelen)</small>"; ?></div>

									<div class="col-md-2 col-6"><?= $yillikAlkopGidenToplam." <small>(Giden)</small>"; ?></div>

									<div class="col-md-2 col-6"><?= $yillikAlkopGelenToplam." <small>(Gelen)</small>"; ?></div>

								</div>

							</div>

    			</div>

    		</div>

    	</div>

    </div>

    <?php include 'template/script.php'; ?>

</body>
</html>