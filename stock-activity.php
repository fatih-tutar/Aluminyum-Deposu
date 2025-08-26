<?php
	include 'functions/init.php';
	if (!isLoggedIn()) {
		header("Location:login.php");
		exit();
	}else{
		if($authUser->permissions->transaction != '1'){
			header("Location:index.php");
			exit();
		}else{
			if(isset($_GET['id']) && empty($_GET['id']) === false) {
				$productId = guvenlik($_GET['id']);
                $stockActivities = $db->query("SELECT * FROM stock_activities WHERE product_id = '{$productId}' AND company_id = '{$authUser->company_id}' ORDER BY id DESC LIMIT 300")->fetchAll(PDO::FETCH_OBJ);
			}else{
                $stockActivities = $db->query("SELECT * FROM stock_activities WHERE company_id = '{$authUser->company_id}' ORDER BY id DESC LIMIT 300")->fetchAll(PDO::FETCH_OBJ);
            }
            $activityLocation = [
                    0 => [
                            'name' => 'Mağaza',
                            'color' => 'warning'
                    ],
                    1 => [
                            'name' => 'Depo',
                            'color' => 'info',
                    ],
                    2 => [
                            'name' => 'Palet',
                            'color' => 'success'
                    ]
            ];
		}
	}
?>
<!DOCTYPE html>
<html>
  <head>
    <title>İşlem Geçmişi</title>
    <?php include 'template/head.php'; ?>
  </head>
  <body class="body-white">
      <?php include 'template/banner.php' ?>
      <div class="container-fluid">
          <div class="row">
              <div id="sidebar" class="col-md-3 d-none">
                  <?php include 'template/sidebar2.php'; ?>
              </div>
              <div id="mainCol" class="col-md-12 col-12">
                  <?= isset($error) ? $error : ''; ?>
                  <div class="d-flex justify-content-between">
                      <button id="menuToggleBtn" class="btn btn-outline-primary btn-sm mr-2 mb-2">
                          <i class="fas fa-bars"></i> Menü
                      </button>
                  </div>
                  <div class="table-wrapper" style="overflow-y: auto;">
                    <table class="table table-bordered">
                      <thead>
                          <tr>
                              <th>Personel</th>
                              <th>Ürün</th>
                              <th>Eski Adet</th>
                              <th>Yeni Adet</th>
                              <th>Fark</th>
                              <th>Yer</th>
                              <th>Tarih</th>
                          </tr>
                      </thead>
                      <tbody>
                      <?php
                        foreach ($stockActivities as $stockActivity):
                            $createdByName = getUsername($stockActivity->created_by);
                            $product = getProduct($stockActivity->product_id);
                            $category = getCategory($product->kategori_bir);
                            $subCategory = getCategory($product->kategori_iki);
                            $datetime = new DateTime($stockActivity->datetime);
                            $diff = $stockActivity->new_quantity - $stockActivity->prev_quantity;
                      ?>
                        <tr>
                            <td><a href="profil.php?id=<?= $stockActivity->created_by ?>"><?= $createdByName ?></a></td>
                            <td><?= $product->urun_adi." / ".$subCategory->kategori_adi." / ".$category->kategori_adi ?></td>
                            <td><?= $stockActivity->prev_quantity ?></td>
                            <td><?= $stockActivity->new_quantity ?></td>
                            <td>
                                <button class="btn btn-<?= $diff >= 0 ? 'success' : 'danger' ?> btn-sm">
                                    <?= $diff ?></td>
                                </button>
                            <td>
                                <button class="btn btn-<?= $activityLocation[$stockActivity->type]['color'] ?> btn-sm">
                                    <?= $activityLocation[$stockActivity->type]['name'] ?>
                                </button>
                            </td>
                            <td><?= $datetime->format('d/m/Y H:i:s') ?></td>
                        </tr>
                      <?php endforeach; ?>
                      </tbody>
                  </table>
                  </div>
              </div>
          </div>
      </div>

    <?php include 'template/script.php'; ?>

    </body>
</html>