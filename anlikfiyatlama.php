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
    <title>Anlık Fiyatlama</title>
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
            <div id="anlikfiyatlamadivi" class="div4 col-md-4 col-12 mt-2 mb-4">
                <h5 style="text-align: center;"><b>ANLIK FİYATLAMA</b></h5>

                <div>

                    <?php

                    $query = $db->query("SELECT * FROM factories WHERE company_id = '{$authUser->company_id}' ORDER BY name ASC", PDO::FETCH_ASSOC);

                    if ( $query->rowCount() ){

                        foreach( $query as $row ){

                            $fabrika_id = guvenlik($row['id']);

                            $fabrika_adi = guvenlik($row['name']);

                            $fabrikaiscilik = guvenlik($row['labor_cost']);

                            $fiyat = ($companyLme + $fabrikaiscilik) * $companyDolar / 1000;

                            $fiyat2=floor($fiyat*100/100*102)/100;

                            $fiyat1=floor($fiyat*100/100*101)/100;

                            $fiyat=floor($fiyat*100)/100;

                            if($fabrikaiscilik != 0){

                                ?>

                                <div class="row">

                                    <div class="col-md-7 col-7 text-fiyat" style="border-right: 2px solid black;"><?= $fabrika_adi; ?></div>

                                    <div class="col-md-5 col-5 px-1 pr-3 text-fiyat">
                                        <div class="d-flex justify-content-between">
                                            <div><?= $fiyat."₺"; ?></div>
                                            <div><?= $fiyat2."₺"; ?></div>
                                        </div>
                                    </div>

                                </div><hr style="margin: 1px; border: 1px solid black;" />


                            <?php } } } ?>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'template/script.php'; ?>
</body>
</html>