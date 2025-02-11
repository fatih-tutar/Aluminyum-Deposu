<?php 

include 'fonksiyonlar/bagla.php'; 

if($girdi == '0'){

    header("Location:index.php");

    exit();

}else{

    if($uye_tipi == '0'){

        header("Location:index.php");

        exit();

    }else{

    if($uye_tipi != '3'){

        if (isset($_POST['kalibiekle'])) {
            
            $musteriadi = guvenlik($_POST['musteriadi']);

            $kalipnumarasi = guvenlik($_POST['kalipnumarasi']);

            $fabrikaid = guvenlik($_POST['fabrikaid']);

            $allow = array('pdf');

            $temp = explode(".", $_FILES['pdf_file']['name']);

            $dosyaadi = $temp[0];

            $extension = end($temp);

            $randomsayi = rand(0,10000);;

            $upload_file = $dosyaadi.$randomsayi.".".$extension;

            move_uploaded_file($_FILES['pdf_file']['tmp_name'], "img/pdf/".$upload_file);

            $query = $db->prepare("INSERT INTO kaliplar SET musteriadi = ?, kalipnumarasi = ?, fabrikaid = ?, pdf = ?, sirketid = ?, silik = ?");

            $insert = $query->execute(array($musteriadi,$kalipnumarasi,$fabrikaid,$upload_file,$uye_sirket,'0'));

            header("Location:kaliplar.php");

            exit();

        }

        if (isset($_POST['bilgileriguncelle'])) {

            $kalipid = guvenlik($_POST['kalipid']);
            
            $musteriadi = guvenlik($_POST['musteriadi']);

            $kalipnumarasi = guvenlik($_POST['kalipnumarasi']);

            $fabrikaid = guvenlik($_POST['fabrikaid']);

            $query = $db->prepare("UPDATE kaliplar SET musteriadi = ?, kalipnumarasi = ?, fabrikaid = ? WHERE kalipid = ?"); 

            $guncelle = $query->execute(array($musteriadi,$kalipnumarasi,$fabrikaid,$kalipid));

            header("Location:kaliplar.php");

            exit();

        }

        if (isset($_POST['kalipsil'])) {
            
            $kalipid = guvenlik($_POST['kalipid']);

            $query = $db->prepare("UPDATE kaliplar SET silik = ? WHERE kalipid = ?"); 

            $guncelle = $query->execute(array('1',$kalipid));

            header("Location:kaliplar.php");

            exit();

        }

    }}

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

        <div class="row">
            
            <div class="col-md-3 col-12">
                
                <div class="div4" style="padding-top: 20px; text-align: center;">

                    <a href="#" onclick="return false" onmousedown="javascript:ackapa('formdivi');"><h5><i class="fas fa-angle-double-down"></i>&nbsp;&nbsp;&nbsp;&nbsp;<b>Kalıp Ekleme Formu</b>&nbsp;&nbsp;&nbsp;&nbsp;<i class="fas fa-angle-double-down"></i></h5></a>

                    <div id="formdivi" style="display: none;">
                    
                        <form action="" method="POST" enctype="multipart/form-data">
                                
                            <div class="search-box">

                                <input name="musteriadi" id="firmainputu" type="text" class="form-control" style="margin-bottom: 10px;" autocomplete="off" placeholder="Firma Adı"/>
                            
                                <ul class="list-group liveresult" id="firmasonuc" style="position: fixed; z-index: 1;"></ul>

                            </div>

                            <div><input type="text" name="kalipnumarasi" placeholder="Kalıp numarasını giriniz." class="form-control" style="margin-bottom: 10px;"></div>

                            <div>
                                
                                <select class="form-control" style="margin-bottom: 10px;" id="exampleFormControlSelect1" name="fabrikaid">

                                    <option selected value="0">Fabrika Seçiniz</option>

                                    <?php

                                    $fabrika = $db->query("SELECT * FROM fabrikalar WHERE sirketid = '{$uye_sirket}' ORDER BY fabrika_adi ASC", PDO::FETCH_ASSOC);

                                    if ( $fabrika->rowCount() ){

                                        foreach( $fabrika as $fbrk ){

                                            $fabrika_id = $fbrk['fabrika_id'];

                                            $fabrika_adi = $fbrk['fabrika_adi'];

                                            echo "<option value='".$fabrika_id."'>".$fabrika_adi."</option>";

                                        }

                                    }

                                    ?>
                               
                                </select>       

                            </div>

                            <div><input type="file" name="pdf_file" style="margin-bottom: 10px;"></div>

                            <div><button type="submit" name="kalibiekle" class="btn btn-info btn-block">Kalıbı Ekle</button></div>
                           
                        </form>

                    </div>

                </div>

            </div>

        </div>

        <div class="div4">
            
            <h5><b>Kalıplar</b></h5>

            <div class="d-none d-sm-block">

                <hr/>

                <div class="row">
                    
                    <div class="col-2"><b>Müşteri Adı</b></div>

                    <div class="col-2"><b>Kalıp Numarası</b></div>

                    <div class="col-2"><b>Fabrika</b></div>

                    <div class="col-2"><b>Pdf Dosyası</b></div>

                </div>

            </div>

            <?php

                $kaliplaricek = $db->query("SELECT * FROM kaliplar WHERE sirketid = '{$uye_sirket}' AND silik = '0' ORDER BY musteriadi ASC", PDO::FETCH_ASSOC);

                if ( $kaliplaricek->rowCount() ){

                    foreach( $kaliplaricek as $row ){

                        $kalipid = $row['kalipid'];

                        $musteriadi = $row['musteriadi'];

                        $kalipnumarasi = $row['kalipnumarasi'];

                        $fabrikaid = $row['fabrikaid'];

                        $fabrikadicek = $db->query("SELECT * FROM fabrikalar WHERE fabrika_id = '{$fabrikaid}' AND sirketid = '{$uye_sirket}'")->fetch(PDO::FETCH_ASSOC);

                        $fabrikaadi = $fabrikadicek['fabrika_adi'];

                        $pdf = $row['pdf'];

            ?>          

                        <hr/>

                        <div class="row">

                            <div class="col-4 d-block d-sm-none" style="margin-top: 5px;"><b style="color: red;">Müşteri</b></div>
                
                            <div class="col-md-2 col-8" style="margin-top: 5px;"><?= $musteriadi; ?></div>

                            <div class="col-4 d-block d-sm-none" style="margin-top: 5px;"><b style="color: red;">Kalıp No</b></div>

                            <div class="col-md-2 col-8" style="margin-top: 5px;"><?= $kalipnumarasi; ?></div>

                            <div class="col-4 d-block d-sm-none" style="margin-top: 5px;"><b style="color: red;">Fabrika</b></div>

                            <div class="col-md-2 col-8" style="margin-top: 5px;"><?= $fabrikaadi; ?></div>

                            <div class="col-4 d-block d-sm-none" style="margin-top: 5px;"><b style="color: red;">Pdf</b></div>

                            <div class="col-md-2 col-8" style="margin-top: 5px;"><a href="img/pdf/<?= $pdf; ?>" target="_blank"><?= $pdf; ?></a></div>

                            <div class="col-4 d-block d-sm-none" style="margin-top: 5px;"><b style="color: red;">Önizleme</b></div>

                            <div class="col-md-2 col-8" style="margin-top: 5px;"><a href="#" onclick="return false" onmousedown="javascript:ackapa('pdfdivi<?= $kalipid; ?>');">Pdf'i Önizleme</a></div>

                            <div class="col-md-1 col-6" style="margin-top: 5px;">

                                <a href="#" onclick="return false" onmousedown="javascript:ackapa('duzenlemedivi<?= $kalipid; ?>');"><button class="btn btn-primary btn-sm btn-block">Düzenle</button></a>
                                
                            </div>

                            <div class="col-md-1 col-6" style="margin-top: 5px;">

                                <a href="#" onclick="return false" onmousedown="javascript:ackapa('silmedivi<?= $kalipid; ?>');"><button class="btn btn-danger btn-sm btn-block">Sil</button></a>
                                
                            </div>

                        </div>

                        <div style="position: fixed; z-index: 1; top:100px; left: 100px; display: none;" id="pdfdivi<?= $kalipid; ?>">

                            <div style="text-align: right;">
                                
                                <a href="#" onclick="return false" onmousedown="javascript:ackapa('pdfdivi<?= $kalipid; ?>');">

                                   <span style="font-size: 3rem;">
                                      <span style="color: red; background-color: white;">
                                      <i class="fas fa-window-close"></i>
                                      </span>
                                    </span>

                                </a>

                            </div>

                            <object width="700" height="500" type="application/pdf" data="img/pdf/<?= $pdf; ?>" id="pdf_content">
                            <p>Pdf dokümanı yüklenemediğinde verilecek hata mesajı...</p>
                            </object>

                        </div>

                        <div id="silmedivi<?= $kalipid; ?>" style="display: none;">

                            <div class="row" style="margin-top: 10px;">
                                
                                <div class="col-md-10 col-12" style="text-align: right;"><b style="font-size: 20px;">Silmek istediğinize emin misiniz?</b></div>

                                 <div class="col-md-1 col-6" style="padding-top: 5px;">
                                
                                    <form action="" method="POST">

                                        <input type="hidden" name="kalipid" value="<?= $kalipid; ?>">
                                    
                                        <button type="submit" name="kalipsil" class="btn btn-success btn-sm btn-block">Evet</button>

                                    </form>

                                </div>    

                                <div class="col-md-1 col-6" style="padding-top: 5px;">
                                    
                                    <a href="#" onclick="return false" onmousedown="javascript:ackapa('silmedivi<?= $kalipid; ?>');"><button class="btn btn-danger btn-sm btn-block">Hayır</button></a>

                                </div>  

                            </div>

                        </div>

                        <div id="duzenlemedivi<?= $kalipid; ?>" style="display: none;">
                            
                            <form action="" method="POST">

                                <input type="hidden" name="kalipid" value="<?= $kalipid; ?>">

                                <div class="row">
                                
                                    <div class="col-md-2 col-12" style="margin-top: 5px;"><input type="text" class="form-control" name="musteriadi" value="<?= $musteriadi; ?>"></div>

                                    <div class="col-md-2 col-12" style="margin-top: 5px;"><input type="text" class="form-control" name="kalipnumarasi" value="<?= $kalipnumarasi; ?>"></div>

                                    <div class="col-md-2 col-12" style="margin-top: 5px;">

                                        <select class="form-control" id="exampleFormControlSelect1" name="fabrikaid">

                                            <option selected value="0">Fabrika Seçiniz</option>

                                            <?php

                                            $fabrika = $db->query("SELECT * FROM fabrikalar WHERE sirketid = '{$uye_sirket}' ORDER BY fabrika_adi ASC", PDO::FETCH_ASSOC);

                                            if ( $fabrika->rowCount() ){

                                                foreach( $fabrika as $fbrk ){

                                                    $fabrika_id = $fbrk['fabrika_id'];

                                                    $fabrika_adi = $fbrk['fabrika_adi'];

                                                    if ($fabrika_id == $fabrikaid) {

                                                        echo "<option selected value='".$fabrika_id."'>".$fabrika_adi."</option>";
                                                        
                                                    }else{

                                                        echo "<option value='".$fabrika_id."'>".$fabrika_adi."</option>";

                                                    }                                                    

                                                }

                                            }

                                            ?>
                                       
                                        </select> 

                                    </div>

                                    <div class="col-md-2 col-12" style="margin-top: 5px;"><button type="submit" class="btn btn-warning btn-sm" name="bilgileriguncelle">Bilgileri Güncelle</button></div>

                                </div>
                           
                            </form>

                        </div>

            <?php

                    }

                }

            ?>

        </div>

    </div>

    <?php include 'template/script.php'; ?>

</body>
</html>