<?php 
	include 'fonksiyonlar/bagla.php'; 
	if ($girdi != '1') {
		header("Location:giris.php");
		exit();
	}else{

	}
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Araçlar</title>
    <?php include 'template/head.php'; ?>
  </head>
  <body>
    <?php include 'template/banner.php' ?>
    <div class="div4">
      <form action="" method="POST" enctype="multipart/form-data">
        <div class="row">
          <div class="col-md-1 col-12">
            <div class="row">
              <div class="col-md-12 col-5">Plaka</div>
              <div class="col-md-12 col-7 pb-2"><input type="text" name="plaka" class="form-control form-control-sm" placeholder="Plaka"></div>
            </div>
          </div>
          <div class="col-md-2 col-12">
            <div class="row">
              <div class="col-md-12 col-5">Kasko Bitiş Tarihi</div>
              <div class="col-md-12 col-7 pb-2"><input type="date" name="kasko_bitis_tarihi" id="kasko_bitis_tarihi_inputu" class="form-control form-control-sm"></div>
            </div>
          </div>
          <div class="col-md-2 col-12">
            <div class="row">
              <div class="col-md-12 col-5">Sigorta Bitiş Tarihi</div>
              <div class="col-md-12 col-7 pb-2"><input type="date" name="sigorta_bitis_tarihi" id="sigorta_bitis_tarihi_inputu" class="form-control form-control-sm"></div>
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
          <div class="col-md-2 col-12">
            <div class="row">
              <div class="col-md-12 col-5">Muayene Tarihi</div>
              <div class="col-md-12 col-7 pb-2"><input type="date" name="muayene_tarihi" id="muayene_tarihi_inputu"></div>
            </div>
          </div>
          <div class="col-md-2 col-12">
            <div class="row">
              <div class="col-md-12 col-5">Kullanan Kişi</div>
              <div class="col-md-12 col-7"><input type="text" name="kullanan_kisi" class="form-control form-control-sm" placeholder="Aracı Kullanan Kişi"></div>
            </div>
          </div>
          <div class="col-md-1 col-12">
            <div class="row">
              <div class="col-md-12 col-5">Ruhsat PDF</div>
              <div class="col-md-12 col-7 pb-2"><input type="file" name="ruhsat_pdf" id="ruhsat_pdf_inputu" style="width:88px;"></div>
            </div>
          </div>
          <div class="col-md-12">
            <button type="submit" name="aracekle" class="btn btn-primary btn-block btn-sm">Araç Ekle</button>
          </div>
        </div>
      </form>
    </div>
    <?php include 'template/script.php'; ?>
</body>
</html>