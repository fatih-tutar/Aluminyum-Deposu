<?php 

	include 'fonksiyonlar/bagla.php'; 

	if ($girdi != '1') {
		
		header("Location:giris.php");

		exit();

	}else{

        if(isset($_POST['faturasikesilenegerial'])){
			$sevkiyatID = guvenlik($_POST['sevkiyatID']);
			$query = $db->prepare("UPDATE sevkiyat SET durum = ? WHERE id = ?");
			$update = $query->execute(array('2',$sevkiyatID));
			header("Location: home.php");
			exit();
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
    
    <div class="div4">
        <div class="row">
            <div class="col-md-2">Firma</div>
            <div class="col-md-1">Ürünler</div>
            <div class="col-md-1">Kilo</div>
            <div class="col-md-5">
                <div class="row">
                    <div class="col-md-4">Oluşturan</div>
                    <div class="col-md-4">Hazırlayan</div>
                    <div class="col-md-4">Faturalayan</div>
                </div>
            </div>   
            <div class="col-md-1">Tarih</div>
            <div class="col-md-2"></div>
        </div>
    </div>

    <div class="div4">
        <?php
            $yeniSevkiyatlar = $db->query("SELECT * FROM sevkiyat WHERE durum = '3' AND sirket_id = '{$uye_sirket}' AND silik = '0' ORDER BY saniye DESC", PDO::FETCH_ASSOC);
            if($yeniSevkiyatlar->rowCount()){
                foreach($yeniSevkiyatlar as $sevkiyat){
                    $sevkiyatID = guvenlik($sevkiyat['id']);
                    $urunler = guvenlik($sevkiyat['urunler']);
                    $urunArray = explode(",",$urunler);
                    $firmaId = guvenlik($sevkiyat['firma_id']);
                    $firmaAdi = getFirmaAdi($firmaId);
                    $adetler = guvenlik($sevkiyat['adetler']);
                    $adetArray = explode(",",$adetler);
                    $kilolar = guvenlik($sevkiyat['kilolar']);
                    if(strpos($kilolar, ',')){
                        $kiloArray = explode(",",$kilolar);
                        $toplamkg = 0;
                        foreach($kiloArray as $kilo){
                            $toplamkg += $kilo;
                        }
                    }
                    $fiyatlar = guvenlik($sevkiyat['fiyatlar']);
                    $fiyatArray = explode("-",$fiyatlar);
                    $olusturan = guvenlik($sevkiyat['olusturan']);
                    $hazirlayan = guvenlik($sevkiyat['hazirlayan']);
                    $faturaci = guvenlik($sevkiyat['faturaci']);
                    $sevkTipi = guvenlik($sevkiyat['sevk_tipi']);
                    $sevkTipleri = ['Müşteri Çağlayan','Müşteri Alkop','Tarafımızca sevk','Ambara tarafımızca sevk'];
                    $aciklama = guvenlik($sevkiyat['aciklama']);
                    $saniye = guvenlik($sevkiyat['saniye']);
                    $tarih = getdmY($saniye);
        ?>
                    <div class="p-2">
                        <form action="" method="POST">
                            <div class="row">
                                <div class="col-md-2 col-6"><b>Firma :</b> <?= $firmaAdi ?></div>
                                <div class="col-md-1 col-2">
                                    <a href="#" onclick="return false" onmousedown="javascript:ackapa4('faturali-siparis-<?= $sevkiyatID ?>');">
                                        Ürünler
                                    </a>
                                </div>
                                <div class="col-md-1"><?= strpos($kilolar,",") ? $toplamkg : $kilolar ?></div>
                                <div class="col-md-5">
                                    <div class="row">
                                        <div class="col-md-4"><?= getUsername($olusturan) ?></div>
                                        <div class="col-md-4"><?= getUsername($hazirlayan) ?></div>
                                        <div class="col-md-4"><?= getUsername($faturaci) ?></div>
                                    </div>
                                </div>
                                <div class="col-md-1 col-6"><?= $tarih ?></div>
                                <div class="col-md-1 col-6">
                                    <input type="hidden" name="sevkiyatID" value="<?= $sevkiyatID ?>">
                                    <button type="submit" name="faturasikesilenegerial" class="btn btn-dark btn-block btn-sm">Geri Al</button>
                                </div>
                                <div class="col-md-1 col-6">
                                    <a href="sevkiyatformu.php?id=<?= $sevkiyatID ?>" target="_blank" class="btn btn-dark btn-block btn-sm">
                                        Yazdır
                                    </a>
                                </div>
                            </div>
                            <div id="faturali-siparis-<?= $sevkiyatID ?>" style="display:none;">
                                <hr class="my-1" style="border-top:1px solid white;"/>
                                <div class="d-none d-sm-block">
                                    <div class="row">
                                        <div class="col-md-4"><b>Ürün</b></div>
                                        <div class="col-md-2"><b>Cinsi</b></div>
                                        <div class="col-md-2"><b>Adet</b></div>
                                        <div class="col-md-2"><b>Kg</b></div>
                                        <div class="col-md-2"><b>Fiyat</b></div>
                                    </div>
                                    <hr class="my-1" style="border-top:1px solid white;"/>
                                </div>
                                <?php
                                    $totalWeight = 0;
                                    $totalPrice = 0;
                                    $malzemeAdeti = 0;
                                    foreach($urunArray as $key => $urunId){
                                        $urun = getUrunInfo($urunId);
                                ?>
                                        <div class="row mb-1">
                                            <div class="col-4 d-block d-sm-none">Ürün Adı : </div>
                                            <div class="col-md-4 col-8"><?= $urun['urun_adi'] ?></div>
                                            <div class="col-4 d-block d-sm-none">Cinsi : </div>
                                            <div class="col-md-2 col-8"><?= getCategoryShortName($urun['kategori_bir']) ?></div>
                                            <div class="col-4 d-block d-sm-none">Adet : </div>
                                            <div class="col-md-2 col-8"><?= $adetArray[$key] ?></div>
                                            <div class="col-4 d-block d-sm-none">Kilo : </div>
                                            <div class="col-md-2 col-8"><?= strpos($kilolar,",") ? $kiloArray[$key] : '' ?></div>
                                            <div class="col-4 d-block d-sm-none">Fiyat : </div>
                                            <div class="col-md-2 col-8 px-3 px-sm-0"><?= $fiyatArray[$key].' TL' ?></div>
                                        </div>
                                        <hr class="my-1" style="border-top:1px solid white;"/>
                                <?php
                                        $malzemeAdeti++;
                                    }
                                ?>
                                <div class="row">
                                    <div class="col-md-6 col-12"></div>
                                    <div class="col-md-2 col-4"><b>Toplam</b></div>
                                    <div class="col-md-4 col-4"><?= strpos($kilolar,",") ? $toplamkg.' KG' : $kilolar.' KG' ?></div>
                                </div>
                                <hr class="my-1" style="border-top:1px solid white;"/>
                                <div class="row">
                                    <div class="col-md-3 col-12"><b>Sevk Tipi: </b><?= $sevkTipleri[$sevkTipi] ?></div>
                                    <div class="col-md-9 col-12"><b>Açıklama: </b><?= $aciklama ?></div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <hr class="m-1"/>
        <?php
                }
            }
        ?>
    </div>

    <?php include 'template/script.php'; ?>

</body>
</html>