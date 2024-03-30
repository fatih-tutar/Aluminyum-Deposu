<div id="isplanidivi" style="display:none;">

    <div class="row">

        <div class="col-12" style="background-color:white; padding-top:10px;"><h3>Gelecek 30 Günlük İşler</h3></div>

    </div>

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

                if($plan_tarihi < $su_an){ $gecmis = 1; }

                $plan_tarihi = date("d-m-Y",$plan_tarihi);

                $plan_tekrar = guvenlik($plancek['plan_tekrar']);

                $plan_durum = guvenlik($plancek['plan_durum']);

    ?>

            <div><form action="" method="POST">

            <?php if($gecmis == '0'){ ?>
                
                <div class="row mb-1" style="background-color:#52c0c0; padding:20px 10px; margin:-10px;">

            <?php }else{ ?>
                
                <div class="row mb-1" style="background-color:#ad3f3f; padding:20px 10px; margin:-10px;">

            <?php } ?>

                    <div class="col-md-2">

                        <input type="hidden" name="plan_id" value="<?php echo $plan_id; ?>">
                            
                        <input type="text" id="tarih<?php echo $plan_id; ?>" name="plan_tarihi" value="<?php echo $plan_tarihi; ?>" class="form-control form-control-sm" style="border-style:none; font-size:1.1rem;">

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

                    <div class="col-md-1 col-6"><button type="submit" class="btn btn-primary btn-block btn-sm" name="plan_duzenle" >Düzenle</button></div>

                    <div class="col-md-2 col-6">

                        <div id="sildivi<?php echo $plan_id; ?>">

                            <a href="#" onclick="return false" onmousedown="javascript:ackapa2('silmeonaydivi<?php echo $plan_id; ?>','sildivi<?php echo $plan_id; ?>');">
                        
                                <button class="btn btn-danger btn-block btn-sm">Sil</button>
                            
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

            </form></div>

    <?php
    
            }
        
        }
    
    ?>

</div>