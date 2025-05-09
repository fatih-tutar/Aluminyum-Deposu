<?php 
	include 'functions/init.php';

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
          $cascoPdf = $uploadDir . uniqid() . "_" . $_FILES['casco_pdf']['name'];
          move_uploaded_file($_FILES['casco_pdf']['tmp_name'], $cascoPdf);
        }

        // Sigorta PDF dosyası yüklendiyse
        if (isset($_FILES['insurance_pdf']) && $_FILES['insurance_pdf']['error'] == 0) {
          $insurancePdf = $uploadDir . uniqid() . "_" . $_FILES['insurance_pdf']['name'];
          move_uploaded_file($_FILES['insurance_pdf']['tmp_name'], $insurancePdf);
        }

        // Ruhsat PDF dosyası yüklendiyse
        if (isset($_FILES['registration_pdf']) && $_FILES['registration_pdf']['error'] == 0) {
            $registrationPdf = $uploadDir . uniqid() . "_" . $_FILES['registration_pdf']['name'];
            move_uploaded_file($_FILES['registration_pdf']['tmp_name'], $registrationPdf);
        }

        $query = $db->prepare("INSERT INTO vehicles SET name = ?, license_plate = ?, casco_end_date = ?, insurance_end_date = ?, casco_pdf = ?, insurance_pdf = ?, inspection_date = ?, driver = ?, registration_pdf = ?, description = ?, is_transport = ?, time = ?, is_deleted = ?");

        $insert = $query->execute(array($name, $licensePlate, $cascoEndDate, $insuranceEndDate, $cascoPdf, $insurancePdf, $inspectionDate, $driver, $registrationPdf, $description, $isTransport, $time, $isDeleted));

        header("Location:vehicle.php");
        exit();
    }

    if(isset($_POST['delete_vehicle'])) {
      $id = guvenlik($_POST['id']);
      $query = $db->prepare("UPDATE vehicles SET is_deleted = ? WHERE id = ?");
      $update = $query->execute(array('1',$id));
      header("Location:vehicle.php");
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

        // Dosya yükleme işlemleri
        $uploads = [];
        $upload_dir = 'files/vehicles/';

        if (!empty($_FILES['casco_pdf']['name'])) {
          $unique_name = uniqid() . '-' . basename($_FILES['casco_pdf']['name']);
          $uploads['casco_pdf'] = $upload_dir . $unique_name;
          move_uploaded_file($_FILES['casco_pdf']['tmp_name'], $uploads['casco_pdf']);
        }

        if (!empty($_FILES['insurance_pdf']['name'])) {
          $unique_name = uniqid() . '-' . basename($_FILES['insurance_pdf']['name']);
          $uploads['insurance_pdf'] = $upload_dir . $unique_name;
          move_uploaded_file($_FILES['insurance_pdf']['tmp_name'], $uploads['insurance_pdf']);
        }

        if (!empty($_FILES['registration_pdf']['name'])) {
          $unique_name = uniqid() . '-' . basename($_FILES['registration_pdf']['name']);
          $uploads['registration_pdf'] = $upload_dir . $unique_name;
          move_uploaded_file($_FILES['registration_pdf']['tmp_name'], $uploads['registration_pdf']);
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

        foreach ($uploads as $key => $path) {
          $sql .= ", $key = ?";
          $params[] = $path;
        }

        $sql .= " WHERE id = ?";
        $params[] = $id;

        // Sorguyu çalıştır
        $stmt = $db->prepare($sql);
        $result = $stmt->execute($params);

        if ($result) {
            header("Location:vehicle.php");
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
    <?php include 'template/head.php'; ?>
    <style>
      .record-row { border-bottom: 1px solid #dee2e6; padding: 10px 0; }
      .label-col { font-weight: bold; }
    </style>
  </head>
  <body>
    <?php include 'template/banner.php' ?>
    <div class="row">		
      <div class="col-md-12">			
          <?= $error ?>
      </div>
		</div>
    <div class="div4" style="text-align: center;">
      <!-- Başlık -->
      <a href="#" onclick="return false" onmousedown="javascript:ackapa('vehicle_add_form_div');">
          <button class="btn btn-primary btn-sm">
              <h2 class="mt-2 text-center">
                  <i class="fas fa-angle-double-down"></i>&nbsp;&nbsp;&nbsp;
                  &nbsp;Araç Ekleme Formu&nbsp;&nbsp;&nbsp;&nbsp;
                  <i class="fas fa-angle-double-down"></i>
              </h2>
          </button>
      </a>
      <form action="" method="POST" enctype="multipart/form-data" style="display:none;" id="vehicle_add_form_div" class="mt-4">
        <div class="row">
          <div class="col-md-1 col-12">
            <div class="row">
                <div class="col-md-12 col-5">Araç Adı</div>
                <div class="col-md-12 col-7 pb-2"><input type="text" name="name" class="form-control form-control-sm" placeholder="Araç Adı"></div>
            </div>
          </div>
          <div class="col-md-1 col-12">
            <div class="row">
              <div class="col-md-12 col-5">Plaka</div>
              <div class="col-md-12 col-7 pb-2"><input type="text" name="license_plate" class="form-control form-control-sm" placeholder="Plaka"></div>
            </div>
          </div>
          <div class="col-md-2 col-12">
            <div class="row">
              <div class="col-md-12 col-5">Kullanan Kişi</div>
              <div class="col-md-12 col-7"><input type="text" name="driver" class="form-control form-control-sm" placeholder="Aracı Kullanan Kişi"></div>
            </div>
          </div>
          <div class="col-md-5 col-12">
              <div class="row">
                  <div class="col-md-4 col-12">
                    <div class="row">
                      <div class="col-md-12 col-5">Kasko Bitiş Tarihi</div>
                      <div class="col-md-12 col-7 pb-2"><input type="date" name="casco_end_date" id="kasko_bitis_tarihi_inputu" class="form-control form-control-sm"></div>
                    </div>
                  </div>
                  <div class="col-md-4 col-12">
                    <div class="row">
                      <div class="col-md-12 col-5">Sigorta Bitiş Tarihi</div>
                      <div class="col-md-12 col-7 pb-2"><input type="date" name="insurance_end_date" id="sigorta_bitis_tarihi_inputu" class="form-control form-control-sm"></div>
                    </div>
                  </div>
                  <div class="col-md-4 col-12">
                    <div class="row">
                      <div class="col-md-12 col-5">Muayene Tarihi</div>
                      <div class="col-md-12 col-7 pb-2"><input type="date" name="inspection_date" id="muayene_tarihi_inputu" class="form-control form-control-sm"></div>
                    </div>
                  </div>
              </div>
          </div>
          <div class="col-md-1 col-12">
            <div class="row">
              <div class="col-md-12 col-5">Kasko PDF</div>
              <div class="col-md-12 col-7 pb-2"><input type="file" name="casco_pdf" id="kasko_pdf_inputu" style="width:88px;"></div>
            </div>
          </div>
          <div class="col-md-1 col-12">
            <div class="row">
              <div class="col-md-12 col-5 px-md-1">Sigorta PDF</div>
              <div class="col-md-12 col-7 px-md-1 pb-2"><input type="file" name="insurance_pdf" id="sigorta_pdf_inputu" style="width:88px;"></div>
            </div>
          </div>
          <div class="col-md-1 col-12">
            <div class="row">
              <div class="col-md-12 col-5">Ruhsat PDF</div>
              <div class="col-md-12 col-7 pb-2"><input type="file" name="registration_pdf" id="ruhsat_pdf_inputu" style="width:88px;"></div>
            </div>
          </div>
          <div class="col-md-12 col-12 mb-2">
            <textarea name="description" id="aciklama" placeholder="Bu alan not girebilirsiniz." class="form-control form-control-sm"></textarea>
          </div>
          <div class="col-md-12">
            <button type="submit" name="add_vehicle" class="btn btn-primary btn-block btn-sm">Araç Ekle</button>
          </div>
        </div>
      </form>
    </div>
    <div class="div4">

      <div class="container-fluid mt-4">
          <!-- Başlık -->
          <h2 class="mb-4 text-center">Araç Listesi</h2>
          
          <!-- Mobil görünüm için başlık altına ayırıcı -->
          <div class="d-md-none mb-4" style="border-top: 2px solid #343a40; width: 100%;"></div>
          
          <!-- Masaüstü için başlık satırları -->
          <div class="row font-weight-bold d-none d-md-flex" style="background-color: #f8f9fa; padding: 10px; border-bottom: 2px solid #343a40;">
              <div class="col-md-1"><button class="btn btn-primary btn-sm">Araç Adı</button></div>
              <div class="col-md-1"><button class="btn btn-primary btn-sm">Plaka</button></div>
              <div class="col-md-2"><button class="btn btn-primary btn-sm">Kullanan Kişi</button></div>
              <div class="col-md-4">
                <div class="row">
                  <div class="col-md-4"><button class="btn btn-primary btn-sm">Kasko Bitiş Tarihi</button></div>
                  <div class="col-md-4"><button class="btn btn-primary btn-sm" style="padding-left:4px; padding-right:4px;">Sigorta Bitiş Tarihi</button></div>
                  <div class="col-md-4"><button class="btn btn-primary btn-sm">Muayene Tarihi</button></div>
                </div>
              </div>
              <div class="col-md-1"><button class="btn btn-primary btn-sm">Kasko PDF</button></div>
              <div class="col-md-1 p-0"><button class="btn btn-primary btn-sm">Sigorta PDF</button></div>
              <div class="col-md-1 p-0"><button class="btn btn-primary btn-sm">Ruhsat PDF</button></div>
          </div>

          <?php foreach ($vehicles as $index => $vehicle): ?>
              <div class="row py-2" style="background-color: <?= $index % 2 === 0 ? '#ffffff' : '#d0d4d7' ?>; border-bottom: 1px solid #dee2e6;">
                <div class="col-md-1 col-12">
                  <span class="d-md-none font-weight-bold">Araç Adı: </span>
                  <?= guvenlik($vehicle->name) ?>
                </div>
                <div class="col-md-1 col-12">
                    <span class="d-md-none font-weight-bold">Plaka: </span>
                    <?= guvenlik($vehicle->license_plate) ?>
                </div>
                <div class="col-md-2 col-12">
                    <span class="d-md-none font-weight-bold">Kullanan Kişi: </span>
                    <?= guvenlik($vehicle->driver) ?>
                </div>
                <div class="col-md-4 col-12">
                  <div class="row">
                    <div class="col-md-4 col-12">
                      <span class="d-md-none font-weight-bold">Kasko Bitiş Tarihi: </span>
                      <?= guvenlik((new DateTime($vehicle->casco_end_date))->format('d/m/Y')) ?>
                    </div>
                    <div class="col-md-4 col-12">
                      <span class="d-md-none font-weight-bold">Sigorta Bitiş Tarihi: </span>
                      <?= guvenlik((new DateTime($vehicle->insurance_end_date))->format('d/m/Y')) ?>
                    </div>
                    <div class="col-md-4 col-12">
                      <span class="d-md-none font-weight-bold">Muayene Tarihi: </span>
                      <?= guvenlik((new DateTime($vehicle->inspection_date))->format('d/m/Y')) ?>
                    </div>
                  </div>
                </div>
                <!-- Kasko PDF -->
                <div class="col-md-1 col-12">
                    <span class="d-md-none font-weight-bold">Kasko PDF: </span>
                    <a href="<?= guvenlik($vehicle->casco_pdf) ?>" target="_blank">Kasko PDF</a>
                </div>
                <!-- Sigorta PDF -->
                <div class="col-md-1 col-12">
                    <span class="d-md-none font-weight-bold">Sigorta PDF: </span>
                    <a href="<?= guvenlik($vehicle->insurance_pdf) ?>" target="_blank">Sigorta PDF</a>
                </div>
                <!-- Ruhsat PDF -->
                <div class="col-md-1 col-12">
                    <span class="d-md-none font-weight-bold">Ruhsat PDF: </span>
                    <a href="<?= guvenlik($vehicle->registration_pdf) ?>" target="_blank">Ruhsat PDF</a>
                </div>
                <div class="col-md-1 col-12">
                  <div class="row">
                    <div class="col-md-6 col-6">
                      <a href="#" onclick="return false" onmousedown="javascript:ackapa('edit-div-<?= $vehicle->id ?>');">
                        <button class="btn btn-success btn-block btn-sm"><i class="fas fa-pen"></i></button>
                      </a>
                    </div>
                    <div class="col-md-6 col-6">
                        <form action="" method="POST">
                          <input type="hidden" name="id" value="<?= $vehicle->id ?>">
                          <button type="submit" name="delete_vehicle" class="btn btn-secondary btn-block btn-sm" onclick="return confirmForm('Aracı silmek istediğinize emin misiniz?');">
                            <i class="fas fa-trash"></i>
                          </button>
                        </form>
                    </div>
                  </div>
                </div>
                <!-- Açıklama (alt satırda) -->
                <div class="col-12">
                    <span class="font-weight-bold">Açıklama: </span>
                    <?= guvenlik($vehicle->description) ?>
                </div>
              </div>
              <div id="edit-div-<?= $vehicle->id ?>" class="my-3" style="display:none;">
                <form action="" method="POST" enctype="multipart/form-data">
                  <div class="row">
                    <div class="col-md-1 col-12">
                      <div class="row">
                          <div class="col-md-12 col-5">Araç Adı</div>
                          <div class="col-md-12 col-7 pb-2"><input type="text" name="name" class="form-control form-control-sm" placeholder="Plaka" value="<?= $vehicle->name; ?>"></div>
                      </div>
                    </div>
                    <div class="col-md-1 col-12">
                      <div class="row">
                        <div class="col-md-12 col-5">Plaka</div>
                        <div class="col-md-12 col-7 pb-2"><input type="text" name="license_plate" class="form-control form-control-sm" placeholder="Plaka" value="<?= $vehicle->license_plate; ?>"></div>
                      </div>
                    </div>
                    <div class="col-md-2 col-12">
                      <div class="row">
                        <div class="col-md-12 col-5">Kullanan Kişi</div>
                        <div class="col-md-12 col-7"><input type="text" name="driver" class="form-control form-control-sm" placeholder="Aracı Kullanan Kişi" value="<?= $vehicle->driver; ?>"></div>
                      </div>
                    </div>
                    <div class="col-md-5 col-12">
                      <div class="row">
                        <div class="col-md-4 col-12">
                          <div class="row">
                            <div class="col-md-12 col-5">Kasko Bitiş Tarihi</div>
                            <div class="col-md-12 col-7 pb-2"><input type="date" name="casco_end_date" class="form-control form-control-sm" value="<?= $vehicle->casco_end_date; ?>"></div>
                          </div>
                        </div>
                        <div class="col-md-4 col-12">
                          <div class="row">
                            <div class="col-md-12 col-5">Sigorta Bitiş Tarihi</div>
                            <div class="col-md-12 col-7 pb-2"><input type="date" name="insurance_end_date" class="form-control form-control-sm" value="<?= $vehicle->insurance_end_date; ?>"></div>
                          </div>
                        </div>
                        <div class="col-md-4 col-12">
                          <div class="row">
                            <div class="col-md-12 col-5">Muayene Tarihi</div>
                            <div class="col-md-12 col-7 pb-2"><input type="date" name="inspection_date" class="form-control form-control-sm" value="<?= $vehicle->inspection_date; ?>"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-1 col-12">
                      <div class="row">
                        <div class="col-md-12 col-5">Kasko PDF</div>
                        <div class="col-md-12 col-7 pb-2"><input type="file" name="casco_pdf" style="width:88px;"></div>
                      </div>
                    </div>
                    <div class="col-md-1 col-12">
                      <div class="row">
                        <div class="col-md-12 col-5 px-md-1">Sigorta PDF</div>
                        <div class="col-md-12 col-7 px-md-1 pb-2"><input type="file" name="insurance_pdf" style="width:88px;"></div>
                      </div>
                    </div>
                    <div class="col-md-1 col-12">
                      <div class="row">
                        <div class="col-md-12 col-5">Ruhsat PDF</div>
                        <div class="col-md-12 col-7 pb-2"><input type="file" name="registration_pdf" style="width:88px;"></div>
                      </div>
                    </div>
                    <div class="col-md-10 col-12">
                      <textarea name="description" placeholder="Bu alan not girebilirsiniz." class="form-control form-control-sm"><?= $vehicle->description; ?></textarea>
                    </div>
                    <div class="col-md-2 col-12">
                        <input type="hidden" name="id" value="<?= $vehicle->id ?>">
                        <input type="checkbox" id="transportCheckbox" name="is_transport" <?= $vehicle->is_transport == '1' ? 'checked' : '' ?>>
                        <label for="transportCheckbox">Nakliye Aracı</label>
                        <button type="submit" name="edit_vehicle" class="btn btn-primary btn-block btn-sm">Kaydet</button>
                    </div>
                  </div>
                </form>
              </div>
          <?php endforeach; ?>
      </div>
    </div>
    <?php include 'template/script.php'; ?>
</body>
</html>