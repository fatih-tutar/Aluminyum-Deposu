<?php
    include 'functions/init.php';
    $aracId = guvenlik($_GET['id']);
    // SEVKİYATLAR
    $sevkiyatlar = $db->query("SELECT * FROM sevkiyat WHERE arac_id = '{$aracId}' AND silik = '0' AND nakliye_durumu = '0'")->fetchAll(PDO::FETCH_OBJ);
    // CLIENTS
    $clients = $db->query("SELECT * FROM clients WHERE is_deleted = '0'")->fetchAll(PDO::FETCH_OBJ);
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
    foreach ($sevkiyatlar as $key => $sevkiyat):
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
        <hr/>
        <b><?= ($key+1).'. ' ?></b>Firma Adı : <?= $firmaAdi; ?><br/>
        &nbsp;&nbsp;&nbsp;&nbsp;Firma Tel : <?= $firma->phone; ?><br/>
        &nbsp;&nbsp;&nbsp;&nbsp;Firma Adres : <?= $firmaAdres; ?><br/>
        &nbsp;&nbsp;&nbsp;&nbsp;Kilo : <?= $toplamkg; ?><br/>
        &nbsp;&nbsp;&nbsp;&nbsp;Açıklama : <?= $sevkiyat->aciklama; ?><br/>
    <?php endforeach; ?>
<?php else: ?>
    Bu araca atanmış sevkiyat bulunmuyor.
<?php endif; ?>
</body>
</html>