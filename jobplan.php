<div id="isplanidivi" style="display:none;">
    <div style="background-color:white; padding:5px;"><h3>Gelecek 30 Günlük İşler</h3></div>
    <?php
        $jobs = $db->query("SELECT * FROM jobs WHERE is_deleted = 0 AND status = 0 ORDER BY due_date ASC")->fetchAll(PDO::FETCH_OBJ);
            foreach( $jobs as $key => $job ){
                $isOverdue = 0;
                if($job->due_date < date('Y-m-d')){
                    $isOverdue = 1;
                }
    ?>
            <div><form action="" method="POST">
            <?php if($isOverdue == 0){ ?>
                <div class="row mb-1 mx-0" style="background-color:#52c0c0;">
            <?php }else{ ?>
                <div class="row mb-1 mx-0" style="background-color:#ad3f3f;">
            <?php } ?>
                    <div class="col-md-2">
                        <input type="hidden" name="plan_id" value="<?= $job->id; ?>">
                        <input type="date" name="due_date" value="<?= $job->due_date; ?>" class="form-control form-control-sm my-1" style="border-style:none; font-size:1.1rem;">
                    </div>
                    <div class="col-md-4"><input type="text" name="plan" class="form-control form-control-sm my-1" placeholder="İş planına eklenecek görev" value="<?= $job->job; ?>" style="border-style:none; font-size:1.1rem;"></div>
                    <div class="col-md-3">
                        <div class="row">
                            <div class="col-md-6">
                                <select name="plan_tekrar" id="plan_tekrar" class="form-control form-control-sm my-1" style="border-style:none; font-size:1.1rem;">
                                    <option value="0" <?= $job->is_repeated == 0 ? 'selected' : '' ?>>Tekrarsız</option>
                                    <option value="1" <?= $job->is_repeated == 1 ? 'selected' : '' ?>>Aylık Tekrarlı</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <select name="plan_durum" id="plan_durum" class="form-control form-control-sm my-1" style="border-style:none; font-size:1.1rem;">
                                    <option value="0" <?= $job->status == 0 ? 'selected' : '' ?>>Sırada</option>
                                    <option value="1" <?= $job->status == 1 ? 'selected' : '' ?>>Tamamlandı</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1 col-6"><button type="submit" class="btn btn-primary btn-block btn-sm my-1" name="plan_duzenle" >Düzenle</button></div>
                    <div class="col-md-2 col-6">
                        <div id="delete_div<?= $job->id; ?>">
                            <a href="#" onclick="return false" onmousedown="javascript:ackapa2('delete_approving_div<?= $job->id; ?>','delete_div<?= $job->id; ?>');">
                                <button class="btn btn-danger btn-block btn-sm my-1">Sil</button>
                            </a>
                        </div>
                        <div id="delete_approving_div<?= $job->id; ?>" style="display:none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="submit" name="delete_job" class="btn btn-success btn-sm btn-block my-1">Evet</button>
                                </div>
                                <div class="col-md-6">
                                    <a href="#" onclick="return false" onmousedown="javascript:ackapa2('delete_div<?= $job->id; ?>','delete_approving_div<?= $job->id; ?>');">
                                        <button class="btn btn-danger btn-block btn-sm my-1">Hayır</button>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form></div>
    <?php
            }
    ?>
</div>