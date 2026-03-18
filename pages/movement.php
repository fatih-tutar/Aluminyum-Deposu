<?php
require_once __DIR__.'/../config/init.php';
if (!isLoggedIn()) {
    header("Location:/login");
    exit();
}else{
    if($authUser->permissions->stock_flow != '1'){
        header("Location:/");
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

            if (empty($date)) {
                $error = '<br/><div class="alert alert-danger" role="alert">Tarih alanı boş bırakılamaz.</div>';
            } elseif (!movementDateExists($date)){
                $query = $db->prepare("INSERT INTO movements SET incoming = ?, outgoing = ?, warehouse_incoming = ?, warehouse_outgoing = ?, date = ?, is_deleted = ?, company_id = ?");
                $insert = $query->execute(array($incoming,$outgoing,$warehouseIncoming,$warehouseOutgoing,$date,0,$authUser->company_id));
                header("Location:/movement");
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
            header("Location:/movement");
            exit();
        }

        if (isset($_POST['delete_movement'])) {
            $id = guvenlik($_POST['id']);
            $query = $db->prepare("UPDATE movements SET is_deleted = ? WHERE id = ?");
            $update = $query->execute(array(1,$id));
            header("Location:/movement");
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

        // Monthly chart data (for all time)
        $monthlyChartData = [];
        foreach ($movements as $m) {
            $d = new DateTime($m->date);
            $key = $d->format('Y-m');
            if (!isset($monthlyChartData[$key])) {
                $monthlyChartData[$key] = [
                    'out'        => 0, // Çağlayan Giden
                    'in'         => 0, // Çağlayan Gelen
                    'alkop_out'  => 0, // Alkop Giden
                    'alkop_in'   => 0, // Alkop Gelen
                ];
            }
            $monthlyChartData[$key]['out']       += (float)$m->outgoing;
            $monthlyChartData[$key]['in']        += (float)$m->incoming;
            $monthlyChartData[$key]['alkop_out'] += (float)$m->warehouse_outgoing;
            $monthlyChartData[$key]['alkop_in']  += (float)$m->warehouse_incoming;
        }
        ksort($monthlyChartData);

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
        $totalTonage = $storeTonage + $warehouseTonage;
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Gelen Giden</title>
        <?php include ROOT_PATH.'/template/head.php'; ?>
    </head>
    <body class="body-white">
        <?php include ROOT_PATH.'/template/banner.php' ?>
        <div class="container-fluid">
            <div class="row">
                <div id="sidebar" class="sidebar col-md-2 pe-0">
                    <button id="closeSidebar" class="close-btn">&times;</button>
                    <?php include ROOT_PATH.'/template/sidebar2.php'; ?>
                </div>
                <div id="mainCol" class="col-md-10 col-12">
                    <?= isset($error) ? $error : ''; ?>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <button id="menuToggleBtn" class="btn btn-outline-primary btn-sm me-2">
                                <i class="fas fa-bars"></i> Menü
                            </button>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-secondary btn-sm" id="toggleChartBtn">
                                <i class="fas fa-chart-line me-1"></i> Grafik
                            </button>
                            <a onclick="openModal('form-div')">
                                <button class="btn btn-primary btn-sm" style="background-color: #003566; border-color: #003566;">
                                    <i class="fas fa-pen me-2"></i> Yeni Kayıt
                                </button>
                            </a>
                        </div>
                        <div id="form-div" class="modal">
                            <span class="close" onclick="closeModal()">&times;</span>
                            <h4><b>Yeni Kayıt</b></h4>
                            <form action="" method="POST">
                                <b>Tarih</b>
                                <input type="date" name="date" class="form-control form-control-sm mb-2" value="<?= $_POST['date'] ?? '' ?>" required/>
                                <b>Çağlayan Giden</b>
                                <input type="text" name="outgoing" class="form-control form-control-sm mb-2" placeholder="Sadece sayı giriniz" value="<?= htmlspecialchars($_POST['outgoing'] ?? '') ?>"/>
                                <b>Çağlayan Gelen</b>
                                <input type="text" name="incoming" class="form-control form-control-sm mb-2" placeholder="Sadece sayı giriniz" value="<?= htmlspecialchars($_POST['incoming'] ?? '') ?>"/>
                                <b>Alkop Giden</b>
                                <input type="text" name="warehouse_outgoing" class="form-control form-control-sm mb-2" placeholder="Sadece sayı giriniz" value="<?= htmlspecialchars($_POST['warehouse_outgoing'] ?? '') ?>"/>
                                <b>Alkop Gelen</b>
                                <input type="text" name="warehouse_incoming" class="form-control form-control-sm mb-2" placeholder="Sadece sayı giriniz" value="<?= htmlspecialchars($_POST['warehouse_incoming'] ?? '') ?>"/>
                                <button type="submit" class="btn btn-primary w-100" name="add_movement">Kaydet</button>
                            </form>
                        </div>
                    </div>
                    <div id="movementChartContainer" class="mb-5" style="display:none;">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title mb-2"><b>Aylık Toplamlar (Tüm Zamanlar)</b></h6>
                                <div id="movementChartLegend" class="mb-2 small"></div>
                                <canvas id="movementChart" height="110"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="table-wrapper mt-3" style="max-height: 3000px; overflow-y: auto;">
                        <table class="table table-bordered th-vertical-align-middle td-vertical-align-middle">
                        <thead>
                            <tr>
                                <th colspan="2"><h5><b>Çağlayan Toplam Tonaj : </b><?= number_format($storeTonage, 2, ',', '.')." Kg" ?></h5></th>
                                <th colspan="2"><h5><b>Alkop Toplam Tonaj : </b><?= number_format($warehouseTonage, 2, ',', '.')." Kg" ?></h5></th>
                                <th colspan="2"><h5><b>Toplam Tonaj : </b><?= number_format($totalTonage, 2, ',', '.')." Kg" ?></h5></th>
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
                                            <a href="/movement?weekly_mode=1"><button class="btn btn-info w-100 btn-sm">Haftalık Mod</button></a>
                                        <?php }else{ ?>
                                            <a href="/movement?weekly_mode=0"><button class="btn btn-info w-100 btn-sm">Günlük Mod</button></a>
                                        <?php } ?>
                                        <a href="/movement-report" target="_blank"><button class="btn btn-secondary btn-sm w-100 ms-2">Rapor</button></a>
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
                                        <tr class="table-info">
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
                                        <tr class="table-warning">
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
                                        <tr class="table-success">
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
                                    // Günlük satırları sadece günlük modda göster
                                    if ($weeklyMode == 0) {
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
                                                            <i class="fas fa-pen me-2"></i> Düzenle
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
                                                            <button type="submit" class="btn btn-primary w-100" name="update_movement">Güncelle</button>
                                                        </form>
                                                    </div>
                                                    <form action="" method="POST">
                                                        <input type="hidden" name="id" value="<?= $movement->id ?>"/>
                                                        <button type="submit" name="delete_movement" class="btn btn-secondary btn-sm" onclick="return confirmForm('<?= (new DateTime($movement->date))->format('d/m/Y') ?> gününe ait kaydı silmek istediğinize emin misiniz?')">
                                                            <i class="fas fa-trash me-2"></i> Sil
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                            <?php
                                    }
                                }
                            ?>
                            <tr class="table-info">
                                <td><?= ($week - 1).". Hafta Toplam"; ?></td>
                                <td><?= $weeklyOutTotal." Kg <small>(Giden)</small>"; ?></td>
                                <td><?= $weeklyInTotal." Kg <small>(Gelen)</small>"; ?></td>
                                <td><?= $weeklyAlkopOutTotal." Kg <small>(Giden)</small>"; ?></td>
                                <td><?= $weeklyAlkopInTotal." Kg <small>(Gelen)</small>"; ?></td>
                            </tr>
                            <tr class="table-warning">
                                <td><?= getTurkishMonthName($month)." Ayı Toplamı"; ?></td>
                                <td><?= $monthlyOutTotal." Kg <small>(Giden)</small>"; ?></td>
                                <td><?= $monthlyInTotal." Kg <small>(Gelen)</small>"; ?></td>
                                <td><?= $monthlyAlkopOutTotal." Kg <small>(Giden)</small>"; ?></td>
                                <td><?= $monthlyAlkopInTotal." Kg <small>(Gelen)</small>"; ?></td>
                            </tr>
                            <tr class="table-success">
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

        <?php include ROOT_PATH.'/template/script.php'; ?>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            (function() {
                const menuToggle = document.getElementById('menuToggleBtn');
                const sidebar = document.getElementById('sidebar');
                const mainCol = document.getElementById('mainCol');
                const closeSidebar = document.getElementById('closeSidebar');
                if (menuToggle) menuToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('sidebar-open');
                    mainCol.classList.toggle('sidebar-open');
                });
                if (closeSidebar) closeSidebar.addEventListener('click', function() {
                    sidebar.classList.remove('sidebar-open');
                    mainCol.classList.remove('sidebar-open');
                });

                const chartContainer = document.getElementById('movementChartContainer');
                const chartBtn = document.getElementById('toggleChartBtn');
                let movementChart = null;
                const monthlyData = <?= json_encode($monthlyChartData, JSON_UNESCAPED_UNICODE); ?>;

                function buildMovementChart() {
                    if (!monthlyData || Object.keys(monthlyData).length === 0) return;
                    const labels = Object.keys(monthlyData);
                    const cOut = [], cIn = [], aOut = [], aIn = [], totalOut = [], totalIn = [];
                    labels.forEach(key => {
                        const m = monthlyData[key];
                        cOut.push(m.out);
                        cIn.push(m.in);
                        aOut.push(m.alkop_out);
                        aIn.push(m.alkop_in);
                        totalOut.push(m.out + m.alkop_out);
                        totalIn.push(m.in + m.alkop_in);
                    });
                    const ctx = document.getElementById('movementChart').getContext('2d');
                    movementChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [
                                {
                                    label: 'Çağlayan Giden',
                                    data: cOut,
                                    borderColor: 'rgba(220, 53, 69, 1)',
                                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                                    tension: 0.2,
                                    hidden: true
                                },
                                {
                                    label: 'Çağlayan Gelen',
                                    data: cIn,
                                    borderColor: 'rgba(25, 135, 84, 1)',
                                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                                    tension: 0.2,
                                    hidden: true
                                },
                                {
                                    label: 'Alkop Giden',
                                    data: aOut,
                                    borderColor: 'rgba(13, 110, 253, 1)',
                                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                                    tension: 0.2,
                                    hidden: true
                                },
                                {
                                    label: 'Alkop Gelen',
                                    data: aIn,
                                    borderColor: 'rgba(255, 193, 7, 1)',
                                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                                    tension: 0.2,
                                    hidden: true
                                },
                                {
                                    label: 'Toplam Giden',
                                    data: totalOut,
                                    borderColor: 'rgba(0, 0, 0, 1)',
                                    borderWidth: 3,
                                    backgroundColor: 'rgba(0, 0, 0, 0)',
                                    tension: 0.25
                                },
                                {
                                    label: 'Toplam Gelen',
                                    data: totalIn,
                                    borderColor: 'rgba(102, 16, 242, 1)',
                                    borderDash: [6, 4],
                                    borderWidth: 3,
                                    backgroundColor: 'rgba(102, 16, 242, 0.05)',
                                    tension: 0.25,
                                    hidden: true
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            interaction: { mode: 'index', intersect: false },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: { display: true, text: 'Kg' }
                                },
                                x: {
                                    ticks: { maxRotation: 0, minRotation: 0 }
                                }
                            }
                        }
                    });

                    // Legend with checkboxes
                    const legendEl = document.getElementById('movementChartLegend');
                    if (legendEl) {
                        const ds = movementChart.data.datasets;
                        const html = ds.map((d, idx) => {
                            const color = d.borderColor || '#000';
                            // Default only Toplam Giden (index 4) checked
                            const checked = idx === 4 ? 'checked' : '';
                            // Apply initial visibility
                            if (idx !== 4) d.hidden = true;
                            return `
                                <label class="me-2">
                                    <input type="checkbox" class="movement-ds-toggle" data-index="${idx}" ${checked}>
                                    <span style="display:inline-block;width:10px;height:10px;background:${color};margin:0 3px 1px 0;"></span>
                                    ${d.label}
                                </label>
                            `;
                        }).join('');
                        legendEl.innerHTML = html;

                        legendEl.querySelectorAll('.movement-ds-toggle').forEach(function(cb) {
                            cb.addEventListener('change', function() {
                                const i = parseInt(this.getAttribute('data-index'), 10);
                                const dsItem = movementChart.data.datasets[i];
                                dsItem.hidden = !this.checked;
                                movementChart.update();
                            });
                        });

                        // Ensure initial state (only Toplam Giden visible)
                        movementChart.update();
                    }
                }

                if (chartBtn && chartContainer) {
                    chartBtn.addEventListener('click', function() {
                        const isHidden = chartContainer.style.display === 'none' || chartContainer.style.display === '';
                        chartContainer.style.display = isHidden ? 'block' : 'none';
                        if (isHidden && !movementChart) {
                            buildMovementChart();
                        }
                    });
                }
            })();
        </script>
    </body>
</html>