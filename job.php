<?php
	include 'functions/init.php';
	if (!isLoggedIn()) {
		header("Location:login.php");
		exit();
	}elseif($authUser->type == '0' || $authUser->type == '1'){
		header("Location:index.php");
		exit();
	}else{
        $prevMonth = date('Y-m', strtotime('-1 month'));
        $repeatingJobs = $db->query("SELECT * FROM jobs WHERE is_deleted = '0' AND is_repeated = '1' ORDER BY due_date DESC")->fetchAll(PDO::FETCH_OBJ);
        foreach ($repeatingJobs as $repeatingJob) {
            $repeatingJobsMonth = date('Y-m', strtotime($repeatingJob->due_date));
            $dueDateArr = explode('-', $repeatingJob->due_date);
            if ($dueDateArr[1] !== '12') {
                $dueDateArr[1]++;
            } else {
                $dueDateArr[0]++;
                $dueDateArr[1] = 1;
            }
            $dueDate = implode("-", $dueDateArr);
            $dueDate = strtotime($dueDate);
            if ($prevMonth == $repeatingJobsMonth) {
                $updateMonthlyJobs = $db->prepare("INSERT INTO jobs SET job = ?, due_date = ?, is_repeated = ?, created_at = ?, status = ?, is_deleted = ?");
                $updateMonthly = $updateMonthlyJobs->execute(array($repeatingJob->job, $dueDate, $repeatingJob->is_repeated, date('Y-m-d'), 0, 0));
                $deleteRepeatedJob = $db->prepare("UPDATE jobs SET is_repeated = ? WHERE id = ?");
                $delete = $deleteRepeatedJob->execute(array(0, $repeatingJob->id));
            }
        }
        if(isset($_POST['add_job'])){
            $job = guvenlik($_POST['job']);
            $dueDate = guvenlik($_POST['due_date']);
            if(isset($_POST['is_repeated'])){ $isRepeated = 1; }else{ $isRepeated = 0; }
            $query = $db->prepare("INSERT INTO jobs SET job = ?, due_date = ?, is_repeated = ?, created_at = ?, status = ?, is_deleted = ?");
            $insert = $query->execute(array($job,$dueDate,$isRepeated,date('Y-m-d'),0,0));
            header("Location: job.php");
            exit();
        }
        if(isset($_POST['edit_job'])){
            $id = guvenlik($_POST['id']);
            $job = guvenlik($_POST['job']);
            $dueDate = guvenlik($_POST['due_date']);
            $isRepeated = guvenlik($_POST['is_repeated']);
            $status = guvenlik($_POST['status']);
            $query = $db->prepare("UPDATE jobs SET job = ?, due_date = ?, is_repeated = ?, status = ? WHERE id = ?");
            $update = $query->execute(array($job,$dueDate,$isRepeated,$status,$id));
            header("Location: job.php");
            exit();
        }
        if(isset($_POST['delete_job'])){
            $id = guvenlik($_POST['id']);
            $query = $db->prepare("UPDATE jobs SET is_deleted = ? WHERE id = ?");
            $update = $query->execute(array(1,$id));
            header("Location: job.php");
            exit();
        }
	}
?>
<!DOCTYPE html>
<html lang="tr">
  <head>
    <title>İşler</title>
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
                                <div class="col-md-5 col-12"><input type="text" name="job" class="form-control" placeholder="İş planına eklenecek görev"></div>
                                <div class="col-md-2 col-12"><input type="date" name="due_date" placeholder="Tarih seçiniz" class="form-control"></div>
                                <div class="col-md-3 col-12"><input type="checkbox" class="form-check-input" id="checkboxmonthly" name="is_repeated"><label class="form-check-label" for="checkboxmonthly" style="font-size:12px;">Aylık olarak hatırlatılmasını istiyorsanız işaretleyiniz.</label></div>
                                <div class="col-md-2 col-12"><button type="submit" class="btn btn-primary w-100" name="add_job">Kaydet</button></div>
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
            $jobs = $db->query("SELECT * FROM jobs WHERE is_deleted = 0 AND status = 0 ORDER BY due_date ASC")->fetchAll(PDO::FETCH_OBJ);
            foreach( $jobs as $key => $job ){
                $isOverdue = 0;
                if($job->due_date < date('Y-m-d')){
                    $isOverdue = 1;
                }
            ?>
                    <form action="" method="POST">
                    <?php if($job->status == 1){ ?>
                        <div class="row mb-1" style="background-color:#36AE7C; padding:5px 10px 10px; margin:-10px;">
                    <?php }elseif($isOverdue == 1){ ?>
                        <div class="row mb-1" style="background-color:#EB5353; padding:5px 10px 10px; margin:-10px;">
                    <?php }else{ ?>
                        <div class="row mb-1" style="background-color:#F9D923; padding:5px 10px 10px; margin:-10px;">
                    <?php } ?>
                            <div class="col-md-2">
                                <div class="row">
                                    <div class="col-md-3"><?= $key + 1; ?><input type="hidden" name="id" value="<?= $job->id; ?>"></div>
                                    <div class="col-md-9"><input type="date" name="due_date" value="<?= $job->due_date; ?>" class="form-control form-control-sm" style="border-style:none; font-size:1.1rem;"></div>
                                </div>
                            </div>
                            <div class="col-md-4"><input type="text" name="plan" class="form-control form-control-sm" placeholder="İş planına eklenecek görev" value="<?= $job->job; ?>" style="border-style:none; font-size:1.1rem;"></div>
                            <div class="col-md-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <select name="plan_tekrar" id="plan_tekrar" class="form-control form-control-sm" style="border-style:none; font-size:1.1rem;">
                                            <option value="0" <?= $job->is_repeated == 0 ? 'selected' : '' ?>>Tekrarsız</option>
                                            <option value="1" <?= $job->is_repeated == 1 ? 'selected' : '' ?>>Aylık Tekrarlı</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <select name="plan_durum" id="plan_durum" class="form-control form-control-sm" style="border-style:none; font-size:1.1rem;">
                                            <option value="0" <?= $job->status == 0 ? 'selected' : '' ?>>Sırada</option>
                                            <option value="1" <?= $job->status == 1 ? 'selected' : '' ?>>Tamamlandı</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1"><button type="submit" class="btn btn-primary w-100 btn-sm" name="edit_job" >Düzenle</button></div>
                            <div class="col-md-2">
                                <div id="delete_div<?= $job->id; ?>">
                                    <a href="#" onclick="return false" onmousedown="javascript:ackapa2('delete_approving_div<?= $job->id; ?>','delete_div<?= $job->id; ?>');">
                                        <button class="btn btn-danger btn-sm" style="width:100px;">Sil</button>
                                    </a>
                                </div>
                                <div id="delete_approving_div<?= $job->id; ?>" style="display:none;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <button type="submit" name="delete_job" class="btn btn-success btn-sm w-100">Evet</button>
                                        </div>
                                        <div class="col-md-6">
                                            <a href="#" onclick="return false" onmousedown="javascript:ackapa2('delete_div<?= $job->id; ?>','delete_approving_div<?= $job->id; ?>');">
                                                <button class="btn btn-danger w-100 btn-sm">Hayır</button>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
            <?php } ?>
            <hr/><h3 style="text-align:center; margin-bottom:1rem;">Tamamlanan İşler</h3>
            <?php
            $jobs = $db->query("SELECT * FROM jobs WHERE is_deleted = 0 AND status = 1 ORDER BY due_date ASC")->fetchAll( PDO::FETCH_OBJ);
                foreach( $jobs as $key => $job ){
                    $isOverdue = 0;
                    if($job->due_date < date('Y-m-d')){
                        $isOverdue = 1;
                    }
            ?>
                    <form action="" method="POST">
                    <?php if($job->status == 1){ ?>
                        <div class="row mb-1" style="background-color:#36AE7C; padding:5px 10px 10px; margin:-10px;">
                    <?php }elseif($isOverdue == 1){ ?>
                        <div class="row mb-1" style="background-color:#EB5353; padding:5px 10px 10px; margin:-10px;">
                    <?php }else{ ?>
                        <div class="row mb-1" style="background-color:#F9D923; padding:5px 10px 10px; margin:-10px;">
                    <?php } ?>
                            <div class="col-md-2">
                                <div class="row">
                                    <div class="col-md-3"><?= $key + 1; ?><input type="hidden" name="id" value="<?= $job->id; ?>"></div>
                                    <div class="col-md-9"><input type="date" name="due_date" value="<?= $job->due_date; ?>" class="form-control form-control-sm" style="border-style:none; font-size:1.1rem;"></div>
                                </div>
                            </div>
                            <div class="col-md-4"><input type="text" name="job" class="form-control form-control-sm" placeholder="İş planına eklenecek görev" value="<?= $job->job; ?>" style="border-style:none; font-size:1.1rem;"></div>
                            <div class="col-md-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <select name="plan_tekrar" id="plan_tekrar" class="form-control form-control-sm" style="border-style:none; font-size:1.1rem;">
                                            <option value="0" <?= $job->is_repeated == 0 ? 'selected' : '' ?>>Tekrarsız</option>
                                            <option value="1" <?= $job->is_repeated == 1 ? 'selected' : '' ?>>Aylık Tekrarlı</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <select name="plan_durum" id="plan_durum" class="form-control form-control-sm" style="border-style:none; font-size:1.1rem;">
                                            <option value="0" <?= $job->status == 0 ? 'selected' : '' ?>>Sırada</option>
                                            <option value="1" <?= $job->status == 1 ? 'selected' : '' ?>>Tamamlandı</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1"><button type="submit" class="btn btn-primary w-100 btn-sm" name="edit_job" >Düzenle</button></div>
                            <div class="col-md-2">
                                <div id="delete_div<?= $job->id; ?>">
                                    <a href="#" onclick="return false" onmousedown="javascript:ackapa2('delete_approving_div<?= $job->id; ?>','delete_div<?= $job->id; ?>');">
                                        <button class="btn btn-danger btn-sm" style="width:100px;">Sil</button>
                                    </a>
                                </div>
                                <div id="delete_approving_div<?= $job->id; ?>" style="display:none;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <button type="submit" name="delete_job" class="btn btn-success btn-sm w-100">Evet</button>
                                        </div>
                                        <div class="col-md-6">
                                            <a href="#" onclick="return false" onmousedown="javascript:ackapa2('delete_div<?= $job->id; ?>','delete_approving_div<?= $job->id; ?>');">
                                                <button class="btn btn-danger w-100 btn-sm">Hayır</button>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
            <?php } ?>
        </div>
    </div>
    <?php include 'template/script.php'; ?>
</body>
</html>