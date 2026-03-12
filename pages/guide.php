<?php 
    require_once __DIR__.'/../config/init.php';

    if (!isLoggedIn()) {
        header("Location:/login");
        exit();
    }
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Kılavuz</title>
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
          <div class="d-flex justify-content-between align-items-center mb-3">
            <button id="menuToggleBtn" class="btn btn-outline-primary btn-sm d-md-none">
              <i class="fas fa-bars"></i> Menü
            </button>
            <h3 class="d-none d-md-block" style="margin-top:.3rem; margin-bottom:0; font-weight: bold;">
              Kılavuz
            </h3>
            <div></div>
          </div>

          <div class="card">
            <div class="card-body">
              <ul class="mb-0">
                <li class="mb-2">
                  Sipariş butonunun kırmızı yanması, sipariş listesindeki bir ürünün termin tarihi geçmesine rağmen hâlâ teslim alınmadığını gösterir.
                </li>
                <li class="mb-2">
                  Ürün adı kırmızı yanıyorsa ürünün stokta az kaldığını gösterir. Ürüne dair stok uyarı adedini düzenle butonuna tıklayıp açılan düzenleme formundaki
                  <strong>Uyarı Adedi</strong> kısmından değiştirebilirsiniz.
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>

    <br/><br/><br/><br/><br/><br/>

    <?php include ROOT_PATH.'/template/script.php'; ?>
  </body>
</html>