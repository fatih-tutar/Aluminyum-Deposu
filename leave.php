<?php
include 'functions/init.php';
if (!isLoggedIn()) {
    header("Location:login.php");
    exit();
}else{
    if(isset($_POST['add_leave'])) {
        $year = date("Y", time());
        $march31 = $year . "-10-31";
        $userId = isset($_POST['user_id']) && !empty($_POST['user_id']) ? guvenlik($_POST['user_id']) : $authUser->id;
        $startDate = guvenlik($_POST['start_date']);
        $returnDate = guvenlik($_POST['return_date']);
        $leaveDays = guvenlik($_POST['leave_days']);
        $remainingLeave = calculateAnnualLeave($userId) - calculateUsedLeave($userId);
        $office = getOfficeType($userId);
        $date = date("Y-m-d",time());
        if(empty($startDate) || empty($returnDate)) {
            $error = '<br/><div class="alert alert-danger" role="alert">Tarih alanları boş bırakılamaz.</div>';
        }elseif($date > $march31) {
            $error = '<br/><div class="alert alert-danger" role="alert">Sadece 1 Ocak ile 31 Mart tarihleri arasında izin girişi yapabilirsiniz. Bu tarihler dışında lütfen yöneticinizle iletişime geçiniz.</div>';
        }elseif($leaveDays > 14) {
            $error = '<br/><div class="alert alert-danger" role="alert">Tek seferde en fazla 14 günlük izin girebilirsiniz.</div>';
        }elseif ($authUser->type != 2 && !isLeaveDateValid($userId, $startDate, $returnDate)) {
            $error = '<br/><div class="alert alert-danger" role="alert">İki izin arasında en az 100 gün olmak zorundadır.</div>';
        }elseif($remainingLeave < $leaveDays) {
            $error = '<br/><div class="alert alert-danger" role="alert">Kalan izin hakkınız '.$remainingLeave.' gündür. Daha fazla izin talep edemezsiniz.</div>';
        }elseif(hasOverlappingLeave($startDate, $returnDate, $office) != 0) {
            $error = '<br/><div class="alert alert-danger" role="alert">Sizin departmanınızda aynı tarihlerde izin alan başka bir çalışan var. Lütfen yıllık izin planından kontrol ediniz.</div>';
        }else{
            $query = $db->prepare("INSERT INTO leaves SET user_id = ?, start_date = ?, return_date = ?, leave_days = ?, status = ?, office = ?, company_id = ?, is_deleted = ?, time = ?");
            $insert = $query->execute(array($userId, $startDate, $returnDate, $leaveDays, '0', $office, $authUser->company_id, '0', time()));
            header("Location: leave.php");
            exit();
        }
    }

    if(isset($_POST['edit_leave'])) {
        $year = date("Y", time());
        $march31 = $year . "-10-31";
        $id = guvenlik($_POST['id']);
        $userId = guvenlik($_POST['user_id']);
        $startDate = guvenlik($_POST['start_date']);
        $returnDate = guvenlik($_POST['return_date']);
        $leaveDays = guvenlik($_POST['leave_days']);
        $status = guvenlik($_POST['status']);
        $remainingLeave = calculateAnnualLeave($userId) - calculateUsedLeave($userId,$id);
        $office = getOfficeType($userId);
        $date = date("Y-m-d",time());
        if(empty($startDate) || empty($returnDate)) {
            $error = '<br/><div class="alert alert-danger" role="alert">Tarih alanları boş bırakılamaz.</div>';
        }elseif($date > $march31) {
            $error = '<br/><div class="alert alert-danger" role="alert">Sadece 1 Ocak ile 31 Mart tarihleri arasında izin girişi yapabilirsiniz. Bu tarihler dışında lütfen yöneticinizle iletişime geçiniz.</div>';
        }elseif($leaveDays > 14) {
            $error = '<br/><div class="alert alert-danger" role="alert">Tek seferde en fazla 14 günlük izin girebilirsiniz.</div>';
        }elseif ($user->type != 2 && !isLeaveDateValid($userId, $startDate, $returnDate,$id)) {
            $error = '<br/><div class="alert alert-danger" role="alert">İzin başlangıç tarihiniz son izninizin bitiş tarihinden en az 100 gün sonra olmalıdır.</div>';
        }elseif($remainingLeave < $leaveDays) {
            $error = '<br/><div class="alert alert-danger" role="alert">Kalan izin hakkınız '.$remainingLeave.' gündür. Daha fazla izin talep edemezsiniz.</div>';
        }elseif(hasOverlappingLeave($startDate, $returnDate, $office) != 0) {
            $error = '<br/><div class="alert alert-danger" role="alert">Sizin departmanınızda aynı tarihlerde izin alan başka bir çalışan var. Lütfen yıllık izin planından kontrol ediniz.</div>';
        }else{
            $query = $db->prepare("UPDATE leaves SET start_date = ?, return_date = ?, leave_days = ?, status = ? WHERE id = ?");
            $update = $query->execute(array($startDate,$returnDate,$leaveDays,'0',$id));
            header("Location: leave.php");
            exit();
        }
    }

    if(isset($_POST['delete_leave'])) {
        $id = guvenlik($_POST['id']);
        $query = $db->prepare("UPDATE leaves SET is_deleted = ? WHERE id = ?");
        $update = $query->execute(array('1',$id));
        header("Location:leave.php");
        exit();
    }

    $leaves = $db->query("
            SELECT * FROM leaves 
            WHERE company_id = '{$user->company_id}' 
            AND YEAR(start_date) = '{$currentYear}' 
            AND is_deleted = '0' 
            ORDER BY start_date ASC, return_date ASC
        ")->fetchAll(PDO::FETCH_OBJ);

    $oldLeaves = array_values(array_filter($leaves, fn($leave) => $leave->return_date <= $date));
    usort($oldLeaves, function($a, $b) {
        $cmp = strcmp($b->start_date, $a->start_date);
        if ($cmp === 0) {
            return strcmp($b->return_date, $a->return_date);
        }
        return $cmp;
    });

    $newLeaves = array_values(array_filter($leaves, fn($leave) => $leave->return_date > $date));

    $leaves = array_merge($newLeaves, $oldLeaves);

    $users = $db->query("
            SELECT * FROM users 
            WHERE type != '2' 
            AND company_id = '{$user->company_id}' 
            AND is_deleted = '0' 
            ORDER BY hire_date ASC
        ")->fetchAll( PDO::FETCH_OBJ);

    $leaveStatuses = ['Onay Bekleniyor','Onaylandı','Reddedildi'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>İzinler</title>
    <?php include 'template/head.php'; ?>
    <style>
        body {
            background-color: white;
        }
        .add-button {
            background-color:#1b9bee;
            border-radius:32px;
            bottom:20px;
            color:white;
            height:64px;
            padding:15px;
            position: fixed;
            right:20px;
            text-align: center;
            width:64px;
            z-index:2;
        }
        .bb-grey {
            border-bottom: 1px solid #f4f4f4;
        }
        .br-grey {
            border-right: 1px solid #f4f4f4;
        }
        .close {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 24px;
            cursor: pointer;
        }
        .icon-button {
            background-color: white;
            border-style: none;
        }
        .modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 30%;
            height: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
            z-index: 1000;
        }
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
        .table-responsive {
            overflow-x: auto;
            white-space: nowrap;
        }
        .table thead th, .table tbody td {
            white-space: nowrap;
        }
        @media screen and (max-width: 768px) {
            .modal {
                width: 96%;
                left: 2%;
                transform: translate(0, -50%);
                border-radius: 0; /* Köşeleri kaldır */
                height: auto; /* İçeriğe göre yükseklik */
            }
        }
    </style>
</head>
<body>
<?php include 'template/banner.php' ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 d-none d-md-block br-grey">
            <?php include 'template/sidebar2.php'; ?>
        </div>
        <div class="col-md-9 col-12">
            <?= isset($error) ? $error : ''; ?>
            <a href="#" onclick="openModal('form-div')">
                <div class="add-button d-block d-md-none">
                    <i class="fa fa-plus fa-2x"></i>
                </div>
            </a>
            <div id="overlay" class="overlay" onclick="closeModal()"></div>
            <div id="form-div" class="modal">
                <span class="close" onclick="closeModal()">&times;</span>
                <div style="padding-top: 20px;">
                    <form action="" method="POST">
                        <?php if($user->type == 2) { ?>
                            <b>Çalışan</b>
                            <select name="user_id" id="user_id" class="form-control mb-2">
                                <option selected>İzin Verilecek Kişiyi Seçiniz</option>
                                <?php
                                $calisanlaricek = $db->query("SELECT * FROM users WHERE company_id = '{$user->company_id}' AND type != '2' ORDER BY name ASC", PDO::FETCH_ASSOC);
                                if ( $calisanlaricek->rowCount() ){
                                    foreach( $calisanlaricek as $cc ){
                                        $calisanId = guvenlik($cc['id']);
                                        $calisanAdi = guvenlik($cc['name']);
                                        ?>
                                        <option value="<?= $calisanId; ?>"><?= $calisanAdi; ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        <?php } ?>
                        <b>İzin Başlangıç Tarihi</b><br/>
                        <input type="date" id="start_date" name="start_date" class="form-control mb-2">
                        <b>İşe Dönüş Tarihi</b><br/>
                        <input type="date" id="return_date" name="return_date" class="form-control mb-2">
                        <b>İzinli Gün Sayısı</b><br/>
                        <input type="text" id="leave_days" name="leave_days" class="form-control mb-2" readonly>
                        <button type="submit" class="btn btn-primary btn-block" name="add_leave">İzni Kaydet</button>
                    </form>
                </div>
            </div>
            <div class="row pl-3 pb-4 pr-3 bb-grey">
                <div style="text-align: right; display: block; width: 100%;" class="d-none d-md-block">
                    <a href="#" onclick="openModal('form-div')">
                        <button class="btn btn-primary btn mb-2" style="background-color: #003566; border-color: #003566;">
                            <i class="fas fa-pen mr-2"></i>
                            Yeni İzin Girişi
                        </button>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                        <tr style="color:#003566">
                            <?php if($authUser->type == 2){ ?><th scope="col">Personel</th><?php } ?>
                            <th scope="col">İzin Başlama Tarihi</th>
                            <th scope="col">İşe Dönüş Tarihi</th>
                            <th scope="col">İzin Süresi</th>
                            <th scope="col">Durum</th>
                            <th scope="col"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $leaveCounter = 0;
                        $oldLeavesCounter = 0;
                        foreach( $leaves as $leave){
                            if ($authUser->type == 2 || $leave->user_id == $authUser->id) {
                                $leaveCounter++;
                                $leaveUser = getUser($leave->user_id);
                                $startDate = strftime('%e %B %Y %A', (new DateTime($leave->start_date))->getTimestamp());
                                $returnDate = strftime('%e %B %Y %A', (new DateTime($leave->return_date))->getTimestamp());
                                $oldLeavesCounter = $leave->return_date <= $date ? ($oldLeavesCounter + 1) : $oldLeavesCounter;
                                ?>
                                <?php if($oldLeavesCounter == 1){ ?>
                                    <tr>
                                        <td colspan="6" style="text-align: center; font-weight: bold; background-color: #f8f8f8;">
                                            Geçmiş İzinler
                                        </td>
                                    </tr>
                                <?php } ?>
                                <tr <?= $leave->return_date < $date ? 'style="background-color:#f8f8f8;"' : ''; ?>>
                                    <?php if($authUser->type == 2){ ?><th scope="row"><?= $leaveUser->name.' '.$leaveUser->surname ?></th><?php } ?>
                                    <td><?= $startDate ?></td>
                                    <td><?= $returnDate ?></td>
                                    <td><?= $leave->leave_days ?></td>
                                    <td><?= $leaveStatuses[$leave->status] ?></td>
                                    <td style="display: flex; justify-content: space-evenly;">
                                        <a href="#" onclick="openModal('edit-div-<?= $leave->id ?>')">
                                            <i class="fas fa-pen mr-3" style="color:#003566"></i>
                                        </a>
                                        <div id="edit-div-<?= $leave->id ?>" class="modal">
                                            <span class="close" onclick="closeModal()">&times;</span>
                                            <div style="padding-top: 20px;">
                                                <form action="" method="POST">
                                                    <b>İzin Başlangıç Tarihi</b><br/>
                                                    <input type="date" id="start_date_<?= $leave->id ?>" name="start_date" class="form-control mb-2" value="<?= $leave->start_date ?>">
                                                    <b>İşe Dönüş Tarihi</b><br/>
                                                    <input type="date" id="return_date_<?= $leave->id ?>" name="return_date" class="form-control mb-2" value="<?= $leave->return_date ?>">
                                                    <b>İzinli Gün Sayısı</b><br/>
                                                    <input type="text" id="leave_days_<?= $leave->id ?>" name="leave_days" class="form-control mb-2" readonly>
                                                    <?php if($authUser->type == 2){ ?>
                                                        <b>Durum</b><br/>
                                                        <select name="status" class="form-control mb-2">
                                                            <?php foreach ($leaveStatuses as $key => $status): ?>
                                                                <option value="<?= $key; ?>" <?= ($leave->status == $key) ? 'selected' : ''; ?>>
                                                                    <?= $status; ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    <?php } ?>
                                                    <input type="hidden" name="id" value="<?= $leave->id ?>">
                                                    <input type="hidden" name="user_id" value="<?= $leave->user_id ?>">
                                                    <button type="submit" class="btn btn-primary btn-block" name="edit_leave">İzni Güncelle</button>
                                                </form>
                                            </div>
                                        </div>
                                        <form action="" method="POST">
                                            <input type="hidden" name="id" value="<?= $leave->id ?>">
                                            <button type="submit" class="icon-button" name="delete_leave" onclick="return confirmForm('İzni listeden kaldıracaksınız, emin misiniz?');">
                                                <i class="fas fa-trash" style="color:dimgrey"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php } }
                        if ($leaveCounter == 0) { ?>
                            <tr>
                                <td colspan="5" style="text-align: center; color: #003566; font-weight: bold;">
                                    Hiç izin kaydınız yoktur.
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row pl-4 pr-1 pt-4 pr-3" style="border-bottom: 1px solid #f4f4f4;">
                <h5 style="font-weight: bold; color: #003566">İZİN KULLANIM KURALLARI</h5>
                <ul style="font-size:12px; padding-left: 0; list-style-position: inside; ">
                    <li>İzin talepleri, 1 Ocak ile 31 Mart tarihleri arasında oluşturulmalı ve bu taleplerin yönetim onayı beklenmelidir; bu tarih aralığı dışında kesinlikle izin talep edilemez.</li>
                    <li>İzin talepleri belirtilen tarihler arasında oluşturulmadığı takdirde, izin hakları bulunan kişiler yönetimin belirlediği tarihlerde izin kullanmak zorundadır.</li>
                    <li>Bir seferde maksimum izin kullanım süresi 14 gündür, yani 2 haftadır.</li>
                    <li>İki izin arasında en az 100 gün fark olmalıdır; bu kural, herkesin eşit dönemde izin hakkının düzenli kullanımını sağlamak için geçerlidir.</li>
                    <li>Aynı departmanda iki kişi, aynı tarihte izin kullanamaz; bu kural, iş akışının devamlılığını sağlamak amacıyla getirilmiştir.</li>
                    <li>Yönetimin inisiyatifiyle belirlediği mücbir sebepler hariç, bu kurallar dışına çıkılamaz; tüm çalışanların bu kurallara uyması beklenmektedir.</li>
                </ul>
            </div>
            <div class="row pl-4 pr-1 pt-4 pr-3">
                <?php if($user->type == 2){ ?>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                            <tr style="color:#003566">
                                <th scope="col">Adı Soyadı</th>
                                <th scope="col">İşe Giriş Tarihi</th>
                                <th scope="col">Hakediş</th>
                                <th scope="col">Kullanılan</th>
                                <th scope="col">Kalan</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach( $users as $user){
                                $remainingLeave = calculateAnnualLeave($user->id) - calculateUsedLeave($user->id);
                                ?>
                                <tr>
                                    <th scope="row"><?= $user->name ?></th>
                                    <td><?= (new DateTime($user->hire_date))->format('d.m.Y') ?></td>
                                    <td><?= calculateAnnualLeave($user->id) ?></td>
                                    <td><?= calculateUsedLeave($user->id) ?></td>
                                    <td><?= $remainingLeave ?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>
            </div>
            <br/><br/><br/><br/><br/>
        </div>
    </div>
</div>
<?php include 'template/script.php'; ?>
<script>
    function hesaplaGunFarki() {
        var baslangicTarihi = document.getElementById("start_date").value;
        var bitisTarihi = document.getElementById("return_date").value;
        if (baslangicTarihi && bitisTarihi) {
            var baslangic = new Date(baslangicTarihi);
            var bitis = new Date(bitisTarihi);
            var farkZaman = bitis.getTime() - baslangic.getTime();
            var gunFarki = farkZaman / (1000 * 3600 * 24); // Milisaniyeleri gün cinsinden hesaplama
            if (gunFarki >= 0) {
                document.getElementById("leave_days").value = gunFarki;
            } else {
                document.getElementById("leave_days").value = 0;
            }
        }
    }
    // Her iki tarih girişini dinleyen olay tetikleyicisi
    document.getElementById("start_date").addEventListener("change", hesaplaGunFarki);
    document.getElementById("return_date").addEventListener("change", hesaplaGunFarki);
    function openModal(divId) {
        document.getElementById(divId).style.display = "block";
        document.getElementById("overlay").style.display = "block";
        var id = divId.replace("edit-div-", "");
        calculateDayDifferenceWithId(id);
    }

    function closeModal() {
        document.querySelectorAll(".modal").forEach(modal => {
            if (modal.style.display === "block") {
                modal.style.display = "none";
            }
        });
        document.getElementById("overlay").style.display = "none";
    }

    function calculateDayDifferenceWithId(id) {
        var baslangicTarihi = document.getElementById("start_date_" + id).value;
        var bitisTarihi = document.getElementById("return_date_" + id).value;
        if (baslangicTarihi && bitisTarihi) {
            var baslangic = new Date(baslangicTarihi);
            var bitis = new Date(bitisTarihi);
            var farkZaman = bitis.getTime() - baslangic.getTime();
            var gunFarki = farkZaman / (1000 * 3600 * 24); // Gün farkını hesapla
            if (gunFarki >= 0) {
                document.getElementById("leave_days_" + id).value = gunFarki;
            } else {
                document.getElementById("leave_days_" + id).value = 0;
            }
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll("[id^='start_date_'], [id^='return_date_']").forEach(function (input) {
            input.addEventListener("change", function () {
                var id = this.id.split("_").pop(); // ID’nin sonundaki sayıyı al
                calculateDayDifferenceWithId(id);
            });
        });
    });

</script>
</body>
</html>