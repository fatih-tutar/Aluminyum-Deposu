<?php
    include 'functions/init.php';
    if (!isLoggedIn()) {
        header("Location:login.php");
        exit();
    }else{
        // SEVKİYATLAR
        $sevkiyatlar = $db->query("SELECT * FROM sevkiyat WHERE silik = '0' AND nakliye_durumu = '0'")->fetchAll(PDO::FETCH_OBJ);
        $sevkiyatGruplari = [];
        foreach ($sevkiyatlar as $sevkiyat) {
            $sevkiyatGruplari[$sevkiyat->arac_id][] = $sevkiyat;
        }
        // ARAÇLAR
        $araclar = $db->query("SELECT * FROM vehicles WHERE is_deleted = '0' AND is_transport = '1'")->fetchAll(PDO::FETCH_OBJ);
        // CLIENTS
        $clients = $db->query("SELECT * FROM clients WHERE is_deleted = '0'")->fetchAll(PDO::FETCH_OBJ);

        if(isset($_POST['manuelSevkiyatKaydet'])){
            $firma = guvenlik($_POST['firma']);
            $kilolar = guvenlik($_POST['kilolar']);
            $arac_id = guvenlik($_POST['arac_id']);
            $aciklama = guvenlik($_POST['aciklama']);
            $firmaId = getFirmaID($firma);
            if(empty($firma)){
                $error = '<br/><div class="alert alert-danger" role="alert">Firma seçmediniz.</div>';
            }else if(empty($kilolar)){
                $error = '<br/><div class="alert alert-danger" role="alert">Kilo bilgisi yazmadınız.</div>';
            }else{
                $query = $db->prepare("INSERT INTO sevkiyat SET urunler = ?, firma_id = ?, adetler = ?, kilolar = ?, fiyatlar = ?, olusturan = ?, hazirlayan = ?, sevk_tipi = ?, arac_id = ?, aciklama = ?, manuel = ?, durum = ?, silik = ?, saniye = ?, sirket_id = ?");
                $insert = $query->execute(array('',$firmaId,'',$kilolar,'',$user->id,'','',$arac_id,$aciklama,'1','0','0',time(), $user->company_id));
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

        if(isset($_POST['sevkiyattoplusil'])){
            $arac_id = guvenlik($_POST['arac_id']);
            $query = $db->prepare("UPDATE sevkiyat SET nakliye_durumu = ? WHERE arac_id = ?");
            $guncelle = $query->execute(array('1',$arac_id));
            header("Location:sevkiyatplan.php");
            exit();
        }

        if(isset($_POST['sevkiyatguncelle'])){
            $sevkiyatId = guvenlik($_POST['sevkiyat_id']);
            $aciklama = guvenlik($_POST['aciklama']);
            $query = $db->prepare("UPDATE sevkiyat SET aciklama = ? WHERE id = ?");
            $guncelle = $query->execute(array($aciklama,$sevkiyatId));
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
                        <div class="col-6">
                            <div class="road">
                                <h5><?= $arac->name ?> Sevkiyat Planı<br/><?= date('d/m/Y'); ?></h5>
                            </div>
                        </div>
                        <div class="col-3">
                            <a href="aracsevkiyat.php?id=<?=$arac->id?>" target="_blank">
                                <button class="btn btn-primary btn-block btn-sm">
                                    Çıktı Al
                                </button>
                            </a>
                        </div>
                        <div class="col-3">
                            <form action="" method="POST">
                                <input type="hidden" name="arac_id" value="<?= $arac->id; ?>">
                                <button type="submit" class="btn btn-secondary btn-block btn-sm" name="sevkiyattoplusil" onclick="return confirmForm('Bu araca ait sevkiyat planını toplu olarak silmek istediğinize emin misiniz?')">
                                    Toplu Sil
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php if (isset($sevkiyatGruplari[$arac->id])): ?>
                        <?php
                        foreach ($sevkiyatGruplari[$arac->id] as $sevkiyat):
                            $filtered = array_filter($clients, fn($firma) => $firma->id == $sevkiyat->firma_id);
                            $firma = reset($filtered);
                            $firmaAdi = $firma->name ?? null;
                            $firmaAdres = $firma->address;
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
                                    <b>Firma Tel : </b><?= $firma->phone; ?><br/>
                                    <b>Firma Adres : </b><?= $firmaAdres; ?>
                                    <form action="" method="POST">
                                        <div class="row">
                                            <div class="col-md-9 col-12">
                                                <input type="text" name="aciklama" class="form-control form-control-sm" value="<?= $sevkiyat->aciklama; ?>" placeholder="Sevkiyat açıklaması giriniz.">
                                            </div>
                                            <div class="col-md-3 col-12">
                                                <input type="hidden" name="sevkiyat_id" value="<?= $sevkiyat->id; ?>">
                                                <button type="submit" class="btn btn-sm btn-primary" name="sevkiyatguncelle">Kaydet</button>
                                            </div>
                                        </div>
                                    </form>
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
                                <input name="firma" id="firmainputu" type="text" class="form-control" autocomplete="off" placeholder="Firma Adı"/>
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
                            <div class="col-12 pt-2">
                                <textarea class="form-control" name="aciklama" placeholder="Sevkiyat ile alakalı açıklama girebilirsiniz."></textarea>
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