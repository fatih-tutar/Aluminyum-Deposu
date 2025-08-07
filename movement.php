<?php
include 'functions/init.php';
if (!isLoggedIn()) {
    header("Location:login.php");
    exit();
}else{
    if($authUser->permissions->stock_flow != '1'){
        header("Location:index.php");
        exit();
    }else{
        if(isset($_POST['add_movement'])){
            $incoming = guvenlik($_POST['incoming']);
            $incoming = str_replace(",",".",$incoming);
            $outgoing = guvenlik($_POST['outgoing']);
            $outgoing = str_replace(",",".",$outgoing);
            $warehouseIncoming = guvenlik($_POST['warehouse_incoming']);
            $warehouseIncoming = str_replace(",",".",$warehouseIncoming);
            $warehouseOutgoing = guvenlik($_POST['warehouse_outgoing']);
            $warehouseOutgoing = str_replace(",",".",$warehouseOutgoing);
            $date = guvenlik($_POST['date']);

            if(!movementDateExists($date)){
                $query = $db->prepare("INSERT INTO movements SET incoming = ?, outgoing = ?, warehouse_incoming = ?, warehouse_outgoing = ?, date = ?, is_deleted = ?, company_id = ?");
                $insert = $query->execute(array($incoming,$outgoing,$warehouseIncoming,$warehouseOutgoing,$date,0,$authUser->company_id));
                header("Location:movement.php");
                exit();
            }else{
                $error = '<br/><div class="alert alert-danger" role="alert">Bu tarihle zaten bir kayıt var onu düzenleyebilirsiniz.</div>';
            }
        }

        if (isset($_POST['update_movement'])) {
            $id = guvenlik($_POST['id']);
            $incoming = guvenlik($_POST['incoming']);
            $incoming = str_replace(",",".",$incoming);
            $outgoing = guvenlik($_POST['outgoing']);
            $outgoing = str_replace(",",".",$outgoing);
            $warehouseIncoming = guvenlik($_POST['warehouse_incoming']);
            $warehouseIncoming = str_replace(",",".",$warehouseIncoming);
            $warehouseOutgoing = guvenlik($_POST['warehouse_outgoing']);
            $warehouseOutgoing = str_replace(",",".",$warehouseOutgoing);
            $date = guvenlik($_POST['date']);
            $query = $db->prepare("UPDATE movements SET date = ?, incoming = ?, outgoing = ?, warehouse_incoming = ?, warehouse_outgoing = ? WHERE id = ?");
            $update = $query->execute(array($date,$incoming,$outgoing,$warehouseIncoming,$warehouseOutgoing,$id));
            header("Location:movement.php");
            exit();
        }

        if (isset($_POST['delete_movement'])) {
            $id = guvenlik($_POST['id']);
            $query = $db->prepare("UPDATE movements SET is_deleted = ? WHERE id = ?");
            $update = $query->execute(array(1,$id));
            header("Location:movement.php");
            exit();
        }

        //PAGE MODE
        $weeklyMode = 0;
        if(isset($_GET['weekly_mode']) && $_GET['weekly_mode'] == 1 && empty($_GET['weekly_mode']) === false){
            $weeklyMode = 1;
        }

        //MOVEMENTS
        $movements = $db->query("SELECT * FROM movements WHERE is_deleted = 0 AND company_id = '{$authUser->company_id}' ORDER BY date DESC")->fetchAll(PDO::FETCH_OBJ);
        $lastMovement = $movements[0];
        $dateObj = new DateTime($lastMovement->date);
        $prevWeek = $dateObj->format('W');
        $prevMonth = $dateObj->format('m');
        $prevYear = $dateObj->format('Y');

        //PRODUCTS for TOTAL WEIGHTS
        $products = $db->query("SELECT * FROM urun WHERE silik = '0' AND sirketid = '{$authUser->company_id}'")->fetchAll(PDO::FETCH_OBJ);
        $storeTonage = 0;
        $warehouseTonage = 0;
        foreach ($products as $product) {
            $productStoreWeight = $product->urun_adet * $product->urun_birimkg;
            $productWarehouseWeight = ($product->urun_depo_adet + $product->urun_palet) * $product->urun_birimkg;
            $storeTonage += $productStoreWeight;
            $warehouseTonage += $productWarehouseWeight;
        }
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Gelen Giden</title>
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
                        <a onclick="openModal('form-div')">
                            <button class="btn btn-primary btn-sm mb-2" style="background-color: #003566; border-color: #003566;">
                                <i class="fas fa-pen mr-2"></i> Yeni Kayıt
                            </button>
                        </a>
                        <div id="form-div" class="modal">
                            <span class="close" onclick="closeModal()">&times;</span>
                            <h4><b>Yeni Kayıt</b></h4>
                            <form action="" method="POST">
                                <b>Tarih</b>
                                <input type="date" name="date" class="form-control form-control-sm mb-2" value="<?= $_POST['date'] ?? '' ?>"/>
                                <b>Çağlayan Giden</b>
                                <input type="text" name="outgoing" class="form-control form-control-sm mb-2" placeholder="Sadece sayı giriniz" value="<?= htmlspecialchars($_POST['outgoing'] ?? '') ?>"/>
                                <b>Çağlayan Gelen</b>
                                <input type="text" name="incoming" class="form-control form-control-sm mb-2" placeholder="Sadece sayı giriniz" value="<?= htmlspecialchars($_POST['incoming'] ?? '') ?>"/>
                                <b>Alkop Giden</b>
                                <input type="text" name="warehouse_outgoing" class="form-control form-control-sm mb-2" placeholder="Sadece sayı giriniz" value="<?= htmlspecialchars($_POST['warehouse_outgoing'] ?? '') ?>"/>
                                <b>Alkop Gelen</b>
                                <input type="text" name="warehouse_incoming" class="form-control form-control-sm mb-2" placeholder="Sadece sayı giriniz" value="<?= htmlspecialchars($_POST['warehouse_incoming'] ?? '') ?>"/>
                                <button type="submit" class="btn btn-primary btn-block" name="add_movement">Kaydet</button>
                            </form>
                        </div>
                    </div>
                    <div class="table-wrapper" style="max-height: 3000px; overflow-y: auto;">
                        <table class="table table-bordered th-vertical-align-middle td-vertical-align-middle">
                        <thead>
                            <tr>
                                <th colspan="3"><h5><b>Çağlayan Toplam Tonaj : </b><?= number_format($storeTonage, 2, ',', '.')." Kg" ?></h5></th>
                                <th colspan="3"><h5><b>Alkop Toplam Tonaj : </b><?= number_format($warehouseTonage, 2, ',', '.')." Kg" ?></h5></th>
                            </tr>
                            <tr>
                                <th><b>Tarih</b></th>
                                <th><b>Çağlayan Giden</b></th>
                                <th><b>Çağlayan Gelen</b></th>
                                <th><b>Alkop Giden</b></th>
                                <th><b>Alkop Gelen</b></th>
                                <th>
                                    <div class="d-flex justify-content-around">
                                        <?php if($weeklyMode == 0){ ?>
                                            <a href="movement.php?weekly_mode=1"><button class="btn btn-info btn-block btn-sm">Haftalık Mod</button></a>
                                        <?php }else{ ?>
                                            <a href="movement.php?weekly_mode=0"><button class="btn btn-info btn-block btn-sm">Günlük Mod</button></a>
                                        <?php } ?>
                                        <a href="movement-report.php" target="_blank"><button class="btn btn-secondary btn-sm btn-block ml-2">Rapor</button></a>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                                // Variable initialization with consistent naming start
                                $periods = ['weekly', 'monthly', 'yearly'];
                                $directions = ['In', 'Out'];
                                $types = ['', 'Alkop'];

                                foreach ($periods as $period) {
                                    foreach ($types as $type) {
                                        foreach ($directions as $dir) {
                                            ${$period . $type . $dir . 'Total'} = 0;
                                        }
                                    }
                                }
                                // Variable initialization with consistent naming end

                                foreach ($movements as $movement) {
                                    $movementDate = new DateTime($movement->date);
                                    $formattedMovementDate = $movementDate->format('d/m/Y');
                                    $week = $movementDate->format("W");
                                    $month = $movementDate->format("m");
                                    $year = $movementDate->format("Y");
                                    if($week != $prevWeek){
                            ?>
                                        <tr style="background-color: #0dcaf0">
                                            <td><?= $week.". Hafta Toplam"; ?></td>
                                            <td><?= $weeklyOutTotal." Kg <small>(Giden)</small>"; ?></td>
                                            <td><?= $weeklyInTotal." Kg <small>(Gelen)</small>"; ?></td>
                                            <td><?= $weeklyAlkopOutTotal." Kg <small>(Giden)</small>"; ?></td>
                                            <td><?= $weeklyAlkopInTotal." Kg <small>(Gelen)</small>"; ?></td>
                                        </tr>
                            <?php
                                        // Reset weekly totals
                                        $weeklyTotals = ['OutTotal', 'InTotal', 'AlkopOutTotal', 'AlkopInTotal'];
                                        foreach ($weeklyTotals as $var) {
                                            ${'weekly' . $var} = 0;
                                        }
                                        $prevWeek = $week;
                                    }

                                    if($month != $prevMonth){
                            ?>
                                        <tr style="background-color: #ffc107">
                                            <td><?= getTurkishMonthName($month+1)." Ayı Toplamı"; ?></td>
                                            <td><?= $monthlyOutTotal." Kg <small>(Giden)</small>"; ?></td>
                                            <td><?= $monthlyInTotal." Kg <small>(Gelen)</small>"; ?></td>
                                            <td><?= $monthlyAlkopOutTotal." Kg <small>(Giden)</small>"; ?></td>
                                            <td><?= $monthlyAlkopInTotal." Kg <small>(Gelen)</small>"; ?></td>
                                        </tr>
                            <?php
                                        // Reset monthly totals
                                        $monthlyTotals = ['OutTotal', 'InTotal', 'AlkopOutTotal', 'AlkopInTotal'];
                                        foreach ($monthlyTotals as $var) {
                                            ${'monthly' . $var} = 0;
                                        }
                                        $prevMonth = $month;
                                    }

                                    if($year != $prevYear) {
                            ?>
                                        <tr style="background-color: #198754">
                                            <td><?= $year." Yılı Toplamı"; ?></td>
                                            <td><?= $yearlyOutTotal." Kg <small>(Giden)</small>"; ?></td>
                                            <td><?= $yearlyInTotal." Kg <small>(Gelen)</small>"; ?></td>
                                            <td><?= $yearlyAlkopOutTotal." Kg <small>(Giden)</small>"; ?></td>
                                            <td><?= $yearlyAlkopInTotal." Kg <small>(Gelen)</small>"; ?></td>
                                        </tr>
                            <?php
                                        // Reset monthly totals
                                        $yearlyTotals = ['OutTotal', 'InTotal', 'AlkopOutTotal', 'AlkopInTotal'];
                                        foreach ($yearlyTotals as $var) {
                                            ${'yearly' . $var} = 0;
                                        }
                                        $prevYear = $year;
                                    }

                                    // Add current movement values to weekly, monthly, and yearly totals
                                    $values = [
                                        'InTotal'        => $movement->incoming,
                                        'OutTotal'       => $movement->outgoing,
                                        'AlkopInTotal'   => $movement->warehouse_incoming,
                                        'AlkopOutTotal'  => $movement->warehouse_outgoing,
                                    ];

                                    foreach ($periods as $period) {
                                        foreach ($values as $suffix => $value) {
                                            ${$period . $suffix} += $value;
                                        }
                                    }
                                    // End of total accumulation
                            ?>
                                    <tr>
                                        <td><?= $formattedMovementDate ?></td>
                                        <td><?= $movement->outgoing ?> Kg</td>
                                        <td><?= $movement->incoming ?> Kg</td>
                                        <td><?= $movement->warehouse_outgoing ?> Kg</td>
                                        <td><?= $movement->warehouse_incoming ?> Kg</td>
                                        <td>
                                            <div class="d-flex justify-content-around">
                                                <a onclick="openModal('edit-div-<?= $movement->id ?>')">
                                                    <button class="btn btn-primary btn-sm">
                                                        <i class="fas fa-pen mr-2"></i> Düzenle
                                                    </button>
                                                </a>
                                                <div id="edit-div-<?= $movement->id ?>" class="modal">
                                                    <span class="close" onclick="closeModal()">&times;</span>
                                                    <h4><b>Kayıt Düzenleme Formu</b></h4>
                                                    <form action="" method="POST">
                                                        <b>Tarih</b>
                                                        <input type="date" name="date" class="form-control form-control-sm mb-2" value="<?= $movement->date ?>"/>
                                                        <b>Çağlayan Giden</b>
                                                        <input type="text" name="outgoing" class="form-control form-control-sm mb-2" placeholder="Sadece sayı giriniz" value="<?= $movement->outgoing ?>"/>
                                                        <b>Çağlayan Gelen</b>
                                                        <input type="text" name="incoming" class="form-control form-control-sm mb-2" placeholder="Sadece sayı giriniz" value="<?= $movement->incoming ?>"/>
                                                        <b>Alkop Giden</b>
                                                        <input type="text" name="warehouse_outgoing" class="form-control form-control-sm mb-2" placeholder="Sadece sayı giriniz" value="<?= $movement->warehouse_outgoing ?>"/>
                                                        <b>Alkop Gelen</b>
                                                        <input type="text" name="warehouse_incoming" class="form-control form-control-sm mb-2" placeholder="Sadece sayı giriniz" value="<?= $movement->warehouse_incoming ?>"/>
                                                        <input type="hidden" name="id" value="<?= $movement->id ?>" />
                                                        <button type="submit" class="btn btn-primary btn-block" name="update_movement">Güncelle</button>
                                                    </form>
                                                </div>
                                                <form action="" method="POST">
                                                    <input type="hidden" name="id" value="<?= $movement->id ?>"/>
                                                    <button type="submit" name="delete_movement" class="btn btn-secondary btn-sm" onclick="return confirmForm('<?= (new DateTime($movement->date))->format('d/m/Y') ?> gününe ait kaydı silmek istediğinize emin misiniz?')">
                                                        <i class="fas fa-trash mr-2"></i> Sil
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                            <?php
                                }
                            ?>
                            <tr style="background-color: #0dcaf0">
                                <td><?= ($week - 1).". Hafta Toplam"; ?></td>
                                <td><?= $weeklyOutTotal." Kg <small>(Giden)</small>"; ?></td>
                                <td><?= $weeklyInTotal." Kg <small>(Gelen)</small>"; ?></td>
                                <td><?= $weeklyAlkopOutTotal." Kg <small>(Giden)</small>"; ?></td>
                                <td><?= $weeklyAlkopInTotal." Kg <small>(Gelen)</small>"; ?></td>
                            </tr>
                            <tr style="background-color: #ffc107">
                                <td><?= getTurkishMonthName($month)." Ayı Toplamı"; ?></td>
                                <td><?= $monthlyOutTotal." Kg <small>(Giden)</small>"; ?></td>
                                <td><?= $monthlyInTotal." Kg <small>(Gelen)</small>"; ?></td>
                                <td><?= $monthlyAlkopOutTotal." Kg <small>(Giden)</small>"; ?></td>
                                <td><?= $monthlyAlkopInTotal." Kg <small>(Gelen)</small>"; ?></td>
                            </tr>
                            <tr style="background-color: #198754">
                                <td><?= $year." Yılı Toplamı"; ?></td>
                                <td><?= $yearlyOutTotal." Kg <small>(Giden)</small>"; ?></td>
                                <td><?= $yearlyInTotal." Kg <small>(Gelen)</small>"; ?></td>
                                <td><?= $yearlyAlkopOutTotal." Kg <small>(Giden)</small>"; ?></td>
                                <td><?= $yearlyAlkopInTotal." Kg <small>(Gelen)</small>"; ?></td>
                            </tr>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>

        <?php include 'template/script.php'; ?>

    </body>
</html>