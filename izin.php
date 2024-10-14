<?php 
	include 'fonksiyonlar/bagla.php'; 
	if ($girdi != '1') {
		header("Location:giris.php");
		exit();
	}else{
        if(isset($_POST['izin_kaydet'])) {
            $yil = date("Y", time());
            $otuzbirMart = $yil . "-03-31";
            $birOcak = $yil . "-01-01";
            if ($tarihv3 <= $otuzbirMart && $tarihv3 >= $birOcak) {
                $gunSayisi = guvenlik($_POST['gun_sayisi']);
                if($gunSayisi <= 14) {
                    $izinli = isset($_POST['izinli']) && !empty($_POST['izinli']) ? guvenlik($_POST['izinli']) : $uye_id;
                    $izinBaslangicTarihi = guvenlik($_POST['izin_baslangic_tarihi']);
                    if(getLastLeaveDate($izinli) + 150 < $izinBaslangicTarihi){
                        $iseBaslamaTarihi = guvenlik($_POST['ise_baslama_tarihi']);
                        $query = $db->prepare("INSERT INTO izinler SET izinli = ?, izin_baslangic_tarihi = ?, ise_baslama_tarihi = ?, gun_sayisi = ?, durum = ?, silik = ?, saniye = ?");
                        $insert = $query->execute(array($izinli, $izinBaslangicTarihi, $iseBaslamaTarihi, $gunSayisi, '0', '0', $su_an));
                        header("Location: izin.php");
                        exit();
                    } else {
                        $hata = '<br/><div class="alert alert-danger" role="alert">İzin başlangıç tarihiniz son izninizin bitiş tarihinden en az 150 gün (5 ay) sonra olmalıdır.</div>';    
                    }
                }else{
                    $hata = '<br/><div class="alert alert-danger" role="alert">Tek seferde en fazla 14 günlük izin girebilirsiniz.</div>';
                }
            }else{
                $hata = '<br/><div class="alert alert-danger" role="alert">Sadece 1 Ocak ile 31 Mart tarihleri arasında izin girişi yapabilirsiniz. Bu tarihler dışında lütfen yöneticinizle iletişime geçiniz.</div>';
            }
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
            <a href="#" onclick="return false" onmousedown="javascript:ackapa('formdivi');"><h5><i class="fas fa-angle-double-down"></i>&nbsp;&nbsp;&nbsp;&nbsp;<b>İzin Giriş Formu</b>&nbsp;&nbsp;&nbsp;&nbsp;<i class="fas fa-angle-double-down"></i></h5></a>
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
        <div class="div4">
            <div class="row">
                <div class="col-md-2">Ad Soyad</div>
                <div class="col-md-2">İşe Giriş Tarihi</div>
                <div class="col-md-1">Toplam Hakediş</div>
                <div class="col-md-2">İzin Başlama Tarihi</div>
                <div class="col-md-2">İşe Başlama Tarihi</div>
                <div class="col-md-1">Onay</div>
                <div class="col-md-1">Kullanılan İzin</div>
                <div class="col-md-1">İzin</div>
            </div>
            <?php
                $izinler = $db->query("SELECT * FROM izinler WHERE sirket = '{$uye_sirket}' AND 'izin_baslangic_tarihi' > '{$tarihv3}' AND silik = '0' ORDER BY saniye");
                if($izinler->rowCount()) {
                    foreach($izinler as $izin) {
                        $izinli = guvenlik($izin['izinli']);
                        $izinliAdi = getUsername($izinli);
                        $izinBaslangicTarihi = guvenlik($izin['izin_baslangic_tarihi']);
                        $iseBaslamaTarihi = guvenlik($izin['ise_baslama_tarihi']);
                        $gunSayisi = guvenlik($izin['gun_sayisi']);
                        $onay = guvenlik($izin['durum']);
            ?>
                        <div class="row">
                            <div class="col-md-2"><?= $izinliAdi; ?></div>
                            <div class="col-md-2"><?= iseGirisTarihiGetir($izinli); ?></div>
                            <div class="col-md-1"><?= yillikIzinHesapla($izinli); ?></div>
                            <div class="col-md-2"><?= $izinBaslangicTarihi; ?></div>
                            <div class="col-md-2"><?= $iseBaslamaTarihi; ?></div>
                            <div class="col-md-1"><?= $onay == 0 ? 'Bekleniyor' : 'Onaylandı'; ?></div>
                            <div class="col-md-1"><?= kullanilanIzinHesapla($izinli); ?></div>
                            <div class="col-md-1"><?= yillikIzinHesapla($izinli) - kullanilanIzinHesapla($izinli); ?></div>
                        </div>
            <?php
                    }
                }
            ?>
        </div>
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