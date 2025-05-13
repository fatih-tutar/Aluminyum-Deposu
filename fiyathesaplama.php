<?php
include 'functions/init.php';
if (!isLoggedIn()) {
    header("Location:login.php");
    exit();
}else{

    if (isset($_POST['hesapla'])) {

        $lmePost = 1;
        $dolarPost = 1;
        $iscilik = 1;

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

                    <h5><b>Hesaplama</b></h5>

                    <form action="" method="POST">

                        <div class="row" style="margin-bottom: 5px;">

                            <div class="col-3">Dolar</div>

                            <div class="col-9"><input type="text" class="form-control" name="dolarkuru" value="<?= $companyDolar ?>"></div>

                        </div>

                        <div class="row" style="margin-bottom: 5px;">

                            <div class="col-3">LME</div>

                            <div class="col-9"><input type="text" class="form-control" name="lme" value="<?= $companyLme ?>"></div>

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