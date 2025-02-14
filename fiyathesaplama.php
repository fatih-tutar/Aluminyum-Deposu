<?php
include 'functions/init.php';
if (!isLoggedIn()) {
    header("Location:login.php");
    exit();
}else{
    $dolar = getDolar();
    $lme = getLME();

    if (isset($_POST['hesapla'])) {

        $dolarPost = guvenlik($_POST['dolarkuru']);

        $lmePost = guvenlik($_POST['lme']);

        $iscilik = guvenlik($_POST['iscilik']);

        $toplam = ($lmePost + $iscilik) * $dolarPost;

        header("Location:fiyathesaplama.php?fiyat=".$toplam);

        exit();

    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Fiyat Hesaplama</title>
    <?php include 'template/head.php'; ?>
</head>
<body>
<?php include 'template/banner.php' ?>

<div class="row">
    <div class="col-md-2 col-12">
        <?php include 'template/sidebar.php'; ?>
    </div>
    <div class="col-md-10">
        <div class="row mx-1">
            <div id="fiyathesaplamadivi" class="div4 col-md-4 col-12 mt-2 mb-4">

                <h5 style="text-align: center;"><b>Fiyat Hesaplama</b></h5>

                <div class="div5">

                    <div class="row">

                        <div class="col-xl-6 col-lg-12 col-6"><?= "<b>Dolar : </b>".$dolar." TL"; ?></div>

                        <div class="col-xl-6 col-lg-12 col-6"><?= "<b>LME : </b>".$lme." $"; ?></div>

                    </div>

                </div>

                <div class="div5">

                    <h5><b>Hesaplama</b></h5>

                    <form action="" method="POST">

                        <div class="row" style="margin-bottom: 5px;">

                            <div class="col-3">Dolar</div>

                            <div class="col-9"><input type="text" class="form-control" name="dolarkuru" value="<?= $dolar; ?>"></div>

                        </div>

                        <div class="row" style="margin-bottom: 5px;">

                            <div class="col-3">LME</div>

                            <div class="col-9"><input type="text" class="form-control" name="lme" value="<?= $lme; ?>"></div>

                        </div>

                        <div class="row" style="margin-bottom: 5px;">

                            <div class="col-3">İşçilik</div>

                            <div class="col-9"><input type="text" class="form-control" name="iscilik" placeholder="İşçilik Giriniz."></div>

                        </div>

                        <button type="submit" name="hesapla" class="btn btn-primary btn-block btn-sm" style="background-color:black;">Hesapla</button>

                    </form>

                </div>

                <?php

                if (isset($_GET['fiyat']) === true && empty($_GET['fiyat']) === false) {

                    ?>

                    <div class="div5">

                        <h5><b>Fiyat</b></h5>

                        <?= $_GET['fiyat']." TL"; ?>

                    </div>

                    <?php

                }

                ?>

            </div>
        </div>
    </div>
</div>

<?php include 'template/script.php'; ?>
</body>
</html>