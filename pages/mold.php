<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__.'/../config/init.php';

if(!isLoggedIn()){

    header("Location:/");

    exit();

}else{

    if($authUser->type == '0'){

        header("Location:/");

        exit();

    }else{
        if (isset($_POST['add_mold'])) {
            // Formdan gelen veriler
            $client = $_POST['client'] ?? '';
            $number = $_POST['number'] ?? '';
            $factoryId = $_POST['factory_id'] ?? '';
            $clientOfferPrice = $_POST['client_offer_price'] ?? '';
            $factoryOfferPrice = $_POST['factory_offer_price'] ?? '';
            $dueDate = $_POST['due_date'] ?? '';
            $contactPerson = $_POST['contact_person'] ?? '';
            $description = $_POST['description'] ?? '';

            // Zorunlu alan kontrolü (açıklama hariç)
            $requiredFields = [
                'client' => $client,
                'number' => $number,
                'factory_id' => $factoryId,
                'client_offer_price' => $clientOfferPrice,
                'factory_offer_price' => $factoryOfferPrice,
                'due_date' => $dueDate,
                'contact_person' => $contactPerson,
            ];

            foreach ($requiredFields as $fieldName => $value) {
                if (trim($value) === '' || ($fieldName === 'factory_id' && $value == '0')) {
                    $error = '<div class="alert alert-danger" role="alert">Boş bıraktığınız alanlar var lütfen kontrol ediniz.</div>';
                    goto end;
                }
            }

            // PDF dosyaları yükleme
            if (empty($_FILES['factory_pdf_file']['name'])) {
                $error = '<div class="alert alert-danger" role="alert">Fabrika PDF dosyası yüklenmelidir.</div>';
                goto end;
            }

            if (empty($_FILES['client_pdf_file']['name'])) {
                $error = '<div class="alert alert-danger" role="alert">Firma PDF dosyası yüklenmelidir.</div>';
                goto end;
            }

            if (empty($_FILES['contract_pdf_file']['name'])) {
                $error = '<div class="alert alert-danger" role="alert">Sözleşme PDF dosyası yüklenmelidir.</div>';
                goto end;
            }

            $uploadDir = 'files/molds/';
            if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true)) {
                $error = '<div class="alert alert-danger" role="alert">Dosya yükleme klasörü oluşturulamadı.</div>';
                goto end;
            }

            $factoryPdfFileName = uniqid() . '-' . basename($_FILES['factory_pdf_file']['name']);
            $factoryPdfTargetFile = $uploadDir . $factoryPdfFileName;

            $clientPdfFileName = uniqid() . '-' . basename($_FILES['client_pdf_file']['name']);
            $clientPdfTargetFile = $uploadDir . $clientPdfFileName;

            $contractPdfFileName = uniqid() . '-' . basename($_FILES['contract_pdf_file']['name']);
            $contractPdfTargetFile = $uploadDir . $contractPdfFileName;

            if (!move_uploaded_file($_FILES['factory_pdf_file']['tmp_name'], $factoryPdfTargetFile)) {
                $error = '<div class="alert alert-danger" role="alert">Fabrika PDF dosyası yüklenemedi.</div>';
                goto end;
            }

            if (!move_uploaded_file($_FILES['client_pdf_file']['tmp_name'], $clientPdfTargetFile)) {
                $error = '<div class="alert alert-danger" role="alert">Firma PDF dosyası yüklenemedi.</div>';
                goto end;
            }

            if (!move_uploaded_file($_FILES['contract_pdf_file']['tmp_name'], $contractPdfTargetFile)) {
                $error = '<div class="alert alert-danger" role="alert">Sözleşme PDF dosyası yüklenemedi.</div>';
                goto end;
            }

            // Veritabanında sadece dosya adlarını tut
            $factoryPdfPath = $factoryPdfFileName;
            $clientPdfPath = $clientPdfFileName;
            $contractPdfPath = $contractPdfFileName;

            // client_id alma
            $client_id = getClientId($client);
            if (!$client_id) {
                $error = '<div class="alert alert-danger" role="alert">Geçersiz müşteri bilgisi.</div>';
                goto end;
            }

            // Sabit alanlar
            $companyId = $authUser->company_id;
            $createdBy = $authUser->id;
            $isArchived = 0;
            $isDeleted = 0;

            // Veritabanına kayıt
            $stmt = $db->prepare("
                INSERT INTO molds (
                    client_id, number, factory_id,
                    client_offer_price, factory_offer_price, due_date,
                    factory_pdf, client_pdf, contract_pdf, contact_person, description,
                    company_id, is_archived, is_deleted, created_by
                ) VALUES (
                    :client_id, :number, :factory_id,
                    :client_offer_price, :factory_offer_price, :due_date,
                    :factory_pdf, :client_pdf, :contract_pdf, :contact_person, :description,
                    :company_id, :is_archived, :is_deleted, :created_by
                )
            ");

            $success = $stmt->execute([
                ':client_id' => $client_id,
                ':number' => $number,
                ':factory_id' => $factoryId,
                ':client_offer_price' => $clientOfferPrice,
                ':factory_offer_price' => $factoryOfferPrice,
                ':due_date' => $dueDate,
                ':factory_pdf' => $factoryPdfPath,
                ':client_pdf' => $clientPdfPath,
                ':contract_pdf' => $contractPdfPath,
                ':contact_person' => $contactPerson,
                ':description' => $description,
                ':company_id' => $companyId,
                ':is_archived' => $isArchived,
                ':is_deleted' => $isDeleted,
                ':created_by' => $createdBy,
            ]);

            if (!$success) {
                $errorInfo = $stmt->errorInfo();
                $error = '<div class="alert alert-danger" role="alert">Kayıt hatası: ' . htmlspecialchars($errorInfo[2]) . '</div>';
                goto end;
            }

            $error = '<div class="alert alert-success" role="alert">Kalıp başarıyla eklendi.</div>';

            end:
        }

        if (isset($_POST['update_mold'])) {
            $id = $_POST['id'];
            $client = $_POST['client'] ?? '';
            $number = $_POST['number'] ?? '';
            $factoryId = $_POST['factory_id'] ?? '';
            $clientOfferPrice = $_POST['client_offer_price'] ?? '';
            $factoryOfferPrice = $_POST['factory_offer_price'] ?? '';
            $dueDate = $_POST['due_date'] ?? '';
            $contactPerson = $_POST['contact_person'] ?? '';
            $description = $_POST['description'] ?? '';

            // Zorunlu alan kontrolü (açıklama hariç)
            $requiredFields = [
                'client' => $client,
                'number' => $number,
                'factory_id' => $factoryId,
                'client_offer_price' => $clientOfferPrice,
                'factory_offer_price' => $factoryOfferPrice,
                'due_date' => $dueDate,
                'contact_person' => $contactPerson,
            ];

            foreach ($requiredFields as $fieldName => $value) {
                if (trim($value) === '' || ($fieldName === 'factory_id' && $value == '0')) {
                    $error = '<div class="alert alert-danger" role="alert">Boş bıraktığınız alanlar var lütfen kontrol ediniz.</div>';
                    goto updateEnd;
                }
            }

            // client_id alma
            $client_id = getClientId($client);
            if (!$client_id) {
                $error = '<div class="alert alert-danger" role="alert">Geçersiz müşteri bilgisi.</div>';
                goto updateEnd;
            }

            // Mevcut kalıp verisini al (eski PDF dosyalarını silebilmek için)
            $stmt = $db->prepare("SELECT factory_pdf, client_pdf, contract_pdf FROM molds WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$existing) {
                $error = '<div class="alert alert-danger" role="alert">Kalıp bulunamadı.</div>';
                goto updateEnd;
            }

            $uploadDir = 'files/molds/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // PDF dosyaları kontrol ve yükleme (varsa)
            // Artık veritabanında sadece dosya adını tutuyoruz.
            $factoryPdfPath = $existing['factory_pdf'];
            if (!empty($_FILES['factory_pdf_file']['name'])) {
                $factoryPdfFileName = uniqid() . '-' . basename($_FILES['factory_pdf_file']['name']);
                $factoryPdfTargetFile = $uploadDir . $factoryPdfFileName;

                if (move_uploaded_file($_FILES['factory_pdf_file']['tmp_name'], $factoryPdfTargetFile)) {
                    // Eski dosyayı sil
                    if (!empty($existing['factory_pdf'])) {
                        $old = $existing['factory_pdf'];
                        $candidates = [];
                        if (strpos($old, 'files/') === 0 || strpos($old, 'img/') === 0) {
                            $candidates[] = $old;
                        }
                        $candidates[] = $uploadDir . $old;
                        foreach ($candidates as $path) {
                            if (file_exists($path)) {
                                @unlink($path);
                                break;
                            }
                        }
                    }
                    $factoryPdfPath = $factoryPdfFileName;
                } else {
                    $error = '<div class="alert alert-danger" role="alert">Fabrika PDF dosyası yüklenemedi.</div>';
                    goto updateEnd;
                }
            }

            $clientPdfPath = $existing['client_pdf'];
            if (!empty($_FILES['client_pdf_file']['name'])) {
                $clientPdfFileName = uniqid() . '-' . basename($_FILES['client_pdf_file']['name']);
                $clientPdfTargetFile = $uploadDir . $clientPdfFileName;

                if (move_uploaded_file($_FILES['client_pdf_file']['tmp_name'], $clientPdfTargetFile)) {
                    if (!empty($existing['client_pdf'])) {
                        $old = $existing['client_pdf'];
                        $candidates = [];
                        if (strpos($old, 'files/') === 0 || strpos($old, 'img/') === 0) {
                            $candidates[] = $old;
                        }
                        $candidates[] = $uploadDir . $old;
                        foreach ($candidates as $path) {
                            if (file_exists($path)) {
                                @unlink($path);
                                break;
                            }
                        }
                    }
                    $clientPdfPath = $clientPdfFileName;
                } else {
                    $error = '<div class="alert alert-danger" role="alert">Firma PDF dosyası yüklenemedi.</div>';
                    goto updateEnd;
                }
            }

            $contractPdfPath = $existing['contract_pdf'];
            if (!empty($_FILES['contract_pdf_file']['name'])) {
                $contractPdfFileName = uniqid() . '-' . basename($_FILES['contract_pdf_file']['name']);
                $contractPdfTargetFile = $uploadDir . $contractPdfFileName;

                if (move_uploaded_file($_FILES['contract_pdf_file']['tmp_name'], $contractPdfTargetFile)) {
                    if (!empty($existing['contract_pdf'])) {
                        $old = $existing['contract_pdf'];
                        $candidates = [];
                        if (strpos($old, 'files/') === 0 || strpos($old, 'img/') === 0) {
                            $candidates[] = $old;
                        }
                        $candidates[] = $uploadDir . $old;
                        foreach ($candidates as $path) {
                            if (file_exists($path)) {
                                @unlink($path);
                                break;
                            }
                        }
                    }
                    $contractPdfPath = $contractPdfFileName;
                } else {
                    $error = '<div class="alert alert-danger" role="alert">Sözleşme PDF dosyası yüklenemedi.</div>';
                    goto updateEnd;
                }
            }

            // Güncelleme sorgusu
            $stmt = $db->prepare("
                UPDATE molds SET
                    client_id = :client_id,
                    number = :number,
                    factory_id = :factory_id,
                    client_offer_price = :client_offer_price,
                    factory_offer_price = :factory_offer_price,
                    due_date = :due_date,
                    factory_pdf = :factory_pdf,
                    client_pdf = :client_pdf,
                    contract_pdf = :contract_pdf,
                    contact_person = :contact_person,
                    description = :description
                WHERE id = :id
            ");

            $success = $stmt->execute([
                ':client_id' => $client_id,
                ':number' => $number,
                ':factory_id' => $factoryId,
                ':client_offer_price' => $clientOfferPrice,
                ':factory_offer_price' => $factoryOfferPrice,
                ':due_date' => $dueDate,
                ':factory_pdf' => $factoryPdfPath,
                ':client_pdf' => $clientPdfPath,
                ':contract_pdf' => $contractPdfPath,
                ':contact_person' => $contactPerson,
                ':description' => $description,
                ':id' => $id,
            ]);

            if ($success) {
                $error = '<div class="alert alert-success" role="alert">Kalıp başarıyla güncellendi.</div>';
            } else {
                $errorInfo = $stmt->errorInfo();
                $error = '<div class="alert alert-danger" role="alert">Güncelleme hatası: ' . htmlspecialchars($errorInfo[2]) . '</div>';
            }

            updateEnd:
        }

        if (isset($_POST['delete_mold'])) {
            $id = $_POST['id'];

            // Kalıp kaydını bul
            $stmt = $db->prepare("SELECT factory_pdf, client_pdf, contract_pdf FROM molds WHERE id = :id AND is_deleted = 0");
            $stmt->execute([':id' => $id]);
            $mold = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$mold) {
                $error = '<div class="alert alert-danger" role="alert">Kalıp bulunamadı veya zaten silinmiş.</div>';
                goto deleteEnd;
            }

            // PDF dosyalarını sil
            $pdfFields = ['factory_pdf', 'client_pdf', 'contract_pdf'];
            foreach ($pdfFields as $field) {
                $value = $mold[$field] ?? '';
                if (empty($value)) {
                    continue;
                }
                $candidates = [];
                // Eski kayıtlar tam yol tutuyor olabilir
                if (strpos($value, 'files/') === 0 || strpos($value, 'img/') === 0) {
                    $candidates[] = $value;
                }
                // Normalize edilmiş kayıtlar sadece dosya adını tutuyor
                $candidates[] = 'files/molds/' . $value;

                foreach ($candidates as $path) {
                    if (file_exists($path)) {
                        unlink($path);
                        break;
                    }
                }
            }

            // Veritabanında is_deleted = 1 yap
            $stmt = $db->prepare("UPDATE molds SET is_deleted = 1 WHERE id = :id");
            $success = $stmt->execute([':id' => $id]);

            if ($success) {
                $error = '<div class="alert alert-success" role="alert">Kalıp başarıyla silindi.</div>';
            } else {
                $errorInfo = $stmt->errorInfo();
                $error = '<div class="alert alert-danger" role="alert">Silme hatası: ' . htmlspecialchars($errorInfo[2]) . '</div>';
            }

            deleteEnd:
        }

        if (isset($_POST['archive_mold'])) {
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
            $stmt = $db->prepare("UPDATE molds SET is_archived = 1 WHERE id = :id");
            $success = $stmt->execute([':id' => $id]);

            if ($success) {
                $error = '<div class="alert alert-success" role="alert">Kalıp başarıyla arşivlendi.</div>';
            } else {
                $errorInfo = $stmt->errorInfo();
                $error = '<div class="alert alert-danger" role="alert">Arşivleme hatası: ' . htmlspecialchars($errorInfo[2]) . '</div>';
            }

            archiveEnd:
        }

        //FACTORIES
        $factories = $db->query("SELECT * FROM factories WHERE company_id = '{$authUser->company_id}' ORDER BY name ASC")->fetchAll(PDO::FETCH_OBJ);

        //MOLDS
        $sql = "
            SELECT molds.*, clients.name AS client_name
            FROM molds
            JOIN clients ON molds.client_id = clients.id
            WHERE molds.company_id = :company_id AND molds.is_deleted = 0 AND molds.is_archived = 0
            ORDER BY clients.name ASC
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute([':company_id' => $authUser->company_id]);
        $molds = $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}

?>

<!DOCTYPE html>

<html>

  <head>

    <title>Alüminyum Deposu</title>

    <?php include ROOT_PATH.'/template/head.php'; ?>

  </head>

  <body class="body-white">

    <?php include ROOT_PATH.'/template/banner.php' ?>

    <div class="container-fluid">
        <div class="row">
            <div id="sidebar" class="sidebar col-md-2 pr-0">
                <button id="closeSidebar" class="close-btn">&times;</button>
                <?php include ROOT_PATH.'/template/sidebar2.php'; ?>
            </div>
            <div id="mainCol" class="col-md-10 col-12">
                <?= isset($error) ? $error : ''; ?>
                <div id="form-div" class="modal">
                    <span class="close" onclick="closeModal()">&times;</span>
                    <div>
                        <h4><b>Kalıp Kayıt Formu</b></h4>
                    </div>
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="search-box mb-2">
                            <b>Firma</b>
                            <input name="client" id="firmainputu" type="text" class="form-control form-control-sm"
                                   autocomplete="off" placeholder="Firma Adı"
                                   value="<?= htmlspecialchars($_POST['client'] ?? '') ?>"/>
                            <ul class="list-group liveresult" id="firmasonuc" style="position: absolute; z-index: 1;"></ul>
                        </div>
                        <b>Kalıp Numarası</b>
                        <input type="text" name="number" class="form-control form-control-sm mb-2" placeholder="Kalıp Numarası" value="<?= htmlspecialchars($_POST['number'] ?? '') ?>"/>
                        <b>Fabrika</b>
                        <select class="form-control form-control-sm mb-2" name="factory_id">
                            <option value="0" <?= (($_POST['factory_id'] ?? '') == 0) ? 'selected' : '' ?>>Fabrika Seçiniz</option>
                            <?php foreach ($factories as $factory): ?>
                                <option value="<?= $factory->id ?>" <?= (($_POST['factory_id'] ?? '') == $factory->id) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($factory->name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <b>Firmaya Verilen Teklif</b>
                        <input type="text" name="client_offer_price" class="form-control form-control-sm mb-2" placeholder="Firmaya verilen teklifi yazınız." value="<?= htmlspecialchars($_POST['client_offer_price'] ?? '') ?>"/>
                        <b>Fabrikadan Alınan Teklif</b>
                        <input type="text" name="factory_offer_price" class="form-control form-control-sm mb-2" placeholder="Fabrikadan alınan teklifi yazınız." value="<?= htmlspecialchars($_POST['factory_offer_price'] ?? '') ?>"/>
                        <b>Termin Tarihi</b>
                        <input type="date" name="due_date" class="form-control form-control-sm mb-2" value="<?= $_POST['due_date'] ?? '' ?>"/>
                        <div class="display-flex">
                            <div>
                                <b>Fabrika Onay Pdf</b><br/>
                                <input type="file" name="factory_pdf_file" style="margin-bottom: 10px; margin-right: 10px; max-width: 150px;"><br/>
                            </div>
                            <div>
                                <b>Firma Onay Pdf</b><br/>
                                <input type="file" name="client_pdf_file" style="margin-bottom: 10px; margin-right: 10px; max-width: 150px;"><br/>
                            </div>
                            <div>
                                <b>Sözleşme</b><br/>
                                <input type="file" name="contract_pdf_file" style="margin-bottom: 10px; margin-right: 10px; max-width: 150px;"><br/>
                            </div>
                        </div>
                        <b>İlgili Kişi</b>
                        <input type="text" name="contact_person" class="form-control form-control-sm mb-2" placeholder="İlgili firma yetkilisini yazınız." value="<?= htmlspecialchars($_POST['contact_person'] ?? '') ?>"/>
                        <b>Açıklama</b>
                        <textarea class="form-control form-control-sm mb-2" name="description"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                        <button type="submit" class="btn btn-primary w-100" name="add_mold">Kaydet</button>
                    </form>
                </div>
                <div class="d-flex justify-content-between align-items-center p-2">
                    <div>
                        <button id="menuToggleBtn" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-bars"></i> Menü
                        </button>
                    </div>
                    <div>
                        <a href="/mold-archive" target="_blank">
                            <button class="btn btn-primary btn-sm mb-2" style="background-color: #6c757d; border-color: #545b62;">
                                <i class="fas fa-archive mr-2"></i> Arşiv
                            </button>
                        </a>
                        <a onclick="openModal('form-div')">
                            <button class="btn btn-primary btn-sm mb-2" style="background-color: #003566; border-color: #003566;">
                                <i class="fas fa-pen mr-2"></i> Yeni Kalıp Girişi
                            </button>
                        </a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>Firma Adı</th>
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
                        // Firma adı al
                        $clientName = getClientName($item->client_id);

                        // Fabrika adı al
                        $factoryName = getFactoryNameById($factories, $item->factory_id);

                        // PDF dosya değeri (veritabanında dosya adı veya eski tam yol olabilir)
                        $factoryPdfPath = $item->factory_pdf;
                        $clientPdfPath = $item->client_pdf;
                        $contractPdfPath = $item->contract_pdf;

                        // Kaydeden kullanıcı adı
                        $creatorName = getUsername($item->created_by);
                        ?>
                        <tr>
                            <td class="truncate-cell-150"><?= htmlspecialchars($clientName) ?></td>
                            <td><?= htmlspecialchars($item->number) ?></td>
                            <td class="truncate-cell-150"><?= htmlspecialchars($factoryName) ?></td>
                            <td><?= htmlspecialchars($item->client_offer_price) ?></td>
                            <td><?= htmlspecialchars($item->factory_offer_price) ?></td>
                            <td><?= htmlspecialchars($item->due_date) ?></td>
                            <?php
                            $factoryPdfUrl = $factoryPdfPath
                                ? (strpos($factoryPdfPath, 'files/') === 0 || strpos($factoryPdfPath, 'img/') === 0
                                    ? $factoryPdfPath
                                    : 'files/molds/' . $factoryPdfPath)
                                : '';
                            $clientPdfUrl = $clientPdfPath
                                ? (strpos($clientPdfPath, 'files/') === 0 || strpos($clientPdfPath, 'img/') === 0
                                    ? $clientPdfPath
                                    : 'files/molds/' . $clientPdfPath)
                                : '';
                            $contractPdfUrl = $contractPdfPath
                                ? (strpos($contractPdfPath, 'files/') === 0 || strpos($contractPdfPath, 'img/') === 0
                                    ? $contractPdfPath
                                    : 'files/molds/' . $contractPdfPath)
                                : '';
                            ?>
                            <td>
                                <?php if ($factoryPdfUrl && file_exists($factoryPdfUrl)): ?>
                                    <a href="<?= htmlspecialchars($factoryPdfUrl) ?>" target="_blank">Fabrika PDF</a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($clientPdfUrl && file_exists($clientPdfUrl)): ?>
                                    <a href="<?= htmlspecialchars($clientPdfUrl) ?>" target="_blank">Firma PDF</a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($contractPdfUrl && file_exists($contractPdfUrl)): ?>
                                    <a href="<?= htmlspecialchars($contractPdfUrl) ?>" target="_blank">Sözleşme PDF</a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($item->contact_person) ?></td>
                            <td>
                                <a href="/profil/<?= urlencode($item->created_by) ?>"><b><?= htmlspecialchars($creatorName) ?></b></a>
                            </td>
                            <td class="display-flex">
                                <a onclick="openModal('edit-div-<?= $item->id ?>')">
                                    <i class="fas fa-pen mr-3" style="color:#004a8e"></i>
                                </a>
                                <form action="" method="POST">
                                    <input type="hidden" name="id" value="<?= $item->id ?>">
                                    <button type="submit" name="archive_mold" class="icon-button" style="width: 32px; padding-left:0" onclick="return confirmForm('<?= $item->number ?> kodlu kalıbı arşive göndermek istediğinize emin misiniz?')">
                                        <i class="fas fa-archive mr-3" style="color:#5a5a5a;"></i>
                                    </button>
                                </form>
                                <form action="" method="POST">
                                    <input type="hidden" name="id" value="<?= $item->id ?>">
                                    <button type="submit" name="delete_mold" class="icon-button" style="width: 32px; padding-left:0" onclick="return confirmForm('<?= $item->number ?> kodlu kalıbı silmek istediğinize emin misiniz?')">
                                        <i class="fas fa-trash mr-3" style="color:#ca0000;"></i>
                                    </button>
                                </form>
                                <a href="#" onclick="return false" onmousedown="javascript:ackapa('factory_pdfdivi<?= $item->id; ?>');">
                                    <i class="fas fa-industry mr-3"></i>
                                </a>
                                <a href="#" onclick="return false" onmousedown="javascript:ackapa('client_pdfdivi<?= $item->id; ?>');">
                                    <i class="fas fa-building"></i>
                                </a>
                                <a href="#" onclick="return false" onmousedown="javascript:ackapa('contract_pdfdivi<?= $item->id; ?>');">
                                    <i class="fas fa-paper"></i>
                                </a>
                                <div id="edit-div-<?= $item->id ?>" class="modal">
                                    <span class="close" onclick="closeModal()">&times;</span>
                                    <div>
                                        <h4><b>Kalıp Düzenleme Formu</b></h4>
                                    </div>
                                    <form action="" method="POST" enctype="multipart/form-data">
                                        <div class="search-box mb-2">
                                            <b>Firma</b>
                                            <input name="client" id="firmainputu" type="text" class="form-control form-control-sm"
                                                   autocomplete="off" placeholder="Firma Adı"
                                                   value="<?= htmlspecialchars($clientName) ?>"/>
                                            <ul class="list-group liveresult" id="firmasonuc" style="position: absolute; z-index: 1;"></ul>
                                        </div>
                                        <b>Kalıp Numarası</b>
                                        <input type="text" name="number" class="form-control form-control-sm mb-2" placeholder="Kalıp Numarası" value="<?= htmlspecialchars($item->number) ?>"/>
                                        <b>Fabrika</b>
                                        <select class="form-control form-control-sm mb-2" name="factory_id">
                                            <option value="0" <?= (($_POST['factory_id'] ?? '') == 0) ? 'selected' : '' ?>>Fabrika Seçiniz</option>
                                            <?php foreach ($factories as $factory): ?>
                                                <option value="<?= $factory->id ?>" <?= ($item->factory_id == $factory->id) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($factory->name) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <b>Firmaya Verilen Teklif</b>
                                        <input type="text" name="client_offer_price" class="form-control form-control-sm mb-2" placeholder="Firmaya verilen teklifi yazınız." value="<?= htmlspecialchars($item->client_offer_price) ?>"/>
                                        <b>Fabrikadan Alınan Teklif</b>
                                        <input type="text" name="factory_offer_price" class="form-control form-control-sm mb-2" placeholder="Fabrikadan alınan teklifi yazınız." value="<?= htmlspecialchars($item->factory_offer_price) ?>"/>
                                        <b>Termin Tarihi</b>
                                        <input type="date" name="due_date" class="form-control form-control-sm mb-2" value="<?= $item->due_date ?>"/>
                                        <div class="display-flex">
                                            <div>
                                                <b>Fabrika Onay Pdf</b><br/>
                                                <input type="file" name="factory_pdf_file" style="margin-bottom: 10px; margin-right: 10px; max-width: 150px;"><br/>
                                            </div>
                                            <div>
                                                <b>Firma Onay Pdf</b><br/>
                                                <input type="file" name="client_pdf_file" style="margin-bottom: 10px; margin-right: 10px; max-width: 150px;"><br/>
                                            </div>
                                            <div>
                                                <b>Sözleşme</b><br/>
                                                <input type="file" name="contract_pdf_file" style="margin-bottom: 10px; margin-right: 10px; max-width: 150px;"><br/>
                                            </div>
                                        </div>
                                        <b>İlgili Kişi</b>
                                        <input type="text" name="contact_person" class="form-control form-control-sm mb-2" placeholder="İlgili firma yetkilisini yazınız." value="<?= htmlspecialchars($item->contact_person) ?>"/>
                                        <b>Açıklama</b>
                                        <textarea class="form-control form-control-sm mb-2" name="description"><?= htmlspecialchars($item->description) ?></textarea>
                                        <input type="hidden" name="id" value="<?= $item->id ?>">
                                        <button type="submit" class="btn btn-primary w-100" name="update_mold">Güncelle</button>
                                    </form>
                                </div>
                                <div id="factory_pdfdivi<?= $item->id; ?>" class="pdf-preview-wrapper" style="display: none;">
                                    <div class="pdf-preview">
                                        <div class="pdf-preview-header">
                                            <h5 class="pdf-preview-title">Fabrika Onay Belgesi</h5>
                                            <button onclick="ackapa('factory_pdfdivi<?= $item->id; ?>')" class="pdf-preview-close">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                        <?php if (!empty($item->factory_pdf)): ?>
                                            <?php
                                            $factoryObjectUrl = (strpos($item->factory_pdf, 'files/') === 0 || strpos($item->factory_pdf, 'img/') === 0)
                                                ? $item->factory_pdf
                                                : 'files/molds/' . $item->factory_pdf;
                                            ?>
                                            <object width="100%" height="500" type="application/pdf" data="<?= $factoryObjectUrl; ?>">
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
                                            <?php
                                            $clientObjectUrl = (strpos($item->client_pdf, 'files/') === 0 || strpos($item->client_pdf, 'img/') === 0)
                                                ? $item->client_pdf
                                                : 'files/molds/' . $item->client_pdf;
                                            ?>
                                            <object width="100%" height="500" type="application/pdf" data="<?= $clientObjectUrl; ?>">
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
                                            <h5 class="pdf-preview-title">Kalıp Sözleşmesi</h5>
                                            <button onclick="ackapa('contract_pdfdivi<?= $item->id; ?>')" class="pdf-preview-close">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                        <?php if (!empty($item->contract_pdf)): ?>
                                            <?php
                                            $contractObjectUrl = (strpos($item->contract_pdf, 'files/') === 0 || strpos($item->contract_pdf, 'img/') === 0)
                                                ? $item->contract_pdf
                                                : 'files/molds/' . $item->contract_pdf;
                                            ?>
                                            <object width="100%" height="500" type="application/pdf" data="<?= $contractObjectUrl; ?>">
                                                <p>Kalıp sözleşmesi yüklenemedi.</p>
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
    </div>

    <?php include ROOT_PATH.'/template/script.php'; ?>

  </body>
</html>