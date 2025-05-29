<?php 

	include 'functions/init.php';

	if (!isLoggedIn()) {
		
		header("Location:login.php");

		exit();

	}else{

        if($user->permissions->visit != '1'){

            header("Location:index.php");

		    exit();

        }else{

            if(isset($_POST['ziyaretkayit'])){
                
                $il = guvenlik($_POST['il']);
                $ilce = guvenlik($_POST['ilce']);
                $iskolu = guvenlik($_POST['iskolu']);
                $musteriismi = guvenlik($_POST['musteriismi']);
                $yetkilikisi = guvenlik($_POST['yetkilikisi']);
                $telefon = guvenlik($_POST['telefon']);
                $ziyarettarihi = guvenlik($_POST['ziyarettarihi']);
                $planlanantarih = guvenlik($_POST['planlanantarih']);
                $acikadres = guvenlik($_POST['acikadres']);
                $ziyaretnotu = guvenlik($_POST['ziyaretnotu']);

                $query = $db->prepare("INSERT INTO ziyaretler SET il = ?, ilce = ?, iskolu = ?, musteriismi = ?, yetkilikisi = ?, telefon = ?, ziyarettarihi = ?, planlanantarih = ?, acikadres = ?, ziyaretnotu = ?, saniye = ?, silik = ? ");
                $insert = $query->execute(array($il,$ilce,$iskolu,$musteriismi,$yetkilikisi,$telefon,$ziyarettarihi,$planlanantarih,$acikadres,$ziyaretnotu,time(),'0'));

                header("Location:ziyaretler.php");
                exit();

            }

            if(isset($_POST['ziyaretguncelle'])){
                
                $ziyaretid = guvenlik($_POST['ziyaretid']);
                $il = guvenlik($_POST['il']);
                $musteriismi = guvenlik($_POST['musteriismi']);
                $yetkilikisi = guvenlik($_POST['yetkilikisi']);
                $telefon = guvenlik($_POST['telefon']);
                $ziyarettarihi = guvenlik($_POST['ziyarettarihi']);
                $planlanantarih = guvenlik($_POST['planlanantarih']);
                $acikadres = guvenlik($_POST['acikadres']);
                $ziyaretnotu = guvenlik($_POST['ziyaretnotu']);

                $query = $db->prepare("UPDATE ziyaretler SET il = ?, musteriismi = ?, yetkilikisi = ?, telefon = ?, ziyarettarihi = ?, planlanantarih = ?, acikadres = ?, ziyaretnotu = ?, saniye = ?, silik = ? WHERE id = ?");
                $insert = $query->execute(array($il,$musteriismi,$yetkilikisi,$telefon,$ziyarettarihi,$planlanantarih,$acikadres,$ziyaretnotu,time(),'0',$ziyaretid));

                header("Location:ziyaretler.php"); exit();

            }

            if(isset($_POST['ziyaretsil'])){
                
                $ziyaretid = guvenlik($_POST['ziyaretid']);

                $query = $db->prepare("UPDATE ziyaretler SET silik = ? WHERE id = ?");
                $insert = $query->execute(array('1',$ziyaretid));

                header("Location:ziyaretler.php"); exit();

            }

            if(isset($_POST['iskolukayit'])){

                $iskoluadi = guvenlik($_POST['iskoluadi']);

                $query = $db->prepare("INSERT INTO ziyaret_kategori SET adi = ?, saniye = ?, silik = ?, sirketid = ?");

                $insert = $query->execute(array($iskoluadi,time(),'0',$user->company_id));

                header("Location:ziyaretler.php"); exit();

            }

            if(isset($_POST['iskoluguncelle'])){

                $iskoluid = guvenlik($_POST['iskoluid']);

                $iskoluadi = guvenlik($_POST['iskoluadi']);

                $query = $db->prepare("UPDATE ziyaret_kategori SET adi = ? WHERE id = ?"); 

                $guncelle = $query->execute(array($iskoluadi,$iskoluid));

                header("Location:ziyaretler.php"); exit();

            }

            if(isset($_POST['iskolusil'])){

                $iskoluid = guvenlik($_POST['iskoluid']);

                $query = $db->prepare("UPDATE ziyaret_kategori SET silik = ? WHERE id = ?"); 

                $guncelle = $query->execute(array('1',$iskoluid));

                header("Location:ziyaretler.php"); exit();

            }

        }

	}

?>

<!DOCTYPE html>

<html>

  <head>

    <title>Alüminyum Deposu</title>

    <?php include 'template/head.php'; ?>

  </head>

  <body>

    <?php include 'template/banner.php' ?>

    <div class="container-fluid">
                
        <div class="div4" style="padding-top: 20px; text-align: center;">

            <div class="row mb-3">

                <div class="col-4">

                    <a href="ziyaretler.php"><b>Ana Sayfa</b></a>

                </div>

                <div class="col-4">
                    
                    <a href="#" onclick="return false" onmousedown="javascript:ackapa2('formdivi','iskoludivi');"><b>Kayıt</b></a>
                
                </div>
                
                <div class="col-4">

                <a href="#" onclick="return false" onmousedown="javascript:ackapa2('iskoludivi','formdivi');"><b>İş Kolları</b></a>

                </div>

            </div>

            <div class="row">

                <div class="col-md-4 col-12"></div>
                <div class="col-md-4 col-12">
                    <div id="formdivi" style="display:none;">
            
                        <form action="" method="POST">

                            <div class="row">
                                
                                <div class="col-12 mb-2">
                                    <select id="Iller" name="il" class="form-control">
                                        <option value="0">Lütfen Bir İl Seçiniz</option>
                                    </select>
                                </div>
                                <div class="col-12 mb-2">
                                    <select id="Ilceler" name="ilce" class="form-control" disabled="disabled">
                                        <option value="0">Lütfen Önce bir İl seçiniz</option>
                                    </select>    
                                </div>
                                <div class="col-12 mb-2">
                                    <select name="iskolu" id="iskolu" class="form-control">
                                        <option value="0">Kategori</option>
                                        <?php
                                            $ziyaret_kategori_list = $db->query("SELECT * FROM ziyaret_kategori WHERE silik = '0' ORDER BY adi ASC", PDO::FETCH_ASSOC);

                                            if ( $ziyaret_kategori_list->rowCount() ){
                                            
                                                foreach( $ziyaret_kategori_list as $zkl ){
                                            
                                                    $iskolulisteid = guvenlik($zkl['id']);
                                                    $iskolulisteadi = guvenlik($zkl['adi']);
                                        ?>
                                                    <option value="<?= $iskolulisteid; ?>"><?= $iskolulisteadi; ?></option>
                                        <?php
                                                    
                                            
                                                }
                                            
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-12 mb-2"><input type="text" name="musteriismi" class="form-control" placeholder="Müşteri İsmi"></div>
                                <div class="col-12 mb-2"><input type="text" name="yetkilikisi" class="form-control" placeholder="Yetkili Kişi"></div>
                                <div class="col-12 mb-2"><input type="text" name="telefon" class="form-control" placeholder="Telefon"></div>
                                <div class="col-12 mb-2"><input type="text" id="tarih1" name="ziyarettarihi" placeholder="Son Ziyaret Tarihi" class="form-control"></div>
                                <div class="col-12 mb-2"><input type="text" id="tarih2" name="planlanantarih" placeholder="Planlanan Ziyaret Tarihi" class="form-control"></div>
                                <div class="col-12 mb-2"><textarea name="acikadres" id="" class="form-control" rows="1" placeholder="Açık Adres"></textarea></div>
                                <div class="col-12 mb-2"><textarea name="ziyaretnotu" id="" class="form-control" rows="1" placeholder="Ziyaret Notu"></textarea></div>
                                <div class="col-12 mb-2"><button type="submit" class="btn btn-primary btn-block" name="ziyaretkayit">Kaydet</button></div>

                            </div>

                        </form>

                    </div>
                </div>
                <div class="col-md-4 col-12">

                    <div id="iskoludivi" style="display:none;">
                        <form action="" method="POST">
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <input type="text" class="form-control" name="iskoluadi" placeholder="İş kolunu giriniz.">
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-block" name="iskolukayit">Kaydet</button>
                                </div>           
                            </div>         
                        </form>

                        <hr/>

                        <?php
                        
                        $ziyaret_kategori_cek = $db->query("SELECT * FROM ziyaret_kategori WHERE silik = '0' ORDER BY adi ASC", PDO::FETCH_ASSOC);

                        if ( $ziyaret_kategori_cek->rowCount() ){

                            foreach( $ziyaret_kategori_cek as $zkc ){

                                $iskoluid = guvenlik($zkc['id']);

                                $iskoluadi = guvenlik($zkc['adi']);

                        ?>

                                <form action="" method="POST">
                                    <div class="row mb-1">
                                        <div class="col-12 mb-2">
                                            <input type="text" class="form-control" name="iskoluadi" placeholder="İş kolunu giriniz." value="<?= $iskoluadi; ?>">
                                        </div>
                                        <div class="col-6">
                                            <input type="hidden" name="iskoluid" value="<?= $iskoluid; ?>">
                                            <button type="submit" class="btn btn-primary btn-block" name="iskoluguncelle">Güncelle</button>
                                        </div>   
                                        <div class="col-6">
                                            <button type="submit" class="btn btn-danger btn-block" name="iskolusil">Sil</button>
                                        </div>          
                                    </div>         
                                </form>        

                        <?php

                            }

                        }
                        
                        ?>

                    </div>

                </div>

            </div>

        </div>

        <div style="padding-top: 20px; text-align: center;">
            <div class="d-none d-sm-block" style="border-bottom:1px solid black; position: sticky; top: 70px; z-index:3;">
                <div class="row pt-2" style="background-color: white;">
                    <div class="col-md-1 mb-1 px-1"><b style="color:#18a2b8; font-size:17px;">İl</b></div>
                    <div class="col-md-1 mb-1 px-1"><b style="color:#18a2b8; font-size:17px;">İlçe</b></div>
                    <div class="col-md-1 mb-1 px-1"><b style="color:#18a2b8; font-size:17px;">İş Kolu</b></div>
                    <div class="col-md-1 mb-1 px-1"><b style="color:#18a2b8; font-size:17px;">Müşteri İsmi</b></div>
                    <div class="col-md-1 mb-1 px-1"><b style="color:#18a2b8; font-size:17px;">Yetkili Kişi</b></div>
                    <div class="col-md-1 mb-1 px-1"><b style="color:#18a2b8; font-size:17px;">Telefon</b></div>
                    <div class="col-md-1 mb-1 px-1"><b style="color:#18a2b8; font-size:17px;">Ziyaret Tarihi</b></div>
                    <div class="col-md-2 mb-1 px-1" style="text-align:left;"><b style="color:#18a2b8; font-size:17px;">Planlanan Tarih</b></div>
                    <div class="col-md-2 mb-1 px-1" style="background-color:#06589c; border-radius:10px;">
                        <select name="" id="IlFiltre" style="border:none; text-align:center; border:none; background-color:#06589c; color:white;">
                            <option value="0">Şehir Filtreleme</option>
                            <?php 
                                if(isset($_GET['il']) === true && empty($_GET['il']) === false){ 
                                    $seciliSehir = guvenlik($_GET['il']);
                            ?>
                                <option value="<?= $seciliSehir; ?>" selected><?= $seciliSehir; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="d-sm-none mb-3">
                <select name="" id="mobilIlFiltre" class="form-control" style="border:none; text-align:center;">
                    <option value="0">İl seçerek filtreleme yapabilirsiniz</option>
                    <?php 
                        if(isset($_GET['il']) === true && empty($_GET['il']) === false){ 
                            $seciliSehir = guvenlik($_GET['il']);
                    ?>
                        <option value="<?= $seciliSehir; ?>" selected><?= $seciliSehir; ?></option>
                    <?php } ?>
                </select>
            </div>

            <?php
            $i = 0;
            $seciliIlce = '';
            if(isset($_GET['ilce'])){ $seciliIlce = guvenlik($_GET['ilce']); }
            if(isset($_GET['iskolu'])){ $seciliiskolu = guvenlik($_GET['iskolu']); }
            if(empty($seciliiskolu) === false){
                $query = $db->query("SELECT * FROM ziyaretler WHERE iskolu = '{$seciliiskolu}' AND silik = '0' ORDER BY saniye DESC", PDO::FETCH_ASSOC);
            }elseif(empty($seciliIlce) === false){
                $query = $db->query("SELECT * FROM ziyaretler WHERE ilce = '{$seciliIlce}' AND silik = '0' ORDER BY saniye DESC", PDO::FETCH_ASSOC);
            }elseif(isset($seciliSehir)){
                $query = $db->query("SELECT * FROM ziyaretler WHERE il = '{$seciliSehir}' AND silik = '0' ORDER BY saniye DESC", PDO::FETCH_ASSOC);
            }else{
                $query = $db->query("SELECT * FROM ziyaretler WHERE silik = '0' ORDER BY saniye DESC", PDO::FETCH_ASSOC);
            }
            if ( $query->rowCount() ){
                foreach( $query as $row ){
                    $i++;
                    $id = guvenlik($row['id']);
                    $il = guvenlik($row['il']);
                    $ilce = guvenlik($row['ilce']);
                    $iskolu = guvenlik($row['iskolu']);
                    $iskoluadicek = $db->query("SELECT * FROM ziyaret_kategori WHERE id = '{$iskolu}'")->fetch(PDO::FETCH_ASSOC);
                    $iskoluadi = guvenlik($iskoluadicek['adi'] ?? null);
                    $musteriismi = guvenlik($row['musteriismi']);
                    $yetkilikisi = guvenlik($row['yetkilikisi']);
                    $telefon = guvenlik($row['telefon']);
                    $ziyarettarihi = guvenlik($row['ziyarettarihi']);
                    $planlanantarih = guvenlik($row['planlanantarih']);
                    $acikadres = guvenlik($row['acikadres']);
                    $ziyaretnotu = guvenlik($row['ziyaretnotu']);
                    $saniye = guvenlik($row['saniye']);
                    $silik = guvenlik($row['silik']);
            ?>
                    
                    <form action="" method="POST">
                        <input type="hidden" name="ziyaretid" value="<?= $id; ?>">
                        <div class="row" style="background-color: <?= $i%2 == 0 ? 'white' : '#c4c4c4'; ?>;">
                            <div class="col-4 d-sm-none pr-0 pt-2" style="text-align:left;"><b>İl</b></div>
                            <div class="col-md-1 col-8 px-1" style="border-right:1px solid black;"><input type="text" name="il" class="form-control p-1" style="font-size: 16px; font-weight:bold; border:none; border-radius:0; background-color: <?= $i%2 == 0 ? 'white' : '#c4c4c4'; ?>;" placeholder="İl" value="<?= $il; ?>"></div>
                            
                            <div class="col-4 d-sm-none pr-0 pt-2" style="text-align:left;"><b>İlçe</b></div>
                            <div class="col-md-1 col-8 px-1 pt-2" style="border-right:1px solid black; text-align:left;">
                                <a href="ziyaretler.php?ilce=<?= $ilce; ?>"><?= $ilce; ?></a>
                            </div>
                            
                            <div class="col-4 d-sm-none pr-0 pt-2" style="text-align:left;"><b>İş Kolu</b></div>
                            <div class="col-md-1 col-8 px-1 pt-1" style="border-right:1px solid black; text-align:left;">
                                <a href="ziyaretler.php?iskolu=<?= $iskolu; ?>"><?= $iskoluadi; ?></a>
                            </div>
                            
                            <div class="col-4 d-sm-none pr-0 pt-2" style="text-align:left;"><b>Müşteri İsmi</b></div>
                            <div class="col-md-1 col-8 px-1" style="border-right:1px solid black;"><input type="text" name="musteriismi" class="form-control p-1" style="font-size: 16px; font-weight:bold; border:none; border-radius:0; background-color: <?= $i%2 == 0 ? 'white' : '#c4c4c4'; ?>;" placeholder="Müşteri İsmi" value="<?= $musteriismi; ?>"></div>
                            
                            <div class="col-4 d-sm-none pr-0 pt-2" style="text-align:left;"><b>Yetkili Kişi</b></div>
                            <div class="col-md-1 col-8 px-1" style="border-right:1px solid black;"><input type="text" name="yetkilikisi" class="form-control p-1" style="font-size: 16px; font-weight:bold; border:none; border-radius:0; background-color: <?= $i%2 == 0 ? 'white' : '#c4c4c4'; ?>;" placeholder="Yetkili Kişi" value="<?= $yetkilikisi; ?>"></div>
                            
                            <div class="col-4 d-sm-none pr-0 pt-2" style="text-align:left;"><b>Telefon</b></div>
                            <div class="col-md-1 col-8 px-1" style="border-right:1px solid black;"><input type="text" name="telefon" class="form-control p-1" style="font-size: 16px; font-weight:bold; border:none; border-radius:0; background-color: <?= $i%2 == 0 ? 'white' : '#c4c4c4'; ?>;" placeholder="Telefon" value="<?= $telefon; ?>"></div>
                            
                            <div class="col-4 d-sm-none pr-0 pt-2" style="text-align:left;"><b>Ziyaret Tarihi</b></div>
                            <div class="col-md-1 col-8 px-1" style="border-right:1px solid black;"><input type="text" id="tarih1<?= $id; ?>" name="ziyarettarihi" placeholder="Son Ziyaret Tarihi" class="form-control p-1" style="font-size: 16px; font-weight:bold; border:none; border-radius:0; background-color: <?= $i%2 == 0 ? 'white' : '#c4c4c4'; ?>;" value="<?= $ziyarettarihi; ?>"></div>
                           
                            <div class="col-4 d-sm-none pr-0 pt-2" style="text-align:left;"><b>Planlanan Tarih</b></div>
                            <div class="col-md-1 col-8 px-1" style="border-right:1px solid black;"><input type="text" id="tarih2<?= $id; ?>" name="planlanantarih" placeholder="Planlanan Ziyaret Tarihi" class="form-control p-1" style="font-size: 16px; font-weight:bold; border:none; border-radius:0; background-color: <?= $i%2 == 0 ? 'white' : '#c4c4c4'; ?>;" value="<?= $planlanantarih; ?>"></div>
                            
                            <div class="col-md-1 col-3 px-1 py-2">
                                <button class="btn btn-success btn-block btn-sm" onclick="return false" onmousedown="javascript:ackapa3('adresdivi<?= $id; ?>','notdivi<?= $id; ?>');">Adres</button>
                            </div>
                            <div class="col-md-1 col-3 px-1 py-2">
                                <button class="btn btn-info btn-block btn-sm" onclick="return false" onmousedown="javascript:ackapa3('notdivi<?= $id; ?>','adresdivi<?= $id; ?>');">Not</button>
                            </div>
                            <div class="col-md-1 col-3 px-1 py-2">
                                <button type="submit" class="btn btn-warning btn-block btn-sm" name="ziyaretguncelle">Güncelle</button>
                            </div>
                            <div class="col-md-1 col-3 px-1 py-2">
                                <button type="submit" class="btn btn-danger btn-block btn-sm" name="ziyaretsil">Sil</button>
                            </div>
                            <div id="adresdivi<?= $id; ?>" class="col-md-12 mb-1 px-1" style="display:none;"><textarea name="acikadres" id="" class="form-control" rows="1" placeholder="Açık Adres"><?= $acikadres; ?></textarea></div>
                            <div id="notdivi<?= $id; ?>" class="col-md-12 mb-1 px-1" style="display:none;"><textarea name="ziyaretnotu" id="" class="form-control" rows="1" placeholder="Ziyaret Notu"><?= $ziyaretnotu; ?></textarea></div>
                        </div>

                    </form>

            <?php
                }
            }
            ?>

        </div>

    </div>

    <div style="height:300px;"></div>

    <?php include 'template/script.php'; ?>

    <script language="javascript" type="text/javascript">

        var data = [
            {
            "il": "Adana",
            "plaka": 1,
            "ilceleri": [
                "Aladağ",
                "Ceyhan",
                "Çukurova",
                "Feke",
                "İmamoğlu",
                "Karaisalı",
                "Karataş",
                "Kozan",
                "Pozantı",
                "Saimbeyli",
                "Sarıçam",
                "Seyhan",
                "Tufanbeyli",
                "Yumurtalık",
                "Yüreğir"
            ]
            },
            {
            "il": "Adıyaman",
            "plaka": 2,
            "ilceleri": [
                "Besni",
                "Çelikhan",
                "Gerger",
                "Gölbaşı",
                "Kahta",
                "Merkez",
                "Samsat",
                "Sincik",
                "Tut"
            ]
            },
            {
            "il": "Afyonkarahisar",
            "plaka": 3,
            "ilceleri": [
                "Başmakçı",
                "Bayat",
                "Bolvadin",
                "Çay",
                "Çobanlar",
                "Dazkırı",
                "Dinar",
                "Emirdağ",
                "Evciler",
                "Hocalar",
                "İhsaniye",
                "İscehisar",
                "Kızılören",
                "Merkez",
                "Sandıklı",
                "Sinanpaşa",
                "Sultandağı",
                "Şuhut"
            ]
            },
            {
            "il": "Ağrı",
            "plaka": 4,
            "ilceleri": [
                "Diyadin",
                "Doğubayazıt",
                "Eleşkirt",
                "Hamur",
                "Merkez",
                "Patnos",
                "Taşlıçay",
                "Tutak"
            ]
            },
            {
            "il": "Aksaray",
            "plaka": 68,
            "ilceleri": [
                "Ağaçören",
                "Eskil",
                "Gülağaç",
                "Güzelyurt",
                "Merkez",
                "Ortaköy",
                "Sarıyahşi"
            ]
            },
            {
            "il": "Amasya",
            "plaka": 5,
            "ilceleri": [
                "Göynücek",
                "Gümüşhacıköy",
                "Hamamözü",
                "Merkez",
                "Merzifon",
                "Suluova",
                "Taşova"
            ]
            },
            {
            "il": "Ankara",
            "plaka": 6,
            "ilceleri": [
                "Altındağ",
                "Ayaş",
                "Bala",
                "Beypazarı",
                "Çamlıdere",
                "Çankaya",
                "Çubuk",
                "Elmadağ",
                "Güdül",
                "Haymana",
                "Kalecik",
                "Kızılcahamam",
                "Nallıhan",
                "Polatlı",
                "Şereflikoçhisar",
                "Yenimahalle",
                "Gölbaşı",
                "Keçiören",
                "Mamak",
                "Sincan",
                "Kazan",
                "Akyurt",
                "Etimesgut",
                "Evren",
                "Pursaklar"
            ]
            },
            {
            "il": "Antalya",
            "plaka": 7,
            "ilceleri": [
                "Akseki",
                "Alanya",
                "Elmalı",
                "Finike",
                "Gazipaşa",
                "Gündoğmuş",
                "Kaş",
                "Korkuteli",
                "Kumluca",
                "Manavgat",
                "Serik",
                "Demre",
                "İbradı",
                "Kemer",
                "Aksu",
                "Döşemealtı",
                "Kepez",
                "Konyaaltı",
                "Muratpaşa"
            ]
            },
            {
            "il": "Ardahan",
            "plaka": 75,
            "ilceleri": [
                "Merkez",
                "Çıldır",
                "Göle",
                "Hanak",
                "Posof",
                "Damal"
            ]
            },
            {
            "il": "Artvin",
            "plaka": 8,
            "ilceleri": [
                "Ardanuç",
                "Arhavi",
                "Merkez",
                "Borçka",
                "Hopa",
                "Şavşat",
                "Yusufeli",
                "Murgul"
            ]
            },
            {
            "il": "Aydın",
            "plaka": 9,
            "ilceleri": [
                "Merkez",
                "Bozdoğan",
                "Efeler",
                "Çine",
                "Germencik",
                "Karacasu",
                "Koçarlı",
                "Kuşadası",
                "Kuyucak",
                "Nazilli",
                "Söke",
                "Sultanhisar",
                "Yenipazar",
                "Buharkent",
                "İncirliova",
                "Karpuzlu",
                "Köşk",
                "Didim"
            ]
            },
            {
            "il": "Balıkesir",
            "plaka": 10,
            "ilceleri": [
                "Altıeylül",
                "Ayvalık",
                "Merkez",
                "Balya",
                "Bandırma",
                "Bigadiç",
                "Burhaniye",
                "Dursunbey",
                "Edremit",
                "Erdek",
                "Gönen",
                "Havran",
                "İvrindi",
                "Karesi",
                "Kepsut",
                "Manyas",
                "Savaştepe",
                "Sındırgı",
                "Gömeç",
                "Susurluk",
                "Marmara"
            ]
            },
            {
            "il": "Bartın",
            "plaka": 74,
            "ilceleri": [
                "Merkez",
                "Kurucaşile",
                "Ulus",
                "Amasra"
            ]
            },
            {
            "il": "Batman",
            "plaka": 72,
            "ilceleri": [
                "Merkez",
                "Beşiri",
                "Gercüş",
                "Kozluk",
                "Sason",
                "Hasankeyf"
            ]
            },
            {
            "il": "Bayburt",
            "plaka": 69,
            "ilceleri": [
                "Merkez",
                "Aydıntepe",
                "Demirözü"
            ]
            },
            {
            "il": "Bilecik",
            "plaka": 11,
            "ilceleri": [
                "Merkez",
                "Bozüyük",
                "Gölpazarı",
                "Osmaneli",
                "Pazaryeri",
                "Söğüt",
                "Yenipazar",
                "İnhisar"
            ]
            },
            {
            "il": "Bingöl",
            "plaka": 12,
            "ilceleri": [
                "Merkez",
                "Genç",
                "Karlıova",
                "Kiğı",
                "Solhan",
                "Adaklı",
                "Yayladere",
                "Yedisu"
            ]
            },
            {
            "il": "Bitlis",
            "plaka": 13,
            "ilceleri": [
                "Adilcevaz",
                "Ahlat",
                "Merkez",
                "Hizan",
                "Mutki",
                "Tatvan",
                "Güroymak"
            ]
            },
            {
            "il": "Bolu",
            "plaka": 14,
            "ilceleri": [
                "Merkez",
                "Gerede",
                "Göynük",
                "Kıbrıscık",
                "Mengen",
                "Mudurnu",
                "Seben",
                "Dörtdivan",
                "Yeniçağa"
            ]
            },
            {
            "il": "Burdur",
            "plaka": 15,
            "ilceleri": [
                "Ağlasun",
                "Bucak",
                "Merkez",
                "Gölhisar",
                "Tefenni",
                "Yeşilova",
                "Karamanlı",
                "Kemer",
                "Altınyayla",
                "Çavdır",
                "Çeltikçi"
            ]
            },
            {
            "il": "Bursa",
            "plaka": 16,
            "ilceleri": [
                "Gemlik",
                "İnegöl",
                "İznik",
                "Karacabey",
                "Keles",
                "Mudanya",
                "Mustafakemalpaşa",
                "Orhaneli",
                "Orhangazi",
                "Yenişehir",
                "Büyükorhan",
                "Harmancık",
                "Nilüfer",
                "Osmangazi",
                "Yıldırım",
                "Gürsu",
                "Kestel"
            ]
            },
            {
            "il": "Çanakkale",
            "plaka": 17,
            "ilceleri": [
                "Ayvacık",
                "Bayramiç",
                "Biga",
                "Bozcaada",
                "Çan",
                "Merkez",
                "Eceabat",
                "Ezine",
                "Gelibolu",
                "Gökçeada",
                "Lapseki",
                "Yenice"
            ]
            },
            {
            "il": "Çankırı",
            "plaka": 18,
            "ilceleri": [
                "Merkez",
                "Çerkeş",
                "Eldivan",
                "Ilgaz",
                "Kurşunlu",
                "Orta",
                "Şabanözü",
                "Yapraklı",
                "Atkaracalar",
                "Kızılırmak",
                "Bayramören",
                "Korgun"
            ]
            },
            {
            "il": "Çorum",
            "plaka": 19,
            "ilceleri": [
                "Alaca",
                "Bayat",
                "Merkez",
                "İskilip",
                "Kargı",
                "Mecitözü",
                "Ortaköy",
                "Osmancık",
                "Sungurlu",
                "Boğazkale",
                "Uğurludağ",
                "Dodurga",
                "Laçin",
                "Oğuzlar"
            ]
            },
            {
            "il": "Denizli",
            "plaka": 20,
            "ilceleri": [
                "Acıpayam",
                "Buldan",
                "Çal",
                "Çameli",
                "Çardak",
                "Çivril",
                "Merkez",
                "Merkezefendi",
                "Pamukkale",
                "Güney",
                "Kale",
                "Sarayköy",
                "Tavas",
                "Babadağ",
                "Bekilli",
                "Honaz",
                "Serinhisar",
                "Baklan",
                "Beyağaç",
                "Bozkurt"
            ]
            },
            {
            "il": "Diyarbakır",
            "plaka": 21,
            "ilceleri": [
                "Kocaköy",
                "Çermik",
                "Çınar",
                "Çüngüş",
                "Dicle",
                "Ergani",
                "Hani",
                "Hazro",
                "Kulp",
                "Lice",
                "Silvan",
                "Eğil",
                "Bağlar",
                "Kayapınar",
                "Sur",
                "Yenişehir",
                "Bismil"
            ]
            },
            {
            "il": "Düzce",
            "plaka": 81,
            "ilceleri": [
                "Akçakoca",
                "Merkez",
                "Yığılca",
                "Cumayeri",
                "Gölyaka",
                "Çilimli",
                "Gümüşova",
                "Kaynaşlı"
            ]
            },
            {
            "il": "Edirne",
            "plaka": 22,
            "ilceleri": [
                "Merkez",
                "Enez",
                "Havsa",
                "İpsala",
                "Keşan",
                "Lalapaşa",
                "Meriç",
                "Uzunköprü",
                "Süloğlu"
            ]
            },
            {
            "il": "Elazığ",
            "plaka": 23,
            "ilceleri": [
                "Ağın",
                "Baskil",
                "Merkez",
                "Karakoçan",
                "Keban",
                "Maden",
                "Palu",
                "Sivrice",
                "Arıcak",
                "Kovancılar",
                "Alacakaya"
            ]
            },
            {
            "il": "Erzincan",
            "plaka": 24,
            "ilceleri": [
                "Çayırlı",
                "Merkez",
                "İliç",
                "Kemah",
                "Kemaliye",
                "Refahiye",
                "Tercan",
                "Üzümlü",
                "Otlukbeli"
            ]
            },
            {
            "il": "Erzurum",
            "plaka": 25,
            "ilceleri": [
                "Aşkale",
                "Çat",
                "Hınıs",
                "Horasan",
                "İspir",
                "Karayazı",
                "Narman",
                "Oltu",
                "Olur",
                "Pasinler",
                "Şenkaya",
                "Tekman",
                "Tortum",
                "Karaçoban",
                "Uzundere",
                "Pazaryolu",
                "Köprüköy",
                "Palandöken",
                "Yakutiye",
                "Aziziye"
            ]
            },
            {
            "il": "Eskişehir",
            "plaka": 26,
            "ilceleri": [
                "Çifteler",
                "Mahmudiye",
                "Mihalıççık",
                "Sarıcakaya",
                "Seyitgazi",
                "Sivrihisar",
                "Alpu",
                "Beylikova",
                "İnönü",
                "Günyüzü",
                "Han",
                "Mihalgazi",
                "Odunpazarı",
                "Tepebaşı"
            ]
            },
            {
            "il": "Gaziantep",
            "plaka": 27,
            "ilceleri": [
                "Araban",
                "İslahiye",
                "Nizip",
                "Oğuzeli",
                "Yavuzeli",
                "Şahinbey",
                "Şehitkamil",
                "Karkamış",
                "Nurdağı"
            ]
            },
            {
            "il": "Giresun",
            "plaka": 28,
            "ilceleri": [
                "Alucra",
                "Bulancak",
                "Dereli",
                "Espiye",
                "Eynesil",
                "Merkez",
                "Görele",
                "Keşap",
                "Şebinkarahisar",
                "Tirebolu",
                "Piraziz",
                "Yağlıdere",
                "Çamoluk",
                "Çanakçı",
                "Doğankent",
                "Güce"
            ]
            },
            {
            "il": "Gümüşhane",
            "plaka": 29,
            "ilceleri": [
                "Merkez",
                "Kelkit",
                "Şiran",
                "Torul",
                "Köse",
                "Kürtün"
            ]
            },
            {
            "il": "Hakkari",
            "plaka": 30,
            "ilceleri": [
                "Çukurca",
                "Merkez",
                "Şemdinli",
                "Yüksekova"
            ]
            },
            {
            "il": "Hatay",
            "plaka": 31,
            "ilceleri": [
                "Altınözü",
                "Arsuz",
                "Defne",
                "Dörtyol",
                "Hassa",
                "Antakya",
                "İskenderun",
                "Kırıkhan",
                "Payas",
                "Reyhanlı",
                "Samandağ",
                "Yayladağı",
                "Erzin",
                "Belen",
                "Kumlu"
            ]
            },
            {
            "il": "Iğdır",
            "plaka": 76,
            "ilceleri": [
                "Aralık",
                "Merkez",
                "Tuzluca",
                "Karakoyunlu"
            ]
            },
            {
            "il": "Isparta",
            "plaka": 32,
            "ilceleri": [
                "Atabey",
                "Eğirdir",
                "Gelendost",
                "Merkez",
                "Keçiborlu",
                "Senirkent",
                "Sütçüler",
                "Şarkikaraağaç",
                "Uluborlu",
                "Yalvaç",
                "Aksu",
                "Gönen",
                "Yenişarbademli"
            ]
            },
            {
            "il": "Mersin",
            "plaka": 33,
            "ilceleri": [
                "Anamur",
                "Erdemli",
                "Gülnar",
                "Mut",
                "Silifke",
                "Tarsus",
                "Aydıncık",
                "Bozyazı",
                "Çamlıyayla",
                "Akdeniz",
                "Mezitli",
                "Toroslar",
                "Yenişehir"
            ]
            },
            {
            "il": "İstanbul",
            "plaka": 34,
            "ilceleri": [
                "Adalar",
                "Bakırköy",
                "Beşiktaş",
                "Beykoz",
                "Beyoğlu",
                "Çatalca",
                "Eyüp",
                "Fatih",
                "Gaziosmanpaşa",
                "Kadıköy",
                "Kartal",
                "Sarıyer",
                "Silivri",
                "Şile",
                "Şişli",
                "Üsküdar",
                "Zeytinburnu",
                "Büyükçekmece",
                "Kağıthane",
                "Küçükçekmece",
                "Pendik",
                "Ümraniye",
                "Bayrampaşa",
                "Avcılar",
                "Bağcılar",
                "Bahçelievler",
                "Güngören",
                "Maltepe",
                "Sultanbeyli",
                "Tuzla",
                "Esenler",
                "Arnavutköy",
                "Ataşehir",
                "Başakşehir",
                "Beylikdüzü",
                "Çekmeköy",
                "Esenyurt",
                "Sancaktepe",
                "Sultangazi"
            ]
            },
            {
            "il": "İzmir",
            "plaka": 35,
            "ilceleri": [
                "Aliağa",
                "Bayındır",
                "Bergama",
                "Bornova",
                "Çeşme",
                "Dikili",
                "Foça",
                "Karaburun",
                "Karşıyaka",
                "Kemalpaşa",
                "Kınık",
                "Kiraz",
                "Menemen",
                "Ödemiş",
                "Seferihisar",
                "Selçuk",
                "Tire",
                "Torbalı",
                "Urla",
                "Beydağ",
                "Buca",
                "Konak",
                "Menderes",
                "Balçova",
                "Çiğli",
                "Gaziemir",
                "Narlıdere",
                "Güzelbahçe",
                "Bayraklı",
                "Karabağlar"
            ]
            },
            {
            "il": "Karabük",
            "plaka": 78,
            "ilceleri": [
                "Eflani",
                "Eskipazar",
                "Merkez",
                "Ovacık",
                "Safranbolu",
                "Yenice"
            ]
            },
            {
            "il": "Karaman",
            "plaka": 70,
            "ilceleri": [
                "Ermenek",
                "Merkez",
                "Ayrancı",
                "Kazımkarabekir",
                "Başyayla",
                "Sarıveliler"
            ]
            },
            {
            "il": "Kars",
            "plaka": 36,
            "ilceleri": [
                "Arpaçay",
                "Digor",
                "Kağızman",
                "Merkez",
                "Sarıkamış",
                "Selim",
                "Susuz",
                "Akyaka"
            ]
            },
            {
            "il": "Kastamonu",
            "plaka": 37,
            "ilceleri": [
                "Abana",
                "Araç",
                "Azdavay",
                "Bozkurt",
                "Cide",
                "Çatalzeytin",
                "Daday",
                "Devrekani",
                "İnebolu",
                "Merkez",
                "Küre",
                "Taşköprü",
                "Tosya",
                "İhsangazi",
                "Pınarbaşı",
                "Şenpazar",
                "Ağlı",
                "Doğanyurt",
                "Hanönü",
                "Seydiler"
            ]
            },
            {
            "il": "Kayseri",
            "plaka": 38,
            "ilceleri": [
                "Bünyan",
                "Develi",
                "Felahiye",
                "İncesu",
                "Pınarbaşı",
                "Sarıoğlan",
                "Sarız",
                "Tomarza",
                "Yahyalı",
                "Yeşilhisar",
                "Akkışla",
                "Talas",
                "Kocasinan",
                "Melikgazi",
                "Hacılar",
                "Özvatan"
            ]
            },
            {
            "il": "Kırklareli",
            "plaka": 39,
            "ilceleri": [
                "Babaeski",
                "Demirköy",
                "Merkez",
                "Kofçaz",
                "Lüleburgaz",
                "Pehlivanköy",
                "Pınarhisar",
                "Vize"
            ]
            },
            {
            "il": "Kırıkkale",
            "plaka": 71,
            "ilceleri": [
                "Delice",
                "Keskin",
                "Merkez",
                "Sulakyurt",
                "Bahşili",
                "Balışeyh",
                "Çelebi",
                "Karakeçili",
                "Yahşihan"
            ]
            },
            {
            "il": "Kırşehir",
            "plaka": 40,
            "ilceleri": [
                "Çiçekdağı",
                "Kaman",
                "Merkez",
                "Mucur",
                "Akpınar",
                "Akçakent",
                "Boztepe"
            ]
            },
            {
            "il": "Kilis",
            "plaka": 79,
            "ilceleri": [
                "Merkez",
                "Elbeyli",
                "Musabeyli",
                "Polateli"
            ]
            },
            {
            "il": "Kocaeli",
            "plaka": 41,
            "ilceleri": [
                "Gebze",
                "Gölcük",
                "Kandıra",
                "Karamürsel",
                "Körfez",
                "Derince",
                "Başiskele",
                "Çayırova",
                "Darıca",
                "Dilovası",
                "İzmit",
                "Kartepe"
            ]
            },
            {
            "il": "Konya",
            "plaka": 42,
            "ilceleri": [
                "Akşehir",
                "Beyşehir",
                "Bozkır",
                "Cihanbeyli",
                "Çumra",
                "Doğanhisar",
                "Ereğli",
                "Hadim",
                "Ilgın",
                "Kadınhanı",
                "Karapınar",
                "Kulu",
                "Sarayönü",
                "Seydişehir",
                "Yunak",
                "Akören",
                "Altınekin",
                "Derebucak",
                "Hüyük",
                "Karatay",
                "Meram",
                "Selçuklu",
                "Taşkent",
                "Ahırlı",
                "Çeltik",
                "Derbent",
                "Emirgazi",
                "Güneysınır",
                "Halkapınar",
                "Tuzlukçu",
                "Yalıhüyük"
            ]
            },
            {
            "il": "Kütahya",
            "plaka": 43,
            "ilceleri": [
                "Altıntaş",
                "Domaniç",
                "Emet",
                "Gediz",
                "Merkez",
                "Simav",
                "Tavşanlı",
                "Aslanapa",
                "Dumlupınar",
                "Hisarcık",
                "Şaphane",
                "Çavdarhisar",
                "Pazarlar"
            ]
            },
            {
            "il": "Malatya",
            "plaka": 44,
            "ilceleri": [
                "Akçadağ",
                "Arapgir",
                "Arguvan",
                "Darende",
                "Doğanşehir",
                "Hekimhan",
                "Merkez",
                "Pütürge",
                "Yeşilyurt",
                "Battalgazi",
                "Doğanyol",
                "Kale",
                "Kuluncak",
                "Yazıhan"
            ]
            },
            {
            "il": "Manisa",
            "plaka": 45,
            "ilceleri": [
                "Akhisar",
                "Alaşehir",
                "Demirci",
                "Gördes",
                "Kırkağaç",
                "Kula",
                "Merkez",
                "Salihli",
                "Sarıgöl",
                "Saruhanlı",
                "Selendi",
                "Soma",
                "Şehzadeler",
                "Yunusemre",
                "Turgutlu",
                "Ahmetli",
                "Gölmarmara",
                "Köprübaşı"
            ]
            },
            {
            "il": "Kahramanmaraş",
            "plaka": 46,
            "ilceleri": [
                "Afşin",
                "Andırın",
                "Dulkadiroğlu",
                "Onikişubat",
                "Elbistan",
                "Göksun",
                "Merkez",
                "Pazarcık",
                "Türkoğlu",
                "Çağlayancerit",
                "Ekinözü",
                "Nurhak"
            ]
            },
            {
            "il": "Mardin",
            "plaka": 47,
            "ilceleri": [
                "Derik",
                "Kızıltepe",
                "Artuklu",
                "Merkez",
                "Mazıdağı",
                "Midyat",
                "Nusaybin",
                "Ömerli",
                "Savur",
                "Dargeçit",
                "Yeşilli"
            ]
            },
            {
            "il": "Muğla",
            "plaka": 48,
            "ilceleri": [
                "Bodrum",
                "Datça",
                "Fethiye",
                "Köyceğiz",
                "Marmaris",
                "Menteşe",
                "Milas",
                "Ula",
                "Yatağan",
                "Dalaman",
                "Seydikemer",
                "Ortaca",
                "Kavaklıdere"
            ]
            },
            {
            "il": "Muş",
            "plaka": 49,
            "ilceleri": [
                "Bulanık",
                "Malazgirt",
                "Merkez",
                "Varto",
                "Hasköy",
                "Korkut"
            ]
            },
            {
            "il": "Nevşehir",
            "plaka": 50,
            "ilceleri": [
                "Avanos",
                "Derinkuyu",
                "Gülşehir",
                "Hacıbektaş",
                "Kozaklı",
                "Merkez",
                "Ürgüp",
                "Acıgöl"
            ]
            },
            {
            "il": "Niğde",
            "plaka": 51,
            "ilceleri": [
                "Bor",
                "Çamardı",
                "Merkez",
                "Ulukışla",
                "Altunhisar",
                "Çiftlik"
            ]
            },
            {
            "il": "Ordu",
            "plaka": 52,
            "ilceleri": [
                "Akkuş",
                "Altınordu",
                "Aybastı",
                "Fatsa",
                "Gölköy",
                "Korgan",
                "Kumru",
                "Mesudiye",
                "Perşembe",
                "Ulubey",
                "Ünye",
                "Gülyalı",
                "Gürgentepe",
                "Çamaş",
                "Çatalpınar",
                "Çaybaşı",
                "İkizce",
                "Kabadüz",
                "Kabataş"
            ]
            },
            {
            "il": "Osmaniye",
            "plaka": 80,
            "ilceleri": [
                "Bahçe",
                "Kadirli",
                "Merkez",
                "Düziçi",
                "Hasanbeyli",
                "Sumbas",
                "Toprakkale"
            ]
            },
            {
            "il": "Rize",
            "plaka": 53,
            "ilceleri": [
                "Ardeşen",
                "Çamlıhemşin",
                "Çayeli",
                "Fındıklı",
                "İkizdere",
                "Kalkandere",
                "Pazar",
                "Merkez",
                "Güneysu",
                "Derepazarı",
                "Hemşin",
                "İyidere"
            ]
            },
            {
            "il": "Sakarya",
            "plaka": 54,
            "ilceleri": [
                "Akyazı",
                "Geyve",
                "Hendek",
                "Karasu",
                "Kaynarca",
                "Sapanca",
                "Kocaali",
                "Pamukova",
                "Taraklı",
                "Ferizli",
                "Karapürçek",
                "Söğütlü",
                "Adapazarı",
                "Arifiye",
                "Erenler",
                "Serdivan"
            ]
            },
            {
            "il": "Samsun",
            "plaka": 55,
            "ilceleri": [
                "Alaçam",
                "Bafra",
                "Çarşamba",
                "Havza",
                "Kavak",
                "Ladik",
                "Terme",
                "Vezirköprü",
                "Asarcık",
                "Ondokuzmayıs",
                "Salıpazarı",
                "Tekkeköy",
                "Ayvacık",
                "Yakakent",
                "Atakum",
                "Canik",
                "İlkadım"
            ]
            },
            {
            "il": "Siirt",
            "plaka": 56,
            "ilceleri": [
                "Baykan",
                "Eruh",
                "Kurtalan",
                "Pervari",
                "Merkez",
                "Şirvan",
                "Tillo"
            ]
            },
            {
            "il": "Sinop",
            "plaka": 57,
            "ilceleri": [
                "Ayancık",
                "Boyabat",
                "Durağan",
                "Erfelek",
                "Gerze",
                "Merkez",
                "Türkeli",
                "Dikmen",
                "Saraydüzü"
            ]
            },
            {
            "il": "Sivas",
            "plaka": 58,
            "ilceleri": [
                "Divriği",
                "Gemerek",
                "Gürün",
                "Hafik",
                "İmranlı",
                "Kangal",
                "Koyulhisar",
                "Merkez",
                "Suşehri",
                "Şarkışla",
                "Yıldızeli",
                "Zara",
                "Akıncılar",
                "Altınyayla",
                "Doğanşar",
                "Gölova",
                "Ulaş"
            ]
            },
            {
            "il": "Şırnak",
            "plaka": 73,
            "ilceleri": [
                "Beytüşşebap",
                "Cizre",
                "İdil",
                "Silopi",
                "Merkez",
                "Uludere",
                "Güçlükonak"
            ]
            },
            {
            "il": "Tekirdağ",
            "plaka": 59,
            "ilceleri": [
                "Çerkezköy",
                "Çorlu",
                "Ergene",
                "Hayrabolu",
                "Malkara",
                "Muratlı",
                "Saray",
                "Süleymanpaşa",
                "Kapaklı",
                "Şarköy",
                "Marmaraereğlisi"
            ]
            },
            {
            "il": "Tokat",
            "plaka": 60,
            "ilceleri": [
                "Almus",
                "Artova",
                "Erbaa",
                "Niksar",
                "Reşadiye",
                "Merkez",
                "Turhal",
                "Zile",
                "Pazar",
                "Yeşilyurt",
                "Başçiftlik",
                "Sulusaray"
            ]
            },
            {
            "il": "Trabzon",
            "plaka": 61,
            "ilceleri": [
                "Akçaabat",
                "Araklı",
                "Arsin",
                "Çaykara",
                "Maçka",
                "Of",
                "Ortahisar",
                "Sürmene",
                "Tonya",
                "Vakfıkebir",
                "Yomra",
                "Beşikdüzü",
                "Şalpazarı",
                "Çarşıbaşı",
                "Dernekpazarı",
                "Düzköy",
                "Hayrat",
                "Köprübaşı"
            ]
            },
            {
            "il": "Tunceli",
            "plaka": 62,
            "ilceleri": [
                "Çemişgezek",
                "Hozat",
                "Mazgirt",
                "Nazımiye",
                "Ovacık",
                "Pertek",
                "Pülümür",
                "Merkez"
            ]
            },
            {
            "il": "Şanlıurfa",
            "plaka": 63,
            "ilceleri": [
                "Akçakale",
                "Birecik",
                "Bozova",
                "Ceylanpınar",
                "Eyyübiye",
                "Halfeti",
                "Haliliye",
                "Hilvan",
                "Karaköprü",
                "Siverek",
                "Suruç",
                "Viranşehir",
                "Harran"
            ]
            },
            {
            "il": "Uşak",
            "plaka": 64,
            "ilceleri": [
                "Banaz",
                "Eşme",
                "Karahallı",
                "Sivaslı",
                "Ulubey",
                "Merkez"
            ]
            },
            {
            "il": "Van",
            "plaka": 65,
            "ilceleri": [
                "Başkale",
                "Çatak",
                "Erciş",
                "Gevaş",
                "Gürpınar",
                "İpekyolu",
                "Muradiye",
                "Özalp",
                "Tuşba",
                "Bahçesaray",
                "Çaldıran",
                "Edremit",
                "Saray"
            ]
            },
            {
            "il": "Yalova",
            "plaka": 77,
            "ilceleri": [
                "Merkez",
                "Altınova",
                "Armutlu",
                "Çınarcık",
                "Çiftlikköy",
                "Termal"
            ]
            },
            {
            "il": "Yozgat",
            "plaka": 66,
            "ilceleri": [
                "Akdağmadeni",
                "Boğazlıyan",
                "Çayıralan",
                "Çekerek",
                "Sarıkaya",
                "Sorgun",
                "Şefaatli",
                "Yerköy",
                "Merkez",
                "Aydıncık",
                "Çandır",
                "Kadışehri",
                "Saraykent",
                "Yenifakılı"
            ]
            },
            {
            "il": "Zonguldak",
            "plaka": 67,
            "ilceleri": [
                "Çaycuma",
                "Devrek",
                "Ereğli",
                "Merkez",
                "Alaplı",
                "Gökçebey"
            ]
            }
        ];

        function search(nameKey, myArray){
            for (var i=0; i < myArray.length; i++) {
                if (myArray[i].il == nameKey) {
                    return myArray[i];
                }
            }
        }
        $( document ).ready(function() {
            $.each(data, function( index, value ) {
                $('#Iller').append($('<option>', {
                    value: value.il,
                    text:  value.il
                }));
            });
            $("#Iller").change(function(){
                var valueSelected = this.value;
                if($('#Iller').val() != 0) {
                    $('#Ilceler').html('');
                    $('#Ilceler').append($('<option>', {
                    value: 0,
                    text:  'Lütfen Bir İlçe seçiniz'
                    }));
                    $('#Ilceler').prop("disabled", false);
                    var resultObject = search($('#Iller').val(), data);
                    $.each(resultObject.ilceleri, function( index, value ) {
                    $('#Ilceler').append($('<option>', {
                        value: value,
                        text:  value
                    }));
                    });
                    return false;
                }
                $('#Ilceler').prop("disabled", true);
            });
            // Müşteri listesinde il bazlı filtreleme
            $.each(data, function( index, value ) {
                $('#IlFiltre').append($('<option>', {
                    value: value.il,
                    text:  value.il
                }));
            });
            $("#IlFiltre").change(function(){
                var url = $(this).val(); // get selected value
                if (url != 0) { // require a URL
                    window.location = "ziyaretler.php?il=" + url; // redirect
                }else{
                    window.location = "ziyaretler.php"; // redirect
                }
                return false;
            });
            $.each(data, function( index, value ) {
                $('#mobilIlFiltre').append($('<option>', {
                    value: value.il,
                    text:  value.il
                }));
            });
            $("#mobilIlFiltre").change(function(){
                var url = $(this).val(); // get selected value
                if (url != 0) { // require a URL
                    window.location = "ziyaretler.php?il=" + url; // redirect
                }else{
                    window.location = "ziyaretler.php"; // redirect
                }
                return false;
            });
        });
        
    </script>

</body>
</html>