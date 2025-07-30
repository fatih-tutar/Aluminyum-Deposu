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
            $isProfile = isset($_GET['id']) && is_numeric($_GET['id']);
            if($isProfile){
                $clientId = guvenlik($_GET['id']);

                if (isset($_POST['delete_offer'])) {
                    $orderId = guvenlik($_POST['order_id']);
                    $id = guvenlik($_POST['id']);
                    $query = $db->prepare("UPDATE teklif SET silik = ? WHERE teklifid = ?");
                    $delete = $query->execute(array('1', $id));
                    header("Location:client.php?id=".$clientId);
                    exit();
                }

                if (isset($_POST['delete_offer_form'])) {
                    $id = guvenlik($_POST['id']);
                    $offerList = guvenlik($_POST['offer_list']);
                    $offerListArray = explode(",", $offerList);
                    foreach ($offerListArray as $key => $value) {
                        $query = $db->prepare("UPDATE teklif SET silik = ? WHERE teklifid = ?");
                        $delete = $query->execute(array('1',$value));
                    }
                    $query = $db->prepare("UPDATE teklifformlari SET silik = ? WHERE tformid = ?");
                    $delete = $query->execute(array('1',$id));
                    header("Location:client.php?id=".$clientId);
                    exit();
                }

                $stmt = $db->prepare("SELECT * FROM clients WHERE id = ? AND is_deleted = ? LIMIT 1");
                $stmt->execute([$_GET['id'],0]);
                $client = $stmt->fetch(PDO::FETCH_OBJ);

                //CUSTOM ORDERS AND ITEMS START
                // Sorguyu çalıştır
                $stmt = $db->prepare("
                    SELECT 
                        co.id, co.client_id, co.delivery_type, co.description, co.datetime, co.created_by,
                        coi.id AS item_id, coi.product, coi.length, coi.factory_id, coi.quantity, coi.price, coi.due_date
                    FROM 
                        custom_orders co
                    LEFT JOIN 
                        custom_order_items coi 
                        ON co.id = coi.custom_order_id AND coi.is_deleted = 0
                    WHERE 
                        co.is_deleted = 0 AND co.company_id = ? AND co.client_id = ?
                    ORDER BY 
                        co.datetime ASC, coi.id ASC
                ");
                $stmt->execute([$authUser->company_id, $client->id]);

                // Tüm sonuçları obje olarak al
                $result = $stmt->fetchAll(PDO::FETCH_OBJ);

                // Boş obje dizisi oluştur
                $customOrders = [];

                $deliveryTypeTexts = [
                    0 => 'Müşteri Çağlayan',
                    1 => 'Müşteri Alkop',
                    2 => 'Tarafımızca sevk',
                    3 => 'Ambar tarafımızca sevk',
                    4 => 'Fabrikadan Teslim',
                ];

                // Sonuçları customOrders dizisine obje yapısında yerleştir
                foreach ($result as $row) {
                    $orderId = $row->id;

                    // Eğer sipariş daha önce eklenmemişse ekle
                    if (!isset($customOrders[$orderId])) {

                        $customOrders[$orderId] = (object)[
                            'id' => $row->id,
                            'client_id' => $row->client_id,
                            'delivery_type' => $row->delivery_type,
                            'delivery_type_name' => $deliveryTypeTexts[$row->delivery_type] ?? 'Bilinmeyen',
                            'description' => $row->description,
                            'datetime' => $row->datetime,
                            'created_by' => $row->created_by,
                            'items' => [],
                        ];
                    }

                    // Eğer item varsa, onu da ekle
                    if ($row->item_id) {
                        $customOrders[$orderId]->items[] = (object)[
                            'id' => $row->item_id,
                            'product' => $row->product,
                            'length' => $row->length,
                            'factory_id' => $row->factory_id,
                            'quantity' => $row->quantity,
                            'price' => $row->price,
                            'due_date' => $row->due_date,
                        ];
                    }
                }

                // İstersen sıfırlanmış indeksli bir array olarak da almak istersen:
                $customOrders = array_values($customOrders);
                //CUSTOM ORDERS AND ITEMS END

                //FACTORIES
                $factories = $db->query("SELECT * FROM factories ORDER BY name ASC")->fetchAll(PDO::FETCH_OBJ);

                //ORDER FORMS
                $offerForms = $db->query("SELECT * FROM teklifformlari WHERE firmaid = '{$client->id}' AND sirketid = '{$authUser->company_id}' AND silik = '0' ORDER BY tformid DESC")->fetchAll(PDO::FETCH_OBJ);

                //MOLDS
                $molds = $db->query("SELECT * FROM molds WHERE client_id = '{$client->id}' AND company_id = '{$authUser->company_id}' AND is_deleted = 0")->fetchAll(PDO::FETCH_OBJ);
            }else {

                $s = isset($_GET['s']) ? guvenlik($_GET['s']) : null;
                $i = isset($_GET['s']) ? (guvenlik($_GET['s']) * 30) - 30 : 0;

                if (isset($_POST['update_client'])) {
                    $orderId = guvenlik($_POST['order_id']);
                    $id = guvenlik($_POST['id']);
                    $name = guvenlik($_POST['name']);
                    $phone = guvenlik($_POST['phone']);
                    $email = guvenlik($_POST['email']);
                    $address = guvenlik($_POST['address']);
                    $query = $db->prepare("UPDATE clients SET name = ?, phone = ?, email = ?, address = ? WHERE id = ?");
                    $update = $query->execute(array($name, $phone, $email, $address, $id));
                    header("Location:client.php#" . ($orderId - 2));
                    exit();
                }

                if (isset($_POST['delete_client'])) {
                    $orderId = guvenlik($_POST['order_id']);
                    $id = guvenlik($_POST['id']);
                    if (isCompanyInUse($id) == '1') {
                        $error = '<br/><div class="alert alert-danger" role="alert">Bu firmanın kayıtlı olduğu bir ürün, teklif veya teklif formu var o yüzden silemiyoruz.</a></div>';
                    } else {
                        $query = $db->prepare("UPDATE clients SET is_deleted = ? WHERE id = ?");
                        $delete = $query->execute(array('1', $id));
                        header("Location:client.php#" . ($orderId - 2));
                        exit();
                    }
                }

                if (isset($_POST['add_client'])) {
                    $name = guvenlik($_POST['name']);
                    $phone = guvenlik($_POST['phone']);
                    $email = guvenlik($_POST['email']);
                    $address = guvenlik($_POST['address']);
                    $query = $db->prepare("INSERT INTO clients SET name = ?, phone = ?, email = ?, address = ?, company_id = ?, is_deleted = ?");
                    $insert = $query->execute(array($name, $phone, $email, $address, $authUser->company_id, '0'));
                    header("Location:client.php");
                    exit();
                }

                $clients = $db->query("SELECT * FROM clients WHERE company_id = '{$authUser->company_id}' AND is_deleted = '0' ORDER BY name ASC LIMIT $i,30")->fetchAll(PDO::FETCH_OBJ);
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
                <div id="sidebar" class="col-md-3 d-none">
                    <?php include 'template/sidebar2.php'; ?>
                </div>
                <div id="mainCol" class="col-md-12 col-12">
                    <?= isset($error) ? $error : ''; ?>

                    <div class="row">
                        <div class="<?= $isProfile ? 'col-md-12' : 'col-md-8' ?> col-12 d-flex align-items-start">
                            <button id="menuToggleBtn" class="btn btn-outline-primary mr-2">
                                <i class="fas fa-bars"></i> Menü
                            </button>
                            <div class="client-search-box flex-grow-1 position-relative mr-2">
                                <input name="firma" id="firmainputu" type="text" class="form-control" autocomplete="off" placeholder="Firma Ara..."/>
                                <ul class="list-group clientliveresult" id="firmasonuc" style="position: absolute; z-index: 1;"></ul>
                            </div>
                            <?php if(!$isProfile){ ?>
                                <button class="btn btn-primary mb-2" onclick="openModal('form-div')">
                                    <i class="fas fa-plus me-2"></i>
                                    Yeni Firma
                                </button>
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
                            <?php } ?>
                        </div>
                    </div>
                    <?php if(!$isProfile){ ?>
                        <div class="table-responsive">
                            <table class="table table-bordered td-vertical-align-middle">
                                <thead>
                                    <tr style="color:#003566">
                                        <th>Firma</th>
                                        <th>Telefon</th>
                                        <th>E-posta</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    foreach ($clients as $clientKey => $client):
                                ?>
                                    <tr>
                                        <td class="truncate-cell-500">
                                            <a href="client.php?id=<?= $client->id ?>"><?= $client->name;?></a>
                                        </td>
                                        <td style="white-space: nowrap;">
                                            <?= $client->phone ?>
                                        </td>
                                        <td style="white-space: nowrap;">
                                            <?= $client->email ?>
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <a onclick="openModal('edit-div-<?= $client->id ?>')">
                                                    <button class="btn btn-warning btn-sm mr-1">Düzenle</button>
                                                </a>
                                                <form action="" method="POST">
                                                    <input type="hidden" name="id" value="<?= $client->id ?>"/>
                                                    <input type="hidden" name="order_id" value="<?= $clientKey ?>"/>
                                                    <button class="btn btn-secondary btn-sm" name="delete_client" onclick="return confirmForm('<?= $client->name ?> adlı firmayı silmek istediğinize emin misiniz?')">Sil</button>
                                                </form>
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
                            $sayfaSayisi = ceil($totalCount / 30);
                            for ($i=1; $i <= $sayfaSayisi; $i++) {
                                echo '<li class="page-item"><a class="page-link" href="client.php?s='.$i.'">'.$i.'</a></li>';
                            } ?>
                          </ul>
                        </nav>
                    <?php }else{ ?>

                        <h5 class="mt-3 bold">
                            <i class="fa fa-building mr-2"></i><?= $client->name ?>
                        </h5>

                        <p>
                            <i class="fa fa-phone mr-2 mt-2"></i><?= $client->phone ?><br/>
                            <i class="fa fa-envelope mr-2 mt-2"></i><?= $client->email ?><br/>
                            <i class="fa fa-map-marker mr-2 mt-2"></i><?= $client->address ?><br/>
                        </p>

                        <div class="mt-4">
                            <!-- Sekmeler -->
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="offer-tab" data-toggle="tab" href="#offer" role="tab"
                                       aria-controls="offer" aria-selected="false">Teklifler</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="custom-order-tab" data-toggle="tab" href="#custom-order" role="tab"
                                       aria-controls="custom-order" aria-selected="false">Özel Siparişler</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="mold-tab" data-toggle="tab" href="#mold" role="tab"
                                       aria-controls="mold" aria-selected="true">Kalıplar</a>
                                </li>
                            </ul>

                            <!-- İçerik -->
                            <div class="tab-content border p-3" id="myTabContent">
                                <div class="tab-pane fade show active" id="offer" role="tabpanel" aria-labelledby="offer-tab">
                                    <div>
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th class="text-center" colspan="5">Bekleyen Teklifler</th>
                                                </tr>
                                                <tr>
                                                    <th>Ürün</th>
                                                    <th>Adet</th>
                                                    <th>Satış Fiyatı</th>
                                                    <th>Toplam</th>
                                                    <th>Tarih</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                $offers = $db->query("SELECT * FROM teklif WHERE tverilenfirma = '{$client->id}' AND formda = '0' AND sirketid = '{$authUser->company_id}' AND silik = '0' ORDER BY teklifid DESC")->fetchAll(PDO::FETCH_OBJ);
                                                if (!$offers) { ?>
                                                    <tr>
                                                        <td colspan="6" style="text-align: center; color: #003566; font-weight: bold;">
                                                            Hiç teklif yoktur.
                                                        </td>
                                                    </tr>
                                                <?php }
                                                foreach ($offers as $offerKey => $offer):
                                                    $product = getProduct($offer->turunid);
                                                    $productName = $product->urun_adi;
                                                    $mainCategory = getCategory($product->kategori_bir)->kategori_adi;
                                                    $subCategory = getCategory($product->kategori_iki)->kategori_adi;
                                                    ?>
                                                    <tr>
                                                        <td><?= $productName.' / '.$subCategory.' / '.$mainCategory ?></td>
                                                        <td><?= $offer->tadet ?></td>
                                                        <td><?= $offer->tsatisfiyati ?></td>
                                                        <td><?= ($offer->tadet * $offer->tsatisfiyati) ?></td>
                                                        <td><?= date('d/m/Y',$offer->tsaniye) ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <?php if($offers){ ?>
                                        <a href="teklif.php?id=<?= $client->id; ?>" target="_blank">
                                            <button class="btn btn-primary btn-sm mb-3">%20 KDV'li Teklif Formuna Git</button>
                                        </a>
                                        <a href="teklif.php?id=<?= $client->id; ?>&withholding=true" target="_blank">
                                            <button class="btn btn-secondary btn-sm mb-3">Tevkifatlı Teklif Formuna Git</button>
                                        </a>
                                        <?php } ?>
                                    </div>
                                    <?php if (!$offerForms) { ?>
                                        <table class="table table-bordered">
                                            <tr>
                                                <td style="text-align: center; color: #003566; font-weight: bold;">
                                                    Kaydedilmiş teklif formu yoktur.
                                                </td>
                                            </tr>
                                        </table>
                                    <?php }else{ ?>
                                        <table class="table table-bordered">
                                            <thead>
                                            <tr>
                                                <th>Kaydedilmiş Teklif Formları</th>
                                                <th>Ürünler</th>
                                                <th>Form</th>
                                                <th>İşlemler</th>
                                            </tr>
                                            </thead>
                                            <?php
                                            foreach ($offerForms as $offerForm):
                                                ?>
                                                <tr>
                                                    <td><?= date('d/m/Y H:i:s', $offerForm->saniye) ?> Tarihli Teklif Formu</td>
                                                    <td>
                                                        <button class="icon-button" onclick="openModal('product-div-<?= $offerForm->tformid ?>')">
                                                            <i class="fa fa-eye"></i>
                                                        </button>
                                                        <div id="product-div-<?= $offerForm->tformid ?>" class="modal" style="width: 70%;">
                                                            <span class="close" onclick="closeModal()">&times;</span>
                                                            <h5>Ürünler</h5>
                                                            <div class="table-responsive mt-2">
                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                    <tr>
                                                                        <th>Ürün Adı</th>
                                                                        <th>Adet</th>
                                                                        <th>Satış Fiyatı</th>
                                                                        <th>Toplam</th>
                                                                        <th>Tarih</th>
                                                                        <th>İşlemler</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    <?php
                                                                    $offerList = explode(",", $offerForm->tekliflistesi);
                                                                    if (!$offerForm->tekliflistesi) { ?>
                                                                        <tr>
                                                                            <td colspan="6" style="text-align: center; color: #003566; font-weight: bold;">
                                                                                Bu form içerisinde hiç ürün yoktur.
                                                                            </td>
                                                                        </tr>
                                                                    <?php }
                                                                    if($offerList){
                                                                        foreach ($offerList as $offerItem):
                                                                            $offerItemObj = getOffer($offerItem);
                                                                            if (!$offerItemObj) {
                                                                                continue;
                                                                            }
                                                                            $offerItemProduct = getProduct($offerItemObj->turunid);
                                                                            if (!$offerItemProduct) {
                                                                                continue;
                                                                            }
                                                                            $productName = $offerItemProduct->urun_adi;
                                                                            $mainCategory = getCategory($offerItemProduct->kategori_bir)->kategori_adi;
                                                                            $subCategory = getCategory($offerItemProduct->kategori_iki)->kategori_adi;
                                                                            ?>
                                                                            <tr>
                                                                                <td><?= $productName.' / '.$subCategory.' / '.$mainCategory ?></td>
                                                                                <td><?= $offerItemObj->tadet ?></td>
                                                                                <td><?= $offerItemObj->tsatisfiyati ?></td>
                                                                                <td><?= ($offerItemObj->tadet * $offerItemObj->tsatisfiyati) ?></td>
                                                                                <td><?= date('d/m/Y',$offerItemObj->tsaniye) ?></td>
                                                                                <td>
                                                                                    <form action="" method="POST">
                                                                                        <input type="hidden" name="id" value="<?= $offerItemObj->teklifid ?>">
                                                                                        <button type="submit" class="icon-button" name="delete_offer" onclick="return confirmForm('Silmek istediğinize emin misiniz?');">
                                                                                            <i class="fas fa-trash"></i>
                                                                                        </button>
                                                                                    </form>
                                                                                </td>
                                                                            </tr>
                                                                        <?php endforeach; } ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <a href="teklifformu.php?id=<?= $offerForm->tformid ?>" target="_blank">
                                                            <button class="icon-button">
                                                                <i class="fa fa-file"></i>
                                                            </button>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <form action="" method="POST">
                                                            <input type="hidden" name="id" value="<?= $offerForm->tformid ?>" />
                                                            <input type="hidden" name="offer_list" value="<?= $offerForm->tekliflistesi ?>" />
                                                            <button type="submit" name="delete_offer_form" class="icon-button" onclick="return confirmForm('Silmek istediğinize emin misiniz?');">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </table>
                                    <?php } ?>
                                </div>
                                <div class="tab-pane fade" id="custom-order" role="tabpanel" aria-labelledby="custom-order-tab">
                                    <table class="table table-bordered">
                                        <tbody>
                                        <?php
                                        foreach ($customOrders as $key => $order) {
                                            $clientName = getClientName($order->client_id);
                                            ?>
                                            <tr style="border-top:2px solid black">
                                                <td style="width: 7%"><?= $key + 1 ?></td>
                                                <td><b style="color:#003566">Sevk Tipi : </b><?= $order->delivery_type_name ?></td>
                                                <td><b style="color:#003566">Oluşturulma Tarihi : </b><?= formatDateAndTime($order->datetime) ?></td>
                                            </tr>
                                            <tr>
                                                <td colspan="4" style="padding:0">
                                                    <table class="table table-bordered mb-0">
                                                        <thead>
                                                        <tr style="color:#003566">
                                                            <th scope="col">No</th>
                                                            <th scope="col">Ürün</th>
                                                            <th scope="col">Ölçü</th>
                                                            <th scope="col">Adet</th>
                                                            <th scope="col">Fiyat</th>
                                                            <th scope="col">Fabrika</th>
                                                            <th scope="col">Termin Tarihi</th>
                                                        </tr>
                                                        <tbody>
                                                        <?php foreach ($order->items as $itemKey => $item){ ?>
                                                            <tr>
                                                                <td style="width: 6%"><?= ($itemKey + 1) ?></td>
                                                                <td style="width: 42%"><?= $item->product ?></td>
                                                                <td style="width: 10%"><?= $item->length ?></td>
                                                                <td style="width: 7%"><?= $item->quantity ?></td>
                                                                <td style="width: 7%"><?= $item->price ?></td>
                                                                <td style="width: 23%"><?= getFactoryNameById($factories, $item->factory_id) ?></td>
                                                                <td style="width: 5%"><?= formatDate($item->due_date) ?></td>
                                                            </tr>
                                                        <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="4" style="padding:3px 10px;">
                                                    <?php if (!empty($order->description)) {
                                                        echo 'Açıklama : ' . $order->description;
                                                    } ?>
                                                    <div style="float:right;">
                                                        Siparişi Oluşturan : <a href="profil.php?id=<?= $order->created_by ?>"><b><?= getUsername($order->created_by) ?></b></a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php }
                                        if (count($customOrders) == 0) { ?>
                                            <tr>
                                                <td colspan="5" style="text-align: center; color: #003566; font-weight: bold;">
                                                    Hiç özel sipariş kaydınız yoktur.
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane fade" id="mold" role="tabpanel" aria-labelledby="mold-tab">
                                    <table class="table table-sm table-bordered m-0">
                                        <thead>
                                        <tr>
                                            <th>Kalıp No</th>
                                            <th>Fabrika</th>
                                            <th>Firma T.</th>
                                            <th>Fabrika T.</th>
                                            <th>Termin Tarihi</th>
                                            <th>Fabrika Pdf</th>
                                            <th>Firma Pdf</th>
                                            <th>Sözleşme Pdf</th>
                                            <th>İlgili Kişi</th>
                                            <th>Kaydeden</th>
                                            <th>İşlemler</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($molds as $item): ?>
                                            <?php
                                            $factoryName = getFactoryNameById($factories, $item->factory_id);
                                            $factoryPdfPath = $item->factory_pdf;
                                            $clientPdfPath = $item->client_pdf;
                                            $contractPdfPath = $item->contract_pdf;
                                            $creatorName = getUsername($item->created_by);
                                            ?>
                                            <tr>
                                                <td><?= htmlspecialchars($item->number) ?></td>
                                                <td class="truncate-cell-150"><?= htmlspecialchars($factoryName) ?></td>
                                                <td><?= htmlspecialchars($item->client_offer_price) ?></td>
                                                <td><?= htmlspecialchars($item->factory_offer_price) ?></td>
                                                <td><?= htmlspecialchars($item->due_date) ?></td>
                                                <td>
                                                    <?php if ($factoryPdfPath && file_exists($factoryPdfPath)): ?>
                                                        <a href="<?= htmlspecialchars($factoryPdfPath) ?>" target="_blank">Fabrika PDF</a>
                                                    <?php else: ?>
                                                        -
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($clientPdfPath && file_exists($clientPdfPath)): ?>
                                                        <a href="<?= htmlspecialchars($clientPdfPath) ?>" target="_blank">Firma PDF</a>
                                                    <?php else: ?>
                                                        -
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($contractPdfPath && file_exists($contractPdfPath)): ?>
                                                        <a href="<?= htmlspecialchars($contractPdfPath) ?>" target="_blank">Sözleşme PDF</a>
                                                    <?php else: ?>
                                                        -
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= htmlspecialchars($item->contact_person) ?></td>
                                                <td>
                                                    <a href="profil.php?id=<?= urlencode($item->created_by) ?>"><b><?= htmlspecialchars($creatorName) ?></b></a>
                                                </td>
                                                <td class="display-flex">
                                                    <a href="#" onclick="return false" onmousedown="javascript:ackapa('factory_pdfdivi<?= $item->id; ?>');">
                                                        <i class="fas fa-industry mr-3"></i>
                                                    </a>
                                                    <a href="#" onclick="return false" onmousedown="javascript:ackapa('client_pdfdivi<?= $item->id; ?>');">
                                                        <i class="fas fa-building mr-3"></i>
                                                    </a>
                                                    <a href="#" onclick="return false" onmousedown="javascript:ackapa('contract_pdfdivi<?= $item->id; ?>');">
                                                        <i class="fas fa-paper mr-3"></i>
                                                    </a>
                                                    <div id="factory_pdfdivi<?= $item->id; ?>" class="pdf-preview-wrapper" style="display: none;">
                                                        <div class="pdf-preview">
                                                            <div class="pdf-preview-header">
                                                                <h5 class="pdf-preview-title">Fabrika Onay Belgesi</h5>
                                                                <button onclick="ackapa('factory_pdfdivi<?= $item->id; ?>')" class="pdf-preview-close">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </div>
                                                            <?php if (!empty($item->factory_pdf)): ?>
                                                                <object width="100%" height="500" type="application/pdf" data="<?= $item->factory_pdf; ?>">
                                                                    <p>Fabrika PDF dokümanı yüklenemedi.</p>
                                                                </object>
                                                            <?php else: ?>
                                                                <div style="padding: 20px; text-align: center; color: #666;">
                                                                    Fabrika PDF dokümanı yüklenmemiş.
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                    <div id="client_pdfdivi<?= $item->id; ?>" class="pdf-preview-wrapper" style="display: none;">
                                                        <div class="pdf-preview">
                                                            <div class="pdf-preview-header">
                                                                <h5 class="pdf-preview-title">Firma Onay Belgesi</h5>
                                                                <button onclick="ackapa('client_pdfdivi<?= $item->id; ?>')" class="pdf-preview-close">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </div>
                                                            <?php if (!empty($item->client_pdf)): ?>
                                                                <object width="100%" height="500" type="application/pdf" data="<?= $item->client_pdf; ?>">
                                                                    <p>Firma PDF dokümanı yüklenemedi.</p>
                                                                </object>
                                                            <?php else: ?>
                                                                <div style="padding: 20px; text-align: center; color: #666;">
                                                                    Firma PDF dokümanı yüklenmemiş.
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                    <div id="contract_pdfdivi<?= $item->id; ?>" class="pdf-preview-wrapper" style="display: none;">
                                                        <div class="pdf-preview">
                                                            <div class="pdf-preview-header">
                                                                <h5 class="pdf-preview-title">Firma Onay Belgesi</h5>
                                                                <button onclick="ackapa('contract_pdfdivi<?= $item->id; ?>')" class="pdf-preview-close">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </div>
                                                            <?php if (!empty($item->contract_pdf)): ?>
                                                                <object width="100%" height="500" type="application/pdf" data="<?= $item->contract_pdf; ?>">
                                                                    <p>Sözleşme PDF dokümanı yüklenemedi.</p>
                                                                </object>
                                                            <?php else: ?>
                                                                <div style="padding: 20px; text-align: center; color: #666;">
                                                                    Sözleşme PDF dokümanı yüklenmemiş.
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    <?php } ?>

                </div>
            </div>
		</div>

		<br/><br/><br/>

		<?php include 'template/script.php'; ?>

	</body>

</html>