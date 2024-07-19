<?php

    include 'fonksiyonlar/bagla.php';


    $sevkiyatID = guvenlik($_GET['id']);

    $sevkiyat = $db->query("SELECT * FROM sevkiyat WHERE id = '{$sevkiyatID}' ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);

    $urunler = guvenlik($sevkiyat['urunler']);
    $urunArray = explode(",",$urunler);
    $firmaId = guvenlik($sevkiyat['firma_id']);
    $firmaInfos = getFirmaInfos($firmaId);
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
    $saat = getHis($saniye);

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
    <div class="container-fluid pt-5" style="background: white;">

        <div class="row">
            
            <div class="col-md-4" style="text-align: center;"><img src="img/file/<?= $sirketlogo; ?>" style="width: 370px; height: auto;"></div>

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

        <div class="row m-3">
            
            <div class="col-md-4">
                
                <b>Firma :</b>
                
                <?= $firmaInfos['firmaadi']."<br/>"?>

            </div>

            <div class="col-md-3">
                
                <b>Tel : </b><?= $firmaInfos['firmatel']."<br/>"?>

            </div>

            <div class="col-md-3">
                
                <b>E-posta : </b><?= $firmaInfos['firmaeposta']."<br/>"?>

            </div>

            <div class="col-md-2">
                <b>Tarih : </b><?= $tarih ?>
            </div>

        </div>

        <div class="row m-3">
            <div class="col-md-10">
                <b>Adres : </b><?= $firmaInfos['firmaadres'] ?>
            </div>
            <div class="col-md-2">
                <b>Saat &nbsp;: </b><?= $saat ?>
            </div>
        </div>

        <div class="row" style="padding: 20px;">
            <div class="col-md-4"><b>Ürün</b></div>
            <div class="col-md-2"><b>Cinsi</b></div>
            <div class="col-md-2"><b>Raf</b></div>
            <div class="col-md-4">
                <div class="row">
                    <div class="col-md-4"><b>Adet</b></div>
                    <div class="col-md-4"><b>Kg</b></div>
                    <div class="col-md-4"><b>Fiyat</b></div>
                </div>
            </div>    
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
                    <div class="col-md-4 col-8"><?= $urun['urun_adi'].' '.getCategoryShortName($urun['kategori_iki']) ?></div>
                    <div class="col-4 d-block d-sm-none">Cinsi : </div>
                    <div class="col-md-2 col-8"><?= getCategoryShortName($urun['kategori_bir']) ?></div>
                    <div class="col-4 d-block d-sm-none">Raf : </div>
                    <div class="col-md-2 col-8"><?= $urun['urun_raf'] ?></div>
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-4 d-block d-sm-none">Adet : </div>
                            <div class="col-md-4 col-8"><?= $adetArray[$key] ?></div>
                            <div class="col-4 d-block d-sm-none">Kilo : </div>
                            <div class="col-md-4 col-8"><?= strpos($kilolar,",") ? $kiloArray[$key] : '' ?></div>  
                            <div class="col-4 d-block d-sm-none">Fiyat : </div>
                            <div class="col-md-4 col-8"><?= $fiyatArray[$key].' TL'; ?></div>
                        </div>
                    </div>
                </div>
        <?php
            }
        ?>

        <hr style="border:2px black solid; margin: 0px;" />

        <div class="row" style="padding: 20px;">
            <div class="col-md-8 col-12"></div>
            <div class="col-md-4">
                <div class="row">
                    <div class="col-md-4 col-8"><b>Toplam Kilo</b></div>
                    <div class="col-md-4 col-8"><?= strpos($kilolar,",") ? $toplamkg : $kilolar ?> KG</div>  
                    <div class="col-md-4 col-8"></div>
                </div>
            </div>
        </div>

        <div class="row" style="padding: 20px;">
            <div class="col-md-4"><b>Siparişi Oluşturan : </b><?= getUsername($olusturan) ?></div>
            <div class="col-md-4"><b>Siparişi Hazırlayan : </b><?= getUsername($hazirlayan) ?></div>
            <div class="col-md-4"><b>Faturayı Kesen : </b><?= getUsername($faturaci) ?></div>
        </div>
        <div class="row" style="padding: 20px;">
            <div class="col-md-4"><b>Sevk Tipi: </b><?= $sevkTipleri[$sevkTipi] ?></div>
            <div class="col-md-8"><b>Açıklama: </b><?= $aciklama ?></div>
        </div>

    <?php include 'template/script.php'; ?>
</body>
</html>