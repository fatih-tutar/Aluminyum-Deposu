<?php

	require_once __DIR__.'/../config/init.php';

	if (!isLoggedIn()) {
		header("Location:/login");
		exit();
	}

	if ($user->type == '0' || $user->type == '1') {
		header("Location:/");
		exit();
	}

	if ($user->type != '3') {

		if (isset($_POST['teklifkaydet'])) {
			$musteri = guvenlik($_POST['musteri']);
			$ilgilikisi = guvenlik($_POST['ilgilikisi']);
			$urunmiktar = guvenlik($_POST['urunmiktar']);
			$fabrika = guvenlik($_POST['fabrika']);
			$aciklama = guvenlik($_POST['aciklama']);
			$query = $db->prepare("INSERT INTO teklif_listesi SET musteri = ?, ilgilikisi = ?, urunmiktar = ?, fabrika = ?, aciklama = ?, teklifveren = ?, tarih = ?, silik = ?");
			$insert = $query->execute(array($musteri, $ilgilikisi, $urunmiktar, $fabrika, $aciklama, $user->id, time(), '0'));
			header("Location:/tekliflistesi");
			exit();
		}

		if (isset($_POST['teklifguncelle'])) {
			$teklifid = guvenlik($_POST['teklifid']);
			$musteri = guvenlik($_POST['musteri']);
			$ilgilikisi = guvenlik($_POST['ilgilikisi']);
			$urunmiktar = guvenlik($_POST['urunmiktar']);
			$fiyat = guvenlik($_POST['fiyat']);
			$fabrika = guvenlik($_POST['fabrika']);
			$fabrikafiyat = guvenlik($_POST['fabrikafiyat']);
			$aciklama = guvenlik($_POST['aciklama']);
			$query = $db->prepare("UPDATE teklif_listesi SET musteri = ?, ilgilikisi = ?, urunmiktar = ?, fiyat = ?, fabrika = ?, fabrikafiyat = ?, teklifveren = ?, aciklama = ?, tarih = ? WHERE teklifid = ?");
			$guncelle = $query->execute(array($musteri, $ilgilikisi, $urunmiktar, $fiyat, $fabrika, $fabrikafiyat, $user->id, $aciklama, time(), $teklifid));
			header("Location:/teklifarsivi");
			exit();
		}

		if (isset($_POST['arsivsil'])) {
			$teklifid = guvenlik($_POST['teklifid']);
			$query = $db->prepare("UPDATE teklif_listesi SET silik = ? WHERE teklifid = ?");
			$guncelle = $query->execute(array('3', $teklifid));
			header("Location:/teklifarsivi");
			exit();
		}

		if (isset($_POST['listeyegonder'])) {
			$teklifid = guvenlik($_POST['teklifid']);
			$query = $db->prepare("UPDATE teklif_listesi SET silik = ? WHERE teklifid = ?");
			$guncelle = $query->execute(array('0', $teklifid));
			header("Location:/tekliflistesi");
			exit();
		}
	}

	$tekliflistesi = $db->query("SELECT * FROM teklif_listesi WHERE silik IN ('1', '2') ORDER BY teklifid DESC")->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html>
  <head>
    <title>Teklif Arşivi</title>
    <?php include ROOT_PATH.'/template/head.php'; ?>
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
          <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <div class="d-flex align-items-center">
              <button id="menuToggleBtn" class="btn btn-outline-primary btn-sm d-md-none me-2">
                <i class="fas fa-bars"></i> Menü
              </button>
              <h3 class="d-none d-md-block mb-0 mt-2" style="font-weight: bold;">Teklif Arşivi</h3>
            </div>
            <div class="d-flex gap-2">
              <a href="/tekliflistesi" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-2"></i> Geri Dön
              </a>
              <button type="button" class="btn btn-primary btn-sm" onclick="openModal('teklif-add-modal')" style="background-color: #003566; border-color: #003566;">
                <i class="fas fa-plus me-2"></i> Yeni Teklif
              </button>
            </div>
          </div>

          <!-- Yeni Teklif Ekleme Modalı -->
          <div id="teklif-add-modal" class="modal">
            <span class="close" onclick="closeModal()">&times;</span>
            <h4><b>Yeni Teklif Kaydı</b></h4>
            <form action="" method="POST" class="mt-3">
              <div class="row">
                <div class="col-12 mb-2">
                  <label><b>Müşteri İsmi</b></label>
                  <input type="text" name="musteri" class="form-control form-control-sm" placeholder="Müşteri İsmi">
                </div>
                <div class="col-12 mb-2">
                  <label><b>İlgili Kişi</b></label>
                  <input type="text" name="ilgilikisi" class="form-control form-control-sm" placeholder="İlgili Kişi">
                </div>
                <div class="col-12 mb-2">
                  <label><b>Ürün / Miktar</b></label>
                  <input type="text" name="urunmiktar" class="form-control form-control-sm" placeholder="Ürün / Miktar">
                </div>
                <div class="col-12 mb-2">
                  <label><b>Fabrika</b></label>
                  <input type="text" name="fabrika" class="form-control form-control-sm" placeholder="Fabrika">
                </div>
                <div class="col-12 mb-2">
                  <label><b>Açıklama</b></label>
				  <textarea name="aciklama" class="form-control form-control-sm" placeholder="Buraya açıklama girebilirsiniz."></textarea>
                </div>
                <div class="col-12">
                  <button type="submit" name="teklifkaydet" class="btn btn-primary btn-sm w-100">Kaydet</button>
                </div>
              </div>
            </form>
          </div>

          <!-- Sil onay modalı -->
          <div id="arsivsil-modal" class="modal">
            <span class="close" onclick="closeModal()">&times;</span>
            <h5 class="mb-3"><b>Bu kaydı silmek istediğinize emin misiniz?</b></h5>
            <form action="" method="POST" class="mt-3">
              <input type="hidden" name="teklifid" id="arsivsil-teklifid" value="">
              <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Vazgeç</button>
                <button type="submit" name="arsivsil" class="btn btn-danger">Sil</button>
              </div>
            </form>
          </div>

          <!-- Listeye geri al onay modalı -->
          <div id="listeyegonder-modal" class="modal">
            <span class="close" onclick="closeModal()">&times;</span>
            <h5 class="mb-3"><b>Bu kaydı listeye geri almak istediğinize emin misiniz?</b></h5>
            <form action="" method="POST" class="mt-3">
              <input type="hidden" name="teklifid" id="listeyegonder-teklifid" value="">
              <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Vazgeç</button>
                <button type="submit" name="listeyegonder" class="btn btn-info">Listeye Geri Al</button>
              </div>
            </form>
          </div>

          <!-- Teklif Düzenleme Modalı -->
          <div id="teklif-edit-modal" class="modal">
            <span class="close" onclick="closeModal()">&times;</span>
            <h4><b>Teklif Düzenle</b></h4>
            <form action="" method="POST" id="teklif-edit-form" class="mt-3">
              <input type="hidden" name="teklifid" id="edit-teklifid">
              <div class="row">
                <div class="col-12 mb-2">
                  <label><b>Müşteri İsmi</b></label>
                  <input type="text" name="musteri" id="edit-musteri" class="form-control form-control-sm">
                </div>
                <div class="col-12 mb-2">
                  <label><b>İlgili Kişi</b></label>
                  <input type="text" name="ilgilikisi" id="edit-ilgilikisi" class="form-control form-control-sm">
                </div>
                <div class="col-12 mb-2">
                  <label><b>Ürün / Miktar</b></label>
                  <input type="text" name="urunmiktar" id="edit-urunmiktar" class="form-control form-control-sm">
                </div>
                <div class="col-12 mb-2">
                  <label><b>Fiyat</b></label>
                  <input type="text" name="fiyat" id="edit-fiyat" class="form-control form-control-sm">
                </div>
                <div class="col-12 mb-2">
                  <label><b>Fabrika</b></label>
                  <input type="text" name="fabrika" id="edit-fabrika" class="form-control form-control-sm">
                </div>
                <div class="col-12 mb-2">
                  <label><b>Fabrika Fiyatı</b></label>
                  <input type="text" name="fabrikafiyat" id="edit-fabrikafiyat" class="form-control form-control-sm">
                </div>
                <div class="col-12 mb-2">
                  <label><b>Açıklama</b></label>
				  <textarea name="aciklama" id="edit-aciklama" class="form-control form-control-sm"></textarea>
                </div>
                <div class="col-12">
                  <button type="submit" name="teklifguncelle" class="btn btn-primary btn-sm w-100">Güncelle</button>
                </div>
              </div>
            </form>
          </div>

          <!-- Tablo -->
          <div class="table-responsive mt-3">
            <table class="table table-bordered td-vertical-align-middle">
              <thead>
                <tr style="background-color:#f8f9fa; color:#003566;">
                  <th>Müşteri İsmi</th>
                  <th>İlgili Kişi</th>
                  <th>Ürün / Miktar</th>
                  <th>Fiyat</th>
                  <th>Fabrika</th>
                  <th>Fabrika Fiyatı</th>
                  <th>Teklif Veren</th>
                  <th>Tarih</th>
                  <th>Durum</th>
                  <th>İşlemler</th>
                </tr>
              </thead>
              <tbody>
              <?php
              if ($tekliflistesi) {
                  foreach ($tekliflistesi as $tlc) {
                      $teklifid = guvenlik($tlc['teklifid']);
                      $musteri = guvenlik($tlc['musteri']);
                      $ilgilikisi = guvenlik($tlc['ilgilikisi']);
                      $urunmiktar = guvenlik($tlc['urunmiktar']);
                      $fiyat = guvenlik($tlc['fiyat'] ?? '');
                      $fabrika = guvenlik($tlc['fabrika'] ?? '');
                      $fabrikafiyat = guvenlik($tlc['fabrikafiyat'] ?? '');
                      $teklifveren = uyeadcek($tlc['teklifveren']);
                      $tekliftarih = date("d-m-Y", (int) $tlc['tarih']);
                      $aciklama = guvenlik($tlc['aciklama'] ?? '');
                      $silik = (int) ($tlc['silik'] ?? 0);
              ?>
                <tr class="<?= $silik == 1 ? 'table-success' : ($silik == 2 ? 'table-danger' : '') ?>">
                  <td class="text-truncate" style="max-width: 140px;" title="<?= $musteri ?>"><?= $musteri ?></td>
                  <td class="text-truncate" style="max-width: 120px;" title="<?= $ilgilikisi ?>"><?= $ilgilikisi ?></td>
                  <td class="text-truncate" style="max-width: 180px;" title="<?= $urunmiktar ?>"><?= $urunmiktar ?></td>
                  <td><?= $fiyat ?></td>
                  <td class="text-truncate" style="max-width: 100px;" title="<?= $fabrika ?>"><?= $fabrika ?></td>
                  <td><?= $fabrikafiyat ?></td>
                  <td><?= $teklifveren ?></td>
                  <td><?= $tekliftarih ?></td>
                  <td>
                    <?php if ($silik == 1): ?>
                      <span class="badge bg-success">Arşiv +</span>
                    <?php else: ?>
                      <span class="badge bg-danger">Arşiv −</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <div class="d-flex flex-wrap gap-1">
                      <button type="button" class="btn btn-primary btn-sm teklif-edit-btn" title="Düzenle"
                        data-teklifid="<?= (int)$teklifid ?>"
                        data-musteri="<?= htmlspecialchars($musteri, ENT_QUOTES, 'UTF-8') ?>"
                        data-ilgilikisi="<?= htmlspecialchars($ilgilikisi, ENT_QUOTES, 'UTF-8') ?>"
                        data-urunmiktar="<?= htmlspecialchars($urunmiktar, ENT_QUOTES, 'UTF-8') ?>"
                        data-fiyat="<?= htmlspecialchars($fiyat, ENT_QUOTES, 'UTF-8') ?>"
                        data-fabrika="<?= htmlspecialchars($fabrika, ENT_QUOTES, 'UTF-8') ?>"
                        data-fabrikafiyat="<?= htmlspecialchars($fabrikafiyat, ENT_QUOTES, 'UTF-8') ?>"
                        data-aciklama="<?= htmlspecialchars($aciklama, ENT_QUOTES, 'UTF-8') ?>">
                        <i class="fas fa-pen"></i>
                      </button>
                      <button type="button" class="btn btn-danger btn-sm arsivsil-btn" title="Sil" data-teklifid="<?= (int)$teklifid ?>"><i class="fas fa-trash"></i></button>
                      <button type="button" class="btn btn-info btn-sm listeyegonder-btn" title="Listeye geri al" data-teklifid="<?= (int)$teklifid ?>"><i class="fas fa-undo"></i></button>
                    </div>
                  </td>
                </tr>
              <?php
                  }
              } else {
              ?>
                <tr>
                  <td colspan="10" class="text-center text-muted">Arşivde kayıt bulunamadı.</td>
                </tr>
              <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <br/><br/><br/><br/><br/><br/>

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
        document.querySelectorAll('.teklif-edit-btn').forEach(function(btn) {
          btn.addEventListener('click', function() {
            var d = this.dataset;
            document.getElementById('edit-teklifid').value = d.teklifid || '';
            document.getElementById('edit-musteri').value = d.musteri || '';
            document.getElementById('edit-ilgilikisi').value = d.ilgilikisi || '';
            document.getElementById('edit-urunmiktar').value = d.urunmiktar || '';
            document.getElementById('edit-fiyat').value = d.fiyat || '';
            document.getElementById('edit-fabrika').value = d.fabrika || '';
            document.getElementById('edit-fabrikafiyat').value = d.fabrikafiyat || '';
            document.getElementById('edit-aciklama').value = d.aciklama || '';
            if (typeof openModal === 'function') openModal('teklif-edit-modal');
          });
        });
        document.querySelectorAll('.arsivsil-btn').forEach(function(btn) {
          btn.addEventListener('click', function() {
            document.getElementById('arsivsil-teklifid').value = this.dataset.teklifid || '';
            if (typeof openModal === 'function') openModal('arsivsil-modal');
          });
        });
        document.querySelectorAll('.listeyegonder-btn').forEach(function(btn) {
          btn.addEventListener('click', function() {
            document.getElementById('listeyegonder-teklifid').value = this.dataset.teklifid || '';
            if (typeof openModal === 'function') openModal('listeyegonder-modal');
          });
        });
      })();
    </script>
  </body>
</html>
