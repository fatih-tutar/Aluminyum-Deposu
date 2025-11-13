<?php
include 'functions/init.php';
if (!isLoggedIn()) {
    header("Location:login.php");
    exit();
}elseif (isLoggedIn()) {
    if($user->type == '0'){
        header("Location:index.php");
        exit();
    }
    
    if (isset($_POST['delete_category'])) {
        $id = guvenlik($_POST['id']);
        if (categoryHasProducts($id) == '1') {
            $error = '<br/><div class="alert alert-danger" role="alert">Silmek istediğiniz kategoride kayıtlı ürünler var. O ürünleri silmeden kategoriyi silemezsiniz.</a></div>';
        }else{
            $query = $db->prepare("UPDATE categories SET is_deleted = ? WHERE id = ?");
            $delete = $query->execute(array('1',$id));
            header("Location:categories.php");
            exit();
        }
    }
    
    if (isset($_POST['add_category'])) {
        $name = guvenlik($_POST['name']);
        $type = guvenlik($_POST['type']);
        $parentId = ($type == '0') ? 0 : guvenlik($_POST['parent_id'] ?? 0);

        // Görsel işlemi
        $uploadFile = null;
        if (!empty($_FILES['uploadfile']['name'])) {
            $temp = explode(".", $_FILES['uploadfile']['name']);
            $filename = pathinfo($_FILES['uploadfile']['name'], PATHINFO_FILENAME);
            $extension = strtolower(end($temp));
            $randomNum = rand(0, 10000);
            $uploadFile = $filename . $randomNum . "." . $extension;
            move_uploaded_file($_FILES['uploadfile']['tmp_name'], "img/kategoriler/" . $uploadFile);
        }

        $manualSalesSelected = false;
        if (!empty($_POST['columns']) && is_array($_POST['columns'])) {
            // Burada 'manual_sales_column_id' yerine senin manuel satış column_id değerini kullan
            $manualSalesColumnId = 19; //manuel satışın category_columns_definitions tablosundaki id'si
            $manualSalesSelected = in_array($manualSalesColumnId, $_POST['columns']);
        }

        // profit_margin sadece manual_sales seçili değilse alınır
        $profitMargin = (!$manualSalesSelected && !empty($_POST['profit_margin'])) ? guvenlik($_POST['profit_margin']) : null;

        $query = $db->prepare("INSERT INTO categories SET name = ?, type = ?, parent_id = ?, image = ?, profit_margin = ?, company_id = ?, is_deleted = ?");
        $insert = $query->execute(array($name,$type,$parentId,$uploadFile,$profitMargin,$user->company_id,'0'));

        if ($insert) {
            $categoryId = $db->lastInsertId();

            // Checkboxlardan gelen sütunları al
            if (!empty($_POST['columns']) && is_array($_POST['columns'])) {
                $columns = $_POST['columns'];

                $colInsert = $db->prepare("
                INSERT INTO category_columns (category_id, column_id) VALUES (?, ?)
            ");

                foreach ($columns as $colId) {
                    $colInsert->execute([$categoryId, $colId]);
                }
            }
        }

        header("Location:categories.php");
        exit();
    }

    if (isset($_POST['add_product'])) {
        $mainCategory = guvenlik($_POST['main_category']);
        $subCategory = guvenlik($_POST['sub_category']);
        $name = guvenlik($_POST['name']);

        // son ürün sırasını bul
        $getLastProduct = $db->query("
            SELECT * FROM urun 
            WHERE kategori_iki = '{$subCategory}' 
              AND kategori_bir = '{$mainCategory}' 
              AND sirketid = '{$user->company_id}' 
            ORDER BY urun_sira DESC 
            LIMIT 1
        ", PDO::FETCH_ASSOC);

        $lastProductNumber = 0;
        if ($getLastProduct->rowCount()) {
            $suc = $getLastProduct->fetch();
            $lastProductNumber = $suc['urun_sira'];
        }
        $lastProductNumber++;

        $query = $db->prepare("INSERT INTO urun SET 
            kategori_bir = ?, 
            kategori_iki = ?, 
            urun_kodu = ?, 
            urun_adi = ?, 
            urun_sira = ?, 
            sirketid = ?, 
            silik = '0'
        ");
        $insert = $query->execute([
            $mainCategory,
            $subCategory,
            '',
            $name,
            $lastProductNumber,
            $user->company_id
        ]);

        header("Location:categories.php");
        exit();
    }

    if (isset($_POST['edit_category'])) {
        $id = guvenlik($_POST['id']);
        $name = guvenlik($_POST['name']);
        $parentId = guvenlik($_POST['parent_id']) ?? null;
        $image = guvenlik($_POST['image']);

        // Dosya yükleme
        if (!empty($_FILES['uploadfile']['name'])) {
            $temp = explode(".", $_FILES['uploadfile']['name']);
            $filename = $temp[0];
            $extension = end($temp);
            $randomNum = rand(0,10000);
            $uploadFile = $filename.$randomNum.".".$extension;
            move_uploaded_file($_FILES['uploadfile']['tmp_name'], "img/kategoriler/".$uploadFile);
        } else {
            $uploadFile = $image;
        }

        // manual_sales checkbox seçili mi?
        $manualSalesSelected = isset($_POST['columns'][$id]) && in_array(19, $_POST['columns'][$id]);

        // Kar yüzdesi yalnızca manual_sales seçili değilse kaydedilecek
        $profitMargin = (!$manualSalesSelected && !empty($_POST['karyuzdesi'])) ? guvenlik($_POST['karyuzdesi']) : null;

        // Kategoriyi güncelle
        $query = $db->prepare("UPDATE categories SET name = ?, parent_id = ?, image = ?, profit_margin = ? WHERE id = ?");
        $update = $query->execute([$name, $parentId, $uploadFile, $profitMargin, $id]);

        // Checkbox işlemleri (category_columns tablosu)
        if (isset($_POST['columns'][$id])) {
            $selectedColumns = $_POST['columns'][$id]; // Formdan gelen seçili checkboxlar
            // Önce mevcut seçimleri al
            $existingQuery = $db->prepare("SELECT column_id FROM category_columns WHERE category_id = ?");
            $existingQuery->execute([$id]);
            $existingColumns = $existingQuery->fetchAll(PDO::FETCH_COLUMN, 0);

            // Silinecekler (önceden var ama artık seçili olmayanlar)
            $toDelete = array_diff($existingColumns, $selectedColumns);
            if (!empty($toDelete)) {
                $in  = str_repeat('?,', count($toDelete) - 1) . '?';
                $deleteQuery = $db->prepare("DELETE FROM category_columns WHERE category_id = ? AND column_id IN ($in)");
                $deleteQuery->execute(array_merge([$id], $toDelete));
            }

            // Eklenecekler (seçili ama önceden yok)
            $toAdd = array_diff($selectedColumns, $existingColumns);
            if (!empty($toAdd)) {
                $insertQuery = $db->prepare("INSERT INTO category_columns (category_id, column_id) VALUES (?, ?)");
                foreach ($toAdd as $colId) {
                    $insertQuery->execute([$id, $colId]);
                }
            }
        } else {
            // Eğer hiçbir checkbox seçili değilse, tüm kayıtları sil
            $deleteQuery = $db->prepare("DELETE FROM category_columns WHERE category_id = ?");
            $deleteQuery->execute([$id]);
        }

        header("Location:categories.php");
        exit();
    }

    $categories = $db->query("
        SELECT * FROM categories
        WHERE company_id = '{$user->company_id}' 
          AND is_deleted = '0'
        ORDER BY 
            CASE WHEN parent_id = 0 THEN id ELSE parent_id END,
            parent_id ASC,
            name ASC
    ")->fetchAll(PDO::FETCH_OBJ);

    foreach ($categories as $cat) {
        $cat->columns = $db->query("
            SELECT ccd.* 
            FROM category_columns cc
            INNER JOIN category_columns_definitions ccd 
                ON ccd.id = cc.column_id
            WHERE cc.category_id = '{$cat->id}'
        ")->fetchAll(PDO::FETCH_OBJ);
    }

    $mainCategories = [];
    $subCategories  = [];

    foreach ($categories as $category) {
        if ($category->type == 0) {
            $mainCategories[] = $category;
        } elseif ($category->type == 1) {
            $subCategories[] = $category;
        }
    }

    $columnDefinitions = $db->query("
        SELECT * FROM category_columns_definitions ORDER BY group_id, ordering ASC
    ")->fetchAll(PDO::FETCH_OBJ);

    $groups = [
        1 => 'Göstergeler',
        2 => 'Düzenleme Formu',
        3 => 'Butonlar'
    ];
}

?>

<!DOCTYPE html>

<html>

<head>

    <title>Kategoriler</title>

    <?php include 'template/head.php'; ?>

</head>

<body class="body-white">

<?php include 'template/banner.php' ?>

<div class="container-fluid">
    <div class="row">
        <div id="sidebar" class="col-md-2">
            <?php include 'template/sidebar2.php'; ?>
        </div>
        <div id="mainCol" class="col-md-10 col-12">
            <div class="d-flex justify-content-between">
                <button id="menuToggleBtn" class="btn btn-outline-primary btn-sm mr-2 mb-2">
                    <i class="fas fa-bars"></i> Menü
                </button>
                <div>
                    <?= isset($error) ? $error : ''; ?>
                </div>
                <div>
                    <button class="btn btn-primary" onclick="openModal('form-div')">
                        Yeni Ürün Ekle
                    </button>
                    <button type="button" class="btn btn-success" onclick="openModal('addCategoryForm')">
                        Yeni Kategori Ekle
                    </button>
                </div>
                <div id="form-div" class="modal">
                    <span class="close" onclick="closeModal()">&times;</span>
                    <form action="" method="POST">

                        <h4>Ürün Ekleme</h4>

                        <div class="row form-group">
                            <div class="col-12">
                                <label for="mainCategorySelect">Ana Kategori Seçiniz</label>
                                <select class="form-control" name="main_category" id="mainCategorySelect" required>
                                    <option value="">Lütfen Ana Kategori Seçiniz</option>
                                    <?php foreach ($mainCategories as $main): ?>
                                        <option value="<?= $main->id; ?>">
                                            <?= htmlspecialchars($main->name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-12">
                                <label for="subCategorySelect">Alt Kategori Seçiniz</label>
                                <select class="form-control" name="sub_category" id="subCategorySelect" required disabled>
                                    <option value="">Önce Ana Kategori Seçiniz</option>
                                </select>
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-12">
                                <label for="name">Ürün Adı</label>
                                <input type="text" name="name" placeholder="Lütfen Ürün Adını Giriniz" class="form-control">
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-12">
                                <button type="submit" class="btn btn-warning btn-block" name="add_product">Ürün Ekle</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered mt-3">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Kategori Adı</th>
                        <th>Kategori Tipi</th>
                        <th>Üst Kategori</th>
                        <th>İşlemler</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php foreach($categories as $key => $category){
                            $parentCategory = getCategoryNew($category->parent_id);
                            $columns_ids = array_column($category->columns, 'id');
                        ?>
                            <tr>
                                <td><?= ++$key; ?></td>
                                <td><?= $category->name; ?></td>
                                <td><?= $category->type == 0 ? 'Üst Kategori' : 'Alt Kategori'; ?></td>
                                <td><?= $parentCategory->name ?? null; ?></td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="openModal('editModal<?= $category->id; ?>')">
                                        <i class="fa fa-pen"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="openModal('deleteModal<?= $category->id; ?>')">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div id="editModal<?= $category->id; ?>" class="modal" style="<?= $category->type == 0 ? 'width:50%;' : '' ?>">
                                <span class="close" onclick="closeModal()">&times;</span>
                                <form action="" method="POST" enctype="multipart/form-data" class="form-container">
                                    <h4>Kategori Düzenle</h4>

                                    <div class="mb-3">
                                        <label class="form-label">Kategori Adı</label>
                                        <input type="text" name="name" class="form-control" value="<?= $category->name; ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Görsel Yükle (Opsiyonel)</label>
                                        <input type="file" name="uploadfile" class="form-control">
                                    </div>

                                    <?php if($category->type == 1){ ?>
                                        <!-- Alt kategori: Üst kategori seçimi -->
                                        <div class="mb-3">
                                            <label class="form-label">Üst Kategori Seç</label>
                                            <select name="parent_id" class="form-control">
                                                <option value="">Seçiniz...</option>
                                                <?php foreach($mainCategories as $mainCategory){ ?>
                                                    <option value="<?= $mainCategory->id ?>" <?= $mainCategory->id == $category->parent_id ? 'selected' : '' ?> ><?= $mainCategory->name ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    <?php } else { ?>
                                        <div id="select_column_<?= $category->id ?>">
                                            <label class="form-label fw-bold">Sütunları Seç:</label>
                                            <div class="row">
                                                <?php foreach ($groups as $groupId => $groupName): ?>
                                                    <?php
                                                    // Bu gruba ait sütunları filtrele
                                                    $groupColumns = array_filter($columnDefinitions, fn($col) => $col->group_id == $groupId);
                                                    ?>
                                                    <div class="col-md-4">
                                                        <b><?= htmlspecialchars($groupName) ?></b>

                                                        <?php foreach ($groupColumns as $col):
                                                            $checked = in_array($col->id, $columns_ids) ? 'checked' : '';
                                                            ?>
                                                            <div class="form-check">
                                                                <input
                                                                        type="checkbox"
                                                                        class="form-check-input"
                                                                        name="columns[<?= $category->id ?>][]"
                                                                        value="<?= $col->id ?>"
                                                                    <?= $checked ?>
                                                                        id="col_<?= $category->id ?>_<?= $col->id ?>"
                                                                    <?= ($col->name === 'manual_sales') ? "onchange=\"yuzdeinputuac('newyuzdeinputu_{$category->id}');\"" : "" ?>
                                                                >
                                                                <label class="form-check-label" for="col_<?= $category->id ?>_<?= $col->id ?>">
                                                                    <?= htmlspecialchars($col->label) ?>
                                                                    <small>(<?= htmlspecialchars($col->unit ?? '1 birim') ?>)</small>
                                                                </label>
                                                            </div>
                                                            <?php if ($col->name === 'manual_sales'): ?>
                                                            <div id="newyuzdeinputu_<?= $category->id ?>" style="display: block;">
                                                                <input type="text" name="karyuzdesi" class="form-control form-control-sm" placeholder="Kâr yüzdenizi sadece sayı ile yazınız.">
                                                            </div>
                                                            <?php endif; ?>

                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php } ?>

                                    <div class="form-actions mt-2">
                                        <input type="hidden" name="id" value="<?= $category->id; ?>">
                                        <input type="hidden" name="image" value="<?= $category->image; ?>">
                                        <button type="submit" name="edit_category" class="btn btn-warning">Kaydet</button>
                                        <button type="button" class="btn btn-secondary" onclick="closeModal()">Kapat</button>
                                    </div>
                                </form>
                            </div>

                            <!-- Delete Modal -->
                            <div id="deleteModal<?= $category->id; ?>" class="modal">
                                <span class="close" onclick="closeModal()">&times;</span>
                                <div class="p-3">
                                    <h5>Silmek istediğinize emin misiniz?</h5>
                                    <form action="" method="POST">
                                        <input type="hidden" name="id" value="<?= $kategori_id; ?>">
                                        <button type="submit" name="delete_category" class="btn btn-danger">Evet, Sil</button>
                                        <button type="button" class="btn btn-secondary" onclick="closeModal()">İptal</button>
                                    </form>
                                </div>
                            </div>

                            <?php
                        }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="addCategoryForm" class="modal" style="width: 50%;">
    <span class="close" onclick="closeModal()">&times;</span>
    <form method="POST" enctype="multipart/form-data" class="form-container">
        <h3>Yeni Kategori Ekle</h3>

        <!-- Kategori tipi seçimi -->
        <div class="mb-3">
            <label class="form-label">Kategori Tipi:</label><br>
            <input type="radio" name="type" value="0" id="ustKategoriRadio" checked>
            <label for="ustKategoriRadio">Üst Kategori</label>
            &nbsp;&nbsp;
            <input type="radio" name="type" value="1" id="altKategoriRadio">
            <label for="altKategoriRadio">Alt Kategori</label>
        </div>

        <!-- Ortak Alanlar -->
        <div class="mb-3">
            <label for="categoryName" class="form-label">Kategori Adı</label>
            <input type="text" class="form-control" id="categoryName" name="name" required>
        </div>

        <div class="mb-3">
            <label for="categoryFile" class="form-label">Görsel Yükle (Opsiyonel)</label>
            <input type="file" class="form-control" id="categoryFile" name="uploadfile">
        </div>

        <!-- Alt kategoriye özel alan -->
        <div class="mb-3 d-none" id="ustKategoriSecimi">
            <label for="ustKategori" class="form-label">Üst Kategori Seç</label>
            <select name="parent_id" id="ustKategori" class="form-control form-select">
                <option value="">Seçiniz...</option>
                <?php foreach ($mainCategories as $mainCategory): ?>
                    <option value="<?= $mainCategory->id ?>"><?= $mainCategory->name ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div id="sutunSecimi">
            <label class="form-label fw-bold">Sütunları Seç:</label>

            <div class="row">
                <?php foreach ($groups as $groupId => $groupName): ?>
                    <?php
                    $groupColumns = array_filter($columnDefinitions, fn($col) => $col->group_id == $groupId);
                    ?>
                    <div class="col-md-4">
                        <b><?= htmlspecialchars($groupName) ?></b>

                        <?php foreach ($groupColumns as $col): ?>
                            <div class="form-check">
                                <input
                                        type="checkbox"
                                        class="form-check-input"
                                        name="columns[]"
                                        value="<?= $col->id ?>"
                                        id="col_<?= $col->id ?>"
                                    <?= ($col->name === 'manual_sales') ? "onchange=\"yuzdeinputuac('newyuzdeinputu');\"" : "" ?>
                                >
                                <label class="form-check-label" for="col_<?= $col->id ?>">
                                    <?= htmlspecialchars($col->label) ?>
                                    <small>(<?= htmlspecialchars($col->unit ?? '1 birim') ?>)</small>
                                </label>
                            </div>
                            <?php if ($col->name === 'manual_sales'): ?>
                                <div id="newyuzdeinputu" style="display:block;">
                                    <input type="text" name="profit_margin" class="form-control form-control-sm" placeholder="Kâr yüzde (sadece sayı)">
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" name="add_category" class="btn btn-primary mt-2">Kaydet</button>
            <button type="button" class="btn btn-danger mt-2" onclick="closeModal()">Kapat</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ustRadio = document.getElementById("ustKategoriRadio");
        const altRadio = document.getElementById("altKategoriRadio");
        const sutunSecimi = document.getElementById("sutunSecimi");
        const ustKategoriSecimi = document.getElementById("ustKategoriSecimi");

        function toggleSections() {
            if (ustRadio.checked) {
                sutunSecimi.classList.remove("d-none");
                ustKategoriSecimi.classList.add("d-none");
            } else {
                sutunSecimi.classList.add("d-none");
                ustKategoriSecimi.classList.remove("d-none");
            }
        }

        ustRadio.addEventListener("change", toggleSections);
        altRadio.addEventListener("change", toggleSections);
        toggleSections();
    });

    // Ürün eklerken alt kategori doldurma
    // PHP'den subCategories verisini JSON olarak al
    const subCategories = <?= json_encode($subCategories, JSON_UNESCAPED_UNICODE); ?>;

    const mainSelect = document.getElementById('mainCategorySelect');
    const subSelect = document.getElementById('subCategorySelect');

    mainSelect.addEventListener('change', function() {
        const mainId = this.value;
        subSelect.innerHTML = ''; // eski alt kategorileri temizle

        if (!mainId) {
            subSelect.disabled = true;
            subSelect.innerHTML = '<option value="">Önce Ana Kategori Seçiniz</option>';
            return;
        }

        // Seçilen ana kategoriye bağlı alt kategorileri filtrele
        const filteredSubs = subCategories.filter(cat => cat.parent_id == mainId);

        if (filteredSubs.length === 0) {
            subSelect.innerHTML = '<option value="">Bu kategoriye ait alt kategori yok</option>';
            subSelect.disabled = true;
            return;
        }

        // Alt kategorileri doldur
        subSelect.disabled = false;
        subSelect.innerHTML = '<option value="">Lütfen Alt Kategori Seçiniz</option>';

        filteredSubs.forEach(cat => {
            const opt = document.createElement('option');
            opt.value = cat.id;
            opt.textContent = cat.name;
            subSelect.appendChild(opt);
        });
    });

</script>

<?php include 'template/script.php'; ?>

</body>

</html>