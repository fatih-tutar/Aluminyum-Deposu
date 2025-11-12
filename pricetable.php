<?php
include 'functions/init.php';
if (!isLoggedIn()) {
    header("Location:login.php");
    exit();
}else{
    if (isset($_POST['hesapla'])) {

        $dolarPost = guvenlik($_POST['dolarkuru']);

        if($dolarPost != $companyDolar){
            $query = $db->prepare("UPDATE companies SET dolar = ? WHERE id = ?");
            $guncelle = $query->execute(array($dolarPost,$authUser->company_id));
        }

        $lmePost = guvenlik($_POST['lme']);

        if($lmePost != $companyLme){
            $query = $db->prepare("UPDATE companies SET lme = ? WHERE id = ?");
            $guncelle = $query->execute(array($lmePost,$authUser->company_id));
        }

        $iscilik = guvenlik($_POST['iscilik']);

        if(empty($dolarPost) || empty($lmePost) || empty($iscilik)){
            $error = '<div class="alert alert-danger" role="alert">Fiyat hesaplama formunda boş bıraktığınız alanlar var, lütfen kontrol ediniz.</div>';
        }else{
            $toplam = ($lmePost + $iscilik) * $dolarPost;

            $toplam = number_format($toplam, 3, ',', '.');

            header("Location:pricetable.php?fiyat=".$toplam);

            exit();
        }
    }
    $factories = $db->query("SELECT * FROM factories WHERE company_id = '{$authUser->company_id}' ORDER BY name ASC")->fetchAll(PDO::FETCH_OBJ);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Anlık Fiyatlama</title>
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
            <?= isset($error) ? $error : ''; ?>
            <div class="d-flex">
                <table class="table table-bordered td-vertical-align-middle mr-3">
                    <thead>
                    <tr>
                        <th colspan="3" style="padding: 10px;">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <button id="menuToggleBtn" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-bars"></i> Menü
                                </button>
                                <h4 style="color: #003566; margin: 0 auto;">
                                    Anlık Fiyatlama Tablosu
                                </h4>
                                <div style="width: 10px;"></div>
                            </div>
                        </th>
                    </tr>

                    <tr style="color:#003566">
                            <th>Fabrika</th>
                            <th>Fiyat 1</th>
                            <th>Fiyat 2</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach ($factories as $factory):
                                $laborCost = $factory->labor_cost;
                                $price = ($companyLme + $laborCost) * $companyDolar / 1000;
                                $otherPrice = floor($price * 100 / 100 * 102) / 100;
                        ?>
                        <tr>
                            <td><?= $factory->name ?></td>
                            <td><?= number_format($price, 2)."₺" ?></td>
                            <td><?= number_format($otherPrice, 2)."₺" ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div style="min-width:300px; height: 300px; padding:10px; border: 1px solid #dee2e6;">
                <h4 style="text-align: center;"><b>Fiyat Hesaplama</b></h4>

                <form action="" method="POST">

                    <div class="row mt-3">

                        <div class="col-3">Dolar</div>

                        <div class="col-9"><input type="text" class="form-control" name="dolarkuru" value="<?= $companyDolar ?>"></div>

                    </div>

                    <div class="row mt-3">

                        <div class="col-3">LME</div>

                        <div class="col-9"><input type="text" class="form-control" name="lme" value="<?= $companyLme ?>"></div>

                    </div>

                    <div class="row mt-3">

                        <div class="col-3">İşçilik</div>

                        <div class="col-9"><input type="text" class="form-control" name="iscilik" placeholder="İşçilik Giriniz."></div>

                    </div>

                    <button type="submit" name="hesapla" class="btn btn-primary btn-block btn-sm mt-3" style="background-color:black;">Hesapla</button>

                </form>

                <?php if (isset($_GET['fiyat']) === true && empty($_GET['fiyat']) === false) { ?>

                    <h5 class="mt-3"><b>Fiyat : </b><?= $_GET['fiyat']." TL"; ?></h5>

                <?php } ?>
            </div>
            </div>
        </div>
    </div>
</div>

<?php include 'template/script.php'; ?>
</body>
</html>