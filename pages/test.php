<?php
require_once __DIR__.'/../config/init.php';
if (!isLoggedIn()) {
    header("Location:/login");
    exit();
}else{

}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Sayfası</title>
    <?php include ROOT_PATH.'/template/head.php'; ?>
</head>
<body>
<?php include ROOT_PATH.'/template/banner.php' ?>

<?php include ROOT_PATH.'/template/script.php'; ?>
</body>
</html>