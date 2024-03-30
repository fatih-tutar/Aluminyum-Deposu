<div id="agirlikhesaplamadivi" class="div4 col-md-4 col-12 mt-2 mb-4" style="display:none;">
						
    <h5 style="text-align: center;"><b>Ağırlık Hesaplama</b></h5>

    <select name="malzemetipi" id="selectkutuID" class="form-control" onchange="degergoster();" style="margin-bottom: 5px;">

        <option value="0">Malzeme Tipini Seçiniz</option>
        
        <option value="1">Alüminyum Levha</option>

        <option value="2">Alüminyum Köşebent</option>

        <option value="3">Alüminyum Çubuk</option>

        <option value="4">Alüminyum Kutu</option>

        <option value="5">Alüminyum Boru</option>

    </select>

    <?php if(isset($mt) && $mt == '1'){ ?><div id="malzeme1"><?php }else{ ?><div id="malzeme1" style="display: none;"><?php } ?>
        
        <hr/>
        
        <h6 style="margin-top: 5px; text-align: center;"><b>Alüminyum Levha Bilgilerini Doldurunuz</b></h6>

        <form action="" method="POST">

            <input type="hidden" name="malzemetipi" value="1">

            <div class="row"><div class="col-3"><b>Kalıklık</b></div><div class="col-9"><?php if(isset($_GET['k'])){ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="kalinlik" value="<?php echo $kalinlik ?>"><?php }else{ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="kalinlik" placeholder="KALINLIK"><?php } ?></div></div>

            <div class="row"><div class="col-3"><b>En</b></div><div class="col-9"><?php if(isset($_GET['e'])){ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="en" value="<?php echo $en; ?>"><?php }else{ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="en" placeholder="EN"><?php } ?></div></div>

            <div class="row"><div class="col-3"><b>Boy</b></div><div class="col-9"><?php if(isset($_GET['b'])){ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="boy" value="<?php echo $boy; ?>"><?php }else{ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="boy" placeholder="BOY"><?php } ?></div></div>

            <div class="row"><div class="col-3"><b>Adet</b></div><div class="col-9"><?php if(isset($_GET['a'])){ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="adet" value="<?php echo $adet; ?>"><?php }else{ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="adet" placeholder="ADET"><?php } ?></div></div>
            
            <button type="submit" class="btn btn-primary btn-block btn-sm" style="margin-bottom: 5px; background-color: black;" name="levhahesapla">Hesapla</button>

        </form>

        <?php if(isset($toplam1)){?><h5 style="text-align: center; margin-top: 20px;"><b>Sonuç : <ins><?php echo $toplam1." KG"; ?></ins></b></h5><?php } ?>

    </div>

    <?php if(isset($mt) && $mt == '2'){ ?><div id="malzeme2"><?php }else{ ?><div id="malzeme2" style="display: none;"><?php } ?>
        
        <hr/>
        
        <h6 style="margin-top: 5px; text-align: center;"><b>Alüminyum Köşebent Bilgilerini Doldurunuz</b></h6>

        <form action="" method="POST">

            <input type="hidden" name="malzemetipi" value="2">

            <div class="row"><div class="col-4"><b>A (mm)</b></div><div class="col-8"><?php if(isset($_GET['a'])){ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="a" value="<?php echo $amm ?>"><?php }else{ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="a" placeholder="A (mm)"><?php } ?></div></div>

            <div class="row"><div class="col-4"><b>B (mm)</b></div><div class="col-8"><?php if(isset($_GET['b'])){ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="b" value="<?php echo $bmm; ?>"><?php }else{ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="b" placeholder="B (mm)"><?php } ?></div></div>

            <div class="row"><div class="col-4"><b>Et Kalınlığı</b></div><div class="col-8"><?php if(isset($_GET['e'])){ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="etkalinligi" value="<?php echo $etkalinligi; ?>"><?php }else{ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="etkalinligi" placeholder="ET KALINLIĞI"><?php } ?></div></div>

            <div class="row"><div class="col-4"><b>Boy</b></div><div class="col-8"><?php if(isset($_GET['boy'])){ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="boy" value="<?php echo $boy; ?>"><?php }else{ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="boy" placeholder="BOY"><?php } ?></div></div>

            <div class="row"><div class="col-4"><b>Adet</b></div><div class="col-8"><?php if(isset($_GET['adet'])){ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="adet" value="<?php echo $adet; ?>"><?php }else{ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="adet" placeholder="ADET"><?php } ?></div></div>
            
            <button type="submit" class="btn btn-primary btn-block btn-sm" style="margin-bottom: 5px;" name="kosebenthesapla">Hesapla</button>

        </form>

        <?php if(isset($toplam2)){?><h5 style="text-align: center; margin-top: 20px;"><b>Sonuç : <ins><?php echo $toplam2." KG"; ?></ins></b></h5><?php } ?>

    </div>

    <?php if(isset($mt) && $mt == '3'){ ?><div id="malzeme3"><?php }else{ ?><div id="malzeme3" style="display: none;"><?php } ?>
        
        <hr/>
        
        <h6 style="margin-top: 5px; text-align: center;"><b>Alüminyum Çubuk Bilgilerini Doldurunuz</b></h6>

        <form action="" method="POST">

            <input type="hidden" name="malzemetipi" value="3">

            <div class="row"><div class="col-4"><b>Çap</b></div><div class="col-8"><?php if(isset($_GET['c'])){ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="cap" value="<?php echo $cap ?>"><?php }else{ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="cap" placeholder="ÇAP"><?php } ?></div></div>

            <div class="row"><div class="col-4"><b>Uzunluk</b></div><div class="col-8"><?php if(isset($_GET['u'])){ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="uzunluk" value="<?php echo $uzunluk; ?>"><?php }else{ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="uzunluk" placeholder="UZUNLUK"><?php } ?></div></div>

            <div class="row"><div class="col-4"><b>Adet</b></div><div class="col-8"><?php if(isset($_GET['a'])){ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="adet" value="<?php echo $adet; ?>"><?php }else{ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="adet" placeholder="ADET"><?php } ?></div></div>
            
            <button type="submit" class="btn btn-primary btn-block btn-sm" style="margin-bottom: 5px;" name="cubukhesapla">Hesapla</button>

        </form>

        <?php if(isset($toplam3)){?><h5 style="text-align: center; margin-top: 20px;"><b>Sonuç : <ins><?php echo $toplam3." KG"; ?></ins></b></h5><?php } ?>

    </div>

    <?php if(isset($mt) && $mt == '4'){ ?><div id="malzeme4"><?php }else{ ?><div id="malzeme4" style="display: none;"><?php } ?>
        
        <hr/>
        
        <h6 style="margin-top: 5px; text-align: center;"><b>Alüminyum Kutu Bilgilerini Doldurunuz</b></h6>

        <form action="" method="POST">

            <input type="hidden" name="malzemetipi" value="4">

            <div class="row"><div class="col-4"><b>A (mm)</b></div><div class="col-8"><?php if(isset($_GET['a'])){ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="a" value="<?php echo $amm ?>"><?php }else{ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="a" placeholder="A (mm)"><?php } ?></div></div>

            <div class="row"><div class="col-4"><b>B (mm)</b></div><div class="col-8"><?php if(isset($_GET['b'])){ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="b" value="<?php echo $bmm; ?>"><?php }else{ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="b" placeholder="B (mm)"><?php } ?></div></div>

            <div class="row"><div class="col-4"><b>Et Kalınlığı</b></div><div class="col-8"><?php if(isset($_GET['e'])){ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="etkalinligi" value="<?php echo $etkalinligi; ?>"><?php }else{ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="etkalinligi" placeholder="ET KALINLIĞI"><?php } ?></div></div>

            <div class="row"><div class="col-4"><b>Boy</b></div><div class="col-8"><?php if(isset($_GET['boy'])){ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="boy" value="<?php echo $boy; ?>"><?php }else{ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="boy" placeholder="BOY"><?php } ?></div></div>

            <div class="row"><div class="col-4"><b>Adet</b></div><div class="col-8"><?php if(isset($_GET['adet'])){ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="adet" value="<?php echo $adet; ?>"><?php }else{ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="adet" placeholder="ADET"><?php } ?></div></div>
            
            <button type="submit" class="btn btn-primary btn-block btn-sm" style="margin-bottom: 5px;" name="kutuhesapla">Hesapla</button>

        </form>

        <?php if(isset($toplam4)){?><h5 style="text-align: center; margin-top: 20px;"><b>Sonuç : <ins><?php echo $toplam4." KG"; ?></ins></b></h5><?php } ?>

    </div>

    <?php if(isset($mt) && $mt == '5'){ ?><div id="malzeme5"><?php }else{ ?><div id="malzeme5" style="display: none;"><?php } ?>
        
        <hr/>
        
        <h6 style="margin-top: 5px; text-align: center;"><b>Alüminyum Boru Bilgilerini Doldurunuz</b></h6>

        <form action="" method="POST">

            <input type="hidden" name="malzemetipi" value="5">

            <div class="row"><div class="col-4"><b>İç Çap</b></div><div class="col-8"><?php if(isset($_GET['iccap'])){ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="iccap" value="<?php echo $iccap ?>"><?php }else{ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="iccap" placeholder="İç Çap"><?php } ?></div></div>

            <div class="row"><div class="col-4"><b>Dış Çap</b></div><div class="col-8"><?php if(isset($_GET['discap'])){ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="discap" value="<?php echo $discap; ?>"><?php }else{ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="discap" placeholder="Dış Çap"><?php } ?></div></div>

            <div class="row"><div class="col-4"><b>Et Kalınlığı</b></div><div class="col-8"><?php if(isset($_GET['e'])){ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="etkalinligi" value="<?php echo $etkalinligi; ?>"><?php }else{ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="etkalinligi" placeholder="ET KALINLIĞI"><?php } ?></div></div>

            <div class="row"><div class="col-4"><b>Boy</b></div><div class="col-8"><?php if(isset($_GET['boy'])){ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="boy" value="<?php echo $boy; ?>"><?php }else{ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="boy" placeholder="BOY"><?php } ?></div></div>

            <div class="row"><div class="col-4"><b>Adet</b></div><div class="col-8"><?php if(isset($_GET['adet'])){ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="adet" value="<?php echo $adet; ?>"><?php }else{ ?><input type="text" class="form-control" style="margin-bottom: 5px;" name="adet" placeholder="ADET"><?php } ?></div></div>
            
            <button type="submit" class="btn btn-primary btn-block btn-sm" style="margin-bottom: 5px;" name="boruhesapla">Hesapla</button>

        </form>

        <?php if(isset($toplam5)){?><h5 style="text-align: center; margin-top: 20px;"><b>Sonuç : <ins><?php echo $toplam5." KG"; ?></ins></b></h5><?php } ?>

    </div>

</div>