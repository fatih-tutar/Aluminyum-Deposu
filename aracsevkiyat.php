<?php
    include 'functions/init.php';
    $aracId = guvenlik($_GET['id']);
    // SEVKİYATLAR
    $sevkiyatlar = $db->query("SELECT * FROM sevkiyat WHERE arac_id = '{$aracId}' AND silik = '0' AND nakliye_durumu = '0'", PDO::FETCH_OBJ)->fetchAll();
    // FİRMALAR
    $firmalar = $db->query("SELECT * FROM firmalar WHERE silik = '0'", PDO::FETCH_OBJ)->fetchAll();
    // ARAÇLAR
    $arac = $db->query("SELECT * FROM vehicles WHERE id = '{$aracId}'")->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sevkiyat Planı</title>
</head>
<body style="padding:20px;">
<h4><i class="fa fa-car"></i> <?= $arac['name'] ?> Sevkiyat Planı - <?= date('d/m/Y'); ?></h4>
<?php if (isset($sevkiyatlar)): ?>
    <?php
    foreach ($sevkiyatlar as $sevkiyat):
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
        <hr/>
        Firma Adı : <?= $firmaAdi; ?><br/>
        Firma Tel : <?= $firma->firmatel; ?><br/>
        Firma Adres : <?= $firmaAdres; ?><br/>
        Kilo : <?= $toplamkg; ?><br/>
        Açıklama : <?= $sevkiyat->aciklama; ?><br/>
    <?php endforeach; ?>
<?php else: ?>
    Bu araca atanmış sevkiyat bulunmuyor.
<?php endif; ?>
</body>
</html>