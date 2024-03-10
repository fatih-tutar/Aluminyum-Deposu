<div class="container-fluid" style="position: fixed; z-index: 2; background-color: white;">

	<div class="row">

		<?php if($girdi == 1){ ?>

			<div class="col-xl-2 col-lg-3 col-md-4 col-sm-5 col-7" style="text-align: left; padding-top: 10px; padding-bottom: 10px;">

				<a href="index.php"><img src="img/file/<?php echo $sirketlogo; ?>" class="img-responsive" alt="Alüminyum Deposu" width="100%" height="auto"></a>

			</div>

			<div class="col-xl-10 col-lg-9 col-md-8 col-sm-7 col-5" style="text-align: right;">

				<div class="dropdown" style="margin: 10px;">
				
					<button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				
						Menü
				
					</button>
				
					<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
				
						<a class="dropdown-item" href="index.php" style="margin: 10px;"><b>ANA SAYFA</b></a>
				
						<a class="dropdown-item" href="profil.php?id=<?php echo $uye_id; ?>" style="margin: 10px;"><b>PROFİL</b></a>
				
						<?php if($uye_tipi == '2' || $uye_tipi == '1' || $uye_tipi == '3'){?>
				
							<?php if($uye_tipi == '2'){?>

								<a class="dropdown-item" href="yonetim.php" style="margin: 10px;"><b>YÖNETİM</b></a>

							<?php } ?>

							<?php if($uye_islemleri_gorme_yetkisi == '1'){?>

								<a class="dropdown-item" href="islemler.php" style="margin: 10px;"><b>İŞLEMLER</b></a>

							<?php } ?>

							<?php if($uye_tipi == '2' || $uye_tipi == '1'){?><a class="dropdown-item" href="kategoriler.php" style="margin: 10px;"><b>KATEGORİLER</b></a><?php } ?>

							<a class="dropdown-item" href="fabrikalar.php" style="margin: 10px;"><b>FABRİKALAR</b></a>

							<a class="dropdown-item" href="firmalar.php" style="margin: 10px;"><b>FİRMALAR</b></a>

							<a class="dropdown-item" href="kaliplar.php" style="margin: 10px;"><b>KALIPLAR</b></a>
					
						<?php } ?>

						<a class="dropdown-item" href="cikis.php" style="margin: 10px;"><b>ÇIKIŞ</b></a>
				
					</div>
				
				</div>

			</div>

		<?php }elseif ($girdi == 0) { ?>

			<div class="col-xl-10 col-lg-8 col-md-6 col-sm-6 col-6" style="text-align: left; padding-top: 10px; padding-bottom: 10px;">

				<a href="index.php"><img src="img/defaultlogo.png" class="img-responsive" alt="Alüminyum Deposu" width="236" height="85"></a>

			</div>

			<div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 col-6" style="text-align: right; padding-top: 30px;">

				<div class="btn-group" role="group" aria-label="Basic example">
					
					<button type="button" class="btn btn-danger" onclick="location.href='uyeol.php'"><i class="fa fa-user-plus"></i>&nbsp;&nbsp;&nbsp;Üye Ol</button>
				
				</div>

			</div>

		<?php } ?>

	</div>

</div>

<br/><br/><br/><br/>

<?php

if(($su_an - (60 * 60 * 7)) > $sirketyedekalmasaniye && $uye_tipi == '2'){

	otomatikyedekal($su_an, $uye_sirket, $sayfa);

}

?>