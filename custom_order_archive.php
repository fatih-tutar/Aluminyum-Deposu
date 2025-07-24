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
            co.status = 1 AND co.is_deleted = 0 AND co.company_id = ?
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
    // CUSTOM ORDER ARCHIVE
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['unarchive_custom_order'])) {
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo '<div class="alert alert-danger">Geçersiz kayıt IDsi.</div>';
        } else {
            try {
                $stmt = $db->prepare("UPDATE custom_orders SET status = 0 WHERE id = ?");
                $stmt->execute([$id]);

                if ($stmt->rowCount() > 0) {
                    // Başarı mesajı veya yönlendirme
                    header("Location: custom_order_archive.php");
                    exit;
                } else {
                    echo '<div class="alert alert-warning">Kayıt bulunamadı veya arşive zaten gönderilmiş.</div>';
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
    <title>Özel Siparişler Arşivi</title>
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
                            <td>
                                <form action="" method="POST">
                                    <input type="hidden" name="id" value="<?= $order->id ?>">
                                    <button type="submit" class="btn btn-secondary btn-sm" name="unarchive_custom_order" onclick="return confirmForm('<?= $clientName ?> adlı firmaya ait siparişi arşivden geri gönderiyorsunuz, emin misiniz?')">
                                        Geri Yükle
                                    </button>
                                </form>
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