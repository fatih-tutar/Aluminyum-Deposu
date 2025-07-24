<?php
include 'functions/init.php';
if (!isLoggedIn()) {
    header("Location:login.php");
    exit();
}else{
    //CUSTOM ORDERS AND ITEMS START
    // Sorguyu çalıştır
    $stmt = $db->prepare("
        SELECT 
            co.id, co.client_id, co.delivery_type, co.description, co.datetime, co.created_by, co.company_id,
            coi.id AS item_id, coi.product, coi.length, coi.factory_id, coi.quantity, coi.price, coi.due_date
        FROM 
            custom_orders co
        LEFT JOIN 
            custom_order_items coi 
            ON co.id = coi.custom_order_id AND coi.is_deleted = 0
        WHERE 
            co.status = 0 AND co.is_deleted = 0 AND co.company_id = ?
        ORDER BY 
            co.datetime ASC, coi.id ASC
    ");
    $stmt->execute([$authUser->company_id]);

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

    // FORMS START
    //INSERT INTO CUSTOM ORDER
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_custom_order'])) {
        // Filtreleme ve doğrulama
        $client = trim($_POST['client'] ?? '');
        $deliveryType = $_POST['delivery_type'] ?? null;
        $description = trim($_POST['description'] ?? '');

        $product = trim($_POST['product'] ?? '');
        $length = trim($_POST['length'] ?? '');
        $factoryId = (int)($_POST['factory_id'] ?? 0);
        $quantity = (float)($_POST['quantity'] ?? 0);
        $price = (float)($_POST['price'] ?? 0);
        $dueDate = $_POST['due_date'] ?? null;

        if ($client === '' || $deliveryType === null || $deliveryType === '' || $deliveryType === 'null' ||
            $product === '' || $length === '' || $factoryId <= 0 || $quantity <= 0 || $price <= 0 || $dueDate === null){
            // Hatalı girişleri geri döndür
            $error = '<br/><div class="alert alert-danger" role="alert">Lütfen formu eksiksiz doldurunuz.</div>';
        }else{
            $clientId = getClientId($client); // Formdan gelen client adıyla id’yi alıyoruz

            $existingOrderFound = false;

            foreach ($customOrders as $order) {
                if ($order->client_id == $clientId) {
                    $existingOrderFound = true;
                    break;
                }
            }

            if ($existingOrderFound) {
                $error = '<br/><div class="alert alert-danger" role="alert">
                        Bu firmaya ait sipariş kaydı zaten var. Lütfen ürünleri mevcut kayda ekleyiniz.
                    </div>';
            }else{
                try {
                    $db->beginTransaction();

                    // custom_orders tablosuna ekle
                    $stmt = $db->prepare("INSERT INTO custom_orders (client_id, delivery_type, description, status, is_deleted, datetime, created_by, company_id) VALUES (?, ?, ?, 0, 0, NOW(), ?, ?)");
                    $stmt->execute([$clientId, $deliveryType, $description, $authUser->id, $authUser->company_id]);

                    $customOrderId = $db->lastInsertId();

                    // custom_order_items tablosuna ekle
                    $stmt = $db->prepare("INSERT INTO custom_order_items (custom_order_id, product, length, factory_id, quantity, price, due_date, is_deleted, datetime, company_id) VALUES (?, ?, ?, ?, ?, ?, ?, 0, NOW(), ?)");
                    $stmt->execute([$customOrderId, $product, $length, $factoryId, $quantity, $price, $dueDate, $authUser->company_id]);

                    $db->commit();
                    header("Location: customorder.php");
                    exit();
                } catch (PDOException $e) {
                    $db->rollBack();
                    die("Hata oluştu: " . $e->getMessage());
                }
            }
        }
    }

    //UPDATE CUSTOM ORDER
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_custom_order'])) {
        // Formdan gelen verileri al
        $orderId = (int)($_POST['id'] ?? 0);
        $client = trim($_POST['client'] ?? '');
        $deliveryType = $_POST['delivery_type'] ?? null;
        $description = trim($_POST['description'] ?? '');

        // Form doğrulama
        if ($orderId <= 0 || $client === '' || $deliveryType === null || $deliveryType === '' || $deliveryType === 'null') {
            $error = '<br/><div class="alert alert-danger" role="alert">Lütfen formu eksiksiz doldurunuz.</div>';
        } else {
            $clientId = getClientId($client); // Firma adı ile client_id'yi bul

            try {
                // Güncelleme sorgusunu hazırla ve çalıştır
                $stmt = $db->prepare("UPDATE custom_orders SET client_id = ?, delivery_type = ?, description = ? WHERE id = ?");
                $stmt->execute([$clientId, $deliveryType, $description, $orderId]);

                $success = '<br/><div class="alert alert-success" role="alert">Sipariş başarıyla güncellendi.</div>';

                // Güncelleme sonrası yönlendirme (isteğe bağlı)
                 header("Location: customorder.php");
                 exit();

            } catch (PDOException $e) {
                $error = '<br/><div class="alert alert-danger" role="alert">Bir hata oluştu: ' . $e->getMessage() . '</div>';
            }
        }
    }

    // CUSTOM ORDER ARCHIVE
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['archive_custom_order'])) {
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo '<div class="alert alert-danger">Geçersiz kayıt IDsi.</div>';
        } else {
            try {
                $stmt = $db->prepare("UPDATE custom_orders SET status = 1 WHERE id = ?");
                $stmt->execute([$id]);

                if ($stmt->rowCount() > 0) {
                    // Başarı mesajı veya yönlendirme
                    header("Location: customorder.php");
                    exit;
                } else {
                    echo '<div class="alert alert-warning">Kayıt bulunamadı veya arşive zaten gönderilmiş.</div>';
                }
            } catch (PDOException $e) {
                echo '<div class="alert alert-danger">Hata: ' . $e->getMessage() . '</div>';
            }
        }
    }

    // CUSTOM ORDER DELETE
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_custom_order'])) {
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo '<div class="alert alert-danger">Geçersiz kayıt IDsi.</div>';
        } else {
            try {
                $stmt = $db->prepare("UPDATE custom_orders SET is_deleted = 1 WHERE id = ?");
                $stmt->execute([$id]);

                if ($stmt->rowCount() > 0) {
                    // Başarı mesajı veya yönlendirme
                    header("Location: customorder.php");
                    exit;
                } else {
                    echo '<div class="alert alert-warning">Kayıt bulunamadı veya zaten silinmiş.</div>';
                }
            } catch (PDOException $e) {
                echo '<div class="alert alert-danger">Hata: ' . $e->getMessage() . '</div>';
            }
        }
    }

    //INSERT INTO CUSTOM ORDER ITEM
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_item_custom_order'])) {
        // Filtreleme ve doğrulama
        $customOrderId = (int)($_POST['custom_order_id'] ?? 0);
        $product = trim($_POST['product'] ?? '');
        $length = trim($_POST['length'] ?? '');
        $factoryId = (int)($_POST['factory_id'] ?? 0);
        $quantity = (float)($_POST['quantity'] ?? 0);
        $price = (float)($_POST['price'] ?? 0);
        $dueDate = $_POST['due_date'] ?? null;

        if ($product === '' || $length === '' || $factoryId <= 0 || $quantity <= 0 || $price <= 0 || $dueDate === null){
            // Hatalı girişleri geri döndür
            $error = '<br/><div class="alert alert-danger" role="alert">Lütfen formu eksiksiz doldurunuz.</div>';
        }else{
            try {
                $db->beginTransaction();

                // custom_order_items tablosuna ekle
                $stmt = $db->prepare("INSERT INTO custom_order_items (custom_order_id, product, length, factory_id, quantity, price, due_date, is_deleted, datetime, company_id) VALUES (?, ?, ?, ?, ?, ?, ?, 0, NOW(), ?)");
                $stmt->execute([$customOrderId, $product, $length, $factoryId, $quantity, $price, $dueDate, $authUser->company_id]);

                $db->commit();
                header("Location: customorder.php");
                exit();
            } catch (PDOException $e) {
                $db->rollBack();
                die("Hata oluştu: " . $e->getMessage());
            }
        }
    }

    // CUSTOM ORDER ITEM UPDATE
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_custom_order_item'])) {
        $itemId = (int)($_POST['id'] ?? 0);
        $product = trim($_POST['product'] ?? '');
        $length = trim($_POST['length'] ?? '');
        $quantity = (int)($_POST['quantity'] ?? 0);
        $price = (float)($_POST['price'] ?? 0);
        $factoryId = (int)($_POST['factory_id'] ?? 0);
        $dueDate = $_POST['due_date'] ?? null;

        // Doğrulama
        if ($itemId <= 0 || $product === '' || $length === '' || $quantity <= 0 || $price <= 0 || $factoryId <= 0 || $dueDate === null) {
            $error = '<br/><div class="alert alert-danger" role="alert">Lütfen formu eksiksiz ve doğru şekilde doldurunuz.</div>';
        } else {
            try {
                $stmt = $db->prepare("UPDATE custom_order_items 
                                  SET product = ?, length = ?, quantity = ?, price = ?, factory_id = ?, due_date = ?
                                  WHERE id = ? AND is_deleted = 0");
                $stmt->execute([$product, $length, $quantity, $price, $factoryId, $dueDate, $itemId]);

                header("Location: customorder.php");
                exit();
            } catch (PDOException $e) {
                $error = '<br/><div class="alert alert-danger" role="alert">Bir hata oluştu: ' . $e->getMessage() . '</div>';
            }
        }
    }

    // CUSTOM ORDER ITEM DELETE
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_custom_order_item'])) {
        $itemId = (int)($_POST['id'] ?? 0);
        if ($itemId <= 0) {
            echo '<div class="alert alert-danger">Geçersiz kayıt IDsi.</div>';
        } else {
            try {
                $stmt = $db->prepare("UPDATE custom_order_items SET is_deleted = 1 WHERE id = ?");
                $stmt->execute([$itemId]);

                if ($stmt->rowCount() > 0) {
                    // Başarı mesajı veya yönlendirme
                    header("Location: customorder.php");
                    exit;
                } else {
                    echo '<div class="alert alert-warning">Kayıt bulunamadı veya zaten silinmiş.</div>';
                }
            } catch (PDOException $e) {
                echo '<div class="alert alert-danger">Hata: ' . $e->getMessage() . '</div>';
            }
        }
    }
    //FORMS END

    //FACTORIES
    $factories = $db->query("SELECT * FROM factories ORDER BY name ASC")->fetchAll(PDO::FETCH_OBJ);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Özel Siparişler</title>
    <?php include 'template/head.php'; ?>
</head>
<body class="body-white">
<?php include 'template/banner.php' ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 d-none d-md-block br-grey">
            <?php include 'template/sidebar2.php'; ?>
        </div>
        <div class="col-md-10 col-12">
            <?= isset($error) ? $error : ''; ?>
            <div id="form-div" class="modal">
                <span class="close" onclick="closeModal()">&times;</span>
                <div>
                    <h4><b>Özel Sipariş Formu</b></h4>
                </div>
                <form action="" method="POST">
                    <div class="search-box mb-2">
                        <b>Firma</b>
                        <input name="client" id="firmainputu" type="text" class="form-control form-control-sm"
                               autocomplete="off" placeholder="Firma Adı"
                               value="<?= htmlspecialchars($_POST['client'] ?? '') ?>"/>
                        <ul class="list-group liveresult" id="firmasonuc" style="position: absolute; z-index: 1;"></ul>
                    </div>
                    <b>Ürün</b>
                    <input type="text" name="product" class="form-control form-control-sm mb-2" placeholder="Ürün Adı" value="<?= htmlspecialchars($_POST['product'] ?? '') ?>"/>
                    <b>Ölçü</b>
                    <input type="text" name="length" class="form-control form-control-sm mb-2" placeholder="Ölçü giriniz." value="<?= htmlspecialchars($_POST['length'] ?? '') ?>"/>
                    <b>Adet</b>
                    <input type="text" name="quantity" class="form-control form-control-sm mb-2" placeholder="Ürün adedini giriniz." value="<?= htmlspecialchars($_POST['quantity'] ?? '') ?>"/>
                    <b>Fiyat</b>
                    <input type="text" name="price" class="form-control form-control-sm mb-2" placeholder="Fiyatı yazınız." value="<?= htmlspecialchars($_POST['price'] ?? '') ?>"/>
                    <b>Fabrika</b>
                    <select class="form-control form-control-sm mb-2" name="factory_id">
                        <option value="0" <?= (($_POST['factory_id'] ?? '') == 0) ? 'selected' : '' ?>>Fabrika Seçiniz</option>
                        <?php foreach ($factories as $factory): ?>
                            <option value="<?= $factory->id ?>" <?= (($_POST['factory_id'] ?? '') == $factory->id) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($factory->name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <b>Termin Tarihi</b>
                    <input type="date" name="due_date" class="form-control form-control-sm mb-2" value="<?= $_POST['due_date'] ?? '' ?>"/>
                    <b>Sevk Tipi</b>
                    <select name="delivery_type" class="form-control form-control-sm mb-2">
                        <option value="null">Sevk tipi seçiniz.</option>
                        <option value="0" <?= (isset($_POST['delivery_type']) && $_POST['delivery_type'] === '0') ? 'selected' : '' ?>>Müşteri Çağlayan</option>
                        <option value="1" <?= (isset($_POST['delivery_type']) && $_POST['delivery_type'] === '1') ? 'selected' : '' ?>>Müşteri Alkop</option>
                        <option value="2" <?= (isset($_POST['delivery_type']) && $_POST['delivery_type'] === '2') ? 'selected' : '' ?>>Tarafımızca sevk</option>
                        <option value="3" <?= (isset($_POST['delivery_type']) && $_POST['delivery_type'] === '3') ? 'selected' : '' ?>>Ambar tarafımızca sevk</option>
                        <option value="4" <?= (isset($_POST['delivery_type']) && $_POST['delivery_type'] === '4') ? 'selected' : '' ?>>Fabrikadan Teslim</option>
                    </select>
                    <b>Açıklama</b>
                    <textarea class="form-control form-control-sm mb-2" name="description"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                    <button type="submit" class="btn btn-primary btn-block" name="add_custom_order">Kaydet</button>
                </form>
            </div>
            <div class="row pl-3 pb-4 pr-3 bb-grey">
                <div style="text-align: right; display: block; width: 100%;">
                    <a href="custom_order_archive.php" target="_blank">
                        <button class="btn btn-primary btn-sm mb-2" style="background-color: #6c757d; border-color: #545b62;">
                            <i class="fas fa-archive mr-2"></i>
                            Arşiv
                        </button>
                    </a>
                    <a href="custom_orders_report.php" target="_blank">
                        <button class="btn btn-primary btn-sm mb-2" style="background-color: #17a2b8; border-color: #117a8b;">
                            <i class="fas fa-file-alt mr-2"></i>
                            Rapor
                        </button>
                    </a>
                    <a onclick="openModal('form-div')">
                        <button class="btn btn-primary btn-sm mb-2" style="background-color: #003566; border-color: #003566;">
                            <i class="fas fa-pen mr-2"></i>
                            Yeni Sipariş Girişi
                        </button>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                        <?php
                            foreach ($customOrders as $order) {
                                $clientName = getClientName($order->client_id);
                        ?>
                            <tr style="border-top:2px solid black">
                                <td><i class="fas fa-building mr-2" style="color:#004a8e;"></i><b><?= $clientName ?></b></td>
                                <td><i class="fas fa-truck mr-2" style="color:#004a8e"></i><?= $order->delivery_type_name ?></td>
                                <td><i class="fas fa-clock mr-2" style="color:#004a8e"></i><?= formatDateAndTime($order->datetime) ?></td>
                                <td class="display-flex">
                                    <!-- CLIENT LIST BUTTONS START -->
                                    <a onclick="openModal('add-item-div-<?= $order->id ?>')">
                                        <i class="fas fa-plus mr-3" style="color:green"></i>
                                    </a>
                                    <a onclick="openModal('edit-div-<?= $order->id ?>')">
                                        <i class="fas fa-pen mr-3" style="color:#004a8e"></i>
                                    </a>
                                    <form action="" method="POST">
                                        <input type="hidden" name="id" value="<?= $order->id ?>">
                                        <button type="submit" name="archive_custom_order" class="icon-button" style="width: 32px; padding-left:0" onclick="return confirmForm('<?= $clientName ?> adlı firmaya ait siparişi arşive gönderiyorsunuz, emin misiniz?')">
                                            <i class="fas fa-archive mr-3" style="color:#5a5a5a;"></i>
                                        </button>
                                    </form>
                                    <form action="" method="POST">
                                        <input type="hidden" name="id" value="<?= $order->id ?>">
                                        <button type="submit" name="delete_custom_order" class="icon-button" style="width: 32px; padding-left:0" onclick="return confirmForm('<?= $clientName ?> adlı firmaya ait siparişi silmek istediğinize emin misiniz?')">
                                            <i class="fas fa-trash mr-3" style="color:#ca0000;"></i>
                                        </button>
                                    </form>
                                    <!-- CLIENT LIST BUTTONS END -->
                                    <!-- CLIENT LIST OPENABLE DIVISIONS START -->
                                    <div id="add-item-div-<?= $order->id ?>" class="modal">
                                        <span class="close" onclick="closeModal()">&times;</span>
                                        <div>
                                            <h4><b>Siparişe Ürün Ekleme Formu</b></h4>
                                        </div>
                                        <form action="" method="POST">
                                            <b>Ürün</b>
                                            <input type="text" name="product" class="form-control form-control-sm mb-2" placeholder="Ürün Adı" value="<?= htmlspecialchars($_POST['product'] ?? '') ?>"/>
                                            <b>Ölçü</b>
                                            <input type="text" name="length" class="form-control form-control-sm mb-2" placeholder="Ölçü giriniz." value="<?= htmlspecialchars($_POST['length'] ?? '') ?>"/>
                                            <b>Adet</b>
                                            <input type="text" name="quantity" class="form-control form-control-sm mb-2" placeholder="Ürün adedini giriniz." value="<?= htmlspecialchars($_POST['quantity'] ?? '') ?>"/>
                                            <b>Fiyat</b>
                                            <input type="text" name="price" class="form-control form-control-sm mb-2" placeholder="Fiyatı yazınız." value="<?= htmlspecialchars($_POST['price'] ?? '') ?>"/>
                                            <b>Fabrika</b>
                                            <select class="form-control form-control-sm mb-2" name="factory_id">
                                                <option value="0" <?= (($_POST['factory_id'] ?? '') == 0) ? 'selected' : '' ?>>Fabrika Seçiniz</option>
                                                <?php foreach ($factories as $factory): ?>
                                                    <option value="<?= $factory->id ?>" <?= (($_POST['factory_id'] ?? '') == $factory->id) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($factory->name) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <b>Termin Tarihi</b>
                                            <input type="date" name="due_date" class="form-control form-control-sm mb-2" value="<?= $_POST['due_date'] ?? '' ?>"/>
                                            <input type="hidden" name="custom_order_id" value="<?= $order->id ?>" />
                                            <button type="submit" class="btn btn-primary btn-block" name="add_item_custom_order">Kaydet</button>
                                        </form>
                                    </div>
                                    <div id="edit-div-<?= $order->id ?>" class="modal">
                                        <span class="close" onclick="closeModal()">&times;</span>
                                        <div>
                                            <h5><b>Özel Sipariş Düzenleme Formu</b></h5>
                                        </div>
                                        <form action="" method="POST">
                                                <div class="search-box mb-2">
                                                    <b>Firma</b>
                                                    <input name="client" id="firmainputu" type="text" class="form-control form-control-sm"
                                                           autocomplete="off" placeholder="Firma Adı"
                                                           value="<?= getClientName($order->client_id) ?>"/>
                                                    <ul class="list-group liveresult" id="firmasonuc" style="position: absolute; z-index: 1;"></ul>
                                                </div>
                                                <b>Sevk Tipi</b>
                                                <select name="delivery_type" class="form-control form-control-sm mb-2">
                                                    <option value="null">Sevk tipi seçiniz.</option>
                                                    <option value="0" <?= $order->delivery_type == '0' ? 'selected' : '' ?>>Müşteri Çağlayan</option>
                                                    <option value="1" <?= $order->delivery_type == '1' ? 'selected' : '' ?>>Müşteri Alkop</option>
                                                    <option value="2" <?= $order->delivery_type == '2' ? 'selected' : '' ?>>Tarafımızca sevk</option>
                                                    <option value="3" <?= $order->delivery_type == '3' ? 'selected' : '' ?>>Ambar tarafımızca sevk</option>
                                                    <option value="4" <?= $order->delivery_type == '4' ? 'selected' : '' ?>>Fabrikadan Teslim</option>
                                                </select>
                                                <b>Açıklama</b>
                                                <textarea class="form-control form-control-sm mb-2" name="description" placeholder="Sipariş hakkında detayları girebilirsiniz.">
                                                    <?= $order->description ?>
                                                </textarea>
                                                <input type="hidden" name="id" value="<?= $order->id ?>" />
                                                <button type="submit" class="btn btn-primary btn-block" name="edit_custom_order">Güncelle</button>
                                            </form>
                                    </div>
                                    <!-- CLIENT LIST OPENABLE DIVISIONS END -->
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4" class="p-0">
                                    <div class="table-responsive">
                                            <table class="table table-bordered mb-0">
                                                <thead>
                                                <tr style="color:#003566">
                                                    <th scope="col">Ürün</th>
                                                    <th scope="col">Ölçü</th>
                                                    <th scope="col">Adet</th>
                                                    <th scope="col">Fiyat</th>
                                                    <th scope="col">Fabrika</th>
                                                    <th scope="col">Termin Tarihi</th>
                                                    <th scope="col">İşlemler</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php foreach ($order->items as $item){ ?>
                                                    <tr>
                                                        <td><?= $item->product ?></td>
                                                        <td><?= $item->length ?></td>
                                                        <td><?= $item->quantity ?></td>
                                                        <td><?= $item->price ?></td>
                                                        <td><?= getFactoryNameById($factories, $item->factory_id) ?></td>
                                                        <td><?= formatDate($item->due_date) ?></td>
                                                        <td class="display-flex">
                                                            <a onclick="openModal('edit-items-div-<?= $item->id ?>')">
                                                                <i class="fas fa-pen mr-3" style="color:#003566"></i>
                                                            </a>
                                                            <form action="" method="POST">
                                                                <input type="hidden" name="id" value="<?= $item->id ?>" />
                                                                <button type="submit" name="delete_custom_order_item" class="icon-button" onclick="return confirm('Sipariş içindeki <?= $item->quantity ?> adet <?= $item->product ?> ürününü silmek istediğinize emin misiniz?')">
                                                                    <i class="fas fa-trash mr-3" style="color:#505050"></i>
                                                                </button>
                                                            </form>
                                                            <div id="edit-items-div-<?= $item->id ?>" class="modal" style="position: absolute; top: -50px; left: 50%; transform: translateX(-50%); z-index: 999;">
                                                                <span class="close mb-3" onclick="closeModal('edit-items-div-<?= $item->id ?>')">&times;</span>
                                                                <h5 class="mt-3"><b>Sipariş Ürün Düzenleme Formu</b></h5>
                                                                <form action="" method="POST">
                                                                    <b>Ürün</b>
                                                                    <input type="text" name="product" class="form-control form-control-sm mb-2" placeholder="Ürün Adı" value="<?= $item->product ?>"/>
                                                                    <b>Ölçü</b>
                                                                    <input type="text" name="length" class="form-control form-control-sm mb-2" placeholder="Ölçü giriniz." value="<?= $item->length ?>"/>
                                                                    <b>Adet</b>
                                                                    <input type="text" name="quantity" class="form-control form-control-sm mb-2" placeholder="Ürün adedini giriniz." value="<?= $item->quantity ?>"/>
                                                                    <b>Fiyat</b>
                                                                    <input type="text" name="price" class="form-control form-control-sm mb-2" placeholder="Fiyatı yazınız." value="<?= $item->price ?>"/>
                                                                    <b>Fabrika</b>
                                                                    <select class="form-control form-control-sm mb-2" name="factory_id">
                                                                        <option value="0" <?= (($_POST['factory_id'] ?? '') == 0) ? 'selected' : '' ?>>Fabrika Seçiniz</option>
                                                                        <?php foreach ($factories as $factory): ?>
                                                                            <option value="<?= $factory->id ?>" <?= ($item->factory_id == $factory->id) ? 'selected' : '' ?>>
                                                                                <?= htmlspecialchars($factory->name) ?>
                                                                            </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                    <b>Termin Tarihi</b>
                                                                    <input type="date" name="due_date" class="form-control form-control-sm mb-2" value="<?= $item->due_date ?>"/>
                                                                    <input type="hidden" name="id" value="<?= $item->id ?>" />
                                                                    <button type="submit" class="btn btn-primary btn-block" name="edit_custom_order_item">Güncelle</button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                    </div>
                                </div>
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
                                <td colspan="4" style="text-align: center; color: #003566; font-weight: bold;">
                                    Hiç özel sipariş kaydınız yoktur.
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'template/script.php'; ?>
</body>
</html>