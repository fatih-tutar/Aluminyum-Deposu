<?php
include 'functions/init.php';
if (!isLoggedIn()) {
    header("Location:login.php");
    exit();
}else{
    $movements = $db->query("SELECT id, tarih FROM movements")->fetchAll(PDO::FETCH_OBJ);

    foreach ($movements as $movement) {
        $originalDate = $movement->tarih; // örnek: 29-05-2025

        // Tarihi dönüştür: gün-ay-yıl -> yıl-ay-gün
        $dateObj = DateTime::createFromFormat('d-m-Y', $originalDate);
        if ($dateObj) {
            $newDate = $dateObj->format('Y-m-d');

            // UPDATE işlemi
            $stmt = $db->prepare("UPDATE movements SET date = :newDate WHERE id = :id");
            $stmt->execute([
                'newDate' => $newDate,
                'id' => $movement->id
            ]);

            echo "Güncellendi: {$originalDate} -> {$newDate}<br/>";
        } else {
            echo "Hatalı tarih formatı: {$originalDate}<br/>";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Araçlar</title>
    <?php include 'template/head.php'; ?>
</head>
<body>
<?php include 'template/banner.php' ?>

<?php include 'template/script.php'; ?>
</body>
</html>