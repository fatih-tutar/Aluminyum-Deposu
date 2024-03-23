<?php 

	include 'fonksiyonlar/bagla.php'; 

?>

<!DOCTYPE html>

<html>

  <head>

    <title>Alüminyum Deposu</title>

    <?php include 'template/head.php'; ?>

  </head>

  <body style="background-color: white">

    <div class="container" style="padding-left: 20px; padding-right: 20px;">

    	<div style="padding: 0px;">

			<div class="row">
    		
    		<div class="col-6" style="padding-top: 4%; text-align: center;"><img src="img/osmanlilogo.jpg" style="width: 75%; height: auto;"></div>

    		<div class="col-6" style="text-align: center; padding: 3px;"><img src="img/aydinlatmalogo.jpg" style="width: 75%; height: auto;"></div>

    	</div>	

    	<div class="row" style="margin-left:-10px;">
    		
    		<div class="col-md-7 col-12">
    			<b style="color:red; font-size:100%;">FİYATLARIMIZ KG X BİRİM FİYAT = METRE MALİYET OLARAK LİSTELENMİŞTİR.</b>
    		</div>
    		<div class="col-md-5 col-12">
    			<?php if($sirketfiyatlistesi == 1){ ?>
    			<b style="font-size:100%;">FİYAT GÜNCELLEME SIRASINDA FİYAT ALMAK İÇİN ARAYINIZ.</b>
    			<?php } ?>
    		</div>

    	</div>
    	
	    	<?php

				$hafizaurunno = "";

	    		$query = $db->query("SELECT * FROM fiyatlar WHERE silik = '0' ORDER BY sira ASC", PDO::FETCH_ASSOC);

				if ( $query->rowCount() ){

					foreach( $query as $row ){

						$fiyatid = guvenlik($row['fiyatid']);
						$urunno = guvenlik($row['urunno']);
						$resim1 = guvenlik($row['resim1']);
						$resim2 = guvenlik($row['resim2']);
						$aciklama = guvenlik($row['aciklama']);

			?>

						<?php if($hafizaurunno != $urunno){ ?>

						<div class="row" style="background-color: dodgerblue; color: white; padding-top: 7px; padding-bottom: 7px;">
				    		
				    		<div class="col-sm-2 col-12">

				    			<div class="row">
		    		
						    		<div class="col-sm-7 col-4" style="padding: 0px 0px 0px 5px;"><b>ÜRÜN NO : </b></div>

						    		<div class="col-sm-5 col-8" style="padding: 0px;"><b><?php echo $urunno; ?></b></div>

						    	</div>

				    		</div>

				    		<div class="col-8 d-none d-sm-block">

				    			<div class="row">
				    				
				    				<div class="col-2"><b>KOD</b></div>

						    		<div class="col-4"><b>MODEL</b></div>

						    		<div class="col-4"><b>ADET / METRE</b></div>

						    		<div class="col-2"><b>FİYAT</b></div>

				    			</div>		

				    		</div>	

				    		<div class="col-2"></div>

				    	</div>

				    	<div class="row">
				    		
				    		<div class="col-sm-2 col-6" style="border:1px solid darkblue;">

				    			<img src="img/fiyatlar/<?php echo $resim1; ?>" style="width: 100%; height: auto;">

				    		</div>

				    		<div class="col-6 d-block d-sm-none" style="border:1px solid darkblue;">

				    			<img src="img/fiyatlar/<?php echo $resim2; ?>" style="width: 100%; height: auto;">

				    		</div>

				    		<div class="col-12 d-block d-sm-none">

				    			<div class="row">
				    				
				    				<div class="col-2" style="padding: 0px; text-align: center; border: 1px solid darkblue;"><b style="font-size: 13px;">KOD</b></div>

						    		<div class="col-4" style="padding: 0px; text-align: center; border: 1px solid darkblue;"><b style="font-size: 13px;">MODEL</b></div>

						    		<div class="col-4" style="padding: 0px; text-align: center; border: 1px solid darkblue;"><b style="font-size: 13px;">ADET / METRE</b></div>

						    		<div class="col-2" style="padding: 0px; text-align: center; border: 1px solid darkblue;"><b style="font-size: 13px;">FİYAT</b></div>

				    			</div>		

				    		</div>	

				    		<div class="col-sm-8 col-12">

				    		<?php

				    			$icsorgu = $db->query("SELECT * FROM fiyatlar WHERE urunno = '{$urunno}' AND silik = '0' ORDER BY fiyatid ASC", PDO::FETCH_ASSOC);

								if ( $icsorgu->rowCount() ){

									foreach( $icsorgu as $row ){
										$kod = guvenlik($row['kod']);
										$model = guvenlik($row['model']);
										$adetmetre = guvenlik($row['adetmetre']);
										$fiyat = guvenlik($row['fiyat']);

				    		?>

				    			<div class="d-none d-sm-block">

					    			<div class="row">
					    				
					    				<div class="col-2" style="padding: 0px 0px 0px 5px; border: solid 1px darkblue;"><?php echo $kod; ?></div>

							    		<div class="col-4" style="padding: 0px 0px 0px 5px; border: solid 1px darkblue;"><?php echo $model; ?></div>

							    		<div class="col-4" style="padding: 0px 0px 0px 5px; border: solid 1px darkblue;"><?php echo $adetmetre; ?></div>

							    		<div class="col-2" style="padding: 0px 0px 0px 5px; border: solid 1px darkblue;">
							    			<?php if($sirketfiyatlistesi == 0){ echo $fiyat; }else{ echo "Güncelleniyor..."; } ?>
							    		</div>

					    			</div>	

				    			</div>

				    			<div class="d-block d-sm-none">

					    			<div class="row">
					    				
					    				<div class="col-2" style="padding: 0px 0px 0px 5px; border: solid 1px darkblue; font-size: 13px;"><?php echo $kod; ?></div>

							    		<div class="col-4" style="padding: 0px 0px 0px 5px; border: solid 1px darkblue; font-size: 13px;"><?php echo $model; ?></div>

							    		<div class="col-4" style="padding: 0px 0px 0px 5px; border: solid 1px darkblue; font-size: 13px;"><?php echo $adetmetre; ?></div>

							    		<div class="col-2" style="padding: 0px 0px 0px 5px; border: solid 1px darkblue; font-size: 13px;"><?php echo $fiyat; ?></div>

					    			</div>	

				    			</div>

				    		<?php } } ?>

				    			<div class="d-none d-sm-block">

					    			<div class="row" style="padding: 10px 0px 10px 0px; border: solid 1px darkblue;">
					    		
							    		<div class="col-12"><em><u><b>AÇIKLAMA :</b></u></em> <?php echo $aciklama; ?></div>

							    	</div>		

				    			</div>

				    			<div class="d-block d-sm-none">

					    			<div class="row" style="padding: 10px 0px 10px 0px; border: solid 1px darkblue; font-size: 13px;">
				    		
							    		<div class="col-12"><em><u><b>AÇIKLAMA :</b></u></em> <?php echo $aciklama; ?></div>

							    	</div>	

				    			</div>

				    			

				    		</div>	

				    		<div class="col-2 d-none d-sm-block" style="border:1px solid darkblue;"><img src="img/fiyatlar/<?php echo $resim2; ?>" style="width: 100%; height: auto;"></div>

				    	</div>

			<?php

						}

						$hafizaurunno = $urunno;

					}

				}

	    	?>

	    	<hr/>

	    	<div class="row">

	    		<div class="col-sm-8 col-12" style="text-align: left; font-size: 100%;">
	    			
	    			<h4>İLETİŞİM</h4>
					<b>OSMANLI ALÜMİNYUM SAN. ve TİC.LTD.ŞTİ.</b><br/>
					Mrk : Hürriyet mah. Eğitim Sk. No. 38 Kağıthane / İSTANBUL<br/>

	    		</div>
	    		
	    		<div class="col-sm-4 col-12" style="text-align: left; font-size: 100%;">

					Tel : (0212) 224 67 56 - 234 03 64 <br/>
					Tarık Türkyılmaz  : 0541 418 92 95<br/>
					Sipariş : tarik@osmanlialuminyum.com.tr<br/>
					Email :info@osmanlialuminyum.com.tr<br/>

	    		</div>

	    	</div>

	    	<hr/>

	    	<br/><br/><br/><br/>

	    </div>

    </div>

    <?php include 'template/script.php'; ?>

</body>
</html>