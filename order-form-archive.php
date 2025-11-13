<?php
include 'functions/init.php';
if (!isLoggedIn()) {
    header("Location:login.php");
    exit();
}else{
    if(isset($_GET['id'])){
        $factoryId = guvenlik($_GET['id']);
        $factory = getFactory($factoryId);
        $orderForms = $db->query("SELECT * FROM siparisformlari WHERE fabrikaid = '{$factoryId}' AND sirketid = '{$authUser->company_id}' AND silik = '0' ORDER BY formid DESC")->fetchAll(PDO::FETCH_OBJ);
    }

    if(isset($_POST['delete_order'])){
        $id = guvenlik($_POST['id']);
        $query = $db->prepare("UPDATE siparis SET silik = ? WHERE siparis_id = ?");
        $delete = $query->execute(array('1',$id));
        header("Location:order-form-archive.php?id=".$factoryId);
        exit();
    }

    if (isset($_POST['delete_order_form'])) {
        $id = guvenlik($_POST['id']);
        $orderList = guvenlik($_POST['order_list']);
        $orderListArray = explode(",", $orderList);
        foreach ($orderListArray as $key => $value) {
            $query = $db->prepare("UPDATE siparis SET silik = ? WHERE siparis_id = ?");
            $delete = $query->execute(array('1',$value));
        }
        $query = $db->prepare("UPDATE siparisformlari SET silik = ? WHERE formid = ?");
        $delete = $query->execute(array('1',$id));
        header("Location:order-form-archive.php?id=".$factoryId);
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sipariş Formları</title>
    <?php include 'template/head.php'; ?>
</head>
<body class="body-white">
<?php include 'template/banner.php' ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 d-none d-md-block br-grey">
            <?php include 'template/sidebar2.php'; ?>
        </div>
        <div class="col-md-9 col-12">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th colspan="4" class="text-center"><?= $factory->name ?> SİPARİŞ FORMLARI</th>
                    </tr>
                    <tr>
                        <th>Sipariş Formu</th>
                        <th>Ürünler</th>
                        <th>Form</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
            <?php
            foreach ($orderForms as $orderForm):
                ?>
                <tr>
                    <td><?= date('d/m/Y H:i:s', $orderForm->saniye) ?> Tarihli Fabrika Sipariş Formu</td>
                    <td>
                        <button class="icon-button" onclick="openModal('product-div-<?= $orderForm->formid ?>')">
                            <i class="fa fa-eye"></i>
                        </button>
                        <div id="product-div-<?= $orderForm->formid ?>" class="modal" style="width: 70%;">
                            <span class="close" onclick="closeModal()">&times;</span>
                            <h5>Ürünler</h5>
                            <div class="table-responsive mt-2">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th>Ürün Adı</th>
                                        <th>Adet</th>
                                        <th>İlgili Kişi</th>
                                        <th>Hazırlayan</th>
                                        <th>Tarih</th>
                                        <th>İşlemler</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $orderList = explode(",", $orderForm->siparisler);
                                    if (!$orderForm->siparisler) { ?>
                                        <tr>
                                            <td colspan="6" style="text-align: center; color: #003566; font-weight: bold;">
                                                Bu form içerisinde hiç ürün yoktur.
                                            </td>
                                        </tr>
                                    <?php }
                                    if($orderList){
                                        foreach ($orderList as $orderItem):
                                            $orderItemObj = getOrder($orderItem);
                                            if (!$orderItemObj) {
                                                continue;
                                            }
                                            $orderItemProduct = getProduct($orderItemObj->urun_id);
                                            if (!$orderItemProduct) {
                                                continue;
                                            }
                                            $productName = $orderItemProduct->urun_adi;
                                            $mainCategory = getCategory($orderItemProduct->kategori_bir)->name;
                                            $subCategory = getCategory($orderItemProduct->kategori_iki)->name;
                                            ?>
                                            <tr>
                                                <td><?= $productName.' / '.$subCategory.' / '.$mainCategory ?></td>
                                                <td><?= $orderItemObj->urun_siparis_aded ?></td>
                                                <td><?= $orderItemObj->ilgilikisi ?></td>
                                                <td><?= $orderItemObj->hazirlayankisi ?></td>
                                                <td><?= date('d/m/Y',$orderItemObj->siparissaniye) ?></td>
                                                <td>
                                                    <form action="" method="POST">
                                                        <input type="hidden" name="id" value="<?= $orderItemObj->teklifid ?>">
                                                        <button type="submit" class="icon-button" name="delete_order" onclick="return confirmForm('Silmek istediğinize emin misiniz?');">
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
                        <a href="siparisform.php?id=<?= $orderForm->formid ?>" target="_blank">
                            <button class="icon-button">
                                <i class="fa fa-file"></i>
                            </button>
                        </a>
                    </td>
                    <td>
                        <form action="" method="POST">
                            <input type="hidden" name="id" value="<?= $orderForm->formid ?>" />
                            <input type="hidden" name="order_list" value="<?= $orderForm->siparisler ?>" />
                            <button type="submit" name="delete_order_form" class="icon-button" onclick="return confirmForm('Silmek istediğinize emin misiniz?');">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </table>
        </div>
    </div>
</div>
<?php include 'template/script.php'; ?>
</body>
</html>