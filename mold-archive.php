<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'functions/init.php';

if(!isLoggedIn()){

    header("Location:index.php");

    exit();

}else{

    if($authUser->type == '0'){

        header("Location:index.php");

        exit();

    }else{

        if (isset($_POST['unarchive_mold'])) {
            $id = $_POST['id'];

            // Önce kalıp mevcut mu kontrol et
            $stmt = $db->prepare("SELECT id FROM molds WHERE id = :id AND is_deleted = 0");
            $stmt->execute([':id' => $id]);
            $mold = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$mold) {
                $error = '<div class="alert alert-danger" role="alert">Kalıp bulunamadı veya silinmiş.</div>';
                goto archiveEnd;
            }

            // is_archived = 1 yap
            $stmt = $db->prepare("UPDATE molds SET is_archived = 0 WHERE id = :id");
            $success = $stmt->execute([':id' => $id]);

            if ($success) {
                $error = '<div class="alert alert-success" role="alert">Kalıp arşivden başarıyla geri yüklendi.</div>';
            } else {
                $errorInfo = $stmt->errorInfo();
                $error = '<div class="alert alert-danger" role="alert">Arşivleme hatası: ' . htmlspecialchars($errorInfo[2]) . '</div>';
            }

            archiveEnd:
        }

        //FACTORIES
        $factories = $db->query("SELECT * FROM factories WHERE company_id = '{$authUser->company_id}' ORDER BY name ASC")->fetchAll(PDO::FETCH_OBJ);

        // SQL sorgusu zaten client_name ile geliyor
        $sql = "
            SELECT molds.*, clients.name AS client_name
            FROM molds
            JOIN clients ON molds.client_id = clients.id
            WHERE molds.company_id = :company_id AND molds.is_deleted = 0 AND molds.is_archived = 1
            ORDER BY clients.name ASC
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute([':company_id' => $authUser->company_id]);
        $molds = $stmt->fetchAll(PDO::FETCH_OBJ);

        // Firma adına göre grupla
        $groupedMolds = [];
        foreach ($molds as $mold) {
            $clientName = $mold->client_name;
            if (!isset($groupedMolds[$clientName])) {
                $groupedMolds[$clientName] = [];
            }
            $groupedMolds[$clientName][] = $mold;
        }
    }
}

?>

<!DOCTYPE html>

<html>

  <head>

    <title>Alüminyum Deposu</title>

    <?php include 'template/head.php'; ?>

  </head>

  <body class="body-white">

    <?php include 'template/banner.php' ?>

    <div class="container-fluid">
        <div class="row">
            <div id="sidebar" class="col-md-2 d-none">
                <?php include 'template/sidebar2.php'; ?>
            </div>
            <div id="mainCol" class="col-md-12 col-12">
                <?= isset($error) ? $error : ''; ?>
                <div class="d-flex justify-content-between align-items-center p-2">
                    <div>
                        <button id="menuToggleBtn" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-bars"></i> Menü
                        </button>
                    </div>
                    <div>
                        <a href="mold.php">
                            <button class="btn btn-primary btn-sm mb-2" style="background-color: #003566; border-color: #003566;">
                                <i class="fas fa-list mr-2"></i> Kalıp Listesi
                            </button>
                        </a>
                    </div>
                </div>
                <div class="table-responsive mt-4">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>Firma Adı</th>
                            <th>Kalıp Sayısı</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($groupedMolds as $clientName => $moldItems): ?>
                            <?php
                            $moldCount = count($moldItems);
                            // Benzersiz id oluşturmak için client adını id'ye çevirelim (boşluk ve özel karakterleri kaldır)
                            $toggleId = "toggle-" . preg_replace('/[^a-zA-Z0-9]/', '', $clientName);
                            ?>
                            <tr class="client-row" style="cursor:pointer; background-color: white" onclick="toggleMolds('<?= $toggleId ?>')">
                                <td><strong><?= htmlspecialchars($clientName) ?></strong></td>
                                <td><?= $moldCount ?></td>
                            </tr>
                            <tr id="<?= $toggleId ?>" class="mold-details" style="display:none;">
                                <td colspan="3" style="padding:0;">
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
                                        <?php foreach ($moldItems as $item): ?>
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
                                                    <form action="" method="POST">
                                                        <input type="hidden" name="id" value="<?= $item->id ?>">
                                                        <button type="submit" class="icon-button"name="unarchive_mold" onclick="return confirmForm('<?= $item->number ?> kodlu kalıbı arşivden geri gönderiyorsunuz, emin misiniz?')">
                                                            <i class="fas fa-undo"></i>
                                                        </button>
                                                    </form>
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
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php include 'template/script.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('menuToggleBtn');
            const closeBtn = document.getElementById('closeSidebarBtn');
            const mainCol = document.getElementById('mainCol');

            if (toggleBtn) {
                toggleBtn.addEventListener('click', () => {
                    sidebar.classList.toggle('d-none');
                    mainCol.classList.toggle('col-md-12');
                    mainCol.classList.toggle('col-md-9');
                });
            }

            if (closeBtn) {
                closeBtn.addEventListener('click', () => {
                    sidebar.classList.add('d-none');
                    mainCol.classList.remove('col-md-9');
                    mainCol.classList.add('col-md-12');
                });
            }
        });

        function toggleMolds(id) {
            const row = document.getElementById(id);
            if (row.style.display === "none" || row.style.display === "") {
                row.style.display = "table-row";
            } else {
                row.style.display = "none";
            }
        }
    </script>
  </body>
</html>