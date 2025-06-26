<?php
include 'functions/init.php';
if (!isLoggedIn()) {
    header("Location:login.php");
    exit();
}else{
    //CUSTOM ORDERS AND ITEMS START
    // Sorguyu çalıştır
    $stmt = $db->query("
        SELECT 
            co.id, co.client_id, co.delivery_type, co.description, co.datetime, co.created_by,
            coi.id AS item_id, coi.product, coi.length, coi.factory_id, coi.quantity, coi.price, coi.due_date
        FROM 
            custom_orders co
        LEFT JOIN 
            custom_order_items coi 
            ON co.id = coi.custom_order_id AND coi.is_deleted = 0
        WHERE 
            co.status = 0 AND co.is_deleted = 0
        ORDER BY 
            co.datetime ASC, coi.id ASC
    ");

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
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Siparişler Raporu</title>
    <?php include 'template/head.php'; ?>
</head>
<body class="body-white">
<div class="container-fluid">
    <h2 class="text-center mt-4">ÖZEL SİPARİŞLER RAPORU</h2>
    <table class="table table-bordered">
        <tbody>
        <?php
        foreach ($customOrders as $key => $order) {
            $clientName = getClientName($order->client_id);
            ?>
            <tr style="border-top:2px solid black">
                <td style="width: 7%"><?= $key + 1 ?></td>
                <td><b style="color:#003566">Firma : <?= $clientName ?></b></td>
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
<?php include 'template/script.php'; ?>
</body>
</html>