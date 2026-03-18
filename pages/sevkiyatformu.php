<?php

    require_once __DIR__.'/../config/init.php';

    if (!isLoggedIn()) {
		header("Location:/login");
		exit();
	}else{

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

    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sevkiyat Formu</title>
    <?php include ROOT_PATH.'/template/head.php'; ?>
</head>
<body>
    <div class="container-fluid pt-5" style="background: white; padding: 15px 25px; font-size: 13px;">

        <div class="row">
            
            <div class="col-md-4" style="text-align: center;">
                <img src="/files/company/<?= $company->photo; ?>" style="width: 230px; height: auto;">
            </div>

            <div class="col-md-8" style="text-align: center; padding: 0px 30px 0px 30px;">

                <p style="font-size: 10px; line-height: 1.3;">
            
                    <?= str_replace("\n", "<br/>", $company->description); ?>

                </p>

            </div>

        </div>

        <div class="row">
            
            <div class="col-md-12" style="text-align: center; padding: 20px;">
                
                <h4><b>SEVKİYAT FORMU</b></h4>

            </div>

        </div>

        <!-- Firma bilgileri -->
        <div class="row my-3">
            <div class="col-12">
                <span style="white-space: nowrap;"><b>Firma : </b><?= $firmaInfos['name']; ?></span>
            </div>
        </div>

        <div class="row my-3">
            <div class="col-12">
                <b>Adres : </b><?= $firmaInfos['address'] ?>
            </div>
        </div>

        <div class="row my-3">
            <div class="col-md-3 col-12 mb-1">
                <span style="white-space: nowrap;"><b>Tel : </b><?= $firmaInfos['phone']; ?></span>
            </div>
            <div class="col-md-5 col-12 mb-1">
                <span style="white-space: nowrap;"><b>E-posta : </b><?= $firmaInfos['email']; ?></span>
            </div>
            <div class="col-md-2 col-12 mb-1">
                <span style="white-space: nowrap; font-size: 13px;"><b>Tarih : </b><?= $tarih ?></span>
            </div>
            <div class="col-md-2 col-12 mb-1">
                <span style="white-space: nowrap; font-size: 13px;"><b>Saat : </b><?= $saat ?></span>
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
                if($urun !== false) {
        ?>
                    <hr style="border: none; border-top: 2px solid black; opacity: 1; margin: 0px;">

                    <div class="row" style="padding: 20px;">
                        <div class="col-4 d-block d-sm-none">Ürün Adı : </div>
                        <div class="col-md-4 col-8"><b><?= $urun['urun_adi'].' '.getCategoryShortName($urun['kategori_iki']) ?></b></div>
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
            }
        ?>

        <hr style="border: none; border-top: 2px solid black; opacity: 1; margin: 0px;">

        <div class="row" style="padding: 20px;">
            <div class="col-md-8 col-12"></div>
            <div class="col-md-4">
                <div class="row">
                    <div class="col-md-4 col-8">
                        <span><b>Toplam Kilo</b></span>
                    </div>
                    <div class="col-md-4 col-8">
                        <span><?= strpos($kilolar,",") ? $toplamkg : $kilolar ?> KG</span>
                    </div>  
                    <div class="col-md-4 col-8"></div>
                </div>
            </div>
        </div>

        <div class="row my-2">
            <div class="col-md-4">
                <span><b>Siparişi Oluşturan : </b><?= getUsername($olusturan) ?></span>
            </div>
            <div class="col-md-4">
                <span><b>Siparişi Hazırlayan : </b><?= getUsername($hazirlayan) ?></span>
            </div>
            <div class="col-md-4">
                <span><b>Faturayı Kesen : </b><?= getUsername($faturaci) ?></span>
            </div>
        </div>
        <div class="row my-2">
            <div class="col-md-4"><b>Sevk Tipi: </b><?= $sevkTipleri[$sevkTipi] ?></div>
            <div class="col-md-8"><b>Açıklama: </b><?= $aciklama ?></div>
        </div>

    <?php include ROOT_PATH.'/template/script.php'; ?>
</body>
</html>