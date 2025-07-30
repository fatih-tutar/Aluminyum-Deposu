<?php
include 'functions/init.php';
if (!isLoggedIn()) {
    header("Location:login.php");
    exit();
}else{


}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Fiyat Hesaplama</title>
    <?php include 'template/head.php'; ?>
</head>
<body class="body-white">
<?php include 'template/banner.php' ?>

<div class="container-fluid">
    <div class="row">
        <div id="sidebar" class="col-md-3">
            <?php include 'template/sidebar2.php'; ?>
        </div>
        <div id="mainCol" class="col-md-9 col-12">
            <div id="fiyathesaplamadivi" class="div4 col-md-4 col-12 mt-2 mb-4 p-3">

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

<?php include 'template/script.php'; ?>
</body>
</html>