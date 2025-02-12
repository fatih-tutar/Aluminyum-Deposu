<?php 
	include 'fonksiyonlar/bagla.php'; 
	if (!isLoggedIn()) {
		header("Location:giris.php");
		exit();
	}else{

    if (isset($_POST['aracekle'])) {
      // Formdan gelen verileri alalım
      $arac_adi = $_POST['arac_adi'];
      $plaka = $_POST['plaka'];
      $kasko_bitis_tarihi = $_POST['kasko_bitis_tarihi'];
      $sigorta_bitis_tarihi = $_POST['sigorta_bitis_tarihi'];
      $muayene_tarihi = $_POST['muayene_tarihi'];
      $araci_kullanan = $_POST['araci_kullanan'];
      $aciklama = $_POST['aciklama'];
      $saniye = time();  // Anlık timestamp değeri
      $silik = 0;  // Varsayılan değer
  
      // Dosya yükleme işlemleri
      $kasko_pdf = null;
      $sigorta_pdf = null;
      $ruhsat_pdf = null;
  
      // Dosyaların kaydedileceği klasör
      $uploadDir = 'files/arac/';
  
      // Kasko PDF dosyası yüklendiyse
      if (isset($_FILES['kasko_pdf']) && $_FILES['kasko_pdf']['error'] == 0) {
          $kasko_pdf = $uploadDir . uniqid() . "_" . $_FILES['kasko_pdf']['name'];
          move_uploaded_file($_FILES['kasko_pdf']['tmp_name'], $kasko_pdf);
      }
  
      // Sigorta PDF dosyası yüklendiyse
      if (isset($_FILES['sigorta_pdf']) && $_FILES['sigorta_pdf']['error'] == 0) {
          $sigorta_pdf = $uploadDir . uniqid() . "_" . $_FILES['sigorta_pdf']['name'];
          move_uploaded_file($_FILES['sigorta_pdf']['tmp_name'], $sigorta_pdf);
      }
  
      // Ruhsat PDF dosyası yüklendiyse
      if (isset($_FILES['ruhsat_pdf']) && $_FILES['ruhsat_pdf']['error'] == 0) {
          $ruhsat_pdf = $uploadDir . uniqid() . "_" . $_FILES['ruhsat_pdf']['name'];
          move_uploaded_file($_FILES['ruhsat_pdf']['tmp_name'], $ruhsat_pdf);
      }
  
      // Veritabanına kayıt ekle
      $sql = "INSERT INTO araclar (arac_adi, plaka, kasko_bitis_tarihi, sigorta_bitis_tarihi, kasko_pdf, sigorta_pdf, muayene_tarihi, araci_kullanan, ruhsat_pdf, aciklama, saniye, silik) 
              VALUES (:arac_adi, :plaka, :kasko_bitis_tarihi, :sigorta_bitis_tarihi, :kasko_pdf, :sigorta_pdf, :muayene_tarihi, :araci_kullanan, :ruhsat_pdf, :aciklama, :saniye, :silik)";
  
      $stmt = $db->prepare($sql);

      $stmt->bindParam(':arac_adi', $arac_adi);
      $stmt->bindParam(':plaka', $plaka);
      $stmt->bindParam(':kasko_bitis_tarihi', $kasko_bitis_tarihi);
      $stmt->bindParam(':sigorta_bitis_tarihi', $sigorta_bitis_tarihi);
      $stmt->bindParam(':kasko_pdf', $kasko_pdf);
      $stmt->bindParam(':sigorta_pdf', $sigorta_pdf);
      $stmt->bindParam(':muayene_tarihi', $muayene_tarihi);
      $stmt->bindParam(':araci_kullanan', $araci_kullanan);
      $stmt->bindParam(':ruhsat_pdf', $ruhsat_pdf);
      $stmt->bindParam(':aciklama', $aciklama);
      $stmt->bindParam(':saniye', $saniye);
      $stmt->bindParam(':silik', $silik);
  
      // Kayıt işlemi başarılı ise
      if ($stmt->execute()) {
          header("Location:arac.php");
          exit();
      } else {
          $hata = '<br/><div class="alert alert-danger" role="alert">Bir hata oluştu, araç eklenemedi.</div>';   
      }
    }

    if(isset($_POST['aracsil'])) {
      $id = guvenlik($_POST['id']);
      $query = $db->prepare("UPDATE araclar SET silik = ? WHERE id = ?");
      $update = $query->execute(array('1',$id));
      header("Location:arac.php");
      exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['aracduzenle'])) {
      // POST verilerini al
      $id = $_POST['id'];
      $arac_adi = $_POST['arac_adi'];
      $plaka = $_POST['plaka'];
      $araci_kullanan = $_POST['araci_kullanan'];
      $kasko_bitis_tarihi = $_POST['kasko_bitis_tarihi'];
      $sigorta_bitis_tarihi = $_POST['sigorta_bitis_tarihi'];
      $muayene_tarihi = $_POST['muayene_tarihi'];
      $aciklama = $_POST['aciklama'];
      $nakliye = '0';
      if(isset($_POST['nakliye'])){ $nakliye = '1'; }

      // Dosya yükleme işlemleri
      $uploads = [];
      $upload_dir = 'files/arac/';

      if (!empty($_FILES['kasko_pdf']['name'])) {
          $unique_name = uniqid() . '-' . basename($_FILES['kasko_pdf']['name']);
          $uploads['kasko_pdf'] = $upload_dir . $unique_name;
          move_uploaded_file($_FILES['kasko_pdf']['tmp_name'], $uploads['kasko_pdf']);
      }
      
      if (!empty($_FILES['sigorta_pdf']['name'])) {
          $unique_name = uniqid() . '-' . basename($_FILES['sigorta_pdf']['name']);
          $uploads['sigorta_pdf'] = $upload_dir . $unique_name;
          move_uploaded_file($_FILES['sigorta_pdf']['tmp_name'], $uploads['sigorta_pdf']);
      }

      if (!empty($_FILES['ruhsat_pdf']['name'])) {
          $unique_name = uniqid() . '-' . basename($_FILES['ruhsat_pdf']['name']);
          $uploads['ruhsat_pdf'] = $upload_dir . $unique_name;
          move_uploaded_file($_FILES['ruhsat_pdf']['tmp_name'], $uploads['ruhsat_pdf']);
      }

      // SQL sorgusunu oluştur
      $sql = "UPDATE araclar SET 
                  arac_adi = ?, 
                  plaka = ?, 
                  araci_kullanan = ?, 
                  kasko_bitis_tarihi = ?, 
                  sigorta_bitis_tarihi = ?, 
                  muayene_tarihi = ?, 
                  aciklama = ?,
                  nakliye = ?";

      // Dosya alanlarını kontrol et ve SQL sorgusuna ekle
      $params = [$arac_adi, $plaka, $araci_kullanan, $kasko_bitis_tarihi, $sigorta_bitis_tarihi, $muayene_tarihi, $aciklama, $nakliye];
      
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
        header("Location:arac.php");
        exit();
      } else {
        $hata = '<br/><div class="alert alert-danger" role="alert">Bir hata oluştu, araç bilgileri güncellenemedi.</div>';  
      }
    } 
	}
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
          <?= isset($hata) ? $hata : ''; ?>
      </div>
		</div>
    <div class="div4" style="text-align: center;">
      <!-- Başlık -->
      <a href="#" onclick="return false" onmousedown="javascript:ackapa('araceklemeformudivi');">
          <button class="btn btn-primary btn-sm">
              <h2 class="mt-2 text-center">
                  <i class="fas fa-angle-double-down"></i>&nbsp;&nbsp;&nbsp;
                  &nbsp;Araç Ekleme Formu&nbsp;&nbsp;&nbsp;&nbsp;
                  <i class="fas fa-angle-double-down"></i>
              </h2>
          </button>
      </a>
      <form action="" method="POST" enctype="multipart/form-data" style="display:none;" id="araceklemeformudivi" class="mt-4">
        <div class="row">
          <div class="col-md-1 col-12">
            <div class="row">
                <div class="col-md-12 col-5">Araç Adı</div>
                <div class="col-md-12 col-7 pb-2"><input type="text" name="arac_adi" class="form-control form-control-sm" placeholder="Araç Adı"></div>
            </div>
          </div>
          <div class="col-md-1 col-12">
            <div class="row">
              <div class="col-md-12 col-5">Plaka</div>
              <div class="col-md-12 col-7 pb-2"><input type="text" name="plaka" class="form-control form-control-sm" placeholder="Plaka"></div>
            </div>
          </div>
          <div class="col-md-2 col-12">
            <div class="row">
              <div class="col-md-12 col-5">Kullanan Kişi</div>
              <div class="col-md-12 col-7"><input type="text" name="araci_kullanan" class="form-control form-control-sm" placeholder="Aracı Kullanan Kişi"></div>
            </div>
          </div>
          <div class="col-md-5 col-12">
              <div class="row">
                  <div class="col-md-4 col-12">
                    <div class="row">
                      <div class="col-md-12 col-5">Kasko Bitiş Tarihi</div>
                      <div class="col-md-12 col-7 pb-2"><input type="date" name="kasko_bitis_tarihi" id="kasko_bitis_tarihi_inputu" class="form-control form-control-sm"></div>
                    </div>
                  </div>
                  <div class="col-md-4 col-12">
                    <div class="row">
                      <div class="col-md-12 col-5">Sigorta Bitiş Tarihi</div>
                      <div class="col-md-12 col-7 pb-2"><input type="date" name="sigorta_bitis_tarihi" id="sigorta_bitis_tarihi_inputu" class="form-control form-control-sm"></div>
                    </div>
                  </div>
                  <div class="col-md-4 col-12">
                    <div class="row">
                      <div class="col-md-12 col-5">Muayene Tarihi</div>
                      <div class="col-md-12 col-7 pb-2"><input type="date" name="muayene_tarihi" id="muayene_tarihi_inputu" class="form-control form-control-sm"></div>
                    </div>
                  </div>
              </div>
          </div>
          <div class="col-md-1 col-12">
            <div class="row">
              <div class="col-md-12 col-5">Kasko PDF</div>
              <div class="col-md-12 col-7 pb-2"><input type="file" name="kasko_pdf" id="kasko_pdf_inputu" style="width:88px;"></div>
            </div>
          </div>
          <div class="col-md-1 col-12">
            <div class="row">
              <div class="col-md-12 col-5 px-md-1">Sigorta PDF</div>
              <div class="col-md-12 col-7 px-md-1 pb-2"><input type="file" name="sigorta_pdf" id="sigorta_pdf_inputu" style="width:88px;"></div>
            </div>
          </div>
          <div class="col-md-1 col-12">
            <div class="row">
              <div class="col-md-12 col-5">Ruhsat PDF</div>
              <div class="col-md-12 col-7 pb-2"><input type="file" name="ruhsat_pdf" id="ruhsat_pdf_inputu" style="width:88px;"></div>
            </div>
          </div>
          <div class="col-md-12 col-12 mb-2">
            <textarea name="aciklama" id="aciklama" placeholder="Bu alan not girebilirsiniz." class="form-control form-control-sm"></textarea>
          </div>
          <div class="col-md-12">
            <button type="submit" name="aracekle" class="btn btn-primary btn-block btn-sm">Araç Ekle</button>
          </div>
        </div>
      </form>
    </div>
    <div class="div4">
      <?php
        // PDO sorgusu ve verilerin çekilmesi
        $araclar = $db->query("SELECT * FROM araclar WHERE silik = '0'");
        $rows = $araclar->fetchAll(PDO::FETCH_ASSOC);
      ?>

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

          <?php foreach ($rows as $index => $row): ?>
            <form action="" method="POST">
              <div class="row py-2" style="background-color: <?= $index % 2 === 0 ? '#ffffff' : '#d0d4d7' ?>; border-bottom: 1px solid #dee2e6;">
                <div class="col-md-1 col-12">
                  <span class="d-md-none font-weight-bold">Araç Adı: </span>
                  <?= guvenlik($row['arac_adi']) ?>
                </div>
                <div class="col-md-1 col-12">
                    <span class="d-md-none font-weight-bold">Plaka: </span>
                    <?= htmlspecialchars($row['plaka']) ?>
                </div>
                <div class="col-md-2 col-12">
                    <span class="d-md-none font-weight-bold">Kullanan Kişi: </span>
                    <?= htmlspecialchars($row['araci_kullanan']) ?>
                </div>
                <div class="col-md-4 col-12">
                  <div class="row">
                    <div class="col-md-4 col-12">
                      <span class="d-md-none font-weight-bold">Kasko Bitiş Tarihi: </span>
                      <?= htmlspecialchars($row['kasko_bitis_tarihi']) ?>
                    </div>
                    <div class="col-md-4 col-12">
                      <span class="d-md-none font-weight-bold">Sigorta Bitiş Tarihi: </span>
                      <?= htmlspecialchars($row['sigorta_bitis_tarihi']) ?>
                    </div>
                    <div class="col-md-4 col-12">
                      <span class="d-md-none font-weight-bold">Muayene Tarihi: </span>
                      <?= htmlspecialchars($row['muayene_tarihi']) ?>
                    </div>
                  </div>
                </div>
                <!-- Kasko PDF -->
                <div class="col-md-1 col-12">
                    <span class="d-md-none font-weight-bold">Kasko PDF: </span>
                    <a href="<?= htmlspecialchars($row['kasko_pdf']) ?>" target="_blank">Kasko PDF</a>
                </div>
                <!-- Sigorta PDF -->
                <div class="col-md-1 col-12">
                    <span class="d-md-none font-weight-bold">Sigorta PDF: </span>
                    <a href="<?= htmlspecialchars($row['sigorta_pdf']) ?>" target="_blank">Sigorta PDF</a>
                </div>
                <!-- Ruhsat PDF -->
                <div class="col-md-1 col-12">
                    <span class="d-md-none font-weight-bold">Ruhsat PDF: </span>
                    <a href="<?= htmlspecialchars($row['ruhsat_pdf']) ?>" target="_blank">Ruhsat PDF</a>
                </div>
                <div class="col-md-1 col-12">
                  <div class="row">
                    <div class="col-md-6 col-6">
                      <a href="#" onclick="return false" onmousedown="javascript:ackapa('duzenlemedivi<?= $row['id'] ?>');">
                        <button class="btn btn-success btn-block btn-sm"><i class="fas fa-pen"></i></button>
                      </a>
                    </div>
                    <div class="col-md-6 col-6">
                      <input type="hidden" name="id" value="<?= $row['id'] ?>">
                      <button type="submit" name="aracsil" class="btn btn-secondary btn-block btn-sm" onclick="return confirmForm('Aracı silmek istediğinize emin misiniz?');">
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
                  </div>
                </div>
                <!-- Açıklama (alt satırda) -->
                <div class="col-12">
                    <span class="font-weight-bold">Açıklama: </span>
                    <?= htmlspecialchars($row['aciklama']) ?>
                </div>
              </div>
            </form>
            <form action="" method="POST" enctype="multipart/form-data">
              <div id="duzenlemedivi<?= $row['id'] ?>" class="my-3" style="display:none;">
                <form action="" method="POST" enctype="multipart/form-data">
                  <div class="row">
                    <div class="col-md-1 col-12">
                      <div class="row">
                          <div class="col-md-12 col-5">Araç Adı</div>
                          <div class="col-md-12 col-7 pb-2"><input type="text" name="arac_adi" class="form-control form-control-sm" placeholder="Plaka" value="<?= $row['arac_adi']?>"></div>
                      </div>
                    </div>
                    <div class="col-md-1 col-12">
                      <div class="row">
                        <div class="col-md-12 col-5">Plaka</div>
                        <div class="col-md-12 col-7 pb-2"><input type="text" name="plaka" class="form-control form-control-sm" placeholder="Plaka" value="<?= $row['plaka']?>"></div>
                      </div>
                    </div>
                    <div class="col-md-2 col-12">
                      <div class="row">
                        <div class="col-md-12 col-5">Kullanan Kişi</div>
                        <div class="col-md-12 col-7"><input type="text" name="araci_kullanan" class="form-control form-control-sm" placeholder="Aracı Kullanan Kişi" value="<?= $row['araci_kullanan'] ?>"></div>
                      </div>
                    </div>
                    <div class="col-md-5 col-12">
                      <div class="row">
                        <div class="col-md-4 col-12">
                          <div class="row">
                            <div class="col-md-12 col-5">Kasko Bitiş Tarihi</div>
                            <div class="col-md-12 col-7 pb-2"><input type="date" name="kasko_bitis_tarihi" id="kasko_bitis_tarihi_inputu" class="form-control form-control-sm" value="<?= $row['kasko_bitis_tarihi'] ?>"></div>
                          </div>
                        </div>
                        <div class="col-md-4 col-12">
                          <div class="row">
                            <div class="col-md-12 col-5">Sigorta Bitiş Tarihi</div>
                            <div class="col-md-12 col-7 pb-2"><input type="date" name="sigorta_bitis_tarihi" id="sigorta_bitis_tarihi_inputu" class="form-control form-control-sm" value="<?= $row['sigorta_bitis_tarihi'] ?>"></div>
                          </div>
                        </div>
                        <div class="col-md-4 col-12">
                          <div class="row">
                            <div class="col-md-12 col-5">Muayene Tarihi</div>
                            <div class="col-md-12 col-7 pb-2"><input type="date" name="muayene_tarihi" id="muayene_tarihi_inputu" class="form-control form-control-sm" value="<?= $row['muayene_tarihi'] ?>"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-1 col-12">
                      <div class="row">
                        <div class="col-md-12 col-5">Kasko PDF</div>
                        <div class="col-md-12 col-7 pb-2"><input type="file" name="kasko_pdf" id="kasko_pdf_inputu" style="width:88px;"></div>
                      </div>
                    </div>
                    <div class="col-md-1 col-12">
                      <div class="row">
                        <div class="col-md-12 col-5 px-md-1">Sigorta PDF</div>
                        <div class="col-md-12 col-7 px-md-1 pb-2"><input type="file" name="sigorta_pdf" id="sigorta_pdf_inputu" style="width:88px;"></div>
                      </div>
                    </div>
                    <div class="col-md-1 col-12">
                      <div class="row">
                        <div class="col-md-12 col-5">Ruhsat PDF</div>
                        <div class="col-md-12 col-7 pb-2"><input type="file" name="ruhsat_pdf" id="ruhsat_pdf_inputu" style="width:88px;"></div>
                      </div>
                    </div>
                    <div class="col-md-10 col-12">
                      <textarea name="aciklama" id="aciklama" placeholder="Bu alan not girebilirsiniz." class="form-control form-control-sm"><?= $row['aciklama'] ?></textarea>
                    </div>
                    <div class="col-md-2 col-12">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <input type="checkbox" id="nakliyeCheckbox" name="nakliye" <?= $row['nakliye'] == '1' ? 'checked' : '' ?>>
                        <label for="nakliyeCheckbox">Nakliye Aracı</label>
                        <button type="submit" name="aracduzenle" class="btn btn-primary btn-block btn-sm">Kaydet</button>
                    </div>
                  </div>
                </form>
              </div>
            </form>
          <?php endforeach; ?>
      </div>
    </div>
    <?php include 'template/script.php'; ?>
</body>
</html>