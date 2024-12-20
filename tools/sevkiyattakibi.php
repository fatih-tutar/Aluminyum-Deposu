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
            
            <div class="col-md-1 col-12">

                <b>Adet</b>
                
                <input type="text" class="form-control" name="adet" placeholder="(Boy)">

            </div>

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

            <div class="col-md-1 col-12">
                <b>Fiyat</b>
                <input type="text" class="form-control" name="fiyat" placeholder="TL">
            </div>

        </div>

        <div class="row">

            <div class="col-md-10 col-12">

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
            $yeniSevkiyatlar = $db->query("SELECT * FROM sevkiyat WHERE durum = '0' AND sirket_id = '{$uye_sirket}' AND silik = '0' ORDER BY saniye DESC", PDO::FETCH_ASSOC);
            if($yeniSevkiyatlar->rowCount()){
                foreach($yeniSevkiyatlar as $sevkiyat){
                    $sevkiyatID = guvenlik($sevkiyat['id']);
                    $urunler = guvenlik($sevkiyat['urunler']);
                    $urunArray = explode(",",$urunler);
                    $firmaId = guvenlik($sevkiyat['firma_id']);
                    $firmaAdi = getFirmaAdi($firmaId);
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
                    $sevkTipi = guvenlik($sevkiyat['sevk_tipi']);
                    $sevkTipleri = ['Müşteri Çağlayan','Müşteri Alkop','Tarafımızca sevk','Ambara tarafımızca sevk'];
                    $aciklama = guvenlik($sevkiyat['aciklama']);
                    $saniye = guvenlik($sevkiyat['saniye']);
                    $tarih = getdmY($saniye);
        ?>
                    <div class="sevkCardBlue p-2 pb-2 pb-sm-0">
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
            }
        ?>
    </div>
    <div class="col-md-4 col-12">
        <div class="sevkCardYellow p-1" style="text-align:center; font-size:25px;">
            Hazırlanan Siparişler
        </div>
        <?php
            $yeniSevkiyatlar = $db->query("SELECT * FROM sevkiyat WHERE durum = '1' AND sirket_id = '{$uye_sirket}' AND silik = '0' ORDER BY saniye DESC", PDO::FETCH_ASSOC);
            if($yeniSevkiyatlar->rowCount()){
                foreach($yeniSevkiyatlar as $sevkiyat){
                    $sevkiyatID = guvenlik($sevkiyat['id']);
                    $urunler = guvenlik($sevkiyat['urunler']);
                    $urunArray = explode(",",$urunler);
                    $firmaId = guvenlik($sevkiyat['firma_id']);
                    $firmaAdi = getFirmaAdi($firmaId);
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
                    $sevkTipi = guvenlik($sevkiyat['sevk_tipi']);
                    $sevkTipleri = ['Müşteri Çağlayan','Müşteri Alkop','Tarafımızca sevk','Ambara tarafımızca sevk'];
                    $aciklama = guvenlik($sevkiyat['aciklama']);
                    $saniye = guvenlik($sevkiyat['saniye']);
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
            }
        ?>
    </div>
    <div class="col-md-4 col-12">
        <div class="sevkCardGreen p-1" style="text-align:center; font-size:25px;">
            Faturası Kesilenler
        </div>
        <?php
            $yeniSevkiyatlar = $db->query("SELECT * FROM sevkiyat WHERE durum = '2' AND sirket_id = '{$uye_sirket}' AND silik = '0' ORDER BY saniye DESC", PDO::FETCH_ASSOC);
            if($yeniSevkiyatlar->rowCount()){
                foreach($yeniSevkiyatlar as $sevkiyat){
                    $sevkiyatID = guvenlik($sevkiyat['id']);
                    $urunler = guvenlik($sevkiyat['urunler']);
                    $urunArray = explode(",",$urunler);
                    $firmaId = guvenlik($sevkiyat['firma_id']);
                    $firmaAdi = getFirmaAdi($firmaId);
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
            }
        ?>
    </div>
</div>