<style>
    .sidebar-font{
        font-size:14px; 
        font-weight:bold;
    }
    .sidebar-item {
        border-style: none;
        padding: 12px;
    }
</style>
<div id="accordion" class="mt-2">
    <?php
        $i = 0;
        $query = $db->query("SELECT * FROM kategori WHERE kategori_tipi = '0' AND sirketid = '{$user->company_id}' AND silik = '0'", PDO::FETCH_ASSOC);
        if ( $query->rowCount() ){
            foreach( $query as $row ){
                $kategori_id = $row['kategori_id'];
                $kategori_adi = $row['kategori_adi'];
                $resim = "img/kategoriler/".$row['resim'];
                $i++;
    ?>
            <div class="card sidebar-item">
                <div class="sidebar-font" style="background-color: white; cursor:pointer;" data-toggle="collapse" data-target="#collapse<?= $i; ?>" aria-expanded="true" aria-controls="collapse<?= $i; ?>">
                    <div class="row pl-1">
                        <div class="col-md-3 col-2">
                            <img src="<?= $resim ?>" alt="<?= $kategori_adi ?>" width="40" height="40">
                        </div>
                        <div class="col-md-9 col-10 d-flex align-items-center">
                            <?= $kategori_adi; ?>
                        </div>
                    </div>							
                </div>
                <div id="collapse<?= $i; ?>" class="collapse px-2" style="border-top:1px solid grey; font-size:11px;" aria-labelledby="heading<?= $i; ?>" data-parent="#accordion">
        <?php
            $cek = $db->query("SELECT * FROM kategori WHERE kategori_ust = '{$kategori_id}' AND kategori_tipi = '1' AND sirketid = '{$user->company_id}' AND silik = '0'", PDO::FETCH_ASSOC);
            if ( $cek->rowCount() ){
                foreach( $cek as $wor ){
                    $alt_kategori_id = $wor['kategori_id'];
                    $alt_kategori_adi = $wor['kategori_adi'];
                    $alt_kategori_resim = "img/kategoriler/".$wor['resim'];
        ?>		
                    <a href="urunler.php?id=<?= $alt_kategori_id; ?>">
                        <div class="row pl-1">
                            <div class="col-md-3 col-2 offset-md-0 ">
                                <img src="<?= $alt_kategori_resim ?>" alt="<?= $alt_kategori_adi ?>" width="35" height="35">
                            </div>
                            <div class="col-md-9 col-9 d-flex align-items-center">
                                <?= $alt_kategori_adi; ?>
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
    <div class="card sidebar-item ">
        <a href="customorder.php" target="_blank" class="sidebar-font">
            <div class="row pl-1">
                <div class="col-md-3 col-2 offset-md-0 ">
                    <img src="img/kategoriler/unnamed39509908.jpg" alt="" width="35" height="35">
                </div>
                <div class="col-md-9 col-9 d-flex align-items-center">
                    ÖZEL SİPARİŞLER
                </div>
            </div>
        </a>
    </div>
    <div class="card sidebar-item ">
        <a href="fiyatlistesi.php" target="_blank" class="sidebar-font">
            <div class="row pl-1">
                <div class="col-md-3 col-2 offset-md-0 ">
                    <img src="img/sidebar/aydinlatma_fiyat.png" alt="" width="35" height="35">
                </div>
                <div class="col-md-9 col-9 d-flex align-items-center">
                    AYDINLATMA FİYAT
                </div>
            </div>	
        </a>
    </div>		
    <div class="card sidebar-item ">
        <a href="tekliflistesi.php" target="_blank" class="sidebar-font">
            <div class="row pl-1">
                <div class="col-md-3 col-2 offset-md-0 ">
                    <img src="img/sidebar/urun_sorgulama.png" alt="" width="35" height="35">
                </div>
                <div class="col-md-9 col-9 d-flex align-items-center">
                    ÜRÜN SORGULAMA
                </div>
            </div>	
        </a>
    </div>	
    <div class="card sidebar-item ">
        <a href="kaliplistesi.php" target="_blank" class="sidebar-font">
            <div class="row pl-1">
                <div class="col-md-3 col-2 offset-md-0 ">
                    <img src="img/sidebar/kalip_sorgulama.png" alt="" width="35" height="35">
                </div>
                <div class="col-md-9 col-9 d-flex align-items-center">
                    KALIP SORGULAMA
                </div>
            </div>	
        </a>
    </div>	
    <div class="card sidebar-item ">
        <a href="anlikfiyatlama.php" target="_blank" class="sidebar-font">
            <div class="row pl-1">
                <div class="col-md-3 col-2 offset-md-0 ">
                    <img src="img/sidebar/anlik_fiyatlama.png" alt="" width="35" height="35">
                </div>
                <div class="col-md-9 col-9 d-flex align-items-center">
                    ANLIK FİYATLAMA
                </div>
            </div>
        </a>
    </div>	
    <div class="card sidebar-item ">
        <a href="fiyathesaplama.php" target="_blank" class="sidebar-font">
            <div class="row pl-1">
                <div class="col-md-3 col-2 offset-md-0 ">
                    <img src="img/sidebar/fiyat_hesaplama.png" alt="" width="35" height="35">
                </div>
                <div class="col-md-9 col-9 d-flex align-items-center">
                    FİYAT HESAPLAMA
                </div>
            </div>
        </a>
    </div>	
    <div class="card sidebar-item ">
        <a href="#" onclick="return false" onmousedown="javascript:ackapa4('agirlikhesaplamadivi','anlikfiyatlamadivi','fiyathesaplamadivi','isplanidivi');" class="sidebar-font">
            <div class="row pl-1">
                <div class="col-md-3 col-2 offset-md-0 ">
                    <img src="img/sidebar/agirlik_hesaplama.png" alt="" width="35" height="35">
                </div>
                <div class="col-md-9 col-9 d-flex align-items-center">
                    AĞIRLIK HESAPLAMA
                </div>
            </div>
        </a>
    </div>		
    <div class="card sidebar-item ">
        <a href="factory.php" target="_blank" class="sidebar-font">
            <div class="row pl-1">
                <div class="col-md-3 col-2 offset-md-0 ">
                    <img src="img/sidebar/fabrikalar.png" alt="" width="35" height="35">
                </div>
                <div class="col-md-9 col-9 d-flex align-items-center">
                    FABRİKALAR
                </div>
            </div>
        </a>
    </div>
    <div class="card sidebar-item ">
        <a href="client.php" target="_blank" class="sidebar-font">
            <div class="row pl-1">
                <div class="col-md-3 col-2 offset-md-0 ">
                    <img src="img/sidebar/clients.png" alt="" width="35" height="35">
                </div>
                <div class="col-md-9 col-9 d-flex align-items-center">
                    FİRMALAR
                </div>
            </div>
        </a>
    </div>
    <div class="card sidebar-item ">
        <a href="kaliplar.php" target="_blank" class="sidebar-font">
            <div class="row pl-1">
                <div class="col-md-3 col-2 offset-md-0 ">
                    <img src="img/sidebar/kaliplar.png" alt="" width="35" height="35">
                </div>
                <div class="col-md-9 col-9 d-flex align-items-center">
                    KALIPLAR
                </div>
            </div>
        </a>
    </div>	
    <div class="card sidebar-item ">
        <a href="#" onclick="return false" onmousedown="javascript:ackapa4('isplanidivi','agirlikhesaplamadivi','anlikfiyatlamadivi','fiyathesaplamadivi');" class="sidebar-font">
            <div class="row pl-1">
                <div class="col-md-3 col-2 offset-md-0 ">
                    <img src="img/sidebar/is_plani.png" alt="" width="35" height="35">
                </div>
                <div class="col-md-9 col-9 d-flex align-items-center">
                    İŞ PLANI
                </div>
            </div>
        </a>
    </div>
    <div class="card sidebar-item ">
        <a href="izin.php" target="_blank" class="sidebar-font">
            <div class="row pl-1">
                <div class="col-md-3 col-2 offset-md-0 ">
                    <img src="img/sidebar/izin.png" alt="" width="35" height="35">
                </div>
                <div class="col-md-9 col-9 d-flex align-items-center">
                    İZİNLER
                </div>
            </div>
        </a>
    </div>
    <div class="card sidebar-item ">
        <a href="organizasyon.php" target="_blank" class="sidebar-font">
            <div class="row pl-1">
                <div class="col-md-3 col-2 offset-md-0 ">
                    <img src="img/sidebar/organizasyon.png" alt="" width="35" height="35">
                </div>
                <div class="col-md-9 col-9 d-flex align-items-center">
                    ORGANİZASYON
                </div>
            </div>
        </a>
    </div>
    <div class="card sidebar-item ">
        <a href="sevkiyatplan.php" target="_blank" class="sidebar-font">
            <div class="row pl-1">
                <div class="col-md-3 col-2 offset-md-0 ">
                    <img src="img/sidebar/sevkiyat_plani.png" alt="" width="35" height="35">
                </div>
                <div class="col-md-9 col-9 d-flex align-items-center">
                    SEVKİYAT PLANI
                </div>
            </div>
        </a>
    </div>
    <div class="card sidebar-item ">
        <a href="sevkiyatarsiv.php" target="_blank" class="sidebar-font">
            <div class="row pl-1">
                <div class="col-md-3 col-2 offset-md-0 ">
                    <img src="img/sidebar/sevkiyat_arsivi.png" alt="" width="35" height="35">
                </div>
                <div class="col-md-9 col-9 d-flex align-items-center">
                    SEVKİYAT ARŞİVİ
                </div>
            </div>
        </a>
    </div>	
</div>