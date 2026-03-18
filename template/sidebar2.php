<style>
    .sidebar-font{
        font-size:14px;
        font-weight:bold;
    }
    .sidebar-item {
        border-style: none;
        padding: 5px;
    }
</style>
<div id="accordion" class="mt-2">
    <?php
        $i = 0;
        $query = $db->query("SELECT * FROM categories WHERE type = '0' AND company_id = '{$user->company_id}' AND is_deleted = '0'", PDO::FETCH_ASSOC);
        if ( $query->rowCount() ){
            foreach( $query as $row ){
                $kategori_id = $row['id'];
                $kategori_adi = $row['name'];
                $resim = "/files/categories/".$row['image'];
                $i++;
    ?>
            <div class="card sidebar-item">
                <div class="sidebar-font" style="background-color: white; cursor:pointer;" data-bs-toggle="collapse" data-bs-target="#collapse<?= $i; ?>" aria-expanded="true" aria-controls="collapse<?= $i; ?>">
                    <div class="row ps-1">
                        <div class="col-md-3 col-2">
                            <img src="<?= $resim ?>" alt="<?= $kategori_adi ?>" width="40" height="40">
                        </div>
                        <div class="col-md-9 col-10 d-flex align-items-center ps-4 ps-md-0">
                            <?= $kategori_adi; ?>
                        </div>
                    </div>							
                </div>
                <div id="collapse<?= $i; ?>" class="collapse px-2" style="border-top:1px solid grey; font-size:11px;" aria-labelledby="heading<?= $i; ?>" data-bs-parent="#accordion">
        <?php
            $cek = $db->query("SELECT * FROM categories WHERE parent_id = '{$kategori_id}' AND type = '1' AND company_id = '{$user->company_id}' AND is_deleted = '0'", PDO::FETCH_ASSOC);
            if ( $cek->rowCount() ){
                foreach( $cek as $wor ){
                    $alt_kategori_id = $wor['id'];
                    $alt_kategori_adi = $wor['name'];
                    $alt_kategori_resim = "/files/categories/".$wor['image'];
        ?>		
                    <a href="/product/<?= $alt_kategori_id; ?>">
                        <div class="row ps-1">
                            <div class="col-md-3 col-2 offset-md-0 ">
                                <img src="<?= $alt_kategori_resim ?>" alt="<?= $alt_kategori_adi ?>" width="35" height="35">
                            </div>
                            <div class="col-md-9 col-9 d-flex align-items-center ps-4 ps-md-0">
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
        <a href="/customorder" class="sidebar-font">
            <div class="row ps-1">
                <div class="col-md-3 col-2 offset-md-0 ">
                    <img src="/files/categories/unnamed39509908.jpg" alt="" width="35" height="35">
                </div>
                <div class="col-md-9 col-9 d-flex align-items-center ps-4 ps-md-0">
                    ÖZEL SİPARİŞLER
                </div>
            </div>
        </a>
    </div>
    <div class="card sidebar-item ">
        <a href="/fiyatlistesi" class="sidebar-font">
            <div class="row ps-1">
                <div class="col-md-3 col-2 offset-md-0 ">
                    <img src="/files/sidebar/aydinlatma_fiyat.png" alt="" width="35" height="35">
                </div>
                <div class="col-md-9 col-9 d-flex align-items-center ps-4 ps-md-0">
                    AYDINLATMA FİYAT
                </div>
            </div>
        </a>
    </div>
    <div class="card sidebar-item ">
        <a href="/tekliflistesi" class="sidebar-font">
            <div class="row ps-1">
                <div class="col-md-3 col-2 offset-md-0 ">
                    <img src="/files/sidebar/urun_sorgulama.png" alt="" width="35" height="35">
                </div>
                <div class="col-md-9 col-9 d-flex align-items-center ps-4 ps-md-0">
                    ÜRÜN SORGULAMA
                </div>
            </div>
        </a>
    </div>
    <div class="card sidebar-item ">
        <a href="/pricetable" class="sidebar-font">
            <div class="row ps-1">
                <div class="col-md-3 col-2 offset-md-0 ">
                    <img src="/files/sidebar/anlik_fiyatlama.png" alt="" width="35" height="35">
                </div>
                <div class="col-md-9 col-9 d-flex align-items-center ps-4 ps-md-0">
                    FİYAT TABLOSU
                </div>
            </div>
        </a>
    </div>
    <div class="card sidebar-item ">
        <a href="/agirlikhesaplama" class="sidebar-font">
            <div class="row ps-1">
                <div class="col-md-3 col-2 offset-md-0 ">
                    <img src="/files/sidebar/agirlik_hesaplama.png" alt="" width="35" height="35">
                </div>
                <div class="col-md-9 col-9 d-flex align-items-center ps-4 ps-md-0">
                    AĞIRLIK HESAPLAMA
                </div>
            </div>
        </a>
    </div>
    <div class="card sidebar-item ">
        <a href="/factory" class="sidebar-font">
            <div class="row ps-1">
                <div class="col-md-3 col-2 offset-md-0 ">
                    <img src="/files/sidebar/fabrikalar.png" alt="" width="35" height="35">
                </div>
                <div class="col-md-9 col-9 d-flex align-items-center ps-4 ps-md-0">
                    FABRİKALAR
                </div>
            </div>
        </a>
    </div>
    <div class="card sidebar-item ">
        <a href="/client" class="sidebar-font">
            <div class="row ps-1">
                <div class="col-md-3 col-2 offset-md-0 ">
                    <img src="/files/sidebar/clients.png" alt="" width="35" height="35">
                </div>
                <div class="col-md-9 col-9 d-flex align-items-center ps-4 ps-md-0">
                    FİRMALAR
                </div>
            </div>
        </a>
    </div>
    <div class="card sidebar-item ">
        <a href="/mold" class="sidebar-font">
            <div class="row ps-1">
                <div class="col-md-3 col-2 offset-md-0 ">
                    <img src="/files/sidebar/kaliplar.png" alt="" width="35" height="35">
                </div>
                <div class="col-md-9 col-9 d-flex align-items-center ps-4 ps-md-0">
                    KALIPLAR
                </div>
            </div>
        </a>
    </div>
    <div class="card sidebar-item ">
        <a href="#" onclick="return false" onmousedown="javascript:ackapa4('isplanidivi','agirlikhesaplamadivi','anlikfiyatlamadivi','fiyathesaplamadivi');" class="sidebar-font">
            <div class="row ps-1">
                <div class="col-md-3 col-2 offset-md-0 ">
                    <img src="/files/sidebar/is_plani.png" alt="" width="35" height="35">
                </div>
                <div class="col-md-9 col-9 d-flex align-items-center ps-4 ps-md-0">
                    İŞ PLANI
                </div>
            </div>
        </a>
    </div>
    <div class="card sidebar-item ">
        <a href="/leave" class="sidebar-font">
            <div class="row ps-1">
                <div class="col-md-3 col-2 offset-md-0 ">
                    <img src="/files/sidebar/izin.png" alt="" width="35" height="35">
                </div>
                <div class="col-md-9 col-9 d-flex align-items-center ps-4 ps-md-0">
                    İZİNLER
                </div>
            </div>
        </a>
    </div>
    <div class="card sidebar-item ">
        <a href="/organization" class="sidebar-font">
            <div class="row ps-1">
                <div class="col-md-3 col-2 offset-md-0 ">
                    <img src="/files/sidebar/organizasyon.png" alt="" width="35" height="35">
                </div>
                <div class="col-md-9 col-9 d-flex align-items-center ps-4 ps-md-0">
                    ORGANİZASYON
                </div>
            </div>
        </a>
    </div>
    <div class="card sidebar-item ">
        <a href="/sevkiyatplan" class="sidebar-font">
            <div class="row ps-1">
                <div class="col-md-3 col-2 offset-md-0 ">
                    <img src="/files/sidebar/sevkiyat_plani.png" alt="" width="35" height="35">
                </div>
                <div class="col-md-9 col-9 d-flex align-items-center ps-4 ps-md-0">
                    SEVKİYAT PLANI
                </div>
            </div>
        </a>
    </div>
    <div class="card sidebar-item ">
        <a href="/sevkiyatarsiv" class="sidebar-font">
            <div class="row ps-1">
                <div class="col-md-3 col-2 offset-md-0 ">
                    <img src="/files/sidebar/sevkiyat_arsivi.png" alt="" width="35" height="35">
                </div>
                <div class="col-md-9 col-9 d-flex align-items-center ps-4 ps-md-0">
                    SEVKİYAT ARŞİVİ
                </div>
            </div>
        </a>
    </div>	
</div>