<?php
include 'functions/init.php';
if (!isLoggedIn()) {
    header("Location:login.php");
    exit();
}else{
    if(isset($_POST['add_leave'])) {
        $year = date("Y", time());
        $march31 = $year . "-10-31";
        $userId = isset($_POST['user_id']) && !empty($_POST['user_id']) ? guvenlik($_POST['user_id']) : $user->id;
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
        }elseif ($user->type != 2 && strtotime(getLastLeaveDate($userId) . ' +100 days') > strtotime($startDate)) {
            $error = '<br/><div class="alert alert-danger" role="alert">İzin başlangıç tarihiniz son izninizin bitiş tarihinden en az 100 gün sonra olmalıdır.</div>';
        }elseif($remainingLeave < $leaveDays) {
            $error = '<br/><div class="alert alert-danger" role="alert">Kalan izin hakkınız '.$remainingLeave.' gündür. Daha fazla izin talep edemezsiniz.</div>';
        }elseif(izinTarihKontrol($startDate, $returnDate, $office) != 0) {
            $error = '<br/><div class="alert alert-danger" role="alert">Sizin departmanınızda aynı tarihlerde izin alan başka bir çalışan var. Lütfen yıllık izin planından kontrol ediniz.</div>';
        }else{
            $query = $db->prepare("INSERT INTO leaves SET user_id = ?, start_date = ?, return_date = ?, leave_days = ?, status = ?, office = ?, company_id = ?, is_deleted = ?, time = ?");
            $insert = $query->execute(array($userId, $startDate, $returnDate, $leaveDays, '0', $office, $user->company_id, '0', time()));
            header("Location: leave.php");
            exit();
        }
    }

    if(isset($_POST['approve_leave'])) {
        $id = guvenlik($_POST['id']);
        $query = $db->prepare("UPDATE leaves SET status = ? WHERE id = ?");
        $update = $query->execute(array('1',$id));
        header("Location:leave.php");
        exit();
    }

    if(isset($_POST['izinreddet'])) {
        $id = guvenlik($_POST['id']);
        $query = $db->prepare("UPDATE leaves SET status = ? WHERE id = ?");
        $update = $query->execute(array('2',$id));
        header("Location:leave.php");
        exit();
    }

    if(isset($_POST['izinsil'])) {
        $id = guvenlik($_POST['id']);
        $query = $db->prepare("UPDATE leaves SET is_deleted = ? WHERE id = ?");
        $update = $query->execute(array('1',$id));
        header("Location:leave.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>İzinler</title>
    <?php include 'template/head.php'; ?>
    <style>
        .dikey-ortala {
            display: flex;
            align-items: center;
        }
    </style>
</head>
<body>
<?php include 'template/banner.php' ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <?= isset($error) ? $error : ''; ?>
        </div>
    </div>
    <div class="div4" style="padding-top: 20px; text-align: center;">
        <a href="#" onclick="return false" onmousedown="javascript:ackapa('form-div');"><h5><i class="fas fa-angle-double-down" style="color:darkblue;"></i>&nbsp;&nbsp;&nbsp;&nbsp;<b style="color:darkblue;">İzin Giriş Formu</b>&nbsp;&nbsp;&nbsp;&nbsp;<i class="fas fa-angle-double-down" style="color:darkblue;"></i></h5></a>
        <div id="form-div" style="display: none;">
            <form action="" method="POST">
                <div class="row mb-2">
                    <?php if($user->type == 2) { ?>
                        <div class="col-md-3 col-12">
                            <b>Çalışan</b>
                            <select name="user_id" id="user_id" class="form-control">
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
                        </div>
                    <?php } ?>
                </div>
                <div class="row">
                    <div class="col-md-3 col-12">
                        <b>İzin Başlangıç Tarihi</b><br/>
                        <input type="date" id="start_date" name="start_date" placeholder="İzin Başlangıç Tarihi" class="form-control mb-2">
                    </div>
                    <div class="col-md-3 col-12">
                        <b>İşe Başlama Tarihi</b><br/>
                        <input type="date" id="return_date" name="return_date" placeholder="İzin Başlangıç Tarihi" class="form-control mb-2">
                    </div>
                    <div class="col-md-3 col-12">
                        <b>İzinli Gün Sayısı</b><br/>
                        <input type="text" id="leave_days" name="leave_days" class="form-control mb-2" style="margin-bottom: 10px;" readonly placeholder="İzinli Gün Sayısı">
                    </div>
                    <div class="col-md-3 col-12">
                        <br/>
                        <button type="submit" class="btn btn-primary btn-block" name="add_leave">İzni Kaydet</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="div4 pt-3 mt-4">
        <h5 style="text-align:center; color:darkblue;"><?= date("Y", time())." YILI İZİN PLANLAMASI" ?></h5>
        <div class="row m-0 d-none d-md-flex">
            <div class="col-md-2"><b>Ad Soyad</b></div>
            <div class="col-md-1 px-0"><b>İşe Giriş Tarihi</b></div>
            <?php if($user->type == 2){ ?><div class="col-md-1"><b>Hakediş</b></div><?php } ?>
            <div class="col-md-2"><b>İzin Başlama Tarihi</b></div>
            <div class="col-md-2"><b>İşe Başlama Tarihi</b></div>
            <div class="col-md-1"><b>Onay</b></div>
            <div class="col-md-1 px-0"><b>Kullanılan İzin</b></div>
            <div class="col-md-1"><b>Kalan İzin</b></div>
        </div>
        <hr/>
        <?php
        $leaves = $db->query("SELECT * FROM leaves WHERE company_id = '{$user->company_id}' AND 'start_date' > '{$date}' AND is_deleted = '0' ORDER BY time");
        if($leaves->rowCount()) {
            foreach($leaves as $key => $izin) {
                $id = guvenlik($izin['id']);
                $userId = guvenlik($izin['user_id']);
                $userName = getUsername($userId);
                $startDate = guvenlik($izin['start_date']);
                $returnDate = guvenlik($izin['return_date']);
                $leaveDays = guvenlik($izin['leave_days']);
                $onay = guvenlik($izin['status']);
                $remainingLeave = calculateAnnualLeave($userId) - calculateUsedLeave($userId);
                //arşivleme
                $izinBaslangicYil = date('Y', strtotime($startDate));
                $iseBaslamaYil = date('Y', strtotime($returnDate));
                $currentYear = date('Y');
                if($izinBaslangicYil < $currentYear && $iseBaslamaYil < $currentYear) {
                    $query = $db->prepare('UPDATE leaves SET status = ? WHERE id = ?');
                    $update = $query->execute(array('3',$id));
                }
                ?>
                <form action="" method="POST">
                    <div class="row" style="margin: 0; <?= $key%2 == 0 ? 'background-color:#d9d9d9;' : ''; ?>">
                        <div class="col-6 d-block d-sm-none"><b>Ad Soyad :</b></div>
                        <div class="col-md-2 col-6 dikey-ortala"><?= $userName ?></div>
                        <div class="col-6 d-block d-sm-none"><b>İşe Giriş Tarihi :</b></div>
                        <div class="col-md-1 col-6 dikey-ortala"><?= (new DateTime(iseGirisTarihiGetir($userId)))->format('d.m.Y') ?></div>
                        <?php if($user->type == 2){ ?>
                            <div class="col-6 d-block d-sm-none"><b>Hakediş :</b></div>
                            <div class="col-md-1 col-6 dikey-ortala"><?= calculateAnnualLeave($userId) ?></div>
                        <?php } ?>
                        <div class="col-6 d-block d-sm-none"><b>İzin Başlama Tarihi :</b></div>
                        <div class="col-md-2 col-6 dikey-ortala"><?= (new DateTime($startDate))->format('d.m.Y') ?></div>
                        <div class="col-6 d-block d-sm-none"><b>İşe Başlama Tarihi :</b></div>
                        <div class="col-md-2 col-6 dikey-ortala"><?= (new DateTime($returnDate))->format('d.m.Y') ?></div>
                        <div class="col-6 d-block d-sm-none"><b>Onay :</b></div>
                        <div class="col-md-1 col-6 dikey-ortala" style="color:<?= $onay == 0 ? 'orange' : ($onay == 1 ? 'green' : 'red'); ?>;"><?= $onay == 0 ? 'Bekleniyor' : ($onay == 1 ? 'Onaylandı' : 'Reddedildi') ?></div>
                        <div class="col-6 d-block d-sm-none"><b>Kullanılan İzin :</b></div>
                        <div class="col-md-1 col-6 dikey-ortala"><?= calculateUsedLeave($userId) ?></div>
                        <div class="col-6 d-block d-sm-none"><b>Kalan İzin :</b></div>
                        <div class="col-md-1 col-6 dikey-ortala"><?= $remainingLeave ?></div>
                        <?php if($user->type == 2) { ?>
                            <div class="col-md-1 col-12 px-0 d-flex">
                                <input type="hidden" name="id" value="<?= $id ?>">
                                <button type="submit" class="btn btn-block btn-sm btn-success mt-0" name="approve_leave" onclick="return confirmForm('İzni onaylıyorsunuz, emin misiniz?');">Onay</button>
                                <button type="submit" class="btn btn-block btn-sm btn-danger mt-0" name="izinreddet" onclick="return confirmForm('İzin iptal edilecek, emin misiniz?');">Red</button>
                                <button type="submit" class="btn btn-block btn-sm btn-secondary mt-0" name="izinsil" onclick="return confirmForm('İzni listeden kaldıracaksınız, emin misiniz?');">Sil</button>
                            </div>
                        <?php } ?>
                    </div>
                </form>
                <?php
            }
        }
        ?>
    </div>
    <div class="div4 pt-3 mt-4">
        <h5 style="text-align:center; color:darkblue;">İZİN KULLANIM KURALLARI</h5>
        <ul style="font-size:10px;">
            <li>İZİN TALEPLERİ, 1 OCAK İLE 31 MART TARİHLERİ ARASINDA OLUŞTURULMALI VE BU TALEPLERİN YÖNETİM ONAYI BEKLENMELİDİR; BU TARİH ARALIĞI DIŞINDA KESİNLİKLE İZİN TALEP EDİLEMEZ.</li>
            <li>İZİN TALEPLERİ BELİRTİLEN TARİHLER ARASINDA OLUŞTURULMADIĞI TAKDİRDE, İZİN HAKLARI BULUNAN KİŞİLER YÖNETİMİN BELİRLEDİĞİ TARİHLERDE İZİN KULLANMAK ZORUNDADIR.</li>
            <li>BİR SEFERDE MAKSİMÜM İZİN KULLANIM SÜRESİ  14  GÜN YANI  2 HAFTADIR.</li>
            <li>İKİ İZİN ARASINDA EN AZ 100 GÜN FARK OLMALIDIR; BU KURAL, HERKEZIN EŞİT DÖNEMDE İZİN HAKKININ DÜZENLİ KULLANIMINI SAĞLAMAK İÇİN GEÇERLİDİR.</li>
            <li>AYNI DEPARTMANDA İKİ KİŞİ, AYNI TARİHTE İZİN KULLANAMAZ; BU KURAL, İŞ AKIŞININ DEVAMLIĞINI SAĞLAMAK AMACIYLA GETİRİLMİŞTİR.</li>
            <li>YÖNETİMİN İNSİYATİFİYLE BELİRLEDİĞİ MÜCBİR SEBEBLER HARİÇ, BU KURALAR DIŞINA ÇIKILAMAZ; TÜM ÇALIŞANLARIN BU KURALLARA UYMASI BEKLENMEKTEDİR.</li>
        </ul>
    </div>
    <?php if($user->type == 2){ ?>
        <div class="div4 pt-3 mt-4">
            <h5 style="text-align:center; color:darkblue;">GENEL İZİN TABLOSU</h5>
            <div class="row m-0 d-none d-md-flex">
                <div class="col-md-3"><b>Adı Soyadı</b></div>
                <div class="col-md-3"><b>İşe Giriş Tarihi</b></div>
                <div class="col-md-2"><b>Toplam Hakediş</b></div>
                <div class="col-md-2"><b>Kullanılan İzin</b></div>
                <div class="col-md-2"><b>Kalan İzin</b></div>
            </div>
            <hr/>
            <?php
            $users = $db->query("SELECT * FROM users WHERE type != '2' AND company_id = '{$user->company_id}' AND is_deleted = '0' ORDER BY name ASC", PDO::FETCH_ASSOC);
            if ( $users->rowCount() ){
                foreach( $users as $key => $uye ){
                    $uyeId = guvenlik($uye['id']);
                    $uyeAdi = guvenlik($uye['name']);
                    $iseGirisTarihi = guvenlik($uye['hire_date']);
                    $toplamHakedis = calculateAnnualLeave($uyeId);
                    $kullanilanIzin = calculateUsedLeave($uyeId);
                    $remainingLeave = $toplamHakedis - $kullanilanIzin;
                    ?>
                    <div class="row" style="margin: 0; <?= $key%2 == 0 ? 'background-color:#c6c6c6;' : ''; ?>">
                        <div class="col-6 d-block d-sm-none"><b>Ad Soyad :</b></div>
                        <div class="col-md-3 col-6"><?= $uyeAdi ?></div>
                        <div class="col-6 d-block d-sm-none"><b>İşe Giriş Tarihi :</b></div>
                        <div class="col-md-3 col-6"><?= (new DateTime($iseGirisTarihi))->format('d.m.Y') ?></div>
                        <div class="col-6 d-block d-sm-none"><b>Toplam Hakediş :</b></div>
                        <div class="col-md-2 col-6"><?= $toplamHakedis ?></div>
                        <div class="col-6 d-block d-sm-none"><b>Kullanılan İzin :</b></div>
                        <div class="col-md-2 col-6"><?= $kullanilanIzin ?></div>
                        <div class="col-6 d-block d-sm-none"><b>Kalan İzin :</b></div>
                        <div class="col-md-2 col-6"><?= $remainingLeave ?></div>
                    </div>
                    <?php
                }
            }
            ?>
            <!--<hr/>
            <ul style="font-size:10px;">
                <li>
                Yıllık izin süresi hesaplama işlemi için öncelikle bir iş yerinde minimum 1 yıl çalışmış olmanız gerekir.
                Eğer 1 yıl çalıştıysanız, yıllık izin süreniz 14 gün olacaktır. 5 yıldan fazla 15 yıldan az çalıştıysanız 20 gün, 15 yıl (dahil)
                ve daha fazla çalıştıysanız en az 26 gün ücretli izin hakkınız vardır.
                </li>
            </ul> -->
        </div>
    <?php } ?>
    <br/><br/><br/><br/><br/>
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
</script>
</body>
</html>