<?php
    include 'fonksiyonlar/bagla.php';
    $aracId = guvenlik($_GET['id']);
    // SEVKİYATLAR
    $sevkiyatlar = $db->query("SELECT * FROM sevkiyat WHERE arac_id = '{$aracId}' AND silik = '0' AND durum != '3'", PDO::FETCH_OBJ)->fetchAll();
    // FİRMALAR
    $firmalar = $db->query("SELECT * FROM firmalar WHERE silik = '0'", PDO::FETCH_OBJ)->fetchAll();
    // ARAÇLAR
    $arac = $db->query("SELECT * FROM araclar WHERE id = '{$aracId}'")->fetch(PDO::FETCH_ASSOC);
?>
<h4><i class="fa fa-car"></i> <?= $arac['arac_adi'] ?></h4>
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
        Firma Adı : <?= $firmaAdi ?><br/>
        Firma Adres : <?= $firmaAdres ?><br/>
        Kilo : <?= $toplamkg ?><br/>
    <?php endforeach; ?>
<?php else: ?>
    Bu araca atanmış sevkiyat bulunmuyor.
<?php endif; ?>
