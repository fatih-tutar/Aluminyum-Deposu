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
        <title>Rapor</title>
        <?php include 'template/head.php'; ?>
    </head>
    <body>
	    <div class="container" style="background: white;">
            <br/><br/>
            <div class="row">
                <div class="col-md-4" style="text-align: center;"><img src="img/file/<?= $company->photo; ?>" style="width: 350px; height: auto;"></div>
                <div class="col-md-8" style="text-align: center; padding: 0px 30px 0px 30px;">
                    <p style="font-size: 15px;">
                        <?= str_replace("\n", "<br/>", $company->description); ?>
                    </p>
                </div>
            </div>
            <table class="table table-bordered th-vertical-align-middle td-vertical-align-middle">
                <thead>
                    <tr>
                        <th colspan="5" style="text-align:center; font-weight: bold; font-size: 32px;">Gelen / Giden Ürün Raporu <br/> (Haftalık - Aylık - Yıllık)</th>
                    </tr>
                    <tr>
                        <th><b>Tarih</b></th>
                        <th><b>Çağlayan Giden</b></th>
                        <th><b>Çağlayan Gelen</b></th>
                        <th><b>Alkop Giden</b></th>
                        <th><b>Alkop Gelen</b></th>
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

                    <tr>
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
                    }
                ?>
                    <tr>
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
	    <?php include 'template/script.php'; ?>
    </body>
</html>