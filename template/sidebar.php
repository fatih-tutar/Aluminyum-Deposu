<style>
    .sidebar-font{
        font-size:14px; 
        font-weight:bold;
    }
</style>
<div id="accordion" class="mt-2">
    <?php
        $i = 0;
        $query = $db->query("SELECT * FROM kategori WHERE kategori_tipi = '0' AND sirketid = '{$uye_sirket}'", PDO::FETCH_ASSOC);
        if ( $query->rowCount() ){
            foreach( $query as $row ){
                $kategori_id = $row['kategori_id'];
                $kategori_adi = $row['kategori_adi'];
                $resim = "img/kategoriler/".$row['resim'];
                $i++;
    ?>
            <div class="card">
                <div class="sidebar-font" style="background-color: white;" data-toggle="collapse" data-target="#collapse<?= $i; ?>" aria-expanded="true" aria-controls="collapse<?= $i; ?>">
                    <div class="row pl-1">
                        <div class="col-md-3 col-2">
                            <img src="<?= $resim ?>" alt="<?= $kategori_adi ?>" width="40" height="40">
                        </div>
                        <div class="col-md-9 col-10 d-flex align-items-center">
                            <?php echo $kategori_adi; ?>
                        </div>
                    </div>							
                </div>
                <div id="collapse<?= $i; ?>" class="collapse px-2" style="border-top:1px solid grey; font-size:11px;" aria-labelledby="heading<?= $i; ?>" data-parent="#accordion">
        <?php
            $cek = $db->query("SELECT * FROM kategori WHERE kategori_ust = '{$kategori_id}' AND kategori_tipi = '1' AND sirketid = '{$uye_sirket}'", PDO::FETCH_ASSOC);
            if ( $cek->rowCount() ){
                foreach( $cek as $wor ){
                    $alt_kategori_id = $wor['kategori_id'];
                    $alt_kategori_adi = $wor['kategori_adi'];
                    $alt_kategori_resim = "img/kategoriler/".$wor['resim'];
        ?>		
                    <a href="urunler.php?id=<?php echo $alt_kategori_id; ?>">
                        <div class="row pl-1">
                            <div class="col-md-3 col-2 offset-md-0 offset-1">
                                <img src="<?= $alt_kategori_resim ?>" alt="<?= $alt_kategori_adi ?>" width="35" height="35">
                            </div>
                            <div class="col-md-9 col-9 d-flex align-items-center">
                                <?php echo $alt_kategori_adi; ?>
                            </div>
                        </div>		
                    </a>
        <?php
                }
            }
        ?>
                </div>
            </div>
    <?php
            }
        }
    ?>	
    <div class="card p-1">
        <a href="fiyatlistesi.php" target="_blank" class="sidebar-font">
            AYDINLATMA FİYAT
        </a>
    </div>		
    <div class="card p-1">
        <a href="tekliflistesi.php" target="_blank" class="sidebar-font">
            ÜRÜN SORGULAMA
        </a>
    </div>	
    <div class="card p-1">
        <a href="kaliplistesi.php" target="_blank" class="sidebar-font">
            KALIP SORGULAMA
        </a>
    </div>	
    <div class="card p-1">
        <a href="#" onclick="return false" onmousedown="javascript:ackapa4('anlikfiyatlamadivi','fiyathesaplamadivi','agirlikhesaplamadivi','isplanidivi');" class="sidebar-font">
            ANLIK FİYATLAMA
        </a>
    </div>	
    <div class="card p-1">
        <a href="#" onclick="return false" onmousedown="javascript:ackapa4('fiyathesaplamadivi','agirlikhesaplamadivi','anlikfiyatlamadivi','isplanidivi');" class="sidebar-font">
            FİYAT HESAPLAMA
        </a>
    </div>	
    <div class="card p-1">
        <a href="#" onclick="return false" onmousedown="javascript:ackapa4('agirlikhesaplamadivi','anlikfiyatlamadivi','fiyathesaplamadivi','isplanidivi');" class="sidebar-font">
            AĞIRLIK HESAPLAMA
        </a>
    </div>		
    <div class="card p-1">
        <a href="fabrikalar.php" target="_blank" class="sidebar-font">
            FABRİKALAR
        </a>
    </div>
    <div class="card p-1">
        <a href="firmalar.php" target="_blank" class="sidebar-font">
            FİRMALAR
        </a>
    </div>
    <div class="card p-1">
        <a href="kaliplar.php" target="_blank" class="sidebar-font">
            KALIPLAR
        </a>
    </div>	
    <div class="card p-1">
        <a href="#" onclick="return false" onmousedown="javascript:ackapa4('isplanidivi','agirlikhesaplamadivi','anlikfiyatlamadivi','fiyathesaplamadivi');" class="sidebar-font">
            İŞ PLANI
        </a>
    </div>
    <div class="card p-1">
        <a href="sevkiyatarsiv.php" target="_blank" class="sidebar-font">
            SEVKİYAT ARŞİVİ
        </a>
    </div>	
</div>