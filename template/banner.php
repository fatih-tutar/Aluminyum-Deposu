<?php if($girdi == 1){ ?>

	<div class="container-fluid" style="position: fixed; z-index: 5; background-color: black;">

		<div class="row">

			<div class="col-xl-2 col-lg-3 col-md-4 col-sm-5 col-7" style="text-align: left; padding-top: 10px; padding-bottom: 10px;">

				<a href="index.php"><img src="img/file/<?php echo $sirketlogo; ?>" class="img-responsive" alt="Alüminyum Deposu" width="100%" height="auto"></a>

			</div>

			<div class="col-xl-10 col-lg-9 col-md-8 col-sm-7 col-5 d-flex align-items-center justify-content-end" style="text-align: right;">

				<div class="dropdown" style="margin: 10px;">
				
					<button class="btn btn-primary dropdown-toggle" style="background-color:black; border-style: none;" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				
						<?= $uye_adi ?>
				
					</button>
				
					<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
				
						<a class="dropdown-item" href="index.php"><b>ANA SAYFA</b></a>
				
						<a class="dropdown-item" href="profil.php?id=<?php echo $uye_id; ?>"><b>PROFİL</b></a>
				
						<?php if($uye_tipi == '2' || $uye_tipi == '1' || $uye_tipi == '3'){?>
				
							<?php if($uye_tipi == '2'){?>

								<hr class="m-1"/>

								<a class="dropdown-item" href="fiyatlar.php"><b>FİYATLAR</b></a>

								<a class="dropdown-item" href="plan.php"><b>PLAN</b></a>

								<a class="dropdown-item" href="yonetim.php"><b>YÖNETİM</b></a>

								<hr class="m-1"/>

							<?php } ?>

							<?php if($uye_ziyaret_yetkisi == '1'){?>

								<a class="dropdown-item" href="ziyaretler.php"><b>ZİYARETLER</b></a>

							<?php } ?>

							<?php if($uye_gelen_giden_yetkisi == '1'){?>

								<a class="dropdown-item" href="gelengiden.php"><b>GELEN/GİDEN</b></a>

							<?php } ?>

							<?php if($uye_islemleri_gorme_yetkisi == '1'){?>

								<a class="dropdown-item" href="islemler.php"><b>İŞLEMLER</b></a>

							<?php } ?>

							<?php if($uye_tipi == '2' || $uye_tipi == '1' || $uye_tipi == '3'){?>
								
								<a class="dropdown-item" href="kategoriler.php"><b>KATEGORİLER</b></a>
							
							<?php } ?>

							<hr class="m-1"/>

							<a class="dropdown-item" href="yardim.php"><b>YARDIM</b></a>
					
						<?php } ?>

						<a class="dropdown-item" href="cikis.php"><b>ÇIKIŞ</b></a>
				
					</div>
				
				</div>

			</div>

		</div>

	</div>

<?php }elseif ($girdi == 0) { ?>

	<div class="container-fluid" style="position: fixed; z-index: 2; background-color: white;">

		<div class="row">

			<div class="col-xl-10 col-lg-8 col-md-6 col-sm-6 col-6" style="text-align: left; padding-top: 10px; padding-bottom: 10px;">

				<a href="index.php"><img src="img/defaultlogo.png" class="img-responsive" alt="Alüminyum Deposu" width="236" height="85"></a>

			</div>

			<div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 col-6" style="text-align: right; padding-top: 30px;">

				<!--<div class="btn-group" role="group" aria-label="Basic example">
					
					<button type="button" class="btn btn-danger" onclick="location.href='uyeol.php'"><i class="fa fa-user-plus"></i>&nbsp;&nbsp;&nbsp;Üye Ol</button>
				
				</div>-->

			</div>

		</div>

	</div>

<?php } ?>

<br/><br/><br/><br/>

<?php

if(giris_yapti_mi() === true){

	if(($su_an - (60 * 60 * 7)) > $sirketyedekalmasaniye && $uye_tipi == '2'){

		otomatikyedekal($su_an, $uye_sirket, $sayfa);

	}

}

?>