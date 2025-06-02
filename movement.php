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
			$weekly = 0;
			if(isset($_GET['weekly']) && $_GET['weekly'] == 1 && empty($_GET['weekly']) === false){
				$weekly = 1;
			}
			//Bugünün kaydı var mı anlayabilmek için son kaydı çekiyoruz.
            $movements = $db->query("SELECT * FROM movements ORDER BY date DESC")->fetchAll(PDO::FETCH_OBJ);
            $lastMovement = $movements[0];
            $lastDate = $lastMovement->date;
            $dateObj = new DateTime($lastDate);
            $tempWeek = $dateObj->format("W");
            $tempMonth = $dateObj->format("m");
            $tempYear = $dateObj->format("Y");
            if(isset($_POST['save_movement'])){
                $incoming = str_replace(",",".", guvenlik($_POST['incoming']));
                $outgoing = str_replace(",",".", guvenlik($_POST['outgoing']));
                $warehouseIncoming = str_replace(",",".", guvenlik($_POST['warehouse_incoming']));
                $warehouseOutgoing = str_replace(",",".", guvenlik($_POST['warehouse_outgoing']));
                $date = guvenlik($_POST['date']);
                if(movementDateExists($date) != true){
                    $query = $db->prepare("INSERT INTO movements SET incoming = ?, outgoing = ?, warehouse_incoming = ?, warehouse_outgoing = ?, date = ?, is_deleted = ?");
                    $insert = $query->execute(array($incoming,$outgoing,$warehouseIncoming,$warehouseOutgoing,$date,'0'));
                    header("Location:movement.php");
                    exit();
                }else{
                    $error = '<br/><div class="alert alert-danger" role="alert">Bu tarihle zaten bir kayıt var onu düzenleyebilirsiniz.</div>';
                }
            }
            if (isset($_POST['update_movement'])) {
                $id = guvenlik($_POST['id']);
                $incoming = str_replace(",",".", guvenlik($_POST['incoming']));
                $outgoing = str_replace(",",".", guvenlik($_POST['outgoing']));
                $warehouseIncoming = str_replace(",",".", guvenlik($_POST['warehouse_incoming']));
                $warehouseOutgoing = str_replace(",",".", guvenlik($_POST['warehouse_outgoing']));
                $query = $db->prepare("UPDATE movements SET incoming = ?, outgoing = ?, warehouse_incoming = ?, warehouse_outgoing = ? WHERE id = ?");
                $update = $query->execute(array($incoming,$outgoing,$warehouseIncoming,$warehouseOutgoing,$id));
                header("Location:movement.php");
                exit();
            }
        }
	}
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Gelen Giden</title>
    <?php include 'template/head.php'; ?>
  </head>
  <body>
    <?php include 'template/banner.php' ?>
    <?= $error; ?>
    <div class="container-fluid">
    	<div class="row">
    		<div class="col-12">
				<div class="div4">
					<?php
						$totalStoreTonnage = 0;
                        $totalWarehouseTonnage = 0;
                        $products = $db->query("SELECT urun_adet, urun_palet, urun_depo_adet, urun_birimkg FROM urun")->fetchAll(PDO::FETCH_OBJ);
						foreach ($products as $product) {
                            $quantity = $product->urun_adet;
                            $paletQuantity = $product->urun_palet;
                            $warehouseQuantity = $product->urun_depo_adet;
                            $unitKg = $product->urun_birimkg;
                            $storeTonnage = $quantity * $unitKg;
                            $warehouseTonnage = ($warehouseQuantity * $paletQuantity) * $unitKg;
                            $totalStoreTonnage += $storeTonnage;
                            $totalWarehouseTonnage += $warehouseTonnage;
                        }
					?>
					<h1><b>Çağlayan Toplam Tonaj : <span style="color:red;"><?= $totalStoreTonnage." KG"; ?></span></b></h1>
					<h1><b>Alkop Toplam Tonaj : <span style="color:red;"><?= $totalWarehouseTonnage." KG"; ?><span></b></h1>
				</div>
    			<div class="div4">
                    <div class="row">
                        <div class="col-md-2 d-none d-sm-block fs-23 fw-bold">Tarih</div>
                        <div class="col-md-2 d-none d-sm-block fs-23 fw-bold">Çağlayan Giden</div>
                        <div class="col-md-2 d-none d-sm-block fs-23 fw-bold">Çağlayan Gelen</div>
                        <div class="col-md-2 d-none d-sm-block fs-23 fw-bold">Alkop Giden</div>
                        <div class="col-md-2 d-none d-sm-block fs-23 fw-bold">Alkop Gelen</div>
                        <div class="col-md-2 col-6">
                            <div class="row">
                                <div class="col-md-6 col-6 p-0">
                                <?php if($weekly == 0){ ?>
                                    <a href="movement.php?weekly=1"><button class="btn btn-info btn-block btn-sm">Özet Göster</button></a>
                                <?php }else{ ?>
                                    <a href="movement.php?weekly=0"><button class="btn btn-info btn-block btn-sm">Özeti Kapat</button></a>
                                <?php } ?>
                                </div>
                                <div class="col-md-6 col-6">
                                    <a href="ggrapor.php" target="_blank"><button class="btn btn-secondary btn-sm btn-block">Rapor</button></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr/>
	    			<form action="" method="POST">
	    				<div class="row">
	    					<div class="col-md-2 col-12" style="font-weight: bold;">
                                <input type="date" name="date" class="form-control form-control-lg" value="<?= date('Y-m-d') ?>"/>
	    					</div>
	    					<div class="col-md-2 col-12" style="font-weight: bold;"><input type="text" name="outgoing" placeholder="SADECE SAYI GİRİNİZ." class="form-control form-control-lg"></div>
	    					<div class="col-md-2 col-12" style="font-weight: bold;"><input type="text" name="incoming" placeholder="SADECE SAYI GİRİNİZ." class="form-control form-control-lg"></div>
							<div class="col-md-2 col-12" style="font-weight: bold;"><input type="text" name="warehouse_outgoing" placeholder="SADECE SAYI GİRİNİZ." class="form-control form-control-lg"></div>
	    					<div class="col-md-2 col-12" style="font-weight: bold;"><input type="text" name="warehouse_incoming" placeholder="SADECE SAYI GİRİNİZ." class="form-control form-control-lg"></div>
	    					<div class="col-md-2 col-12"><button type="submit" name="save_movement" class="btn btn-success btn-block btn-sm">Bugünü Kaydet</button></div>
	    				</div>
	    			</form>
                    <hr style="margin:5px 0px 1px 0px;" />
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

									if($hafta != $tempWeek){

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

										$tempWeek = $hafta;

									}

									if($ay != $tempMonth){

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

										$tempMonth = $ay;

									}

									if($yil != $tempYear){

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

										$tempYear = $yil;

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

									<?php if($weekly == 0){ ?><div id="listedivi"><?php }else{ ?><div id="listedivi" style="display: none;"><?php } ?>

										<form action="" method="POST">

					    				<div class="row mb-1">
				    					
					    					<div class="col-md-2 col-12" style="font-weight: bold; font-size: 18px; padding: 1px 0px 5px 20px;"><?= $tarih; ?></div>

					    					<div class="col-md-2 col-12" style="font-weight: bold;"><input type="text" name="outgoing" value="<?= $giden; ?>" class="form-control form-control-lg"></div>

					    					<div class="col-md-2 col-12" style="font-weight: bold;"><input type="text" name="incoming" value="<?= $gelen; ?>" class="form-control form-control-lg"></div>

											<div class="col-md-2 col-12" style="font-weight: bold;"><input type="text" name="warehouse_outgoing" value="<?= $alkopGiden; ?>" class="form-control form-control-lg"></div>

					    					<div class="col-md-2 col-12" style="font-weight: bold;"><input type="text" name="warehouse_incoming" value="<?= $alkopGelen; ?>" class="form-control form-control-lg"></div>

					    					<div class="col-md-2 col-12">

					    						<input type="hidden" name="id" value="<?= $id; ?>">

					    						<button type="submit" name="update_movement" class="btn btn-primary btn-block btn-lg">Düzenle</button></div>

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