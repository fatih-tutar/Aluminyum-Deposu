<?php
    // SEVKİYATLAR
    $sevkiyatlar = $db->query("SELECT * FROM sevkiyat WHERE silik = '0' AND manuel = '0' AND durum != '3'", PDO::FETCH_OBJ)->fetchAll();
    $alinanSiparisler = [];
    $hazirlananSiparisler = [];
    $faturasiKesilenler = [];
    foreach ($sevkiyatlar as $sevkiyat) {
        if ($sevkiyat->durum == 0) {
            $alinanSiparisler[] = $sevkiyat;
        } elseif ($sevkiyat->durum == 1) {
            $hazirlananSiparisler[] = $sevkiyat;
        } elseif ($sevkiyat->durum == 2) {
            $faturasiKesilenler[] = $sevkiyat;
        }
    }
    $sevkiyatGruplari = [];
    foreach ($sevkiyatlar as $sevkiyat) {
        $sevkiyatGruplari[$sevkiyat->arac_id][] = $sevkiyat;
    }
    // ARAÇLAR
    $araclar = $db->query("SELECT * FROM araclar WHERE silik = '0'", PDO::FETCH_OBJ)->fetchAll();

    // FİRMALAR
    $firmalar = $db->query("SELECT * FROM firmalar WHERE silik = '0'", PDO::FETCH_OBJ)->fetchAll();

    if(isset($_POST['sevkiyathazir'])){
        $sevkiyatID = guvenlik($_POST['sevkiyatID']);
        $malzemeAdeti = guvenlik($_POST['malzemeAdeti']);
        $kilolar = guvenlik($_POST['kilolar']);
        if(!empty($kilolar) && !is_numeric($kilolar)){
            $hata = '<br/><div class="alert alert-danger" role="alert">Kilo kısmına sadece sayısal bir değer girebilirsiniz.</div>';
        }
        if(empty($kilolar)){
            for($i = 0; $i < $malzemeAdeti; $i++){
                if(!empty(guvenlik($_POST['kilo_'.$i]))){
                    if(is_numeric($_POST['kilo_'.$i])){
                        if($i == 0){
                            $kilolar = guvenlik($_POST['kilo_'.$i]);
                        }else{
                            $kilolar = $kilolar.",".guvenlik($_POST['kilo_'.$i]);
                        }
                    }else{
                        $hata = '<br/><div class="alert alert-danger" role="alert">Kilo kısmına sadece sayısal bir değer girebilirsiniz.</div>';
                    }
                }
            }
        }
        if(!empty($kilolar)){
            $query = $db->prepare("UPDATE sevkiyat SET kilolar = ?, durum = ?, hazirlayan = ? WHERE id = ?");
            $update = $query->execute(array($kilolar,'1',$user->id,$sevkiyatID));
        }else{
            $hata = '<br/><div class="alert alert-danger" role="alert">Ürünlere tek tek veya toplam olarak kilo girmelisiniz.</div>';
        }
        if(!$hata){
            header("Location: index.php");
            exit();
        }
    }

    if(isset($_POST['sevkiyatsil'])){
        $sevkiyatID = guvenlik($_POST['sevkiyatID']);
        $query = $db->prepare("UPDATE sevkiyat SET silik = ? WHERE id = ?");
        $update = $query->execute(array('1',$sevkiyatID));
        header("Location: index.php");
        exit();
    }

    if(isset($_POST['faturahazir'])){
        $sevkiyatID = guvenlik($_POST['sevkiyatID']);
        $query = $db->prepare("UPDATE sevkiyat SET durum = ?, faturaci = ? WHERE id = ?");
        $update = $query->execute(array('2',$user->id,$sevkiyatID));
        header("Location: index.php");
        exit();
    }

    if(isset($_POST['alinanagerial'])){
        $sevkiyatID = guvenlik($_POST['sevkiyatID']);
        $query = $db->prepare("UPDATE sevkiyat SET durum = ? WHERE id = ?");
        $update = $query->execute(array('0',$sevkiyatID));
        header("Location: index.php");
        exit();
    }

    if(isset($_POST['arsivegonder'])){
        $sevkiyatID = guvenlik($_POST['sevkiyatID']);
        $query = $db->prepare("UPDATE sevkiyat SET durum = ? WHERE id = ?");
        $update = $query->execute(array('3',$sevkiyatID));
        header("Location: index.php");
        exit();
    }

    if(isset($_POST['hazirlananagerial'])){
        $sevkiyatID = guvenlik($_POST['sevkiyatID']);
        $query = $db->prepare("UPDATE sevkiyat SET durum = ? WHERE id = ?");
        $update = $query->execute(array('1',$sevkiyatID));
        header("Location: index.php");
        exit();
    }

    if(isset($_POST['sevkiyattanurunsil'])){
        $malzemeIndex = guvenlik($_POST['sevkiyattanurunsil']);
        $sevkiyatID = guvenlik($_POST['sevkiyatID']);
        $sevkiyat = getSevkiyatInfo($sevkiyatID);

        $sevkiyatUrunler = $sevkiyat['urunler'];
        $urunArray = explode(",",$sevkiyatUrunler);
        unset($urunArray[$malzemeIndex]);
        $sevkiyatUrunler = implode(",",array_values($urunArray));

        $sevkiyatAdetler = $sevkiyat['adetler'];
        $adetArray = explode(",",$sevkiyatAdetler);
        unset($adetArray[$malzemeIndex]);
        $sevkiyatAdetler = implode(",",array_values($adetArray));

        $sevkiyatKilolar = $sevkiyat['kilolar'];
        $kiloArray = explode(",",$sevkiyatKilolar);
        unset($kiloArray[$malzemeIndex]);
        $sevkiyatKilolar = implode(",",array_values($kiloArray));

        $sevkiyatFiyatlar = $sevkiyat['fiyatlar'];
        $fiyatArray = explode("-",$sevkiyatFiyatlar);
        unset($fiyatArray[$malzemeIndex]);
        $sevkiyatFiyatlar = implode("-",array_values($fiyatArray));

        $query = $db->prepare("UPDATE sevkiyat SET urunler = ?, adetler = ?, kilolar = ?, fiyatlar  = ? WHERE id = ?");
        $update = $query->execute(array($sevkiyatUrunler,$sevkiyatAdetler,$sevkiyatKilolar,$sevkiyatFiyatlar,$sevkiyatID));
        header("Location: index.php");
        exit();
    }

    if (isset($_POST['sevkiyatkaydet'])) {
        $urun = $_POST['urun'];
        $adet = guvenlik($_POST['adet']);
        $fiyat = guvenlik($_POST['fiyat']);
        $sevkTipi =  guvenlik($_POST['sevk_tipi']);
        $arac_id =  guvenlik($_POST['arac_id']);
        $aciklama =  guvenlik($_POST['aciklama']);
        $firma = guvenlik($_POST['firma']);
        if(empty($urun)){
            $hata = '<br/><div class="alert alert-danger" role="alert">Müşteri sipariş formu için lütfen bir ürün seçiniz.</div>';
        }else if(empty($firma)){
            $hata = '<br/><div class="alert alert-danger" role="alert">Müşteri sipariş formu için lütfen bir firma seçiniz.</div>';
        }else if(empty($adet)){
            $hata = '<br/><div class="alert alert-danger" role="alert">Müşteri sipariş formu için lütfen bir adet belirtiniz.</div>';
        }else if(empty($fiyat)){
            $hata = '<br/><div class="alert alert-danger" role="alert">Müşteri sipariş formu için lütfen bir fiyat yazınız.</div>';
        }else if($sevkTipi === "null") {
            $hata = '<br/><div class="alert alert-danger" role="alert">Müşteri sipariş formu için lütfen bir sevk tipi seçiniz.</div>';
        }else{
            $urunArray = explode("/",$urun);
            $urun = trim($urunArray[0]);
            $kategori_iki = trim($urunArray[1]);
            $kategori_bir = trim($urunArray[2]);
            $urunId = getUrunID($urun,$kategori_iki,$kategori_bir);
            $firmaId = getFirmaID($firma);
            $sevkiyatList = $db->query("SELECT * FROM sevkiyat WHERE firma_id = '{$firmaId}' AND durum = '0' AND silik = '0' AND manuel = '0' AND sirket_id = '{$user->company_id}' ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            if($sevkiyatList){
                $urunler = guvenlik($sevkiyatList['urunler']);
                $adetler = guvenlik($sevkiyatList['adetler']);
                $fiyatlar = guvenlik($sevkiyatList['fiyatlar']);
                $urunler = $urunler.",".$urunId;
                $adetler = $adetler.",".$adet;
                $fiyatlar = $fiyatlar."-".$fiyat;
                $query = $db->prepare("UPDATE sevkiyat SET urunler = ?, adetler = ?, fiyatlar = ? WHERE firma_id = ? AND durum = ? AND silik = ? AND sirket_id = ?");
                $update = $query->execute(array($urunler, $adetler, $fiyatlar, $firmaId, '0', '0', $user->company_id));
            }else{
                $query = $db->prepare("INSERT INTO sevkiyat SET urunler = ?, firma_id = ?, adetler = ?, kilolar = ?, fiyatlar = ?, olusturan = ?, hazirlayan = ?, sevk_tipi = ?, arac_id = ?, aciklama = ?, manuel = ?, durum = ?, silik = ?, saniye = ?, sirket_id = ?");
                $insert = $query->execute(array($urunId,$firmaId,$adet,'',$fiyat,$user->id,'',$sevkTipi,$arac_id,$aciklama,'0','0','0',$su_an, $user->company_id));
            }
            header("Location:index.php");
            exit();
        }
    }
?>
<div class="div4 p-2 mb-4">
    <form action="" method="POST">
                                
        <div class="row">

            <div class="col-12">
                <h3><b>Müşteri Sipariş Formu</b></h3>
            </div>

            <div class="col-md-5 col-12 urun-search-box">

                <b>Ürün</b>

                <input autofocus="autofocus" name="urun" id="uruninputu" type="text" class="form-control" autocomplete="off" placeholder="Ürün Adı"/>

                <ul class="list-group urunliveresult" id="urunsonuc" style="position: absolute; z-index: 1;"></ul>

            </div>

            <div class="col-md-3 col-12 search-box">

                <b>Firma</b>
                
                <input autofocus="autofocus" name="firma" id="firmainputu" type="text" class="form-control" autocomplete="off" placeholder="Firma Adı"/>

                <ul class="list-group liveresult" id="firmasonuc" style="position: absolute; z-index: 1;"></ul>

            </div>
            
            <div class="col-md-2 col-12">

                <b>Adet</b>
                
                <input type="text" class="form-control" name="adet" placeholder="(Boy)">

            </div>

            <div class="col-md-2 col-12">
                <b>Fiyat</b>
                <input type="text" class="form-control" name="fiyat" placeholder="TL">
            </div>

        </div>

        <div class="row">

            <div class="col-md-2 col-12">

                <b>Sevk Tipi</b>

                <select name="sevk_tipi" id="sevk_tipi" class="form-control">

                    <option value="null">Sevk tipi seçiniz.</option>
                    <option value="0">Müşteri Çağlayan</option>
                    <option value="1">Müşteri Alkop</option>
                    <option value="2">Tarafımızca sevk</option>
                    <option value="3">Ambara tarafımızca sevk</option>

                </select>

            </div>

            <div class="col-md-2 col-12">

                <b>Araç</b>

                <select name="arac_id" id="arac_id" class="form-control">

                    <option value="null">Araç seçiniz.</option>
                    <?php
                            foreach( $araclar as $arac ){
                    ?>
                                <option value="<?= $arac->id ?>"><?= $arac->arac_adi ?></option>
                    <?php
                            }
                    ?>

                </select>

            </div>

            <div class="col-md-6 col-12">

                <b>Açıklama</b>

                <input type="text" class="form-control" name="aciklama" placeholder="Sevkiyat ile ilgili açıklama yazabilirsiniz.">

            </div>

            <div class="col-md-2 col-12">
                <br/>
                <button class="btn btn-warning btn-block" name="sevkiyatkaydet">Kaydet</button>

            </div>

        </div>

    </form>
</div>
<div id="sevkiyattakibidivi" class="row">
    <div class="col-md-4 col-12">
        <div class="sevkCardBlue p-1" style="text-align:center; font-size:25px;">
            Alınan Siparişler
        </div>
        <?php
                foreach($alinanSiparisler as $alinanSiparis){
                    $sevkiyatID = guvenlik($alinanSiparis->id);
                    $urunler = guvenlik($alinanSiparis->urunler);
                    $urunArray = explode(",",$urunler);
                    $firmaId = guvenlik($alinanSiparis->firma_id);
                    $firmaAdi = reset(array_filter($firmalar, fn($firma) => $firma->firmaid == $firmaId))->firmaadi;
                    $adetler = guvenlik($alinanSiparis->adetler);
                    $adetArray = explode(",",$adetler);
                    $kilolar = guvenlik($alinanSiparis->kilolar);
                    if(strpos($kilolar, ',')){
                        $kiloArray = explode(",",$kilolar);
                        $toplamkg = 0;
                        foreach($kiloArray as $kilo){
                            $toplamkg += $kilo;
                        }
                    }
                    $fiyatlar = guvenlik($alinanSiparis->fiyatlar);
                    $fiyatArray = explode("-",$fiyatlar);
                    $olusturan = guvenlik($alinanSiparis->olusturan);
                    $hazirlayan = guvenlik($alinanSiparis->hazirlayan);
                    $sevkTipi = guvenlik($alinanSiparis->sevk_tipi);
                    $sevkTipleri = ['Müşteri Çağlayan','Müşteri Alkop','Tarafımızca sevk','Ambara tarafımızca sevk'];
                    $arac_id = guvenlik($alinanSiparis->arac_id);
                    $arac_adi = reset(array_filter($araclar, fn($arac) => $arac->id == $arac_id))->arac_adi;
                    $aciklama = guvenlik($alinanSiparis->aciklama);
                    $saniye = guvenlik($alinanSiparis->saniye);
                    $tarih = getdmY($saniye);
        ?>
                    <div class="<?= $sevkTipi == '0' ? 'sevkCardDarkBlue' : 'sevkCardBlue' ?> p-2 pb-2 pb-sm-0">
                        <form action="" method="POST">
                            <a href="#" onclick="return false" onmousedown="javascript:ackapa4('alinan-siparis-<?= $sevkiyatID ?>');">
                                <div class="row">
                                    <div class="col-md-8 col-6"><b>Firma :</b> <?= $firmaAdi ?></div>
                                    <div class="col-md-4 col-6" style="text-align:right;"><?= $tarih ?></div>
                                </div>
                            </a>
                            <div id="alinan-siparis-<?= $sevkiyatID ?>" style="display:none;">
                                <hr class="my-1" style="border-top:1px solid white;"/>
                                <div class="d-none d-sm-block">
                                    <div class="row">
                                        <div class="col-md-4"><b>Ürün</b></div>
                                        <div class="col-md-2"><b>Cinsi</b></div>
                                        <div class="col-md-2"><b>Adet</b></div>
                                        <div class="col-md-2"><b>Kg</b></div>
                                        <div class="col-md-2"><b>Fiyat</b></div>
                                    </div>
                                    <hr class="my-1" style="border-top:1px solid white;"/>
                                </div>
                                <?php
                                    $totalWeight = 0;
                                    $totalPrice = 0;
                                    $malzemeAdeti = 0;
                                    foreach($urunArray as $key => $urunId){
                                        $urun = getUrunInfo($urunId);
                                        if($urun !== false) {
                                ?>
                                            <div class="row mb-1">
                                                <div class="col-4 d-block d-sm-none">Ürün Adı : </div>
                                                <div class="col-md-4 col-8"><?= $urun['urun_adi'].' '. getCategoryShortName($urun['kategori_iki']) ?></div>
                                                <div class="col-4 d-block d-sm-none">Cinsi : </div>
                                                <div class="col-md-2 col-8"><?= getCategoryShortName($urun['kategori_bir']) ?></div>
                                                <div class="col-4 d-block d-sm-none">Adet : </div>
                                                <div class="col-md-2 col-8"><?= $adetArray[$key] ?></div>
                                                <div class="col-4 d-block d-sm-none">Kilo : </div>
                                                <div class="col-md-2 col-8 pl-0"><input type="text" name="kilo_<?= $key ?>" class="form-control form-control-sm" style="height:25px;" value="<?= strpos($kilolar,",") ? $kiloArray[$key] : '' ?>"></div>
                                                <div class="col-4 d-block d-sm-none">Fiyat : </div>
                                                <div class="col-md-2 col-8 px-3 px-sm-0"><?= $fiyatArray[$key].' TL' ?></div>
                                            </div>
                                            <div class="row">
                                                <div class="offset-md-10 col-md-2">
                                                    <button type="submit" name="sevkiyattanurunsil" value="<?= $malzemeAdeti ?>" style="border-style:none; background-color:#17a2b8;">Sil</button>
                                                </div>
                                            </div>
                                            <hr class="my-1" style="border-top:1px solid white;"/>
                                <?php
                                            $malzemeAdeti++;
                                        }
                                    }
                                ?>
                                <div class="row">
                                    <div class="col-md-6 col-12"></div>
                                    <div class="col-md-2 col-4"><b>Toplam</b></div>
                                    <div class="col-md-4 col-8"><input type="text" class="form-control form-control-sm" placeholder="TOPLAM KG" name="kilolar" value="<?= strpos($kilolar,",") ? $toplamkg : $kilolar ?>"></div>
                                </div>
                                <hr class="my-1" style="border-top:1px solid white;"/>
                                <div class="row">
                                    <div class="col-12"><b>Siparişi Oluşturan : </b><?= getUsername($olusturan) ?></div>
                                </div>
                                <div class="row">
                                    <div class="col-12"><b>Sevk Tipi: </b><?= $sevkTipleri[$sevkTipi] ?></div>
                                </div>
                                <div class="row">
                                    <div class="col-12"><b>Araç: </b><?= $arac_adi ?></div>
                                </div>
                                <div class="row">
                                    <div class="col-12"><b>Açıklama: </b><?= $aciklama ?></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2 col-2 pr-0">
                                        <button type="submit" name="sevkiyatsil" class="btn btn-danger btn-block btn-sm">Sil</button>
                                    </div>
                                    <div class="col-md-5 col-5 mb-2">
                                        <a href="sevkiyatformu.php?id=<?= $sevkiyatID ?>" target="_blank" class="btn btn-light btn-block btn-sm">
                                            Siparişi yazdır
                                        </a>
                                    </div>
                                    <div class="col-md-5 col-5">
                                        <input type="hidden" name="sevkiyatID" value="<?= $sevkiyatID ?>">
                                        <input type="hidden" name="malzemeAdeti" value="<?= $malzemeAdeti ?>">
                                        <button type="submit" name="sevkiyathazir" class="btn btn-light btn-block btn-sm">Sevkiyat Hazır</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
        <?php
                }
        ?>
    </div>
    <div class="col-md-4 col-12">
        <div class="sevkCardYellow p-1" style="text-align:center; font-size:25px;">
            Hazırlanan Siparişler
        </div>
        <?php
                foreach($hazirlananSiparisler as $hazirlananSiparis){
                    $sevkiyatID = guvenlik($hazirlananSiparis->id);
                    $urunler = guvenlik($hazirlananSiparis->urunler);
                    $urunArray = explode(",",$urunler);
                    $firmaId = guvenlik($hazirlananSiparis->firma_id);
                    $firmaAdi = reset(array_filter($firmalar, fn($firma) => $firma->firmaid == $firmaId))->firmaadi;
                    $adetler = guvenlik($hazirlananSiparis->adetler);
                    $adetArray = explode(",",$adetler);
                    $kilolar = guvenlik($hazirlananSiparis->kilolar);
                    if(strpos($kilolar, ',')){
                        $kiloArray = explode(",",$kilolar);
                        $toplamkg = 0;
                        foreach($kiloArray as $kilo){
                            $toplamkg += $kilo;
                        }
                    }
                    $fiyatlar = guvenlik($hazirlananSiparis->fiyatlar);
                    $fiyatArray = explode("-",$fiyatlar);
                    $olusturan = guvenlik($hazirlananSiparis->olusturan);
                    $hazirlayan = guvenlik($hazirlananSiparis->hazirlayan);
                    $sevkTipi = guvenlik($hazirlananSiparis->sevk_tipi);
                    $sevkTipleri = ['Müşteri Çağlayan','Müşteri Alkop','Tarafımızca sevk','Ambara tarafımızca sevk'];
                    $arac_id = guvenlik($hazirlananSiparis->arac_id);
                    $arac_adi = reset(array_filter($araclar, fn($arac) => $arac->id == $arac_id))->arac_adi;
                    $aciklama = guvenlik($hazirlananSiparis->aciklama);
                    $saniye = guvenlik($hazirlananSiparis->saniye);
                    $tarih = getdmY($saniye);
        ?>
                    <div class="sevkCardYellow p-2">
                        <form action="" method="POST">
                            <a href="#" onclick="return false" onmousedown="javascript:ackapa4('hazirlanan-siparis-<?= $sevkiyatID ?>');">
                                <div class="row">
                                    <div class="col-md-8 col-6"><b>Firma :</b> <?= $firmaAdi ?></div>
                                    <div class="col-md-4 col-6" style="text-align:right;"><?= $tarih ?></div>
                                </div>
                            </a>
                            <div id="hazirlanan-siparis-<?= $sevkiyatID ?>" style="display:none;">
                                <hr class="my-1" style="border-top:1px solid white;"/>
                                <div class="d-none d-sm-block">
                                    <div class="row">
                                        <div class="col-md-4"><b>Ürün</b></div>
                                        <div class="col-md-2"><b>Cinsi</b></div>
                                        <div class="col-md-2"><b>Adet</b></div>
                                        <div class="col-md-2"><b>Kg</b></div>
                                        <div class="col-md-2"><b>Fiyat</b></div>
                                    </div>
                                    <hr class="my-1" style="border-top:1px solid white;"/>
                                </div>
                                <?php
                                    $totalWeight = 0;
                                    $totalPrice = 0;
                                    $malzemeAdeti = 0;
                                    foreach($urunArray as $key => $urunId){
                                        $urun = getUrunInfo($urunId);
                                        if($urun !== false) {
                                ?>
                                            <div class="row mb-1">
                                                <div class="col-4 d-block d-sm-none">Ürün Adı : </div>
                                                <div class="col-md-4 col-8"><?= $urun['urun_adi'].' '. getCategoryShortName($urun['kategori_iki']) ?></div>
                                                <div class="col-4 d-block d-sm-none">Cinsi : </div>
                                                <div class="col-md-2 col-8"><?= getCategoryShortName($urun['kategori_bir']) ?></div>
                                                <div class="col-4 d-block d-sm-none">Adet : </div>
                                                <div class="col-md-2 col-8"><?= $adetArray[$key] ?></div>
                                                <div class="col-4 d-block d-sm-none">Kilo : </div>
                                                <div class="col-md-2 col-8"><?= strpos($kilolar,",") ? $kiloArray[$key] : '' ?></div>
                                                <div class="col-4 d-block d-sm-none">Fiyat : </div>
                                                <div class="col-md-2 col-8 px-3 px-sm-0"><?= $fiyatArray[$key].' TL' ?></div>
                                            </div>
                                            <hr class="my-1" style="border-top:1px solid white;"/>
                                <?php
                                            $malzemeAdeti++;
                                        }
                                    }
                                ?>
                                <div class="row">
                                    <div class="col-md-6 col-12"></div>
                                    <div class="col-md-2 col-4"><b>Toplam</b></div>
                                    <div class="col-md-4 col-4"><?= strpos($kilolar,",") ? $toplamkg.' KG' : $kilolar.' KG' ?></div>
                                </div>
                                <hr class="my-1" style="border-top:1px solid white;"/>
                                <div class="row">
                                    <div class="col-12"><b>Siparişi Oluşturan : </b><?= getUsername($olusturan) ?></div>
                                </div>
                                <div class="row">
                                    <div class="col-12"><b>Siparişi Hazırlayan : </b><?= getUsername($hazirlayan) ?></div>
                                </div>
                                <div class="row">
                                    <div class="col-12"><b>Sevk Tipi: </b><?= $sevkTipleri[$sevkTipi] ?></div>
                                </div>
                                <div class="row">
                                    <div class="col-12"><b>Araç: </b><?= $arac_adi ?></div>
                                </div>
                                <div class="row">
                                    <div class="col-12"><b>Açıklama: </b><?= $aciklama ?></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-6">
                                        <input type="hidden" name="sevkiyatID" value="<?= $sevkiyatID ?>">
                                        <button type="submit" name="alinanagerial" class="btn btn-light btn-block btn-sm">Geri Al</button>
                                    </div>
                                    <div class="col-md-6 col-6">
                                        <input type="hidden" name="sevkiyatID" value="<?= $sevkiyatID ?>">
                                        <button type="submit" name="faturahazir" class="btn btn-light btn-block btn-sm">Fatura Hazır</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
        <?php
                }
        ?>
    </div>
    <div class="col-md-4 col-12">
        <div class="sevkCardGreen p-1" style="text-align:center; font-size:25px;">
            Faturası Kesilenler
        </div>
        <?php
                foreach($faturasiKesilenler as $faturasiKesilen){
                    $sevkiyatID = guvenlik($faturasiKesilen->id);
                    $urunler = guvenlik($faturasiKesilen->urunler);
                    $urunArray = explode(",",$urunler);
                    $firmaId = guvenlik($faturasiKesilen->firma_id);
                    $firmaAdi = reset(array_filter($firmalar, fn($firma) => $firma->firmaid == $firmaId))->firmaadi;
                    $adetler = guvenlik($faturasiKesilen->adetler);
                    $adetArray = explode(",",$adetler);
                    $kilolar = guvenlik($faturasiKesilen->kilolar);
                    if(strpos($kilolar, ',')){
                        $kiloArray = explode(",",$kilolar);
                        $toplamkg = 0;
                        foreach($kiloArray as $kilo){
                            $toplamkg += $kilo;
                        }
                    }
                    $fiyatlar = guvenlik($faturasiKesilen->fiyatlar);
                    $fiyatArray = explode("-",$fiyatlar);
                    $olusturan = guvenlik($faturasiKesilen->olusturan);
                    $hazirlayan = guvenlik($faturasiKesilen->hazirlayan);
                    $faturaci = guvenlik($faturasiKesilen->faturaci);
                    $sevkTipi = guvenlik($faturasiKesilen->sevk_tipi);
                    $sevkTipleri = ['Müşteri Çağlayan','Müşteri Alkop','Tarafımızca sevk','Ambara tarafımızca sevk'];
                    $arac_id = guvenlik($faturasiKesilen->arac_id);
                    $arac_adi = $db->query("SELECT * FROM araclar WHERE id = '{$arac_id}'")->fetch(PDO::FETCH_ASSOC)['arac_adi'];
                    $aciklama = guvenlik($faturasiKesilen->aciklama);
                    $saniye = guvenlik($faturasiKesilen->saniye);
                    $tarih = getdmY($saniye);
        ?>
                    <div class="sevkCardGreen p-2">
                        <form action="" method="POST">
                            <a href="#" onclick="return false" onmousedown="javascript:ackapa4('faturali-siparis-<?= $sevkiyatID ?>');">
                                <div class="row">
                                    <div class="col-md-8 col-6"><b>Firma :</b> <?= $firmaAdi ?></div>
                                    <div class="col-md-4 col-6" style="text-align:right;"><?= $tarih ?></div>
                                </div>
                            </a>
                            <div id="faturali-siparis-<?= $sevkiyatID ?>" style="display:none;">
                                <hr class="my-1" style="border-top:1px solid white;"/>
                                <div class="d-none d-sm-block">
                                    <div class="row">
                                        <div class="col-md-4"><b>Ürün</b></div>
                                        <div class="col-md-2"><b>Cinsi</b></div>
                                        <div class="col-md-2"><b>Adet</b></div>
                                        <div class="col-md-2"><b>Kg</b></div>
                                        <div class="col-md-2"><b>Fiyat</b></div>
                                    </div>
                                    <hr class="my-1" style="border-top:1px solid white;"/>
                                </div>
                                <?php
                                    $totalWeight = 0;
                                    $totalPrice = 0;
                                    $malzemeAdeti = 0;
                                    foreach($urunArray as $key => $urunId){
                                        $urun = getUrunInfo($urunId);
                                        if($urun !== false) {
                                ?>
                                            <div class="row mb-1">
                                                <div class="col-4 d-block d-sm-none">Ürün Adı : </div>
                                                <div class="col-md-4 col-8"><?= $urun['urun_adi'].' '. getCategoryShortName($urun['kategori_iki']) ?></div>
                                                <div class="col-4 d-block d-sm-none">Cinsi : </div>
                                                <div class="col-md-2 col-8"><?= getCategoryShortName($urun['kategori_bir']) ?></div>
                                                <div class="col-4 d-block d-sm-none">Adet : </div>
                                                <div class="col-md-2 col-8"><?= $adetArray[$key] ?></div>
                                                <div class="col-4 d-block d-sm-none">Kilo : </div>
                                                <div class="col-md-2 col-8"><?= strpos($kilolar,",") ? $kiloArray[$key] : '' ?></div>
                                                <div class="col-4 d-block d-sm-none">Fiyat : </div>
                                                <div class="col-md-2 col-8 px-3 px-sm-0"><?= $fiyatArray[$key].' TL' ?></div>
                                            </div>
                                            <hr class="my-1" style="border-top:1px solid white;"/>
                                <?php
                                            $malzemeAdeti++;
                                        }
                                    }
                                ?>
                                <div class="row">
                                    <div class="col-md-6 col-12"></div>
                                    <div class="col-md-2 col-4"><b>Toplam</b></div>
                                    <div class="col-md-4 col-4"><?= strpos($kilolar,",") ? $toplamkg.' KG' : $kilolar.' KG' ?></div>
                                </div>
                                <hr class="my-1" style="border-top:1px solid white;"/>
                                <div class="row">
                                    <div class="col-12"><b>Siparişi Oluşturan : </b><?= getUsername($olusturan) ?></div>
                                </div>
                                <div class="row">
                                    <div class="col-12"><b>Siparişi Hazırlayan : </b><?= getUsername($hazirlayan) ?></div>
                                </div>
                                <div class="row">
                                    <div class="col-12"><b>Faturayı Kesen : </b><?= getUsername($faturaci) ?></div>
                                </div>
                                <div class="row">
                                    <div class="col-12"><b>Sevk Tipi: </b><?= $sevkTipleri[$sevkTipi] ?></div>
                                </div>
                                <div class="row">
                                    <div class="col-12"><b>Araç: </b><?= $arac_adi ?></div>
                                </div>
                                <div class="row">
                                    <div class="col-12"><b>Açıklama: </b><?= $aciklama ?></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-6">
                                        <input type="hidden" name="sevkiyatID" value="<?= $sevkiyatID ?>">
                                        <button type="submit" name="hazirlananagerial" class="btn btn-light btn-block btn-sm">Geri Al</button>
                                    </div>
                                    <div class="col-md-6 col-6">
                                        <input type="hidden" name="sevkiyatID" value="<?= $sevkiyatID ?>">
                                        <button type="submit" name="arsivegonder" class="btn btn-light btn-block btn-sm">Arşive Gönder</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
        <?php
                }
        ?>
    </div>
</div>