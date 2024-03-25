<?php

    include 'fonksiyonlar/bagla.php';


    $sevkiyatID = guvenlik($_GET['id']);

    $sevkiyat = $db->query("SELECT * FROM sevkiyat WHERE id = '{$sevkiyatID}' ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);

    $urunler = guvenlik($sevkiyat['urunler']);
    $urunArray = explode(",",$urunler);
    $firmaId = guvenlik($sevkiyat['firma_id']);
    $firmaAdi = getFirmaAdi($firmaId);
    $adetler = guvenlik($sevkiyat['adetler']);
    $adetArray = explode(",",$adetler);
    $kilolar = guvenlik($sevkiyat['kilolar']);
    $kiloArray = explode(",",$kilolar);
    $fiyatlar = guvenlik($sevkiyat['fiyatlar']);
    $fiyatArray = explode("-",$fiyatlar);
    $olusturan = guvenlik($sevkiyat['olusturan']);
    $hazirlayan = guvenlik($sevkiyat['hazirlayan']);
    $sevkTipi = guvenlik($sevkiyat['sevk_tipi']);
    $sevkTipleri = ['Müşteri Çağlayan','Müşteri Alkop','Tarafımızca sevk','Ambara tarafımızca sevk'];
    $aciklama = guvenlik($sevkiyat['aciklama']);
    $saniye = guvenlik($sevkiyat['saniye']);
    $tarih = getdmY($saniye);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sevkiyat Formu</title>
    <?php include 'template/head.php'; ?>
</head>
<body>
    <div class="container" style="background: white;">

        <div class="row">
            
            <div class="col-md-4" style="text-align: center;"><img src="img/file/<?php echo $sirketlogo; ?>" style="width: 370px; height: auto;"></div>

            <div class="col-md-8" style="text-align: center; padding: 0px 30px 0px 30px;">

                <p style="font-size: 15px;">
            
                    <?php $sirketaciklama = str_replace("\n", "<br/>", $sirketaciklama); echo $sirketaciklama; ?>

                </p>

            </div>

        </div>

        <div class="row">
            
            <div class="col-md-12" style="text-align: center; padding: 20px;">
                
                <h4><b>SEVKİYAT FORMU</b></h4>

            </div>

        </div>

        <div class="row" style="padding: 20px;">
            
            <div class="col-md-2">
                
                <b>Firma Adı :</b>

            </div>

            <div class="col-md-5">
                
                <?php echo $firmaAdi."<br/>"?>

            </div>

            <div class="col-md-5" style="text-align:right;">
                <?php echo "Tarih : ".$tarih; ?>
            </div>

        </div>

        <div class="row" style="padding: 20px;">
            <div class="col-md-4"><b>Ürün</b></div>
            <div class="col-md-2"><b>Cinsi</b></div>
            <div class="col-md-2"><b>Adet</b></div>
            <div class="col-md-2"><b>Kg</b></div>
            <div class="col-md-2"><b>Fiyat</b></div>
        </div>

        <?php
            $totalWeight = 0;
            $totalPrice = 0;
            foreach($urunArray as $key => $urunId){
                $urun = getUrunInfo($urunId);
        ?>
                <hr style="border:2px black solid; margin: 0px;" />

                <div class="row" style="padding: 20px;">
                    <div class="col-4 d-block d-sm-none">Ürün Adı : </div>
                    <div class="col-md-4 col-8"><?= $urun['urun_adi'] ?></div>
                    <div class="col-4 d-block d-sm-none">Cinsi : </div>
                    <div class="col-md-2 col-8"><?= getCategoryShortName($urun['kategori_bir']) ?></div>
                    <div class="col-4 d-block d-sm-none">Adet : </div>
                    <div class="col-md-2 col-8"><?= $adetArray[$key] ?></div>
                    <div class="col-4 d-block d-sm-none">Kilo : </div>
                    <div class="col-md-2 col-8"><?= $kiloArray[$key] ?></div>
                    <div class="col-4 d-block d-sm-none">Fiyat : </div>
                    <div class="col-md-2 col-8"><?= $fiyatArray[$key].' TL' ?></div>
                </div>
        <?php
            }
        ?>

        <hr style="border:2px black solid; margin: 0px;" />

        <div class="row" style="padding: 20px;">
            <div class="col-md-6 col-12"></div>
            <div class="col-md-2 col-4"><b>Toplam</b></div>
            <div class="col-md-2 col-4"></div>
            <div class="col-md-2 col-4"><?= $totalPrice.' TL' ?></div>
        </div>

        <div class="row" style="padding: 20px;">
            <div class="col-md-4"><b>Siparişi Oluşturan : </b><?= getUsername($olusturan) ?></div>
            <div class="col-md-4"><b>Siparişi Hazırlayan : </b><?= getUsername($hazirlayan) ?></div>
            <div class="col-md-4"><b>Sevk Tipi: </b><?= $sevkTipleri[$sevkTipi] ?></div>
        </div>
        <div class="row" style="padding: 20px;">
            <div class="col-md-8"><b>Açıklama: </b><?= $aciklama ?></div>
        </div>

    <?php include 'template/script.php'; ?>
</body>
</html>