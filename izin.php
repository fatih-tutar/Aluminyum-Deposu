<?php 
	include 'fonksiyonlar/bagla.php'; 
	if ($girdi != '1') {
		header("Location:giris.php");
		exit();
	}else{
        if(isset($_POST['izin_kaydet'])) {
            $yil = date("Y", time());
            $otuzbirMart = $yil . "-10-31";
            $birOcak = $yil . "-01-01";
            $izinli = isset($_POST['izinli']) && !empty($_POST['izinli']) ? guvenlik($_POST['izinli']) : $uye_id;
            $izinBaslangicTarihi = guvenlik($_POST['izin_baslangic_tarihi']);
            $iseBaslamaTarihi = guvenlik($_POST['ise_baslama_tarihi']);
            $gunSayisi = guvenlik($_POST['gun_sayisi']);
            $kalanIzin = yillikIzinHesapla($izinli) - kullanilanIzinHesapla($izinli);
            $ofis = getOfisType($izinli);
            if(empty($izinBaslangicTarihi) || empty($iseBaslamaTarihi)) {
                $hata = '<br/><div class="alert alert-danger" role="alert">Tarih alanları boş bırakılamaz.</div>';        
            }elseif($tarihv3 > $otuzbirMart) {
                $hata = '<br/><div class="alert alert-danger" role="alert">Sadece 1 Ocak ile 31 Mart tarihleri arasında izin girişi yapabilirsiniz. Bu tarihler dışında lütfen yöneticinizle iletişime geçiniz.</div>';
            }elseif($gunSayisi > 14) {
                $hata = '<br/><div class="alert alert-danger" role="alert">Tek seferde en fazla 14 günlük izin girebilirsiniz.</div>';
            }elseif(getLastLeaveDate($izinli) + 150 > $izinBaslangicTarihi){
                $hata = '<br/><div class="alert alert-danger" role="alert">İzin başlangıç tarihiniz son izninizin bitiş tarihinden en az 150 gün (5 ay) sonra olmalıdır.</div>';    
            }elseif($kalanIzin < $gunSayisi) {
                $hata = '<br/><div class="alert alert-danger" role="alert">Kalan izin hakkınız '.$kalanIzin.' gündür. Daha fazla izin talep edemezsiniz.</div>';    
            }elseif(izinTarihKontrol($izinBaslangicTarihi, $iseBaslamaTarihi, $ofis) != 0) {
                $hata = '<br/><div class="alert alert-danger" role="alert">Sizin departmanınızda aynı tarihlerde izin alan başka bir çalışan var. Lütfen yıllık izin planından kontrol ediniz.</div>';        
            }else{
                $query = $db->prepare("INSERT INTO izinler SET izinli = ?, izin_baslangic_tarihi = ?, ise_baslama_tarihi = ?, gun_sayisi = ?, durum = ?, ofis = ?, sirket = ?, silik = ?, saniye = ?");
                $insert = $query->execute(array($izinli, $izinBaslangicTarihi, $iseBaslamaTarihi, $gunSayisi, '0', $ofis, $uye_sirket, '0', $su_an));
                header("Location: izin.php");
                exit();
            }
        }

        if(isset($_POST['izinonayla'])) {
            $id = guvenlik($_POST['id']);
            $query = $db->prepare("UPDATE izinler SET durum = ? WHERE id = ?");
            $update = $query->execute(array('1',$id));
            header("Location:izin.php");
            exit();
        }

        if(isset($_POST['izinreddet'])) {
            $id = guvenlik($_POST['id']);
            $query = $db->prepare("UPDATE izinler SET durum = ? WHERE id = ?");
            $update = $query->execute(array('2',$id));
            header("Location:izin.php");
            exit();
        }

        if(isset($_POST['izinsil'])) {
            $id = guvenlik($_POST['id']);
            $query = $db->prepare("UPDATE izinler SET silik = ? WHERE id = ?");
            $update = $query->execute(array('1',$id));
            header("Location:izin.php");
            exit();
        }
	}
?>

<!DOCTYPE html>
<html>
  <head>
    <title>İzinler</title>
    <?php include 'template/head.php'; ?>
  </head>
  <body>
    <?php include 'template/banner.php' ?>
    <div class="container-fluid">
        <div class="row">		
            <div class="col-md-12">			
                <?= isset($hata) ? $hata : ''; ?>
            </div>
		</div>
        <div class="div4" style="padding-top: 20px; text-align: center;">
            <a href="#" onclick="return false" onmousedown="javascript:ackapa('formdivi');"><h5><i class="fas fa-angle-double-down" style="color:darkblue;"></i>&nbsp;&nbsp;&nbsp;&nbsp;<b style="color:darkblue;">İzin Giriş Formu</b>&nbsp;&nbsp;&nbsp;&nbsp;<i class="fas fa-angle-double-down" style="color:darkblue;"></i></h5></a>
            <div id="formdivi" style="display: none;">
                <form action="" method="POST">
                    <div class="row mb-2">
                        <?php if($uye_tipi == 2) { ?>
                        <div class="col-md-3 col-12">
                            <b>Çalışan</b>
                            <select name="izinli" id="izinli" class="form-control">
                                <option selected>İzin Verilecek Kişiyi Seçiniz</option>                        
                                <?php
                                    $calisanlaricek = $db->query("SELECT * FROM uyeler WHERE uye_firma = '{$uye_sirket}' AND uye_tipi != '2' ORDER BY uye_adi ASC", PDO::FETCH_ASSOC);
                                    if ( $calisanlaricek->rowCount() ){
                                        foreach( $calisanlaricek as $cc ){
                                            $calisanId = guvenlik($cc['uye_id']);
                                            $calisanAdi = guvenlik($cc['uye_adi']);
                                ?>
                                            <option value="<?php echo $calisanId; ?>"><?php echo $calisanAdi; ?></option>
                                <?php
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                        <?php } ?>
                    </div>
                    <div class="row">
                        <div class="col-md-3 col-12">
                            <b>İzin Başlangıç Tarihi</b><br/>
                            <input type="date" id="izin_baslangic_tarihi" name="izin_baslangic_tarihi" placeholder="İzin Başlangıç Tarihi" class="form-control mb-2">
                        </div>
                        <div class="col-md-3 col-12">
                            <b>İşe Başlama Tarihi</b><br/>
                            <input type="date" id="ise_baslama_tarihi" name="ise_baslama_tarihi" placeholder="İzin Başlangıç Tarihi" class="form-control mb-2">
                        </div>
                        <div class="col-md-3 col-12">
                            <b>İzinli Gün Sayısı</b><br/>
                            <input type="text" id="gun_sayisi" name="gun_sayisi" class="form-control mb-2" style="margin-bottom: 10px;" readonly placeholder="İzinli Gün Sayısı">
                        </div>
                        <div class="col-md-3 col-12">
                            <br/>
                            <button type="submit" class="btn btn-primary btn-block" name="izin_kaydet">İzni Kaydet</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="div4 pt-3 mt-4">
            <h5 style="text-align:center; color:darkblue;"><?= date("Y", time())." YILI İZİN PLANLAMASI" ?></h5>
            <div class="row m-0 d-none d-md-flex">
                <div class="col-md-2"><b>Ad Soyad</b></div>
                <div class="col-md-1 px-0"><b>İşe Giriş Tarihi</b></div>
                <div class="col-md-1"><b>Hakediş</b></div>
                <div class="col-md-2"><b>İzin Başlama Tarihi</b></div>
                <div class="col-md-2"><b>İşe Başlama Tarihi</b></div>
                <div class="col-md-1"><b>Onay</b></div>
                <div class="col-md-1 px-0"><b>Kullanılan İzin</b></div>
                <div class="col-md-1"><b>Kalan İzin</b></div>
            </div>
            <hr/>
            <?php
                $izinler = $db->query("SELECT * FROM izinler WHERE sirket = '{$uye_sirket}' AND 'izin_baslangic_tarihi' > '{$tarihv3}' AND silik = '0' ORDER BY saniye");
                if($izinler->rowCount()) {
                    foreach($izinler as $key => $izin) {
                        $id = guvenlik($izin['id']);
                        $izinli = guvenlik($izin['izinli']);
                        $izinliAdi = getUsername($izinli);
                        $izinBaslangicTarihi = guvenlik($izin['izin_baslangic_tarihi']);
                        $iseBaslamaTarihi = guvenlik($izin['ise_baslama_tarihi']);
                        $gunSayisi = guvenlik($izin['gun_sayisi']);
                        $onay = guvenlik($izin['durum']);
                        $kalanIzin = yillikIzinHesapla($izinli) - kullanilanIzinHesapla($izinli);
                        //arşivleme
                        $izinBaslangicYil = date('Y', strtotime($izinBaslangicTarihi));
                        $iseBaslamaYil = date('Y', strtotime($iseBaslamaTarihi));
                        $currentYear = date('Y');
                        if($izinBaslangicYil < $currentYear && $iseBaslamaYil < $currentYear) {
                            $query = $db->prepare('UPDATE izinler SET durum = ? WHERE id = ?');
                            $update = $query->execute(array('3',$id));
                        }
            ?>
                        <form action="" method="POST">
                            <div class="row" style="margin: 0; <?= $key%2 == 0 ? 'background-color:#c6c6c6;' : ''; ?>">
                                <div class="col-6 d-block d-sm-none"><b>Ad Soyad :</b></div>
                                <div class="col-md-2 col-6"><?= $izinliAdi ?></div>
                                <div class="col-6 d-block d-sm-none"><b>İşe Giriş Tarihi :</b></div>
                                <div class="col-md-1 col-6"><?= (new DateTime(iseGirisTarihiGetir($izinli)))->format('d.m.Y') ?></div>
                                <div class="col-6 d-block d-sm-none"><b>,Hakediş :</b></div>
                                <div class="col-md-1 col-6"><?= yillikIzinHesapla($izinli) ?></div>
                                <div class="col-6 d-block d-sm-none"><b>İzin Başlama Tarihi :</b></div>
                                <div class="col-md-2 col-6"><?= (new DateTime($izinBaslangicTarihi))->format('d.m.Y') ?></div>
                                <div class="col-6 d-block d-sm-none"><b>İşe Başlama Tarihi :</b></div>
                                <div class="col-md-2 col-6"><?= (new DateTime($iseBaslamaTarihi))->format('d.m.Y') ?></div>
                                <div class="col-6 d-block d-sm-none"><b>Onay :</b></div>
                                <div class="col-md-1 col-6"><?= $onay == 0 ? 'Bekleniyor' : ($onay == 1 ? 'Onaylandı' : 'Reddedildi') ?></div>
                                <div class="col-6 d-block d-sm-none"><b>Kullanılan İzin :</b></div>
                                <div class="col-md-1 col-6"><?= kullanilanIzinHesapla($izinli) ?></div>
                                <div class="col-6 d-block d-sm-none"><b>Kalan İzin :</b></div>
                                <div class="col-md-1 col-6"><?= $kalanIzin ?></div>
                                <?php if($uye_tipi == 2) { ?>
                                    <div class="col-md-1 col-12 px-0 d-flex">
                                        <input type="hidden" name="id" value="<?= $id ?>">
                                        <button type="submit" class="btn btn-block btn-sm btn-success mt-0" name="izinonayla" onclick="return confirmForm('İzni onaylıyorsunuz, emin misiniz?');">Onay</button>
                                        <button type="submit" class="btn btn-block btn-sm btn-danger mt-0" name="izinreddet" onclick="return confirmForm('İzin iptal edilecek, emin misiniz?');">Red</button>
                                        <button type="submit" class="btn btn-block btn-sm btn-secondary mt-0" name="izinsil" onclick="return confirmForm('İzni listeden kaldıracaksınız, emin misiniz?');">Sil</button>
                                    </div>
                                <?php } ?>
                            </div>
                        </form>
            <?php
                    }
                }
            ?>
        </div>
        <div class="div4 pt-3 mt-4">
            <h5 style="text-align:center; color:darkblue;">İZİN KULLANIM KURALLARI</h5>
            <ul style="font-size:10px;">
                <li>İZİN TALEPLERİ, 1 OCAK İLE 31 MART TARİHLERİ ARASINDA OLUŞTURULMALI VE BU TALEPLERİN YÖNETİM ONAYI BEKLENMELİDİR; BU TARİH ARALIĞI DIŞINDA KESİNLİKLE İZİN TALEP EDİLEMEZ.</li>
                <li>İZİN TALEPLERİ BELİRTİLEN TARİHLER ARASINDA OLUŞTURULMADIĞI TAKDİRDE, İZİN HAKLARI BULUNAN KİŞİLER YÖNETİMİN BELİRLEDİĞİ TARİHLERDE İZİN KULLANMAK ZORUNDADIR.</li>
                <li>BİR SEFERDE MAKSİMÜM İZİN KULLANIM SÜRESİ  14  GÜN YANI  2 HAFTADIR.</li>
                <li>İKİ İZİN ARASINDA EN AZ 150 GÜN FARK OLMALIDIR; BU KURAL, HERKEZIN EŞİT DÖNEMDE İZİN HAKKININ DÜZENLİ KULLANIMINI SAĞLAMAK İÇİN GEÇERLİDİR.</li>
                <li>AYNI DEPARTMANDA İKİ KİŞİ, AYNI TARİHTE İZİN KULLANAMAZ; BU KURAL, İŞ AKIŞININ DEVAMLIĞINI SAĞLAMAK AMACIYLA GETİRİLMİŞTİR.</li>
                <li>YÖNETİMİN İNSİYATİFİYLE BELİRLEDİĞİ MÜCBİR SEBEBLER HARİÇ, BU KURALAR DIŞINA ÇIKILAMAZ; TÜM ÇALIŞANLARIN BU KURALLARA UYMASI BEKLENMEKTEDİR.</li>
            </ul>
        </div>
        <div class="div4 pt-3 mt-4">
            <h5 style="text-align:center; color:darkblue;">GENEL İZİN TABLOSU</h5>
            <div class="row m-0 d-none d-md-flex">
                <div class="col-md-3"><b>Adı Soyadı</b></div>
                <div class="col-md-3"><b>İşe Giriş Tarihi</b></div>
                <div class="col-md-2"><b>Toplam Hakediş</b></div>
                <div class="col-md-2"><b>Kullanılan İzin</b></div>
                <div class="col-md-2"><b>Kalan İzin</b></div>
            </div>
            <hr/>
            <?php
                $uyeler = $db->query("SELECT * FROM uyeler WHERE uye_tipi != '2' AND uye_firma = '{$uye_sirket}' AND uye_silik = '0' ORDER BY uye_adi ASC", PDO::FETCH_ASSOC);
                if ( $uyeler->rowCount() ){
                    foreach( $uyeler as $key => $uye ){
                        $uyeId = guvenlik($uye['uye_id']);
                        $uyeAdi = guvenlik($uye['uye_adi']);
                        $iseGirisTarihi = guvenlik($uye['ise_giris_tarihi']);
                        $toplamHakedis = yillikIzinHesapla($uyeId);
                        $kullanilanIzin = kullanilanIzinHesapla($uyeId);
                        $kalanIzin = $toplamHakedis - $kullanilanIzin;
            ?>
                        <div class="row" style="margin: 0; <?= $key%2 == 0 ? 'background-color:#c6c6c6;' : ''; ?>">
                            <div class="col-6 d-block d-sm-none"><b>Ad Soyad :</b></div>
                            <div class="col-md-3 col-6"><?= $uyeAdi ?></div>
                            <div class="col-6 d-block d-sm-none"><b>İşe Giriş Tarihi :</b></div>
                            <div class="col-md-3 col-6"><?= (new DateTime($iseGirisTarihi))->format('d.m.Y') ?></div>
                            <div class="col-6 d-block d-sm-none"><b>Toplam Hakediş :</b></div>
                            <div class="col-md-2 col-6"><?= $toplamHakedis ?></div>
                            <div class="col-6 d-block d-sm-none"><b>Kullanılan İzin :</b></div>
                            <div class="col-md-2 col-6"><?= $kullanilanIzin ?></div>
                            <div class="col-6 d-block d-sm-none"><b>Kalan İzin :</b></div>
                            <div class="col-md-2 col-6"><?= $kalanIzin ?></div>
                        </div>
            <?php
                    }
                }
            ?>
            <!--<hr/>
            <ul style="font-size:10px;">
                <li>
                Yıllık izin süresi hesaplama işlemi için öncelikle bir iş yerinde minimum 1 yıl çalışmış olmanız gerekir. 
                Eğer 1 yıl çalıştıysanız, yıllık izin süreniz 14 gün olacaktır. 5 yıldan fazla 15 yıldan az çalıştıysanız 20 gün, 15 yıl (dahil) 
                ve daha fazla çalıştıysanız en az 26 gün ücretli izin hakkınız vardır.
                </li>
            </ul> -->
        </div>
        <br/><br/><br/><br/><br/>
    </div>
    <?php include 'template/script.php'; ?>
    <script>
        function hesaplaGunFarki() {
            var baslangicTarihi = document.getElementById("izin_baslangic_tarihi").value;
            var bitisTarihi = document.getElementById("ise_baslama_tarihi").value;
            if (baslangicTarihi && bitisTarihi) {
                var baslangic = new Date(baslangicTarihi);
                var bitis = new Date(bitisTarihi);
                var farkZaman = bitis.getTime() - baslangic.getTime();
                var gunFarki = farkZaman / (1000 * 3600 * 24); // Milisaniyeleri gün cinsinden hesaplama
                if (gunFarki >= 0) {
                    document.getElementById("gun_sayisi").value = gunFarki;
                } else {
                    document.getElementById("gun_sayisi").value = 0;
                }
            }
        }
        // Her iki tarih girişini dinleyen olay tetikleyicisi
        document.getElementById("izin_baslangic_tarihi").addEventListener("change", hesaplaGunFarki);
        document.getElementById("ise_baslama_tarihi").addEventListener("change", hesaplaGunFarki);      
    </script>
</body>
</html>