<?php 

	include 'functions/init.php';

	if (!isLoggedIn()) {
		
		header("Location:login.php");

		exit();

	}else{

		if (isset($_GET['id']) && empty($_GET['id']) === false) {
			
			$fabrika_id = guvenlik($_GET['id']);

			$fabrikaadcek = $db->query("SELECT * FROM factories WHERE id = '{$fabrika_id}' AND company_id = '{$user->company_id}'")->fetch(PDO::FETCH_ASSOC);

			$fabrika_adi = $fabrikaadcek['name'];

		}

	}

?>

<!DOCTYPE html>

<html>

  <head>

    <title>Alüminyum Deposu</title>

    <?php include 'template/head.php'; ?>

  </head>

  <body>

    <?php include 'template/banner.php' ?>

    <div class="container-fluid">
    	
    	<div class="div4">

    		<div class="row">
    			
    			<div class="col-md-8 col-12">
    				<h5><b style="line-height: 40px;"><?= $fabrika_adi; ?> Sipariş Listesi</b></h5>
    			</div>
    			<div class="col-md-2 col-12">
    				<a href="fabsipcikti.php?id=<?= $fabrika_id; ?>">
    					<button class="btn btn-secondary btn-block">Çıktı Sayfası</button>
    				</a>
    			</div>
    			<div class="col-md-2 col-12">
    				<a href="#" onclick="return false" onmousedown="javascript:ackapa('kilodivi');">
    					<button class="btn btn-info btn-block">Geçmiş Siparişler</button>
    				</a>
    			</div>

    		</div>

    		<div id="kilodivi" style="display:none;">
    			<?php

    				$aytoplami = 0;

    				$yiltoplami = 0;

    				$hafizaay = 0;

    				$hafizayil = 0;

    				$i = 0;

					$sipariscek = $db->query("SELECT * FROM siparis WHERE urun_fabrika_id = '{$fabrika_id}' AND taslak = '0' AND sirketid = '{$user->company_id}' AND silik = '0' ORDER BY siparissaniye DESC", PDO::FETCH_ASSOC);

					if ( $sipariscek->rowCount() ){

						foreach( $sipariscek as $row ){

							$i++;

							$siparis_id = $row['siparis_id'];

							$urun_adi = $row['urun_adi'];

							$hazirlayankisi = $row['hazirlayankisi'];

							$urun_siparis_aded = $row['urun_siparis_aded'];

							$urun_fabrika_id = $row['urun_fabrika_id'];

							$ilgilikisi = $row['ilgilikisi'];

							$siparissaniye = $row['siparissaniye'];

							$terminsaniye = $row['terminsaniye'];

							$siparisboy = $row['siparisboy'];

							$urun_siparis_aded = $row['urun_siparis_aded'];

							$siparistarih = date("d-m-Y", $siparissaniye);

							$siparisayi= date("m", $siparissaniye); if($i == 1){ $hafizaay = $siparisayi; }

							$siparisyili= date("Y", $siparissaniye); if($i == 1){ $hafizayil = $siparisyili; }

							$termintarih = date("d-m-Y", $terminsaniye);

							$urun_id = $row['urun_id'];

							$urunbilcek = $db->query("SELECT * FROM urun WHERE urun_id = '{$urun_id}'")->fetch(PDO::FETCH_ASSOC);

							if($urunbilcek) {

								$kategori_bir = $urunbilcek['kategori_bir'];	

								$kategori_iki = $urunbilcek['kategori_iki'];

								$katadcek = $db->query("SELECT * FROM kategori WHERE kategori_id = '{$kategori_bir}'")->fetch(PDO::FETCH_ASSOC);

								$kategori_bir_adi = $katadcek['kategori_adi'];

								$katadcek = $db->query("SELECT * FROM kategori WHERE kategori_id = '{$kategori_iki}'")->fetch(PDO::FETCH_ASSOC);

								$kategori_iki_adi = $katadcek['kategori_adi'];

								$urun_birimkg = $urunbilcek['urun_birimkg'];

								$kilo = $urun_siparis_aded * $urun_birimkg;

								if($siparisayi == $hafizaay){

									$aytoplami += $kilo;

								}

								if($siparisyili == $hafizayil){

									$yiltoplami += $kilo;

								}

								if($siparisyili != $hafizayil){

					?>

									<hr style="border-color: black; border-width: 2px;" />

									<div class="row">
										<div class="col-12" style="font-weight: bold; font-size: 21px; color:red;">
											<?= ($siparisyili + 1)." yılında ".$yiltoplami." kg sipariş teslim alınmıştır."; ?>
										</div>
									</div>

					<?php
									$yiltoplami = $kilo;
									$hafizayil = $siparisyili;

								}

								if($siparisayi != $hafizaay){

					?>

									<hr style="border-color: black; border-width: 2px;" />

									<div class="row">
										<div class="col-12">
											<?= $siparisyili." ".ayAdi($siparisayi + 1)." ayında ".$aytoplami." kg sipariş teslim alınmıştır."; ?>
										</div>
									</div>

					<?php
									$aytoplami = $kilo;
									$hafizaay = $siparisayi;

								}

							}

						}

					}

					?>
    			<hr style="border-color: black; border-width: 2px;" />
    		</div>

				<div class="d-none d-sm-block">

					<div class="row">
									
						<div class="col-2"><b>Malzeme</b></div>

						<div class="col-1"><b>Boy</b></div>

						<div class="col-1"><b>Miktar Adet</b></div>

						<div class="col-1"><b>Kilo</b></div>	

						<div class="col-1"><b>Kategori</b></div>	

						<div class="col-1"><b>Miktar</b></div>

						<div class="col-1"><b>Tarih</b></div>

						<div class="col-1"><b>Termin</b></div>

					</div>

				</div>
    		
    		<?php

    			$toplamkilo = 0;

					$sipariscek = $db->query("SELECT * FROM siparis WHERE urun_fabrika_id = '{$fabrika_id}' AND taslak = '1' AND sirketid = '{$user->company_id}' AND silik = '0' ORDER BY siparissaniye DESC", PDO::FETCH_ASSOC);

					if ( $sipariscek->rowCount() ){

						foreach( $sipariscek as $row ){

							$siparis_id = $row['siparis_id'];

							$urun_adi = $row['urun_adi'];

                            $urun_id = $row['urun_id'];

                            if(controlProductById($urun_id)){

                                $hazirlayankisi = $row['hazirlayankisi'];

                                $urun_siparis_aded = $row['urun_siparis_aded'];

                                $urun_fabrika_id = $row['urun_fabrika_id'];

                                $ilgilikisi = $row['ilgilikisi'];

                                $siparissaniye = $row['siparissaniye'];

                                $terminsaniye = $row['terminsaniye'];

                                $siparisboy = $row['siparisboy'];

                                $urun_siparis_aded = $row['urun_siparis_aded'];

                                $siparistarih = date("d-m-Y", $siparissaniye);

                                $termintarih = date("d-m-Y", $terminsaniye);

                                $urunbilcek = $db->query("SELECT * FROM urun WHERE urun_id = '{$urun_id}'")->fetch(PDO::FETCH_ASSOC);
                                if($urunbilcek) {
                                    $kategori_bir = $urunbilcek['kategori_bir'];
                                    $kategori_iki = $urunbilcek['kategori_iki'];
                                    $katadcek = $db->query("SELECT * FROM kategori WHERE kategori_id = '{$kategori_bir}'")->fetch(PDO::FETCH_ASSOC);
                                    if($katadcek) {
                                        $kategori_bir_adi = $katadcek['kategori_adi'];
                                    }else {
                                        $kategori_bir_adi = "";
                                    }
                                    $katadcek = $db->query("SELECT * FROM kategori WHERE kategori_id = '{$kategori_iki}'")->fetch(PDO::FETCH_ASSOC);
                                    if($katadcek) {
                                        $kategori_iki_adi = $katadcek['kategori_adi'];
                                    }else{
                                        $kategori_iki_adi = "";
                                    }
                                    $urun_birimkg = $urunbilcek['urun_birimkg'];
                                }else{
                                    $urun_birimkg = 0;
                                }

                                $kilo = $urun_siparis_aded * $urun_birimkg;

                                $toplamkilo += $kilo;

				?>

                                <hr style="border-color: black; border-width: 2px;" />

                                <form action="" method="POST">

                                    <div class="row" style="margin: 2px;">

                                        <div class="col-4 d-block d-sm-none">Malzeme :</div>

                                        <div class="col-md-2 col-8"><?= $urun_adi." ".$kategori_iki_adi; ?></div>

                                        <div class="col-4 d-block d-sm-none">Boy :</div>

                                        <div class="col-md-1 col-8"><?= $siparisboy; ?></div>

                                        <div class="col-4 d-block d-sm-none">Miktar :</div>

                                        <div class="col-md-1 col-8"><?= $urun_siparis_aded." adet "; ?></div>

                                        <div class="col-4 d-block d-sm-none">Kilo :</div>

                                        <div class="col-md-1 col-8"><?= $kilo." KG"; ?></div>

                                        <div class="col-4 d-block d-sm-none">Kategori :</div>

                                        <div class="col-md-1 col-8"><?= $kategori_bir_adi; ?></div>

                                        <div class="col-4 d-block d-sm-none">Miktar :</div>

                                        <div class="col-md-1 col-8"><input type="text" name="eklenenadet" class="form-control form-control-sm" value="<?= $urun_siparis_aded; ?>"></div>

                                        <div class="col-4 d-block d-sm-none">Tarih :</div>

                                        <div class="col-md-1 col-8"><?= $siparistarih; ?></div>

                                        <div class="col-4 d-block d-sm-none">Termin :</div>

                                        <div class="col-md-1 col-8"><?= $termintarih; ?></div>

                                    </div>

                                </form>

				<?php
                            }

						}

					}

				?>
				<hr style="border-color: black; border-width: 2px;" />
				<div class="row" style="font-size: 27px;">
					<div class="col-4" style="text-align: right;"><b>Toplam Kilo : </b></div>
					<div class="col-8"><b style="color:red;"><?= $toplamkilo." KG"; ?></b></div>
				</div>

    	</div>

    </div>

    <br/><br/><br/>

    <?php include 'template/script.php'; ?>

</body>
</html>