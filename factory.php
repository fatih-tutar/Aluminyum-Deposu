<?php
	include 'functions/init.php';
	if (!isLoggedIn()) {
		header("Location:login.php");
		exit();
	}elseif (isLoggedIn()) {
		if($authUser->type == '0'){
			header("Location:index.php");
			exit();
		}else{
            if (isset($_POST['add_factory'])) {
                $name = guvenlik($_POST['name']);
                $phone = guvenlik($_POST['phone']);
                $email = guvenlik($_POST['email']);
                $address = guvenlik($_POST['address']);
                if (empty($name) || empty($phone) || empty($email) || empty($address)) {
                    $error = '<div class="alert alert-danger mb-1" role="alert">Boş bıraktığınız alanlar var, lütfen tüm alanları doldurunuz.</div>';
                } else {
                    $query = $db->prepare("INSERT INTO factories SET name = ?, phone = ?, email = ?, address = ?, company_id = ?, is_deleted = ?");
                    $insert = $query->execute(array($name, $phone, $email, $address, $authUser->company_id, 0));
                    header("Location:factory.php");
                    exit();
                }
            }
            if (isset($_POST['update_factory'])) {
                $id = guvenlik($_POST['id']);
                $name = guvenlik($_POST['name']);
                $phone = guvenlik($_POST['phone']);
                $email = guvenlik($_POST['email']);
                $laborCost = guvenlik($_POST['labor_cost']);
                $address = guvenlik($_POST['address']);
                $query = $db->prepare("UPDATE factories SET name = ?, phone = ?, email = ?, labor_cost = ?, address = ? WHERE id = ?");
                $guncelle = $query->execute(array($name,$phone,$email,$laborCost,$address,$id));
                $orderId = guvenlik($_POST['order_id']);
                header("Location:factory.php#".($orderId - 2));
                exit();
            }
            if (isset($_POST['delete_factory'])) {
                $id = guvenlik($_POST['id']);
                if (isFactoryInUse($id) == '1') {
                    $error = '<br/><div class="alert alert-danger" role="alert">Bu fabrikanın kayıtlı olduğu bir ürün, sipariş veya sipariş formu var o yüzden silemiyoruz.</div>';
                }else{
                    $delete = $db->prepare("UPDATE factories SET is_deleted = ? WHERE id = ?");
                    $deleting = $delete->execute(array('1',$id));
                    $orderId = guvenlik($_POST['order_id']);
                    header("Location:factory.php#".($orderId - 2));
                    exit();
                }
            }
            if (isset($_POST['delete_order'])) {
                $id = guvenlik($_POST['id']);
                $query = $db->prepare("UPDATE siparis SET silik = ? WHERE siparis_id = ?");
                $update = $query->execute(array('1',$id));
                $orderId = guvenlik($_POST['order_id']);
                header("Location:factory.php#".($orderId - 2));
                exit();
            }

            //FACTORIES
            $factories = $db->query("SELECT * FROM factories WHERE is_deleted = 0 AND company_id = '{$authUser->company_id}' ORDER BY name ASC")->fetchAll(PDO::FETCH_OBJ);
        }
	}

?>

<!DOCTYPE html>

<html>

	<head>

		<title>Fabrikalar</title>

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
                    <div class="d-flex justify-content-between">
                        <button id="menuToggleBtn" class="btn btn-outline-primary btn-sm mr-2 mb-2">
                            <i class="fas fa-bars"></i> Menü
                        </button>
                        <div>
                            <?= isset($error) ? $error : ''; ?>
                        </div>
                        <button class="btn btn-primary mb-2" onclick="openModal('form-div')">
                            <i class="fas fa-plus me-2"></i>
                            Yeni Fabrika
                        </button>
                        <div id="form-div" class="modal">
                            <span class="close" onclick="closeModal()">&times;</span>
                            <h4><b>Fabrika Kayıt Formu</b></h4>
                            <form action="" method="POST" class="mt-3">
                                <input type="text" name="name" class="form-control mb-2" placeholder="Fabrika adını giriniz" value="<?= $_POST['name'] ?? '' ?>">
                                <input type="text" name="phone" class="form-control mb-2" placeholder="Telefon numarasını giriniz" value="<?= $_POST['phone'] ?? '' ?>">
                                <input type="text" name="email" class="form-control mb-2" placeholder="E-posta adresini yazınız." value="<?= $_POST['email'] ?? '' ?>">
                                <textarea name="address" class="form-control mb-2" rows="3" placeholder="Fabrika adresini yazınız."><?= $_POST['address'] ?? '' ?></textarea>
                                <button type="submit" class="btn btn-primary btn-block" name="add_factory">Kaydet</button>
                            </form>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered td-vertical-align-middle">
                            <thead>
                                <tr style="color:#003566">
                                    <th>Fabrika</th>
                                    <th>Telefon</th>
                                    <th>E-posta</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($factories as $factoryKey => $factory): ?>
                                <tr>
                                    <td><?= $factory->name ?></td>
                                    <td><?= $factory->phone ?></td>
                                    <td><?= $factory->email ?></td>
                                    <td>
                                        <div class="d-flex">
                                            <button class="btn btn-primary btn-sm mr-1" onclick="openModal('orders-div-<?= $factory->id ?>')">Açılan Siparişler</button>
                                            <a href="fabrikasiparis.php?id=<?= $factory->id ?>" target="_blank">
                                                <button class="btn btn-info btn-sm mr-1">Bekleyen Sipariş</button>
                                            </a>
                                            <a href="order-form-archive.php?id=<?= $factory->id ?>" target="_blank">
                                                <button class="btn btn-success btn-sm mr-1">Form Arşivi</button>
                                            </a>
                                            <a onclick="openModal('edit-div-<?= $factory->id ?>')">
                                                <button class="btn btn-warning btn-sm mr-1">Düzenle</button>
                                            </a>
                                            <form action="" method="POST">
                                                <input type="hidden" name="id" value="<?= $factory->id ?>"/>
                                                <input type="hidden" name="order_id" value="<?= $factoryKey ?>"/>
                                                <button class="btn btn-secondary btn-sm" name="delete_factory" onclick="return confirmForm('<?= $factory->name ?> adlı fabrikayı silmek istediğinize emin misiniz?')">Sil</button>
                                            </form>
                                            <div id="edit-div-<?= $factory->id ?>" class="modal">
                                                <span class="close" onclick="closeModal()">&times;</span>
                                                <h4><b>Fabrika Düzenleme Formu</b></h4>
                                                <form action="" method="POST" class="mt-3">
                                                    <input type="text" name="name" class="form-control mb-2" placeholder="Fabrika adını giriniz" value="<?= $factory->name ?>">
                                                    <input type="text" name="phone" class="form-control mb-2" placeholder="Telefon numarasını giriniz" value="<?= $factory->phone ?>">
                                                    <input type="text" name="email" class="form-control mb-2" placeholder="E-posta adresini yazınız." value="<?= $factory->email ?>">
                                                    <textarea name="address" class="form-control mb-2" rows="3" placeholder="Fabrika adresini yazınız."><?= $factory->address ?></textarea>
                                                    <input type="hidden" name="id" value="<?= $factory->id ?>"/>
                                                    <input type="hidden" name="order_id" value="<?= $factoryKey ?>"/>
                                                    <button type="submit" class="btn btn-primary btn-block" name="update_factory">Güncelle</button>
                                                </form>
                                            </div>
                                        </div>
                                        <div id="orders-div-<?= $factory->id ?>" class="modal" style="width: 80%;">
                                            <span class="close" onclick="closeModal()">&times;</span>
                                            <h5><?= $factory->name ?> SİPARİŞ LİSTESİ</h5>
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                    <tr>
                                                        <th>Ürün</th>
                                                        <th>Adet</th>
                                                        <th>İlgili Kişi</th>
                                                        <th>Hazırlayan</th>
                                                        <th>Tarih</th>
                                                        <th>İşlemler</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php
                                                    $orders = $db->query("SELECT * FROM siparis WHERE urun_fabrika_id = '{$factory->id}' AND formda = '0' AND sirketid = '{$authUser->company_id}' AND silik = '0'")->fetchAll(PDO::FETCH_OBJ);
                                                    if (!$orders) { ?>
                                                        <tr>
                                                            <td colspan="6" style="text-align: center; color: #003566; font-weight: bold;">
                                                                Hiç sipariş yoktur.
                                                            </td>
                                                        </tr>
                                                    <?php }
                                                    foreach ($orders as $orderKey => $order):
                                                            $productId = $order->urun_id;
                                                            $product = getProduct($productId);
                                                            $category = getCategory($product->kategori_bir);
                                                            $subCategory = getCategory($product->kategori_iki);
                                                            $orderName = $product->urun_adi." ".$category->kategori_adi." ".$subCategory->kategori_adi;
                                                        ?>
                                                        <tr>
                                                            <td><?= $orderName; ?></td>
                                                            <td><?= $order->urun_siparis_aded ?></td>
                                                            <td><?= $order->ilgilikisi ?></td>
                                                            <td><?= $order->hazirlayankisi ?></td>
                                                            <td><?= date("d-m-Y", $order->siparissaniye) ?></td>
                                                            <td>
                                                                <form action="" method="POST">
                                                                    <input type="hidden" name="id" value="<?= $order->siparis_id ?>">
                                                                    <input type="hidden" name="order_id" value="<?= $orderKey + 1 ?>">
                                                                    <button type="submit" class="icon-button" name="delete_order" onclick="return confirmForm('Siparişten <?= $orderName ?> ürününü silmek istediğinize emin misiniz?')">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <a href="pdf.php?id=<?= $factory->id; ?>" target="_blank">
                                                <button class="btn btn-primary btn-sm">Sipariş Formuna Git</button>
                                            </a>
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

		<?php include 'template/script.php'; ?>

	</body>

</html>