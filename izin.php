<?php 

	include 'fonksiyonlar/bagla.php'; 

	if ($girdi != '1') {
		
		header("Location:giris.php");

		exit();

	}else{


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
        <div class="div4" style="padding-top: 20px; text-align: center;">
            <a href="#" onclick="return false" onmousedown="javascript:ackapa('formdivi');"><h5><i class="fas fa-angle-double-down"></i>&nbsp;&nbsp;&nbsp;&nbsp;<b>İzin Giriş Formu</b>&nbsp;&nbsp;&nbsp;&nbsp;<i class="fas fa-angle-double-down"></i></h5></a>
            <div id="formdivi" style="display: none;">
                <form action="" method="POST">
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