<?php

	require_once __DIR__.'/../config/init.php';

	if (!isLoggedIn()) {
		header("Location:/login");
		exit();
	}

	if (isset($_POST['faturasikesilenegerial'])) {
		$sevkiyatID = guvenlik($_POST['sevkiyatID']);
		$query = $db->prepare("UPDATE sevkiyat SET durum = ? WHERE id = ?");
		$update = $query->execute(array('2', $sevkiyatID));
		header("Location:/sevkiyatplan");
		exit();
	}

	$sevkiyatListesi = $db->query("SELECT * FROM sevkiyat WHERE durum = '3' AND sirket_id = '{$user->company_id}' AND silik = '0' AND manuel = '0' ORDER BY saniye DESC LIMIT 200")->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html>
  <head>
    <title>Sevkiyat Arşivi</title>
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
              <button id="menuToggleBtn" type="button" class="btn btn-outline-primary btn-sm d-md-none me-2">
                <i class="fas fa-bars"></i> Menü
              </button>
              <h3 class="d-none d-md-block mb-0 mt-2" style="font-weight: bold;">Sevkiyat Arşivi</h3>
            </div>
            <div>
              <a href="/sevkiyatplan" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-2"></i> Geri Dön
              </a>
            </div>
          </div>

          <!-- Geri Al onay modalı -->
          <div id="gerial-modal" class="modal">
            <span class="close" onclick="closeModal()">&times;</span>
            <h5 class="mb-3"><b>Bu sevkiyatı geri almak istediğinize emin misiniz?</b></h5>
            <form action="" method="POST" class="mt-3">
              <input type="hidden" name="sevkiyatID" id="gerial-sevkiyatid" value="">
              <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Vazgeç</button>
                <button type="submit" name="faturasikesilenegerial" class="btn btn-primary">Geri Al</button>
              </div>
            </form>
          </div>

          <div class="table-responsive mt-3">
            <table class="table table-bordered td-vertical-align-middle">
              <thead>
                <tr style="background-color:#f8f9fa; color:#003566;">
                  <th>Firma</th>
                  <th>Ürünler</th>
                  <th>Kilo</th>
                  <th>Oluşturan</th>
                  <th>Hazırlayan</th>
                  <th>Faturalayan</th>
                  <th>Tarih / Saat</th>
                  <th>İşlemler</th>
                </tr>
              </thead>
              <tbody>
              <?php
              if ($sevkiyatListesi) {
                  $sevkTipleri = ['Müşteri Çağlayan', 'Müşteri Alkop', 'Tarafımızca sevk', 'Ambara tarafımızca sevk'];
                  foreach ($sevkiyatListesi as $sevkiyat) {
                      $sevkiyatID = (int) $sevkiyat['id'];
                      $urunler = $sevkiyat['urunler'];
                      $urunArray = explode(",", $urunler);
                      $firmaId = $sevkiyat['firma_id'];
                      $firmaAdi = getClientName($firmaId);
                      $adetler = $sevkiyat['adetler'];
                      $adetArray = explode(",", $adetler);
                      $kilolar = $sevkiyat['kilolar'];
                      $kiloArray = [];
                      $toplamkg = 0;
                      if (strpos($kilolar, ',') !== false) {
                          $kiloArray = array_map('trim', explode(",", $kilolar));
                          foreach ($kiloArray as $k) {
                              if (is_numeric($k)) $toplamkg += (float) $k;
                          }
                      } else {
                          $toplamkg = is_numeric($kilolar) ? (float) $kilolar : 0;
                      }
                      $fiyatlar = $sevkiyat['fiyatlar'];
                      $fiyatArray = explode("-", $fiyatlar);
                      $olusturan = getUsername($sevkiyat['olusturan']);
                      $hazirlayan = getUsername($sevkiyat['hazirlayan']);
                      $faturaci = getUsername($sevkiyat['faturaci']);
                      $sevkTipi = (int) ($sevkiyat['sevk_tipi'] ?? 0);
                      $aciklama = guvenlik($sevkiyat['aciklama'] ?? '');
                      $saniye = $sevkiyat['saniye'];
                      $tarih = getdmY($saniye);
                      $saat = getHis($saniye);
              ?>
                <tr>
                  <td class="text-truncate" style="max-width: 160px;" title="<?= htmlspecialchars($firmaAdi) ?>"><?= htmlspecialchars($firmaAdi) ?></td>
                  <td>
                    <button type="button" class="btn btn-outline-secondary btn-sm py-0 toggle-detail" data-target="detail-<?= $sevkiyatID ?>" aria-expanded="false">Ürünler</button>
                  </td>
                  <td><?= strpos($kilolar, ',') !== false ? $toplamkg : $kilolar ?></td>
                  <td><?= htmlspecialchars($olusturan) ?></td>
                  <td><?= htmlspecialchars($hazirlayan) ?></td>
                  <td><?= htmlspecialchars($faturaci) ?></td>
                  <td><?= $tarih ?> / <?= $saat ?></td>
                  <td>
                    <div class="d-flex flex-wrap gap-1">
                      <button type="button" class="btn btn-primary btn-sm gerial-btn" title="Geri al" data-sevkiyatid="<?= $sevkiyatID ?>"><i class="fas fa-undo"></i></button>
                      <a href="/sevkiyatformu/<?= $sevkiyatID ?>" target="_blank" class="btn btn-secondary btn-sm" title="Yazdır"><i class="fas fa-print"></i></a>
                    </div>
                  </td>
                </tr>
                <tr id="detail-<?= $sevkiyatID ?>" class="detail-row bg-light" style="display: none;">
                  <td colspan="8" class="p-3">
                    <div class="small">
                      <table class="table table-sm table-bordered mb-2">
                        <thead>
                          <tr>
                            <th>Ürün</th>
                            <th>Cinsi</th>
                            <th>Adet</th>
                            <th>Kg</th>
                            <th>Fiyat</th>
                          </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($urunArray as $key => $urunId) {
                            $urun = getUrunInfo($urunId);
                            if ($urun !== false) {
                                $kgVal = (strpos($kilolar, ',') !== false && isset($kiloArray[$key])) ? $kiloArray[$key] : '';
                                $fiyatVal = isset($fiyatArray[$key]) ? $fiyatArray[$key] . ' TL' : '';
                        ?>
                          <tr>
                            <td><?= htmlspecialchars($urun['urun_adi']) ?></td>
                            <td><?= htmlspecialchars(getCategoryShortName($urun['kategori_bir'])) ?></td>
                            <td><?= htmlspecialchars($adetArray[$key] ?? '') ?></td>
                            <td><?= $kgVal ?></td>
                            <td><?= $fiyatVal ?></td>
                          </tr>
                        <?php
                            }
                        }
                        ?>
                        </tbody>
                      </table>
                      <div class="mb-1"><b>Toplam:</b> <?= (strpos($kilolar, ',') !== false ? $toplamkg : $kilolar) ?> KG</div>
                      <div class="mb-1"><b>Sevk Tipi:</b> <?= $sevkTipleri[$sevkTipi] ?? '-' ?></div>
                      <div><b>Açıklama:</b> <?= htmlspecialchars($aciklama) ?></div>
                    </div>
                  </td>
                </tr>
              <?php
                  }
              } else {
              ?>
                <tr>
                  <td colspan="8" class="text-center text-muted">Arşivde sevkiyat bulunamadı.</td>
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
        document.querySelectorAll('.toggle-detail').forEach(function(btn) {
          btn.addEventListener('click', function() {
            var id = this.getAttribute('data-target');
            var row = id ? document.getElementById(id) : null;
            if (row) {
              var show = row.style.display === 'none';
              row.style.display = show ? 'table-row' : 'none';
              this.setAttribute('aria-expanded', show ? 'true' : 'false');
              this.textContent = show ? 'Ürünler ▲' : 'Ürünler';
            }
          });
        });
        document.querySelectorAll('.gerial-btn').forEach(function(btn) {
          btn.addEventListener('click', function() {
            document.getElementById('gerial-sevkiyatid').value = this.dataset.sevkiyatid || '';
            if (typeof openModal === 'function') openModal('gerial-modal');
          });
        });
      })();
    </script>
  </body>
</html>
