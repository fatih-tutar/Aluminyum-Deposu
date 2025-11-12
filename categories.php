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

    if (isset($_POST['kategorisil'])) {

        $kategori_id = guvenlik($_POST['kategori_id']);

        if (kategoridolumu($kategori_id) == '1') {

            $error = '<br/><div class="alert alert-danger" role="alert">Silmek istediğiniz kategoride kayıtlı ürünler var. O ürünleri silmeden kategoriyi silemezsiniz.</a></div>';

        }else{

            $sil = $db->prepare("UPDATE kategori SET silik = ? WHERE kategori_id = ?");

            $delete = $sil->execute(array('1',$kategori_id));

            header("Location:kategoriler.php");

            exit();

        }

    }

    if (isset($_POST['kategoriekle'])) {

        $sutunlar = '1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1';

        $sutunlararray = explode(",",$sutunlar);

        $kategori_adi = guvenlik($_POST['kategori_adi']);

        $kategori_tipi = guvenlik($_POST['kategori_tipi']);

        if($kategori_tipi == '0'){

            if(!isset($_POST['sutunadet'])){ $sutunlararray[0] = 0; }

            if(!isset($_POST['sutunbirimkg'])){ $sutunlararray[1] = 0; }

            if(!isset($_POST['sutuntoplam'])){ $sutunlararray[2] = 0; }

            if(!isset($_POST['sutunalis'])){ $sutunlararray[3] = 0; }

            if(!isset($_POST['sutunsatis'])){ $sutunlararray[4] = 0; }

            if(!isset($_POST['sutunfabrika'])){ $sutunlararray[5] = 0; }

            if(!isset($_POST['sutunteklifbutonu'])){ $sutunlararray[6] = 0; }

            if(!isset($_POST['sutunsiparisbutonu'])){ $sutunlararray[7] = 0; }

            if(!isset($_POST['sutunduzenlebutonu'])){ $sutunlararray[8] = 0; }

            if(!isset($_POST['sutunsiparisadedi'])){ $sutunlararray[9] = 0; }

            if(!isset($_POST['sutunuyariadedi'])){ $sutunlararray[10] = 0; }

            if(!isset($_POST['sutunsipariskilo'])){ $sutunlararray[11] = 0; }

            if(!isset($_POST['sutunboyolcusu'])){ $sutunlararray[12] = 0; }

            if(!isset($_POST['sutunmusteriismi'])){ $sutunlararray[13] = 0; }

            if(!isset($_POST['sutuntarih'])){ $sutunlararray[14] = 0; }

            if(!isset($_POST['sutuntermin'])){ $sutunlararray[15] = 0; }

            if(!isset($_POST['sutunmanuelsatis'])){ $sutunlararray[16] = 0; }

            if(!isset($_POST['sutunurunkodu'])){ $sutunlararray[17] = 0; }

            if(!isset($_POST['sutundepoadet'])){ $sutunlararray[18] = 0; }

            if(!isset($_POST['sutundepouyariadet'])){ $sutunlararray[19] = 0; }

            if(!isset($_POST['sutunraf'])){ $sutunlararray[20] = 0; }

            if(!isset($_POST['sutunsevkiyatbutonu'])){ $sutunlararray[21] = 0; }

            if(!isset($_POST['sutunpalet'])){ $sutunlararray[22] = 0; }

            $sutunlar = implode(",",$sutunlararray);

        }else if($kategori_tipi == '1'){

            $sutunlar = '';

        }

        $allow = array('pdf');

        $temp = explode(".", $_FILES['uploadfile']['name']);

        $dosyaadi = $temp[0];

        $extension = end($temp);

        $randomsayi = rand(0,10000);

        $upload_file = $dosyaadi.$randomsayi.".".$extension;

        move_uploaded_file($_FILES['uploadfile']['tmp_name'], "img/kategoriler/".$upload_file);

        $query = $db->prepare("INSERT INTO kategori SET kategori_adi = ?, kategori_tipi = ?, kategori_ust = ?, resim = ?, sutunlar = ?, sirketid = ?, silik = ?");

        $insert = $query->execute(array($kategori_adi,$kategori_tipi,'0',$upload_file,$sutunlar,$user->company_id,'0'));

        header("Location:kategoriler.php");

        exit();

    }

    if (isset($_POST['urunekle'])) {

        $kategori_iki = guvenlik($_POST['kategori_iki']);

        $katbircek = $db->query("SELECT * FROM kategori WHERE kategori_id = '{$kategori_iki}' AND sirketid = '{$user->company_id}'")->fetch(PDO::FETCH_ASSOC);

        $kategori_bir = $katbircek['kategori_ust'];

        $urun_adi = guvenlik($_POST['urun_adi']);

        $sonuruncek = $db->query("SELECT * FROM urun WHERE kategori_iki = '{$kategori_iki}' AND kategori_bir = '{$kategori_bir}' AND sirketid = '{$user->company_id}' ORDER BY urun_sira DESC LIMIT 1", PDO::FETCH_ASSOC);

        if ( $sonuruncek->rowCount() ){

            foreach( $sonuruncek as $suc ){

                $sonurunsirasi = $suc['urun_sira'];

            }

        }

        $sonurunsirasi++;

        $query = $db->prepare("INSERT INTO urun SET kategori_bir = ?, kategori_iki = ?, urun_kodu = ?, urun_adi = ?, urun_adet = ?, urun_palet = ?, urun_depo_adet = ?, urun_raf = ?, urun_birimkg = ?, urun_boy_olcusu = ?, urun_alis = ?, urun_fabrika = ?, urun_stok = ?, urun_uyari_stok_adedi = ?, urun_depo_uyari_adet = ?, urun_sira = ?, musteri_ismi = ?, tarih = ?, termin = ?, satis = ?, sirketid = ?, silik = ?");

        $insert = $query->execute(array($kategori_bir,$kategori_iki,'',$urun_adi,'','','','','','','','','','','',$sonurunsirasi,'','','','',$user->company_id,'0'));

        header("Location:kategoriler.php");

        exit();

    }

    if (isset($_POST['kategoriduzenle'])) {

        $kategori_id = guvenlik($_POST['kategori_id']);

        $kategori_adi = guvenlik($_POST['kategori_adi']);

        $kategori_tipi = guvenlik($_POST['kategori_tipi']);

        $kategori_ust = guvenlik($_POST['kategori_ust']);

        $eskiresim = guvenlik($_POST['eskiresim']);

        if ($kategori_tipi == 0) {

            $kategori_ust = 0;

        }

        $sutunlar = '1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1';

        $sutunlararray = explode(",",$sutunlar);

        if(!isset($_POST['sutunadet'])){ $sutunlararray[0] = 0; }

        if(!isset($_POST['sutunbirimkg'])){ $sutunlararray[1] = 0; }

        if(!isset($_POST['sutuntoplam'])){ $sutunlararray[2] = 0; }

        if(!isset($_POST['sutunalis'])){ $sutunlararray[3] = 0; }

        if(!isset($_POST['sutunsatis'])){ $sutunlararray[4] = 0; }

        if(!isset($_POST['sutunfabrika'])){ $sutunlararray[5] = 0; }

        if(!isset($_POST['sutunteklifbutonu'])){ $sutunlararray[6] = 0; }

        if(!isset($_POST['sutunsiparisbutonu'])){ $sutunlararray[7] = 0; }

        if(!isset($_POST['sutunduzenlebutonu'])){ $sutunlararray[8] = 0; }

        if(!isset($_POST['sutunsiparisadedi'])){ $sutunlararray[9] = 0; }

        if(!isset($_POST['sutunuyariadedi'])){ $sutunlararray[10] = 0; }

        if(!isset($_POST['sutunsipariskilo'])){ $sutunlararray[11] = 0; }

        if(!isset($_POST['sutunboyolcusu'])){ $sutunlararray[12] = 0; }

        if(!isset($_POST['sutunmusteriismi'])){ $sutunlararray[13] = 0; }

        if(!isset($_POST['sutuntarih'])){ $sutunlararray[14] = 0; }

        if(!isset($_POST['sutuntermin'])){ $sutunlararray[15] = 0; }

        if(!isset($_POST['sutunmanuelsatis'])){ $sutunlararray[16] = 0; }

        if(!isset($_POST['sutunurunkodu'])){ $sutunlararray[17] = 0; }

        if(!isset($_POST['sutundepoadet'])){ $sutunlararray[18] = 0; }

        if(!isset($_POST['sutundepouyariadet'])){ $sutunlararray[19] = 0; }

        if(!isset($_POST['sutunraf'])){ $sutunlararray[20] = 0; }

        if(!isset($_POST['sutunsevkiyatbutonu'])){ $sutunlararray[21] = 0; }

        if(!isset($_POST['sutunpalet'])){ $sutunlararray[22] = 0; }

        $sutunlar = implode(",",$sutunlararray);

        $allow = array('pdf');

        $temp = explode(".", $_FILES['uploadfile']['name']);

        $dosyaadi = $temp[0];

        $extension = end($temp);

        $randomsayi = rand(0,10000);

        if (empty($dosyaadi)) {

            $upload_file = $eskiresim;

        }else{

            $upload_file = $dosyaadi.$randomsayi.".".$extension;

        }

        move_uploaded_file($_FILES['uploadfile']['tmp_name'], "img/kategoriler/".$upload_file);

        $query = $db->prepare("UPDATE kategori SET kategori_adi = ?, kategori_tipi = ?, kategori_ust = ?, resim = ?, sutunlar = ? WHERE kategori_id = ?");

        $guncelle = $query->execute(array($kategori_adi,$kategori_tipi,$kategori_ust,$upload_file,$sutunlar,$kategori_id));

        header("Location:kategoriler.php");

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

    $column_definitions = $db->query("
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

    <title>Alüminyum Deposu</title>

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
                    <button type="button" class="btn btn-success" onclick="openModal('kategoriEkleForm')">
                        Yeni Kategori Ekle
                    </button>
                </div>
                <div id="form-div" class="modal">
                    <span class="close" onclick="closeModal()">&times;</span>
                    <form action="" method="POST">

                        <h4>Ürün Ekleme</h4>

                        <div class="row form-group">

                            <div class="col-12">

                                <select class="form-control" name="kategori_iki">

                                    <option selected>Lütfen Bir Kategori Seçiniz</option>

                                    <?php foreach($subCategories as $category){
                                        $mainCategory = getCategoryNew($category->parent_id);
                                        ?>
                                        <option value="<?= $category->id; ?>" ><?= $category->name." (".$mainCategory->name.")"; ?></option>
                                    <?php } ?>

                                </select>

                            </div>

                        </div>

                        <div class="row form-group">

                            <div class="col-12">

                                <input type="text" name="urun_adi" placeholder="Lütfen Ürün Adını Giriniz" class="form-control">

                            </div>

                        </div>

                        <div class="row form-group">

                            <div class="col-12">

                                <button type="submit" class="btn btn-warning btn-block" name="urunekle">Ürün Ekle</button>

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
                                        <input type="text" name="kategori_adi" class="form-control" value="<?= $category->name; ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Görsel Yükle (Opsiyonel)</label>
                                        <input type="file" name="dosya" class="form-control">
                                    </div>

                                    <?php if($category->type == 1){ ?>
                                        <!-- Alt kategori: Üst kategori seçimi -->
                                        <div class="mb-3">
                                            <label class="form-label">Üst Kategori Seç</label>
                                            <select name="ust_kategori_id" class="form-control">
                                                <option value="">Seçiniz...</option>
                                                <?php foreach($mainCategories as $mainCategory){ ?>
                                                    <option value="<?= $mainCategory->id ?>" <?= $mainCategory->id == $category->parent_id ? 'selected' : '' ?> ><?= $mainCategory->name ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    <?php } else { ?>
                                        <div id="sutunSecimi">
                                            <label class="form-label fw-bold">Sütunları Seç:</label>
                                            <div class="row">
                                                <?php foreach ($groups as $groupId => $groupName): ?>
                                                    <?php
                                                    // Bu gruba ait sütunları filtrele
                                                    $groupColumns = array_filter($column_definitions, fn($col) => $col->group_id == $groupId);
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
                                                            <div id="newyuzdeinputu_<?= $category->id ?>" style="display: none;">
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
                                        <input type="hidden" name="kategori_id" value="<?= $category->id; ?>">
                                        <input type="hidden" name="eskiresim" value="<?= $category->image; ?>">
                                        <button type="submit" name="kategoriduzenle" class="btn btn-warning">Kaydet</button>
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
                                        <input type="hidden" name="kategori_id" value="<?= $kategori_id; ?>">
                                        <button type="submit" name="kategorisil" class="btn btn-danger">Evet, Sil</button>
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

<div id="kategoriEkleForm" class="modal" style="width: 50%;">
    <span class="close" onclick="closeModal()">&times;</span>
    <form method="POST" enctype="multipart/form-data" class="form-container">
        <h3>Yeni Kategori Ekle</h3>

        <!-- Kategori tipi seçimi -->
        <div class="mb-3">
            <label class="form-label">Kategori Tipi:</label><br>
            <input type="radio" name="kategori_tipi" value="1" id="ustKategoriRadio" checked>
            <label for="ustKategoriRadio">Üst Kategori</label>
            &nbsp;&nbsp;
            <input type="radio" name="kategori_tipi" value="0" id="altKategoriRadio">
            <label for="altKategoriRadio">Alt Kategori</label>
        </div>

        <!-- Ortak Alanlar -->
        <div class="mb-3">
            <label for="kategoriAdi" class="form-label">Kategori Adı</label>
            <input type="text" class="form-control" id="kategoriAdi" name="kategori_adi" required>
        </div>

        <div class="mb-3">
            <label for="kategoriDosya" class="form-label">Görsel Yükle (Opsiyonel)</label>
            <input type="file" class="form-control" id="kategoriDosya" name="dosya">
        </div>

        <!-- Alt kategoriye özel alan -->
        <div class="mb-3 d-none" id="ustKategoriSecimi">
            <label for="ustKategori" class="form-label">Üst Kategori Seç</label>
            <select name="ust_kategori_id" id="ustKategori" class="form-control form-select">
                <option value="">Seçiniz...</option>
                <?php
                $ustKategoriler = $db->query("SELECT * FROM categories WHERE type='0' AND is_deleted='0' ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
                foreach ($ustKategoriler as $row) {
                    echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
                }
                ?>
            </select>
        </div>

        <div class="d-none" id="sutunSecimi">
            <label class="form-label fw-bold">Sütunları Seç:</label>

            <div class="row">
                <!-- Sütun 1 -->
                <div class="col-md-4">
                    <b>Göstergeler</b>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="exampleCheck0" name="sutunurunkodu" checked>
                        <label class="form-check-label" for="exampleCheck0">Ürün Kodu<small>(1 birim)</small></label>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="sutunAdetCheck" name="sutunadet" checked>
                        <label class="form-check-label" for="sutunAdetCheck">Adet <small>(1 birim)</small></label>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="sutunPaletCheck" name="sutunpalet" checked>
                        <label class="form-check-label" for="sutunPaletCheck">Palet <small>(1 birim)</small></label>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="sutunDepoAdetCheck" name="sutundepoadet" checked>
                        <label class="form-check-label" for="sutunDepoAdetCheck">Depo Adet <small>(1 birim)</small></label>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="sutunrafCheck" name="sutunraf" checked>
                        <label class="form-check-label" for="sutunrafCheck">Raf <small>(1 birim)</small></label>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="exampleCheck2" name="sutunbirimkg" checked>
                        <label class="form-check-label" for="exampleCheck2">Birim Kg <small>(1 birim)</small></label>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="exampleCheck3" name="sutuntoplam" checked>
                        <label class="form-check-label" for="exampleCheck3">Toplam <small>(1 birim)</small></label>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="exampleCheck4" name="sutunalis" checked>
                        <label class="form-check-label" for="exampleCheck4">Alış <small>(1 birim)</small></label>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="exampleCheck5" name="sutunsatis" checked>
                        <label class="form-check-label" for="exampleCheck5">Satış <small>(1 birim)</small></label>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="exampleCheck6" name="sutunfabrika" checked>
                        <label class="form-check-label" for="exampleCheck6">Fabrika <small>(2 birim)</small></label>
                    </div>
                </div>

                <!-- Sütun 2 -->
                <div class="col-md-4">
                    <b>Düzenleme Formu</b>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="exampleCheck10" name="sutunsiparisadedi" checked>
                        <label class="form-check-label" for="exampleCheck10">Sipariş Adedi<small>(1 birim)</small></label>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="sutunuyariadedi" name="sutunuyariadedi" checked>
                        <label class="form-check-label" for="sutunuyariadedi">Uyarı Adedi<small>(1 birim)</small></label>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="sutundepouyariadet" name="sutundepouyariadet" checked>
                        <label class="form-check-label" for="sutundepouyariadet">Depo Uyarı Adedi<small>(1 birim)</small></label>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="sutunsipariskilo" name="sutunsipariskilo" checked>
                        <label class="form-check-label" for="sutunsipariskilo">Sipariş Kilo<small>(1 birim)</small></label>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="sutunboyolcusu" name="sutunboyolcusu" checked>
                        <label class="form-check-label" for="sutunboyolcusu">Boy Ölçüsü<small>(1 birim)</small></label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="sutunmusteriismi" name="sutunmusteriismi" checked>
                        <label class="form-check-label" for="sutunmusteriismi">Müşteri İsmi<small>(1 birim)</small></label>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="sutuntarih" name="sutuntarih" checked>
                        <label class="form-check-label" for="sutuntarih">Tarih<small>(1 birim)</small></label>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="sutuntermin" name="sutuntermin" checked>
                        <label class="form-check-label" for="sutuntermin">Termin<small>(1 birim)</small></label>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="sutunmanuelsatis" name="sutunmanuelsatis" onchange="yuzdeinputuac('newyuzdeinputu');" checked >
                        <label class="form-check-label" for="sutunmanuelsatis">Manuel Satış<small>(1 birim)</small></label>
                    </div>

                    <div class="row form-group">
                        <div class="col-12">
                            <div id="newyuzdeinputu" style="display: none;">
                                <input type="text" name="karyuzdesi" class="form-control form-control-sm" placeholder="Kâr yüzdenizi sadece sayı ile yazınız.">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sütun 3 -->
                <div class="col-md-4">
                    <b>Butonlar</b>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="exampleCheck7" name="sutunteklifbutonu" checked>
                        <label class="form-check-label" for="exampleCheck7">Teklif Butonu <small>(1 birim)</small></label>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="exampleCheck8" name="sutunsiparisbutonu" checked>
                        <label class="form-check-label" for="exampleCheck8">Sipariş Butonu <small>(1 birim)</small></label>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="sevkiyatButonuCheckCreate" name="sutunsevkiyatbutonu" checked>
                        <label class="form-check-label" for="sevkiyatButonuCheckCreate">Sevkiyat Butonu <small>(1 birim)</small></label>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="exampleCheck9" name="sutunduzenlebutonu" checked>
                        <label class="form-check-label" for="exampleCheck9">Düzenle Butonu <small>(1 birim)</small></label>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" name="kategoriekle" class="btn btn-primary mt-2">Kaydet</button>
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
</script>

<?php include 'template/script.php'; ?>

</body>

</html>