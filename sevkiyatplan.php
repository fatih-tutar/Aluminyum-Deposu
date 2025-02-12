<?php
    include 'fonksiyonlar/bagla.php';
    if (!isLoggedIn()) {
        header("Location:giris.php");
        exit();
    }else{
        // SEVKİYATLAR
        $sevkiyatlar = $db->query("SELECT * FROM sevkiyat WHERE silik = '0' AND nakliye_durumu = '0'", PDO::FETCH_OBJ)->fetchAll();
        $sevkiyatGruplari = [];
        foreach ($sevkiyatlar as $sevkiyat) {
            $sevkiyatGruplari[$sevkiyat->arac_id][] = $sevkiyat;
        }
        // ARAÇLAR
        $araclar = $db->query("SELECT * FROM araclar WHERE silik = '0' AND nakliye = '1'", PDO::FETCH_OBJ)->fetchAll();
        // FİRMALAR
        $firmalar = $db->query("SELECT * FROM firmalar WHERE silik = '0'", PDO::FETCH_OBJ)->fetchAll();

        if(isset($_POST['manuelSevkiyatKaydet'])){
            $firma = guvenlik($_POST['firma']);
            $kilolar = guvenlik($_POST['kilolar']);
            $arac_id = guvenlik($_POST['arac_id']);
            $firmaId = getFirmaID($firma);
            if(empty($firma)){
                $hata = '<br/><div class="alert alert-danger" role="alert">Firma seçmediniz.</div>';
            }else if(empty($kilolar)){
                $hata = '<br/><div class="alert alert-danger" role="alert">Kilo bilgisi yazmadınız.</div>';
            }else{
                $query = $db->prepare("INSERT INTO sevkiyat SET urunler = ?, firma_id = ?, adetler = ?, kilolar = ?, fiyatlar = ?, olusturan = ?, hazirlayan = ?, sevk_tipi = ?, arac_id = ?, aciklama = ?, manuel = ?, durum = ?, silik = ?, saniye = ?, sirket_id = ?");
                $insert = $query->execute(array('',$firmaId,'',$kilolar,'',$user->id,'','',$arac_id,'','1','0','0',time(), $user->company_id));
                header("Location:sevkiyatplan.php");
                exit();
            }
        }

        if(isset($_POST['sevkiyatplanisil'])){
            $sevkiyatId = guvenlik($_POST['sevkiyat_id']);
            $query = $db->prepare("UPDATE sevkiyat SET nakliye_durumu = ? WHERE id = ?");
            $guncelle = $query->execute(array('1',$sevkiyatId));
            header("Location:sevkiyatplan.php");
            exit();
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sevkiyat Planı</title>
    <?php include 'template/head.php'; ?>
    <style>
        .sevkCardBlue{
            background-color: #17a2b8;
            border-radius: 5px;
            color: black;
            margin-bottom: 5px;
            padding:5px;
        }
        .sevkCardGreen{
            background-color: #28a745;
            border-radius: 5px;
            color: black;
            margin-bottom: 5px;
            padding:5px;
        }
        .road {
            position: relative;
            width: 100%;
            height: 30px;
        }
        .truck {
            position: absolute;
            bottom: 5px;
            width: 30px;
            animation: moveCar 20s ease-in-out infinite;
        }
        @keyframes moveCar {
            from {
                left: 50px;
            }
            to {
                left: 100%;
            }
        }
    </style>
</head>
<body>
<?php include 'template/banner.php' ?>
<div class="container-fluid">
    <div class="row">
    <div class="col-md-2 col-12">
        <?php include 'template/sidebar.php'; ?>
    </div>
    <div class="col-md-9">
        <?php
        $ids = [];
        foreach ($araclar as $arac) {
            $ids[] = $arac->id;
        }
        $column = 12 / count($araclar);
        ?>
        <div class="row cerceve" style="padding-top: 10px; padding-bottom:10px;">
            <?php
            foreach ($araclar as $key => $arac) {
                ?>
                <div class="col-12 col-md-<?=$column?>">
                    <div class="row pb-1">
                        <div class="col-8 col-md-9">
                            <div class="road">
                                <?= $arac->arac_adi ?>
                                <img src="https://st2.depositphotos.com/47577860/46115/v/950/depositphotos_461154212-stock-illustration-pickup-truck-transport-icon.jpg" class="truck" alt="Truck Icon">
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <a href="aracsevkiyat.php?id=<?=$arac->id?>" target="_blank">
                                <button class="btn btn-primary btn-block btn-sm">
                                    Çıktı Al
                                </button>
                            </a>
                        </div>
                    </div>
                    <?php if (isset($sevkiyatGruplari[$arac->id])): ?>
                        <?php
                        foreach ($sevkiyatGruplari[$arac->id] as $sevkiyat):
                            $firma = reset(array_filter($firmalar, fn($firma) => $firma->firmaid == $sevkiyat->firma_id));
                            $firmaAdi = $firma->firmaadi;
                            $firmaAdres = $firma->firmaadres;
                            $kilolar = $sevkiyat->kilolar;
                            $toplamkg = 0;
                            if(strpos($kilolar, ',')){
                                $kiloArray = explode(",",$kilolar);
                                foreach($kiloArray as $kilo){
                                    $toplamkg += $kilo;
                                }
                            }else{
                                $toplamkg = $kilolar;
                            }
                            ?>
                            <div class="<?= $key%2 == 0 ? 'sevkCardBlue' : 'sevkCardGreen' ?>">
                                <a href="" onclick="return false" onmousedown="javascript:ackapa('firmakart<?=$sevkiyat->id?>');">
                                    <b>Firma Adı : </b><?= $firmaAdi ?>
                                </a>
                                <div class="row">
                                    <div class="col-6">
                                        <b>Kilo : </b><?= $toplamkg ?>
                                    </div>
                                    <div class="col-6" style="text-align: right;">
                                        <form action="" method="POST">
                                            <input type="hidden" name="sevkiyat_id" value="<?=$sevkiyat->id?>">
                                            <button type="submit" name="sevkiyatplanisil" class="btn btn-secondary btn-sm px-1 py-0" onclick="return confirmForm('Sevkiyat listesinden kaldırmak istediğinize emin misiniz?');">Kaldır</button>
                                        </form>
                                    </div>
                                </div>
                                <div id="firmakart<?=$sevkiyat->id?>" style="display: none;">
                                    <b>Firma Adres : </b><?= $firmaAdres ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        Bu araca atanmış sevkiyat bulunmuyor.
                    <?php endif; ?>
                    <hr/>
                    <form action="" method="POST">
                        <div class="row">
                            <div class="col-md-6 col-12 search-box">
                                <b>Firma</b>
                                <input autofocus="autofocus" name="firma" id="firmainputu" type="text" class="form-control" autocomplete="off" placeholder="Firma Adı"/>
                                <ul class="list-group liveresult" id="firmasonuc" style="position: absolute; z-index: 1;"></ul>
                            </div>
                            <div class="col-md-3 col-12">
                                <b>Kilo</b>
                                <input type="text" class="form-control" name="kilolar" placeholder="Kilo">
                            </div>
                            <div class="col-md-3 col-12">
                                <br/>
                                <input type="hidden" name="arac_id" value="<?=$arac->id?>">
                                <button type="submit" name="manuelSevkiyatKaydet" class="btn btn-primary btn-block btn-sm">Kaydet</button>
                            </div>
                        </div>
                    </form>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</div>
</div>
<?php include 'template/script.php'; ?>
</body>
</html>