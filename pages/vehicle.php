<?php 
	require_once __DIR__.'/../config/init.php';

    if (isset($_POST['add_vehicle'])) {
        // Formdan gelen verileri alalım
        $name = $_POST['name'];
        $licensePlate = $_POST['license_plate'];
        $cascoEndDate = $_POST['casco_end_date'];
        $insuranceEndDate = $_POST['insurance_end_date'];
        $inspectionDate = $_POST['inspection_date'];
        $driver = $_POST['driver'];
        $description = $_POST['description'];
        $time = time();  // Anlık timestamp değeri
        $isDeleted = 0;  // Varsayılan değer
        $isTransport = 0;

        // Dosya yükleme işlemleri
        $cascoPdf = '';
        $insurancePdf = '';
        $registrationPdf = '';

        // Dosyaların kaydedileceği klasör
        $uploadDir = 'files/vehicles/';

        // Kasko PDF dosyası yüklendiyse
        if (isset($_FILES['casco_pdf']) && $_FILES['casco_pdf']['error'] == 0) {
          $cascoFileName = uniqid() . "_" . basename($_FILES['casco_pdf']['name']);
          $cascoTarget   = $uploadDir . $cascoFileName;
          if (move_uploaded_file($_FILES['casco_pdf']['tmp_name'], $cascoTarget)) {
              // Veritabanında sadece dosya adını tut
              $cascoPdf = $cascoFileName;
          }
        }

        // Sigorta PDF dosyası yüklendiyse
        if (isset($_FILES['insurance_pdf']) && $_FILES['insurance_pdf']['error'] == 0) {
          $insuranceFileName = uniqid() . "_" . basename($_FILES['insurance_pdf']['name']);
          $insuranceTarget   = $uploadDir . $insuranceFileName;
          if (move_uploaded_file($_FILES['insurance_pdf']['tmp_name'], $insuranceTarget)) {
              $insurancePdf = $insuranceFileName;
          }
        }

        // Ruhsat PDF dosyası yüklendiyse
        if (isset($_FILES['registration_pdf']) && $_FILES['registration_pdf']['error'] == 0) {
            $registrationFileName = uniqid() . "_" . basename($_FILES['registration_pdf']['name']);
            $registrationTarget   = $uploadDir . $registrationFileName;
            if (move_uploaded_file($_FILES['registration_pdf']['tmp_name'], $registrationTarget)) {
                $registrationPdf = $registrationFileName;
            }
        }

        $query = $db->prepare("INSERT INTO vehicles SET name = ?, license_plate = ?, casco_end_date = ?, insurance_end_date = ?, casco_pdf = ?, insurance_pdf = ?, inspection_date = ?, driver = ?, registration_pdf = ?, description = ?, is_transport = ?, time = ?, is_deleted = ?");

        $insert = $query->execute(array($name, $licensePlate, $cascoEndDate, $insuranceEndDate, $cascoPdf, $insurancePdf, $inspectionDate, $driver, $registrationPdf, $description, $isTransport, $time, $isDeleted));

        header("Location:/vehicle");
        exit();
    }

    if(isset($_POST['delete_vehicle'])) {
      $id = guvenlik($_POST['id']);
      $query = $db->prepare("UPDATE vehicles SET is_deleted = ? WHERE id = ?");
      $update = $query->execute(array('1',$id));
      header("Location:/vehicle");
      exit();
    }

    if (isset($_POST['edit_vehicle'])) {
        // POST verilerini al
        $id = $_POST['id'];
        $name = $_POST['name'];
        $licensePlate = $_POST['license_plate'];
        $driver = $_POST['driver'];
        $cascoEndDate = $_POST['casco_end_date'];
        $insuranceEndDate = $_POST['insurance_end_date'];
        $inspectionDate = $_POST['inspection_date'];
        $description = $_POST['description'];
        $isTransport = '0';
        if(isset($_POST['is_transport'])){ $isTransport = '1'; }

        // Mevcut kayıtları al (eski PDF'leri silebilmek için)
        $existing = $db->prepare("SELECT casco_pdf, insurance_pdf, registration_pdf FROM vehicles WHERE id = ?");
        $existing->execute([$id]);
        $existingFiles = $existing->fetch(PDO::FETCH_ASSOC) ?: ['casco_pdf' => null, 'insurance_pdf' => null, 'registration_pdf' => null];

        // Dosya yükleme işlemleri
        $uploads = [];
        $upload_dir = 'files/vehicles/';

        if (!empty($_FILES['casco_pdf']['name'])) {
          $unique_name = uniqid() . '-' . basename($_FILES['casco_pdf']['name']);
          $target      = $upload_dir . $unique_name;
          if (move_uploaded_file($_FILES['casco_pdf']['tmp_name'], $target)) {
              // eski dosyayı sil
              $old = $existingFiles['casco_pdf'] ?? '';
              if (!empty($old)) {
                  $candidates = [];
                  if (strpos($old, 'files/') === 0) {
                      $candidates[] = $old;
                  }
                  $candidates[] = $upload_dir . $old;
                  foreach ($candidates as $path) {
                      if (file_exists($path)) {
                          @unlink($path);
                          break;
                      }
                  }
              }
              // sadece dosya adını sakla
              $uploads['casco_pdf'] = $unique_name;
          }
        }

        if (!empty($_FILES['insurance_pdf']['name'])) {
          $unique_name = uniqid() . '-' . basename($_FILES['insurance_pdf']['name']);
          $target      = $upload_dir . $unique_name;
          if (move_uploaded_file($_FILES['insurance_pdf']['tmp_name'], $target)) {
              $old = $existingFiles['insurance_pdf'] ?? '';
              if (!empty($old)) {
                  $candidates = [];
                  if (strpos($old, 'files/') === 0) {
                      $candidates[] = $old;
                  }
                  $candidates[] = $upload_dir . $old;
                  foreach ($candidates as $path) {
                      if (file_exists($path)) {
                          @unlink($path);
                          break;
                      }
                  }
              }
              $uploads['insurance_pdf'] = $unique_name;
          }
        }

        if (!empty($_FILES['registration_pdf']['name'])) {
          $unique_name = uniqid() . '-' . basename($_FILES['registration_pdf']['name']);
          $target      = $upload_dir . $unique_name;
          if (move_uploaded_file($_FILES['registration_pdf']['tmp_name'], $target)) {
              $old = $existingFiles['registration_pdf'] ?? '';
              if (!empty($old)) {
                  $candidates = [];
                  if (strpos($old, 'files/') === 0) {
                      $candidates[] = $old;
                  }
                  $candidates[] = $upload_dir . $old;
                  foreach ($candidates as $path) {
                      if (file_exists($path)) {
                          @unlink($path);
                          break;
                      }
                  }
              }
              $uploads['registration_pdf'] = $unique_name;
          }
        }

        // SQL sorgusunu oluştur
        $sql = "UPDATE vehicles SET 
                  name = ?, 
                  license_plate = ?, 
                  driver = ?, 
                  casco_end_date = ?, 
                  insurance_end_date = ?, 
                  inspection_date = ?, 
                  description = ?,
                  is_transport = ?";

        // Dosya alanlarını kontrol et ve SQL sorgusuna ekle
        $params = [$name, $licensePlate, $driver, $cascoEndDate, $insuranceEndDate, $inspectionDate, $description, $isTransport];

        foreach ($uploads as $key => $fileName) {
          $sql .= ", $key = ?";
          $params[] = $fileName;
        }

        $sql .= " WHERE id = ?";
        $params[] = $id;

        // Sorguyu çalıştır
        $stmt = $db->prepare($sql);
        $result = $stmt->execute($params);

        if ($result) {
            header("Location:/vehicle");
            exit();
        } else {
            $error = '<br/><div class="alert alert-danger" role="alert">Bir hata oluştu, araç bilgileri güncellenemedi.</div>';
        }
    }

    $vehicles = $db->query("SELECT * FROM vehicles WHERE is_deleted = '0'")->fetchAll(PDO::FETCH_OBJ);

?>
<!DOCTYPE html>
<html>
  <head>
    <title>Araçlar</title>
    <?php include ROOT_PATH.'/template/head.php'; ?>
  </head>
  <body class="body-white">
    <?php include ROOT_PATH.'/template/banner.php' ?>

    <div class="container-fluid">
      <div class="row">
        <div id="sidebar" class="sidebar col-md-2 pe-0">
          <button id="closeSidebar" class="close-btn">&times;</button>
          <?php include ROOT_PATH.'/template/sidebar2.php'; ?>
        </div>

        <div id="mainCol" class="col-md-10 col-12">
          <?= isset($error) ? $error : ''; ?>

          <div class="d-flex justify-content-between align-items-center mb-3">
            <button id="menuToggleBtn" class="btn btn-outline-primary btn-sm d-md-none">
              <i class="fas fa-bars"></i> Menü
            </button>
            <h3 class="d-none d-md-block" style="margin-top:.3rem; margin-bottom:0; font-weight: bold;">
              Araçlar
            </h3>
            <button class="btn btn-primary btn-sm" onclick="openModal('vehicle-add-modal')" style="background-color: #003566; border-color: #003566;">
              <i class="fas fa-plus me-2"></i> Yeni Araç
            </button>
          </div>

          <!-- Araç Ekleme Modalı -->
          <div id="vehicle-add-modal" class="modal">
            <span class="close" onclick="closeModal()">&times;</span>
            <h4><b>Araç Ekleme Formu</b></h4>
            <form action="" method="POST" enctype="multipart/form-data" class="mt-3">
              <div class="row">
                <div class="col-md-6 col-12 mb-2">
                  <label><b>Araç Adı</b></label>
                  <input type="text" name="name" class="form-control form-control-sm" placeholder="Araç Adı">
                </div>
                <div class="col-md-6 col-12 mb-2">
                  <label><b>Plaka</b></label>
                  <input type="text" name="license_plate" class="form-control form-control-sm" placeholder="Plaka">
                </div>
                <div class="col-md-6 col-12 mb-2">
                  <label><b>Kullanan Kişi</b></label>
                  <input type="text" name="driver" class="form-control form-control-sm" placeholder="Aracı kullanan kişi">
                </div>
                <div class="col-md-6 col-6 mb-2">
                  <label><b>Kasko Bitiş Tarihi</b></label>
                  <input type="date" name="casco_end_date" class="form-control form-control-sm">
                </div>
                <div class="col-md-6 col-6 mb-2">
                  <label><b>Sigorta Bitiş Tarihi</b></label>
                  <input type="date" name="insurance_end_date" class="form-control form-control-sm">
                </div>
                <div class="col-md-6 col-6 mb-2">
                  <label><b>Muayene Tarihi</b></label>
                  <input type="date" name="inspection_date" class="form-control form-control-sm">
                </div>
                <div class="col-md-6 col-6 mb-2">
                  <label><b>Kasko PDF</b></label>
                  <input type="file" name="casco_pdf" class="form-control form-control-sm">
                </div>
                <div class="col-md-6 col-6 mb-2">
                  <label><b>Sigorta PDF</b></label>
                  <input type="file" name="insurance_pdf" class="form-control form-control-sm">
                </div>
                <div class="col-md-6 col-6 mb-2">
                  <label><b>Ruhsat PDF</b></label>
                  <input type="file" name="registration_pdf" class="form-control form-control-sm">
                </div>
                <div class="col-12 mb-3">
                  <label><b>Açıklama</b></label>
                  <textarea name="description" class="form-control form-control-sm" rows="3" placeholder="Bu alana not girebilirsiniz."></textarea>
                </div>
                <div class="col-12">
                  <button type="submit" name="add_vehicle" class="btn btn-primary w-100 btn-sm">Araç Ekle</button>
                </div>
              </div>
            </form>
          </div>

          <!-- Araç Listesi -->
          <div class="table-responsive mt-3">
            <table class="table table-bordered td-vertical-align-middle">
              <thead>
                <tr style="background-color:#f8f9fa; color:#003566;">
                  <th>Araç Adı</th>
                  <th>Plaka</th>
                  <th>Kullanan Kişi</th>
                  <th>Kasko Bitiş</th>
                  <th>Sigorta Bitiş</th>
                  <th>Muayene Tarihi</th>
                  <th>Kasko PDF</th>
                  <th>Sigorta PDF</th>
                  <th>Ruhsat PDF</th>
                  <th>Açıklama</th>
                  <th>Nakliye</th>
                  <th>İşlemler</th>
                </tr>
              </thead>
              <tbody>
              <?php foreach ($vehicles as $vehicle): ?>
                <tr>
                  <td><?= guvenlik($vehicle->name) ?></td>
                  <td><?= guvenlik($vehicle->license_plate) ?></td>
                  <td><?= guvenlik($vehicle->driver) ?></td>
                  <td><?= guvenlik((new DateTime($vehicle->casco_end_date))->format('d/m/Y')) ?></td>
                  <td><?= guvenlik((new DateTime($vehicle->insurance_end_date))->format('d/m/Y')) ?></td>
                  <td><?= guvenlik((new DateTime($vehicle->inspection_date))->format('d/m/Y')) ?></td>
                  <td>
                    <?php
                      $cascoValue = $vehicle->casco_pdf;
                      $cascoUrl = $cascoValue
                        ? (strpos($cascoValue, 'files/') === 0 ? $cascoValue : 'files/vehicles/' . $cascoValue)
                        : '';
                    ?>
                    <?php if ($cascoUrl): ?>
                      <a href="<?= guvenlik($cascoUrl) ?>" target="_blank">Görüntüle</a>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php
                      $insuranceValue = $vehicle->insurance_pdf;
                      $insuranceUrl = $insuranceValue
                        ? (strpos($insuranceValue, 'files/') === 0 ? $insuranceValue : 'files/vehicles/' . $insuranceValue)
                        : '';
                    ?>
                    <?php if ($insuranceUrl): ?>
                      <a href="<?= guvenlik($insuranceUrl) ?>" target="_blank">Görüntüle</a>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php
                      $registrationValue = $vehicle->registration_pdf;
                      $registrationUrl = $registrationValue
                        ? (strpos($registrationValue, 'files/') === 0 ? $registrationValue : 'files/vehicles/' . $registrationValue)
                        : '';
                    ?>
                    <?php if ($registrationUrl): ?>
                      <a href="<?= guvenlik($registrationUrl) ?>" target="_blank">Görüntüle</a>
                    <?php endif; ?>
                  </td>
                  <td><?= guvenlik($vehicle->description) ?></td>
                  <td><?= $vehicle->is_transport == '1' ? 'Evet' : 'Hayır' ?></td>
                  <td>
                    <div class="d-flex">
                      <button class="btn btn-primary btn-sm me-2" onclick="openModal('edit-vehicle-<?= $vehicle->id ?>')">
                        <i class="fas fa-pen"></i>
                      </button>
                      <button class="btn btn-secondary btn-sm" onclick="openModal('delete-vehicle-<?= $vehicle->id ?>')">
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>

                    <!-- Araç Düzenleme Modalı -->
                    <div id="edit-vehicle-<?= $vehicle->id ?>" class="modal">
                      <span class="close" onclick="closeModal()">&times;</span>
                      <h4><b>Araç Düzenleme Formu</b></h4>
                      <form action="" method="POST" enctype="multipart/form-data" class="mt-3">
                        <div class="row">
                          <div class="col-md-6 col-12 mb-2">
                            <label><b>Araç Adı</b></label>
                            <input type="text" name="name" class="form-control form-control-sm" value="<?= $vehicle->name; ?>">
                          </div>
                          <div class="col-md-6 col-12 mb-2">
                            <label><b>Plaka</b></label>
                            <input type="text" name="license_plate" class="form-control form-control-sm" value="<?= $vehicle->license_plate; ?>">
                          </div>
                          <div class="col-md-6 col-12 mb-2">
                            <label><b>Kullanan Kişi</b></label>
                            <input type="text" name="driver" class="form-control form-control-sm" value="<?= $vehicle->driver; ?>">
                          </div>
                          <div class="col-md-6 col-6 mb-2">
                            <label><b>Kasko Bitiş Tarihi</b></label>
                            <input type="date" name="casco_end_date" class="form-control form-control-sm" value="<?= $vehicle->casco_end_date; ?>">
                          </div>
                          <div class="col-md-6 col-6 mb-2">
                            <label><b>Sigorta Bitiş Tarihi</b></label>
                            <input type="date" name="insurance_end_date" class="form-control form-control-sm" value="<?= $vehicle->insurance_end_date; ?>">
                          </div>
                          <div class="col-md-6 col-6 mb-2">
                            <label><b>Muayene Tarihi</b></label>
                            <input type="date" name="inspection_date" class="form-control form-control-sm" value="<?= $vehicle->inspection_date; ?>">
                          </div>
                          <div class="col-md-6 col-6 mb-2">
                            <label><b>Kasko PDF</b></label>
                            <input type="file" name="casco_pdf" class="form-control form-control-sm">
                          </div>
                          <div class="col-md-6 col-6 mb-2">
                            <label><b>Sigorta PDF</b></label>
                            <input type="file" name="insurance_pdf" class="form-control form-control-sm">
                          </div>
                          <div class="col-md-6 col-6 mb-2">
                            <label><b>Ruhsat PDF</b></label>
                            <input type="file" name="registration_pdf" class="form-control form-control-sm">
                          </div>
                          <div class="col-12 mb-3">
                            <label><b>Açıklama</b></label>
                            <textarea name="description" class="form-control form-control-sm" rows="3"><?= $vehicle->description; ?></textarea>
                          </div>
                          <div class="col-md-6 col-12 mb-2 d-flex align-items-center">
                            <input type="checkbox" id="transportCheckbox-<?= $vehicle->id ?>" name="is_transport" <?= $vehicle->is_transport == '1' ? 'checked' : '' ?> class="me-2">
                            <label for="transportCheckbox-<?= $vehicle->id ?>" class="mb-0">Nakliye Aracı</label>
                          </div>
                          <div class="col-md-6 col-12 mb-2 text-right">
                            <input type="hidden" name="id" value="<?= $vehicle->id ?>">
                            <button type="submit" name="edit_vehicle" class="btn btn-primary btn-sm w-100">Kaydet</button>
                          </div>
                        </div>
                      </form>
                    </div>

                    <!-- Araç Silme Onay Modalı -->
                    <div id="delete-vehicle-<?= $vehicle->id ?>" class="modal">
                      <span class="close" onclick="closeModal()">&times;</span>
                      <h5 class="mb-3"><b>Aracı silmek istediğinize emin misiniz?</b></h5>
                      <p><b><?= guvenlik($vehicle->name) ?></b> - <?= guvenlik($vehicle->license_plate) ?></p>
                      <form action="" method="POST" class="mt-3">
                        <input type="hidden" name="id" value="<?= $vehicle->id ?>">
                        <div class="d-flex justify-content-end">
                          <button type="button" class="btn btn-secondary me-2" onclick="closeModal()">Vazgeç</button>
                          <button type="submit" name="delete_vehicle" class="btn btn-danger">Sil</button>
                        </div>
                      </form>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <br/><br/><br/><br/><br/><br/>

    <?php include ROOT_PATH.'/template/script.php'; ?>
  </body>
</html>