<?php
	include 'functions/init.php';
	if (!isLoggedIn()) {
		header("Location:login.php");
		exit();
	}elseif (isLoggedIn()) {
		if($user->type == '0'){
			header("Location:index.php");
			exit();
		}
	    if($user->type != '3'){
            $s = isset($_GET['s']) ? guvenlik($_GET['s']) : null;
            $i = isset($_GET['s']) ? (guvenlik($_GET['s']) * 20) - 20 : 0;
            if (isset($_POST['update_client'])) {
                $orderId = guvenlik($_POST['order_id']);
                $id = guvenlik($_POST['id']);
                $name = guvenlik($_POST['name']);
                $phone = guvenlik($_POST['phone']);
                $email = guvenlik($_POST['email']);
                $address = guvenlik($_POST['address']);
                $query = $db->prepare("UPDATE clients SET name = ?, phone = ?, email = ?, address = ? WHERE id = ?");
                $update = $query->execute(array($name, $phone, $email, $address, $id));
                header("Location:client.php#".($orderId - 2));
                exit();
            }

            if (isset($_POST['delete_client'])) {
                $orderId = guvenlik($_POST['order_id']);
                $id = guvenlik($_POST['id']);
                if (isCompanyInUse($id) == '1') {
                    $error = '<br/><div class="alert alert-danger" role="alert">Bu firmanın kayıtlı olduğu bir ürün, teklif veya teklif formu var o yüzden silemiyoruz.</a></div>';
                }else{
                    $query = $db->prepare("UPDATE clients SET is_deleted = ? WHERE id = ?");
                    $delete = $query->execute(array('1',$id));
                    header("Location:client.php#".($orderId - 2));
                    exit();
                }
            }

            if(isset($_POST['delete_order'])){
                $orderId = guvenlik($_POST['order_id']);
                $id = guvenlik($_POST['id']);
                $query = $db->prepare("UPDATE teklif SET silik = ? WHERE teklifid = ?");
                $delete = $query->execute(array('1',$id));
                header("Location:client.php?s=".$s."#".($orderId - 2));
                exit();
            }

            if (isset($_POST['add_client'])) {
                $name = guvenlik($_POST['name']);
                $phone = guvenlik($_POST['phone']);
                $email = guvenlik($_POST['email']);
                $address = guvenlik($_POST['address']);
                $debt = 0;
                $dueDate = 0;
                $query = $db->prepare("INSERT INTO clients SET name = ?, phone = ?, email = ?, address = ?, debt = ?, due_date = ?, company_id = ?, is_deleted = ?");
                $insert = $query->execute(array($name, $phone, $email, $address, $debt, $dueDate, $authUser->company_id,'0'));
                header("Location:client.php");
                exit();
            }

            if (isset($_POST['called'])) {
                $orderId = guvenlik($_POST['order_id']);
                $id = guvenlik($_POST['id']);
                $debt = guvenlik($_POST['debt']);
                $dueDate = guvenlik($_POST['due_date']);
                $dueDate = strtotime($dueDate);
                $query = $db->prepare("UPDATE clients SET debt = ?, due_date = ? WHERE id = ?");
                $update = $query->execute(array($debt, $dueDate, $id));
                header("Location:client.php#".($orderId - 2));
                exit();
            }

            if (isset($_POST['save'])) {
                $orderId = guvenlik($_POST['order_id']);
                $id = guvenlik($_POST['id']);
                $debt = guvenlik($_POST['debt']);
                $dueDate = 0;
                $query = $db->prepare("UPDATE clients SET debt = ?, due_date = ? WHERE id = ?");
                $update = $query->execute(array($debt, $dueDate, $id));
                header("Location:client.php#".($orderId - 2));
                exit();
            }

            if (isset($_POST['payment_completed'])) {
                $orderId = guvenlik($_POST['order_id']);
                $id = guvenlik($_POST['id']);
                $debt = 0;
                $dueDate = 0;
                $query = $db->prepare("UPDATE clients SET debt = ?, due_date = ? WHERE id = ?");
                $update = $query->execute(array($debt, $dueDate, $id));
                header("Location:client.php#".($orderId - 2));
                exit();
            }

            if(isset($_GET['call_list'])){
                $clients = $db->query("SELECT * FROM clients WHERE company_id = '{$user->company_id}' AND debt != 0 AND due_date != '0' AND due_date > '{$bugununsaniyesi}' ORDER BY name ASC")->fetchAll(PDO::FETCH_OBJ);
            }elseif (isset($_GET['payments'])) {
                $clients = $db->query("SELECT * FROM clients WHERE company_id = '{$user->company_id}' AND debt != 0 ORDER BY name ASC")->fetchAll(PDO::FETCH_OBJ);
            }elseif (isset($_GET['overdue_payments'])) {
                $clients = $db->query("SELECT * FROM clients WHERE company_id = '{$user->company_id}' AND debt != 0 AND due_date != '0' AND due_date <= '{$bugununsaniyesi}' ORDER BY name ASC")->fetchAll(PDO::FETCH_OBJ);
            }else{
                $clients = $db->query("SELECT * FROM clients WHERE company_id = '{$user->company_id}' AND is_deleted = '0' ORDER BY name ASC LIMIT $i,20")->fetchAll(PDO::FETCH_OBJ);
            }
	    }
    }
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Firmalar</title>
		<?php include 'template/head.php'; ?>
	</head>
	<body style="background-color: white">
		<?php include 'template/banner.php' ?>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<?= $error; ?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-2 col-12">
                    <a href="#" onclick="openModal('form-div')">
                        <button class="btn btn-primary mb-2 mt-2">
                            <i class="fas fa-plus mr-2"></i>
                            Yeni Firma
                        </button>
                    </a>
                    <div id="form-div" class="modal">
                        <span class="close" onclick="closeModal()">&times;</span>
                        <h4><b>Firma Kayıt Formu</b></h4>
                        <form action="" method="POST" class="mt-3">
                            <input type="text" name="name" class="form-control mb-2" placeholder="Firma adını giriniz">
                            <input type="text" name="phone" class="form-control mb-2" placeholder="Telefon numarasını giriniz">
                            <input type="text" name="email" class="form-control mb-2" placeholder="E-posta adresini yazınız.">
                            <textarea name="address" class="form-control mb-2" rows="3" placeholder="Firma adresini yazınız."></textarea>
                            <button type="submit" class="btn btn-primary btn-block" name="add_client">Kaydet</button>
                        </form>
                    </div>
				</div>
				<div class="col-md-10 col-12 pt-3" style="text-align: right;">
					<a href="client.php"><button class="btn btn-sm btn-success">Tüm Liste</button></a>
					<a href="client.php?payments"><button class="btn btn-sm btn-info">Tutarlılar</button></a>
					<a href="client.php?call_list"><button class="btn btn-sm btn-primary">Aranılanlar</button></a>
					<a href="client.php?overdue_payments"><button class="btn btn-sm btn-danger">Gecikenler</button></a>
				</div>
			</div>

            <div class="table-responsive">
                <table class="table table-bordered td-vertical-align-middle">
                    <thead>
                        <tr style="color:#003566">
                            <th>Firma</th>
                            <th>Telefon</th>
                            <th>Tutar</th>
                            <th>Ödeme Tarihi</th>
                            <th>Ödeme Tarihi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $totalDebt = 0;
                        foreach ($clients as $clientKey => $client):
                    ?>
                        <tr>
                            <td class="truncate-cell-400">
                                <?= $client->name;?>
                            </td>
                            <td style="white-space: nowrap;">
                                <?= $client->phone ?>
                            </td>
                            <td>
                                <div class="d-flex">
                                    <input type="text" name="debt" class="form-control form-control-sm" <?= $client->debt == 0 ? 'placeholder="Tutar giriniz."' : 'value="'.$client->debt.'"' ?> placeholder="Tutar giriniz.">
                                    <button class="btn btn-dark btn-sm" type="submit" name="save"><i class="fas fa-save"></i></button>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex">
                                    <input type="date" id="tarih<?= $client->id; ?>" name="due_date" <?= $client->due_date == 0 ? 'placeholder="Tarih seçiniz"' : 'value="'.$client->due_date.'"' ?> class="form-control form-control-sm">
                                    <input type="hidden" id="tarih-db" name="due_date">
                                    <input type="hidden" name="order_id" value="<?= $client->id; ?>">
                                    <input type="hidden" name="id" value="<?= $client->id; ?>">
                                    <button class="btn btn-dark btn-sm" type="submit" name="called"><i class="fas fa-phone"></i></button>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex">
                                    <button class="btn btn-primary btn-sm mr-1" onclick="openModal('orders-div-<?= $client->id ?>')">Teklifler</button>
                                    <a href="order-form-archive.php?id=<?= $client->id ?>" target="_blank">
                                        <button class="btn btn-info btn-sm mr-1">Formlar</button>
                                    </a>
                                    <button class="btn btn-success btn-sm mr-1" type="submit" name="payment_completed">Temizle</button>
                                    <a onclick="openModal('edit-div-<?= $client->id ?>')">
                                        <button class="btn btn-warning btn-sm">Düzenle</button>
                                    </a>
                                    <a href="#" onclick="return false" onmousedown="javascript:ackapa('silmedivi<?= $client->id; ?>');">
                                        <button class="btn btn-secondary btn-sm">Sil</button>
                                    </a>
                                    <div id="edit-div-<?= $client->id ?>" class="modal">
                                        <span class="close" onclick="closeModal()">&times;</span>
                                        <h4><b>Firma Düzenleme Formu</b></h4>
                                        <form action="" method="POST" class="mt-3">
                                            <input type="text" name="name" class="form-control mb-2" placeholder="Firma adını giriniz" value="<?= $client->name ?>">
                                            <input type="text" name="phone" class="form-control mb-2" placeholder="Telefon numarasını giriniz" value="<?= $client->phone ?>">
                                            <input type="text" name="email" class="form-control mb-2" placeholder="E-posta adresini yazınız." value="<?= $client->email ?>">
                                            <textarea name="address" class="form-control mb-2" rows="3" placeholder="Firma adresini yazınız."><?= $client->address ?></textarea>
                                            <input type="hidden" name="id" value="<?= $client->id ?>"/>
                                            <input type="hidden" name="order_id" value="<?= $clientKey ?>"/>
                                            <button type="submit" class="btn btn-primary btn-block" name="update_client">Güncelle</button>
                                        </form>
                                    </div>
                                </div>
                                <div id="orders-div-<?= $client->id ?>" class="modal" style="width: 80%;">
                                    <span class="close" onclick="closeModal()">&times;</span>
                                    <h5><?= $client->name ?> TEKLİF LİSTESİ</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                            <tr>
                                                <th>Ürün</th>
                                                <th>Adet</th>
                                                <th>Satış Fiyatı</th>
                                                <th>Toplam</th>
                                                <th>Tarih</th>
                                                <th>İşlemler</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                                $orders = $db->query("SELECT * FROM teklif WHERE tverilenfirma = '{$client->id}' AND formda = '0' AND sirketid = '{$authUser->company_id}' AND silik = '0' ORDER BY teklifid DESC")->fetchAll(PDO::FETCH_OBJ);
                                                if (!$orders) { ?>
                                                    <tr>
                                                        <td colspan="6" style="text-align: center; color: #003566; font-weight: bold;">
                                                            Hiç teklif yoktur.
                                                        </td>
                                                    </tr>
                                                <?php }
                                                foreach ($orders as $orderKey => $order):
                                                    $product = getProduct($order->turunid);
                                                    $productName = $product->urun_adi;
                                                    $mainCategory = getCategory($product->kategori_bir)->kategori_adi;
                                                    $subCategory = getCategory($product->kategori_iki)->kategori_adi;
                                            ?>
                                                <tr>
                                                    <td><?= $productName.' / '.$subCategory.' / '.$mainCategory ?></td>
                                                    <td><?= $order->tadet ?></td>
                                                    <td><?= $order->tsatisfiyati ?></td>
                                                    <td><?= ($order->tadet * $order->tsatisfiyati) ?></td>
                                                    <td><?= date('d/m/Y',$order->tsaniye) ?></td>
                                                    <td>
                                                        <form action="" method="POST">
                                                            <input type="hidden" name="id" value="<?= $order->teklifid ?>">
                                                            <input type="hidden" name="order_id" value="<?= $orderKey + 1 ?>">
                                                            <button type="submit" class="icon-button" name="delete_order">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <a href="teklif.php?id=<?= $client->id; ?>" target="_blank">
                                        <button class="btn btn-primary btn-sm">Teklif Formuna Git</button>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <nav aria-label="Page navigation example">
                <ul class="pagination justify-content-center">
                <?php
                $totalCount = $db->query("SELECT COUNT(*) FROM clients WHERE company_id = '{$authUser->company_id}'")->fetchColumn();
                $sayfaSayisi = ceil($totalCount / 20);
                for ($i=1; $i <= $sayfaSayisi; $i++) {
                    echo '<li class="page-item"><a class="page-link" href="client.php?s='.$i.'">'.$i.'</a></li>';
                } ?>
              </ul>
            </nav>

		</div>

		<br/><br/><br/>

		<?php include 'template/script.php'; ?>

	</body>

</html>