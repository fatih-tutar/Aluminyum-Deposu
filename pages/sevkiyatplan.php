<?php
    require_once __DIR__.'/../config/init.php';
    if (!isLoggedIn()) {
        header("Location:/login");
        exit();
    }

    $sevkiyatlar = $db->query("SELECT * FROM sevkiyat WHERE silik = '0' AND nakliye_durumu = '0'")->fetchAll(PDO::FETCH_OBJ);
    $sevkiyatGruplari = [];
    foreach ($sevkiyatlar as $sevkiyat) {
        $sevkiyatGruplari[$sevkiyat->arac_id][] = $sevkiyat;
    }
    $araclar = $db->query("SELECT * FROM vehicles WHERE is_deleted = '0' AND is_transport = '1'")->fetchAll(PDO::FETCH_OBJ);
    $clients = $db->query("SELECT * FROM clients WHERE is_deleted = '0'")->fetchAll(PDO::FETCH_OBJ);

    if (isset($_POST['manuelSevkiyatKaydet'])) {
        $firma = guvenlik($_POST['firma']);
        $kilolar = guvenlik($_POST['kilolar']);
        $arac_id = guvenlik($_POST['arac_id']);
        $aciklama = guvenlik($_POST['aciklama']);
        $firmaId = getClientId($firma);
        if (empty($firma)) {
            $error = '<br/><div class="alert alert-danger" role="alert">Firma seçmediniz.</div>';
        } elseif (empty($kilolar)) {
            $error = '<br/><div class="alert alert-danger" role="alert">Kilo bilgisi yazmadınız.</div>';
        } else {
            $query = $db->prepare("INSERT INTO sevkiyat SET urunler = ?, firma_id = ?, adetler = ?, kilolar = ?, fiyatlar = ?, olusturan = ?, hazirlayan = ?, sevk_tipi = ?, arac_id = ?, aciklama = ?, manuel = ?, durum = ?, silik = ?, saniye = ?, sirket_id = ?");
            $insert = $query->execute(array('', $firmaId, '', $kilolar, '', $user->id, '', '', $arac_id, $aciklama, '1', '0', '0', time(), $user->company_id));
            header("Location:/sevkiyatplan");
            exit();
        }
    }

    if (isset($_POST['sevkiyatplanisil'])) {
        $sevkiyatId = guvenlik($_POST['sevkiyat_id']);
        $query = $db->prepare("UPDATE sevkiyat SET nakliye_durumu = ? WHERE id = ?");
        $guncelle = $query->execute(array('1', $sevkiyatId));
        header("Location:/sevkiyatplan");
        exit();
    }

    if (isset($_POST['sevkiyattoplusil'])) {
        $arac_id = guvenlik($_POST['arac_id']);
        $query = $db->prepare("UPDATE sevkiyat SET nakliye_durumu = ? WHERE arac_id = ?");
        $guncelle = $query->execute(array('1', $arac_id));
        header("Location:/sevkiyatplan");
        exit();
    }

    if (isset($_POST['sevkiyatguncelle'])) {
        $sevkiyatId = guvenlik($_POST['sevkiyat_id']);
        $aciklama = guvenlik($_POST['aciklama']);
        $query = $db->prepare("UPDATE sevkiyat SET aciklama = ? WHERE id = ?");
        $guncelle = $query->execute(array($aciklama, $sevkiyatId));
        header("Location:/sevkiyatplan");
        exit();
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sevkiyat Planı</title>
    <?php include ROOT_PATH.'/template/head.php'; ?>
    <style>
        .sevkCardBlue { background-color: #17a2b8; border-radius: 5px; color: black; margin-bottom: 5px; padding: 5px; }
        .sevkCardGreen { background-color: #28a745; border-radius: 5px; color: black; margin-bottom: 5px; padding: 5px; }
    </style>
</head>
<body class="body-white">
<?php include ROOT_PATH.'/template/banner.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div id="sidebar" class="sidebar col-md-2 pe-0">
            <button id="closeSidebar" class="close-btn">&times;</button>
            <?php include ROOT_PATH.'/template/sidebar2.php'; ?>
        </div>
        <div id="mainCol" class="col-md-10 col-12">
            <?= isset($error) ? $error : ''; ?>

            <!-- Yeni sevkiyat ekleme modalı (araç seçimi butonla belirlenir) -->
            <div id="sevkiyat-add-modal" class="modal">
                <span class="close" onclick="closeModal()">&times;</span>
                <h4><b>Yeni Sevkiyat Kaydı</b></h4>
                <form action="" method="POST" class="mt-3">
                    <input type="hidden" name="arac_id" id="sevkiyat-add-aracid" value="">
                    <div class="row">
                        <div class="col-12 mb-2 search-box">
                            <label><b>Firma</b></label>
                            <input name="firma" type="text" class="form-control form-control-sm" autocomplete="off" placeholder="Firma Adı"/>
                            <ul class="list-group liveresult" style="position: absolute; z-index: 10;"></ul>
                        </div>
                        <div class="col-12 mb-2">
                            <label><b>Kilo</b></label>
                            <input type="text" class="form-control form-control-sm" name="kilolar" placeholder="Kilo">
                        </div>
                        <div class="col-12 mb-2">
                            <label><b>Açıklama</b></label>
                            <textarea class="form-control form-control-sm" name="aciklama" rows="2" placeholder="Sevkiyat ile alakalı açıklama girebilirsiniz."></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" name="manuelSevkiyatKaydet" class="btn btn-primary btn-sm w-100">Kaydet</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Toplu sil onay modalı -->
            <div id="toplusil-modal" class="modal">
                <span class="close" onclick="closeModal()">&times;</span>
                <h5 class="mb-3"><b>Bu araca ait sevkiyat planını toplu olarak silmek istediğinize emin misiniz?</b></h5>
                <form action="" method="POST" class="mt-3">
                    <input type="hidden" name="arac_id" id="toplusil-aracid" value="">
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" onclick="closeModal()">Vazgeç</button>
                        <button type="submit" name="sevkiyattoplusil" class="btn btn-danger">Toplu Sil</button>
                    </div>
                </form>
            </div>

            <!-- Kaldır onay modalı -->
            <div id="kaldir-modal" class="modal">
                <span class="close" onclick="closeModal()">&times;</span>
                <h5 class="mb-3"><b>Sevkiyat listesinden kaldırmak istediğinize emin misiniz?</b></h5>
                <form action="" method="POST" class="mt-3">
                    <input type="hidden" name="sevkiyat_id" id="kaldir-sevkiyatid" value="">
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" onclick="closeModal()">Vazgeç</button>
                        <button type="submit" name="sevkiyatplanisil" class="btn btn-danger">Kaldır</button>
                    </div>
                </form>
            </div>

            <button id="menuToggleBtn" type="button" class="btn btn-outline-primary btn-sm d-md-none me-2 mb-2">
                <i class="fas fa-bars"></i> Menü
            </button>

            <div class="row" style="padding-top: 10px; padding-bottom: 10px;">
            <?php
            $column = count($araclar) > 0 ? 12 / count($araclar) : 12;
            foreach ($araclar as $key => $arac):
            ?>
                <div class="col-12 col-md-<?= (int)$column ?> mb-3">
                    <div class="row pb-1 align-items-center">
                        <div class="col-12 col-md-5">
                            <h5 class="mb-0"><?= htmlspecialchars($arac->name) ?> Sevkiyat Planı <small><?= date('d/m/Y'); ?></small></h5>
                        </div>
                        <div class="col-12 col-md-7">
                            <div class="d-flex flex-wrap gap-1 justify-content-end mt-2">
                                <button type="button" class="btn btn-primary btn-sm open-add-modal" data-aracid="<?= (int)$arac->id ?>">
                                    <i class="fas fa-plus me-1"></i> Yeni Kayıt Ekle
                                </button>
                                <a href="/aracsevkiyat/<?= $arac->id ?>" target="_blank" class="btn btn-outline-primary btn-sm">Çıktı Al</a>
                                <button type="button" class="btn btn-outline-danger btn-sm toplusil-btn" data-aracid="<?= (int)$arac->id ?>">Toplu Sil</button>
                            </div>
                        </div>
                    </div>

                    <?php if (isset($sevkiyatGruplari[$arac->id])): ?>
                        <?php foreach ($sevkiyatGruplari[$arac->id] as $sevkiyat):
                            $filtered = array_filter($clients, fn($f) => $f->id == $sevkiyat->firma_id);
                            $firma = reset($filtered);
                            $firmaAdi = $firma ? $firma->name : '';
                            $firmaAdres = $firma ? $firma->address : '';
                            $firmaPhone = $firma ? $firma->phone : '';
                            $kilolar = $sevkiyat->kilolar;
                            $toplamkg = 0;
                            if (strpos($kilolar, ',') !== false) {
                                $kiloArray = explode(",", $kilolar);
                                foreach ($kiloArray as $k) { $toplamkg += (float)$k; }
                            } else {
                                $toplamkg = $kilolar;
                            }
                        ?>
                            <div class="<?= $key % 2 == 0 ? 'sevkCardBlue' : 'sevkCardGreen' ?>">
                                <a href="#" class="text-dark text-decoration-none toggle-detail" data-target="firmakart<?= $sevkiyat->id ?>" onclick="return false;">
                                    <b>Firma Adı : </b><?= htmlspecialchars($firmaAdi) ?>
                                </a>
                                <div class="row">
                                    <div class="col-6">
                                        <b>Kilo : </b><?= $toplamkg ?>
                                    </div>
                                    <div class="col-6 text-end">
                                        <button type="button" class="btn btn-secondary btn-sm kaldir-btn" data-sevkiyatid="<?= (int)$sevkiyat->id ?>">Kaldır</button>
                                    </div>
                                </div>
                                <div id="firmakart<?= $sevkiyat->id ?>" style="display: none;">
                                    <b>Firma Tel : </b><?= htmlspecialchars($firmaPhone) ?><br/>
                                    <b>Firma Adres : </b><?= htmlspecialchars($firmaAdres) ?>
                                    <form action="" method="POST" class="mt-2">
                                        <div class="row">
                                            <div class="col-md-9 col-12">
                                                <input type="text" name="aciklama" class="form-control form-control-sm" value="<?= htmlspecialchars($sevkiyat->aciklama) ?>" placeholder="Sevkiyat açıklaması giriniz.">
                                            </div>
                                            <div class="col-md-3 col-12">
                                                <input type="hidden" name="sevkiyat_id" value="<?= $sevkiyat->id ?>">
                                                <button type="submit" class="btn btn-sm btn-primary w-100" name="sevkiyatguncelle">Kaydet</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted small">Bu araca atanmış sevkiyat bulunmuyor.</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <?php if (empty($araclar)): ?>
                <div class="col-12">
                    <p class="text-muted">Nakliye aracı tanımlı değil.</p>
                </div>
            <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include ROOT_PATH.'/template/script.php'; ?>
<script>
(function() {
    var menuToggle = document.getElementById('menuToggleBtn');
    var sidebar = document.getElementById('sidebar');
    var mainCol = document.getElementById('mainCol');
    var closeSidebar = document.getElementById('closeSidebar');
    if (menuToggle) menuToggle.addEventListener('click', function() {
        sidebar.classList.toggle('sidebar-open');
        mainCol.classList.toggle('sidebar-open');
    });
    if (closeSidebar) closeSidebar.addEventListener('click', function() {
        sidebar.classList.remove('sidebar-open');
        mainCol.classList.remove('sidebar-open');
    });

    document.querySelectorAll('.open-add-modal').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var aracId = this.getAttribute('data-aracid') || '';
            var el = document.getElementById('sevkiyat-add-aracid');
            if (el) el.value = aracId;
            if (typeof openModal === 'function') openModal('sevkiyat-add-modal');
        });
    });

    document.querySelectorAll('.toplusil-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var aracId = this.getAttribute('data-aracid') || '';
            var el = document.getElementById('toplusil-aracid');
            if (el) el.value = aracId;
            if (typeof openModal === 'function') openModal('toplusil-modal');
        });
    });

    document.querySelectorAll('.kaldir-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = this.getAttribute('data-sevkiyatid') || '';
            var el = document.getElementById('kaldir-sevkiyatid');
            if (el) el.value = id;
            if (typeof openModal === 'function') openModal('kaldir-modal');
        });
    });

    document.querySelectorAll('.toggle-detail').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            var id = this.getAttribute('data-target');
            var el = id ? document.getElementById(id) : null;
            if (el) el.style.display = el.style.display === 'none' ? 'block' : 'none';
        });
    });
})();
</script>
</body>
</html>
