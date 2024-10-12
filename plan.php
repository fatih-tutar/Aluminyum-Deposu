<?php 

	include 'fonksiyonlar/bagla.php'; 

	if ($girdi != '1') {
		
		header("Location:giris.php");

		exit();

	}elseif($uye_tipi == '0' || $uye_tipi == '1'){

		header("Location:index.php");

		exit();

	}else{

        $gecenAy = date('Y-m', strtotime('-1 month'));

        $tekrarCek = $db->query("SELECT * FROM plan WHERE plan_silik = '0' AND plan_tekrar = '1' ORDER BY plan_tarihi DESC", PDO::FETCH_ASSOC);

        if ( $tekrarCek->rowCount() ){
        
            foreach( $tekrarCek as $ptc ){

                $plan_id = guvenlik($ptc['plan_id']);

                $plan = guvenlik($ptc['plan']);

                $plan_tarihi = guvenlik($ptc['plan_tarihi']);

                $planAy = date("Y-m", $plan_tarihi);

                $plan_tarih = date("Y-m-d",$plan_tarihi);

                $plan_tarih_array = explode('-',$plan_tarih);

                if($plan_tarih_array[1] !== '12'){ $plan_tarih_array[1]++; }else{ $plan_tarih_array[0]++; $plan_tarih_array[1] = 1; }

                $plan_tarih = implode("-",$plan_tarih_array);

                $plan_tarihi = strtotime($plan_tarih);

                $plan_tekrar = guvenlik($ptc['plan_tekrar']);

                $plan_durum = guvenlik($ptc['plan_durum']);

                if($gecenAy == $planAy){

                    $ayliklariGuncelle = $db->prepare("INSERT INTO plan SET plan = ?, plan_tarihi = ?, plan_tekrar = ?, plan_saniye = ?, plan_durum = ?, plan_silik = ?");

                    $aylikGuncelle = $ayliklariGuncelle->execute(array($plan,$plan_tarihi,$plan_tekrar,$su_an,'0','0'));

                    $plantekrarsil = $db->prepare("UPDATE plan SET plan_tekrar = ? WHERE plan_id = ?"); 

                    $tekrarsil = $plantekrarsil->execute(array('0',$plan_id));

                }
        
            }

        }

        if(isset($_POST['plan_ekle'])){

            $plan = guvenlik($_POST['plan']);

            $plan_tarihi = guvenlik($_POST['plan_tarihi']);

			$plan_tarihi = strtotime($plan_tarihi);

            if(isset($_POST['plan_tekrar'])){ $plan_tekrar = '1'; }else{ $plan_tekrar = '0'; }

            $query = $db->prepare("INSERT INTO plan SET plan = ?, plan_tarihi = ?, plan_tekrar = ?, plan_saniye = ?, plan_durum = ?, plan_silik = ?");

            $insert = $query->execute(array($plan,$plan_tarihi,$plan_tekrar,$su_an,'0','0'));

            header("Location: plan.php");

            exit();

        }

        if(isset($_POST['plan_duzenle'])){

            $plan_id = guvenlik($_POST['plan_id']);

            $plan = guvenlik($_POST['plan']);

            $plan_tarihi = guvenlik($_POST['plan_tarihi']);

			$plan_tarihi = strtotime($plan_tarihi);

            $plan_tekrar = guvenlik($_POST['plan_tekrar']);
            
            $plan_durum = guvenlik($_POST['plan_durum']);

            $query = $db->prepare("UPDATE plan SET plan = ?, plan_tarihi = ?, plan_tekrar = ?, plan_durum = ? WHERE plan_id = ?"); 

            $guncelle = $query->execute(array($plan,$plan_tarihi,$plan_tekrar,$plan_durum,$plan_id));

            header("Location: plan.php");

            exit();

        }

        if(isset($_POST['plan_sil'])){

            $plan_id = guvenlik($_POST['plan_id']);

            $query = $db->prepare("UPDATE plan SET plan_silik = ? WHERE plan_id = ?"); 

            $guncelle = $query->execute(array('1',$plan_id));

            header("Location: plan.php");

            exit();

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

        <div class="row">
                    
            <div class="col-md-12 col-12">
                
                <div class="div4" style="padding-top: 20px; text-align: center;">

                    <a href="#" onclick="return false" onmousedown="javascript:ackapa('formdivi');"><h5><i class="fas fa-angle-double-down"></i>&nbsp;&nbsp;&nbsp;&nbsp;<b>Görev Ekle</b>&nbsp;&nbsp;&nbsp;&nbsp;<i class="fas fa-angle-double-down"></i></h5></a>

                    <div id="formdivi">
                    
                        <form action="" method="POST">

                            <div class="row">

                                <div class="col-md-5 col-12"><input type="text" name="plan" class="form-control" placeholder="İş planına eklenecek görev"></div>
                                <div class="col-md-2 col-12"><input type="text" id="tarih1" name="plan_tarihi" placeholder="Tarih seçiniz" class="form-control"></div>
                                <div class="col-md-3 col-12"><input type="checkbox" class="form-check-input" id="checkboxaylik" name="plan_tekrar"><label class="form-check-label" for="checkboxaylik" style="font-size:12px;">Aylık olarak hatırlatılmasını istiyorsanız işaretleyiniz.</label></div>
                                <div class="col-md-2 col-12"><button type="submit" class="btn btn-primary btn-block" name="plan_ekle">Kaydet</button></div>

                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>

        <div class="div4">

            <div class="row">

                <div class="col-md-2">
                
                    <div class="row">
                        <div class="col-md-3"><button class="btn btn-primary" style="font-size:1.3rem;">No</button></div>
                        <div class="col-md-9"><button class="btn btn-primary" style="font-size:1.3rem;">Tarih</button></div>
                    </div>

                </div>

                <div class="col-md-4"><button class="btn btn-primary" style="font-size:1.3rem;"><b>Görev Tanımı</b></button></div>

                <div class="col-md-3">

                    <div class="row">
                        <div class="col-md-6">
                            <button class="btn btn-primary" style="font-size:1.3rem;"><b>Tekrariyet</b></button>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-primary" style="font-size:1.3rem;"><b>Durum</b></button>
                        </div>
                    </div>
                </div>

            </div>

            <hr/>

            <?php

            $i = 0;
            
            $plan_cek = $db->query("SELECT * FROM plan WHERE plan_silik = '0' AND plan_durum = '0' ORDER BY plan_tarihi ASC", PDO::FETCH_ASSOC);

            if ( $plan_cek->rowCount() ){
            
                foreach( $plan_cek as $plancek ){

                    $i++;
            
                    $plan_id = guvenlik($plancek['plan_id']);

                    $plan = guvenlik($plancek['plan']);

                    $plan_tarihi = guvenlik($plancek['plan_tarihi']);

                    $gecmis = 0;

                    if($plan_tarihi < $su_an){
                        $gecmis = 1;
                    }

                    $plan_tarihi = date("d-m-Y",$plan_tarihi);

                    $plan_tekrar = guvenlik($plancek['plan_tekrar']);

                    $plan_durum = guvenlik($plancek['plan_durum']);

            ?>

                    <form action="" method="POST">

                    <?php if($plan_durum == '1'){ ?>

                        <div class="row mb-1" style="background-color:#36AE7C; padding:5px 10px 10px; margin:-10px;">

                    <?php }elseif($gecmis == 1){ ?>
                        
                        <div class="row mb-1" style="background-color:#EB5353; padding:5px 10px 10px; margin:-10px;">

                    <?php }else{ ?>
                        
                        <div class="row mb-1" style="background-color:#F9D923; padding:5px 10px 10px; margin:-10px;">

                    <?php } ?>

                            <div class="col-md-2">

                                <div class="row">
                                    
                                    <div class="col-md-3"><?php echo $i; ?><input type="hidden" name="plan_id" value="<?php echo $plan_id; ?>"></div>
                                    
                                    <div class="col-md-9"><input type="text" id="tarih<?php echo $plan_id; ?>" name="plan_tarihi" value="<?php echo $plan_tarihi; ?>" class="form-control form-control-sm" style="border-style:none; font-size:1.1rem;"></div>
                                
                                </div>

                            </div>

                            <div class="col-md-4"><input type="text" name="plan" class="form-control form-control-sm" placeholder="İş planına eklenecek görev" value="<?php echo $plan; ?>" style="border-style:none; font-size:1.1rem;"></div>

                            <div class="col-md-3">

                                <div class="row">
                                    <div class="col-md-6">
                                        <select name="plan_tekrar" id="plan_tekrar" class="form-control form-control-sm" style="border-style:none; font-size:1.1rem;">

                                        <?php if($plan_tekrar == '0'){ ?>

                                            <option value="0" selected>Tekrarsız</option>
                                            <option value="1">Aylık Tekrarlı</option>
                                            
                                        <?php }else{ ?>

                                            <option value="0">Tekrarsız</option>
                                            <option value="1" selected>Aylık Tekrarlı</option>
                                            
                                        <?php } ?>

                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <select name="plan_durum" id="plan_durum" class="form-control form-control-sm" style="border-style:none; font-size:1.1rem;">

                                        <?php if($plan_durum == '0'){ ?>

                                            <option value="0" selected>Sırada</option>
                                            <option value="1">Tamamlandı</option>
                                            
                                        <?php }else{ ?>

                                            <option value="0">Sırada</option>
                                            <option value="1" selected>Tamamlandı</option>
                                            
                                        <?php } ?>

                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-1"><button type="submit" class="btn btn-primary btn-block btn-sm" name="plan_duzenle" >Düzenle</button></div>

                            <div class="col-md-2">

                                <div id="sildivi<?php echo $plan_id; ?>">

                                    <a href="#" onclick="return false" onmousedown="javascript:ackapa2('silmeonaydivi<?php echo $plan_id; ?>','sildivi<?php echo $plan_id; ?>');">
                                
                                        <button class="btn btn-danger btn-sm" style="width:100px;">Sil</button>
                                    
                                    </a>

                                </div>

                                <div id="silmeonaydivi<?php echo $plan_id; ?>" style="display:none;">
                            
                                    <div class="row">

                                        <div class="col-md-6">

                                            <button type="submit" name="plan_sil" class="btn btn-success btn-sm btn-block">Evet</button>

                                        </div>
                                        
                                        <div class="col-md-6">

                                            <a href="#" onclick="return false" onmousedown="javascript:ackapa2('sildivi<?php echo $plan_id; ?>','silmeonaydivi<?php echo $plan_id; ?>');">
                                    
                                                <button class="btn btn-danger btn-block btn-sm">Hayır</button>
                                            
                                            </a>

                                        </div>

                                    </div>

                                </div>
                            
                            </div>

                        </div>

                    </form>

            <?php
            
                }
            
            }
            
            ?>

            <hr/><h3 style="text-align:center; margin-bottom:1rem;">Tamamlanan İşler</h3>

            <?php

            $i = 0;
            
            $plan_cek = $db->query("SELECT * FROM plan WHERE plan_silik = '0' AND plan_durum = '1' ORDER BY plan_tarihi ASC", PDO::FETCH_ASSOC);

            if ( $plan_cek->rowCount() ){
            
                foreach( $plan_cek as $plancek ){

                    $i++;
            
                    $plan_id = guvenlik($plancek['plan_id']);

                    $plan = guvenlik($plancek['plan']);

                    $plan_tarihi = guvenlik($plancek['plan_tarihi']);

                    $gecmis = 0;

                    if($plan_tarihi < $su_an){ $gecmis = 1; }

                    $plan_tarihi = date("d-m-Y",$plan_tarihi);

                    $plan_tekrar = guvenlik($plancek['plan_tekrar']);

                    $plan_durum = guvenlik($plancek['plan_durum']);

            ?>

                    <form action="" method="POST">

                    <?php if($plan_durum == '1'){ ?>

                        <div class="row mb-1" style="background-color:#36AE7C; padding:5px 10px 10px; margin:-10px;">

                    <?php }elseif($gecmis == 1){ ?>
                        
                        <div class="row mb-1" style="background-color:#EB5353; padding:5px 10px 10px; margin:-10px;">

                    <?php }else{ ?>
                        
                        <div class="row mb-1" style="background-color:#F9D923; padding:5px 10px 10px; margin:-10px;">

                    <?php } ?>

                            <div class="col-md-2">

                                <div class="row">
                                    
                                    <div class="col-md-3"><?php echo $i; ?><input type="hidden" name="plan_id" value="<?php echo $plan_id; ?>"></div>
                                    
                                    <div class="col-md-9"><input type="text" id="tarih<?php echo $plan_id; ?>" name="plan_tarihi" value="<?php echo $plan_tarihi; ?>" class="form-control form-control-sm" style="border-style:none; font-size:1.1rem;"></div>
                                
                                </div>

                            </div>

                            <div class="col-md-4"><input type="text" name="plan" class="form-control form-control-sm" placeholder="İş planına eklenecek görev" value="<?php echo $plan; ?>" style="border-style:none; font-size:1.1rem;"></div>

                            <div class="col-md-3">

                                <div class="row">
                                    <div class="col-md-6">
                                        <select name="plan_tekrar" id="plan_tekrar" class="form-control form-control-sm" style="border-style:none; font-size:1.1rem;">

                                        <?php if($plan_tekrar == '0'){ ?>

                                            <option value="0" selected>Tekrarsız</option>
                                            <option value="1">Aylık Tekrarlı</option>
                                            
                                        <?php }else{ ?>

                                            <option value="0">Tekrarsız</option>
                                            <option value="1" selected>Aylık Tekrarlı</option>
                                            
                                        <?php } ?>

                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <select name="plan_durum" id="plan_durum" class="form-control form-control-sm" style="border-style:none; font-size:1.1rem;">

                                        <?php if($plan_durum == '0'){ ?>

                                            <option value="0" selected>Sırada</option>
                                            <option value="1">Tamamlandı</option>
                                            
                                        <?php }else{ ?>

                                            <option value="0">Sırada</option>
                                            <option value="1" selected>Tamamlandı</option>
                                            
                                        <?php } ?>

                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-1"><button type="submit" class="btn btn-primary btn-block btn-sm" name="plan_duzenle" >Düzenle</button></div>

                            <div class="col-md-2">

                                <div id="sildivi<?php echo $plan_id; ?>">

                                    <a href="#" onclick="return false" onmousedown="javascript:ackapa2('silmeonaydivi<?php echo $plan_id; ?>','sildivi<?php echo $plan_id; ?>');">
                                
                                        <button class="btn btn-danger btn-sm" style="width:100px;">Sil</button>
                                    
                                    </a>

                                </div>

                                <div id="silmeonaydivi<?php echo $plan_id; ?>" style="display:none;">
                            
                                    <div class="row">

                                        <div class="col-md-6">

                                            <button type="submit" name="plan_sil" class="btn btn-success btn-sm btn-block">Evet</button>

                                        </div>
                                        
                                        <div class="col-md-6">

                                            <a href="#" onclick="return false" onmousedown="javascript:ackapa2('sildivi<?php echo $plan_id; ?>','silmeonaydivi<?php echo $plan_id; ?>');">
                                    
                                                <button class="btn btn-danger btn-block btn-sm">Hayır</button>
                                            
                                            </a>

                                        </div>

                                    </div>

                                </div>
                            
                            </div>

                        </div>

                    </form>

            <?php
            
                }
            
            }
            
            ?>

        </div>

    </div>

    <?php include 'template/script.php'; ?>

</body>
</html>