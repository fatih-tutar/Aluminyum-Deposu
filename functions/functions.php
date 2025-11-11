<?php

function formatDate($datetime) {
    return date("d/m/Y", strtotime($datetime));
}
function formatDateAndTime($datetime) {
    return date("d/m/Y H:i", strtotime($datetime));
}
function getProduct($productId)
{
    global $db;
    $product = $db->query("SELECT * FROM urun WHERE urun_id = {$productId} AND silik = '0'");
    return $product->fetch(PDO::FETCH_OBJ);
}

function getCategory($categoryId)
{
    global $db;
    $category = $db->query("SELECT * FROM kategori WHERE kategori_id = {$categoryId} AND silik = 0");
    return $category->fetch(PDO::FETCH_OBJ);
}

function getCategoryNew($categoryId)
{
    global $db;
    $category = $db->query("SELECT * FROM categories WHERE id = {$categoryId} AND is_deleted = 0");
    return $category->fetch(PDO::FETCH_OBJ);
}

function getOffer($offerId)
{
    global $db;
    $order = $db->query("SELECT * FROM teklif WHERE teklifid = {$offerId} AND silik = '0'");
    return $order->fetch(PDO::FETCH_OBJ);
}

function getOrder($orderId)
{
    global $db;
    $order = $db->query("SELECT * FROM siparis WHERE siparis_id = {$orderId} AND silik = '0'");
    return $order->fetch(PDO::FETCH_OBJ);
}

function getClient($clientId)
{
    global $db;
    $client = $db->query("SELECT * FROM clients WHERE id = {$clientId} AND is_deleted = '0'");
    return $client->fetch(PDO::FETCH_OBJ);
}

function controlProductById($productId){
    global $db;
    $product = $db->query("SELECT * FROM urun WHERE urun_id='{$productId}' AND silik='0'");
    if($product->rowCount() > 0){
        return true;
    }else{
        return false;
    }
}

function getFactoryNameById($factories, $id) {
    foreach ($factories as $factory) {
        if ($factory->id == $id) {
            return $factory->name;
        }
    }
    return 'Bilinmeyen Fabrika'; // ID bulunamazsa
}

function getFactory($factoryId) {
    global $db;
    $factory = $db->query("SELECT * FROM factories WHERE id = '{$factoryId}' LIMIT 1")->fetch(PDO::FETCH_OBJ);
    return $factory;
}

function hasOverlappingLeave($startDate, $returnDate, $office, $excludeId = null) {
    global $db;

    $sql = "SELECT * FROM leaves WHERE office = :office AND is_deleted = '0' AND status != '2' 
            AND (
                (:startDate >= start_date AND :startDate < return_date) 
                OR 
                (:returnDate >= start_date AND :returnDate < return_date)
            )";

    // Eğer $excludeId varsa, bu ID'yi hariç tut
    if ($excludeId !== null) {
        $sql .= " AND id != :exclude_id";
    }

    $query = $db->prepare($sql);
    $query->bindParam(':office', $office, PDO::PARAM_INT);
    $query->bindParam(':startDate', $startDate, PDO::PARAM_STR);
    $query->bindParam(':returnDate', $returnDate, PDO::PARAM_STR);

    if ($excludeId !== null) {
        $query->bindParam(':exclude_id', $excludeId, PDO::PARAM_INT);
    }

    $query->execute();
    return $query->rowCount();
}

function getOfficeType($uyeId) {
    global $db;
    $uye = $db->query("SELECT * FROM users WHERE id = '{$uyeId}' AND is_deleted = '0'")->fetch(PDO::FETCH_ASSOC);
    $yetkiArray = explode(",", $uye['permissions']);
    return $yetkiArray[14];
}

function iseGirisTarihiGetir($uyeId) {
    global $db;
    $uye = $db->query("SELECT * FROM users WHERE id = '{$uyeId}' AND is_deleted = '0'")->fetch(PDO::FETCH_ASSOC);
    return $uye['hire_date'];
}

function calculateUsedLeave($uyeId, $leaveId = null) {
    global $db;
    $year = date("Y");

    // SQL sorgusu oluştur
    $sql = "SELECT SUM(leave_days) as total_used_leave 
            FROM leaves 
            WHERE user_id = :uyeId 
            AND YEAR(start_date) = :year 
            AND status = '1' 
            AND is_deleted = '0'";

    // Eğer leaveId verilmişse, bu ID'yi hariç tut
    if ($leaveId !== null) {
        $sql .= " AND id != :leaveId";
    }

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':uyeId', $uyeId, PDO::PARAM_INT);
    $stmt->bindParam(':year', $year, PDO::PARAM_INT);

    // Eğer leaveId varsa parametre olarak ekle
    if ($leaveId !== null) {
        $stmt->bindParam(':leaveId', $leaveId, PDO::PARAM_INT);
    }

    $stmt->execute();
    $usedLeave = $stmt->fetch(PDO::FETCH_ASSOC);

    return !$usedLeave['total_used_leave'] ? 0 : $usedLeave['total_used_leave'];
}

function yillikIzinHesapla($uyeId) {
    global $db;
    $uye = $db->query("SELECT * FROM users WHERE id = '{$uyeId}' AND is_deleted = '0'")->fetch(PDO::FETCH_ASSOC);
    $iseGirisTarihi = $uye['hire_date'];
    $bugun = new DateTime();
    $baslamaTarihi = new DateTime($iseGirisTarihi);
    $fark = $bugun->diff($baslamaTarihi);
    $yilFarki = $fark->y;
    if ($yilFarki < 1) {
        return 0; // 0-1 yıl arası izin hakkı yok
    } elseif ($yilFarki >= 1 && $yilFarki < 5) {
        return 14; // 1-5 yıl arası 14 gün izin
    } elseif ($yilFarki >= 5 && $yilFarki < 15) {
        return 20; // 5-15 yıl arası 20 gün izin
    } else {
        return 26; // 15 yıldan fazla ise 26 gün izin
    }
}

function calculateAnnualLeave($uyeId) {
    global $db;
    $uye = $db->query("SELECT * FROM users WHERE id = '{$uyeId}' AND is_deleted = '0'")->fetch(PDO::FETCH_ASSOC);
    $hireDate = $uye['hire_date'];
    $today = new DateTime();
    $startDate = new DateTime($hireDate);
    $differance = $today->diff($startDate);
    $yearDifference = $differance->y;
    if ($yearDifference < 1) {
        return 0; // 0-1 yıl arası izin hakkı yok
    } elseif ($yearDifference >= 1 && $yearDifference < 5) {
        return 14; // 1-5 yıl arası 14 gün izin
    } elseif ($yearDifference >= 5 && $yearDifference < 15) {
        return 20; // 5-15 yıl arası 20 gün izin
    } else {
        return 26; // 15 yıldan fazla ise 26 gün izin
    }
}

function isLeaveDateValid($userId, $startDate, $returnDate, $excludeId = null) {
    global $db;

    // Kullanıcının izinlerini alıyoruz
    $sql = "SELECT * FROM leaves WHERE user_id = :user_id AND is_deleted = '0' AND status != '2'";

    // Eğer bir excludeId varsa, o izni hariç tutuyoruz
    if ($excludeId !== null) {
        $sql .= " AND id != :exclude_id";
    }

    $sql .= " ORDER BY start_date ASC"; // İzinleri başlangıç tarihine göre sıralıyoruz

    $query = $db->prepare($sql);
    $query->bindParam(':user_id', $userId, PDO::PARAM_INT);

    if ($excludeId !== null) {
        $query->bindParam(':exclude_id', $excludeId, PDO::PARAM_INT);
    }

    $query->execute();
    $leaves = $query->fetchAll(PDO::FETCH_ASSOC);

    // İzinlerin her biri için kontrol yapıyoruz
    foreach ($leaves as $leave) {
        // Mevcut izinlerin başlangıç ve bitiş tarihlerini alıyoruz
        $leaveStartDate = $leave['start_date'];
        $leaveReturnDate = $leave['return_date'];

        // Yeni izin başlangıç tarihi ile mevcut izin bitiş tarihi arasındaki farkı kontrol ediyoruz
        $dateDiff1 = abs(strtotime($startDate) - strtotime($leaveReturnDate)); // Mutlak fark
        if ($dateDiff1 < 100 * 24 * 60 * 60) { // 100 gün = 100 * 24 * 60 * 60 saniye
            // Eğer yeni izin başlangıç tarihi ile mevcut iznin bitiş tarihi arasında 100 gün yoksa
            return false;
        }

        // Yeni izin bitiş tarihi ile mevcut izin başlangıç tarihi arasındaki farkı kontrol ediyoruz
        $dateDiff2 = abs(strtotime($returnDate) - strtotime($leaveStartDate)); // Mutlak fark
        if ($dateDiff2 < 100 * 24 * 60 * 60) { // 100 gün = 100 * 24 * 60 * 60 saniye
            // Eğer yeni izin bitiş tarihi ile mevcut iznin başlangıç tarihi arasında 100 gün yoksa
            return false;
        }
    }

    // Eğer hiç bir çakışma yoksa, tarih geçerli
    return true;
}

function getLastLeaveDate($izinli, $excludeId = null) {
    global $db;

    $sql = "SELECT * FROM leaves WHERE user_id = :user_id AND is_deleted = '0' AND status != '2'";

    if ($excludeId !== null) {
        $sql .= " AND id != :exclude_id";
    }

    $sql .= " ORDER BY start_date DESC LIMIT 1";

    $query = $db->prepare($sql);
    $query->bindParam(':user_id', $izinli, PDO::PARAM_INT);

    if ($excludeId !== null) {
        $query->bindParam(':exclude_id', $excludeId, PDO::PARAM_INT);
    }

    $query->execute();
    $lastLeave = $query->fetch(PDO::FETCH_ASSOC);

    return $lastLeave ? $lastLeave['return_date'] : null;
}

function getSevkiyatInfo($sevkiyatID){
    global $db;
    $sevkiyat = $db->query("SELECT * FROM sevkiyat WHERE id = '{$sevkiyatID}' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    return $sevkiyat;
}

function getUser($userId){
    global $db;
    $user = $db->query("SELECT * FROM users WHERE id = '{$userId}' LIMIT 1")->fetch(PDO::FETCH_OBJ);
    if($user) {
        return $user;
    }else{
        return null;
    }
}

function getUsername($userId){
    global $db;
    $user = $db->query("SELECT name FROM users WHERE id = '{$userId}' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    if($user) {
        return $user['name'];
    }else{
        return null;
    }
}

function getCategoryInfo($categoryId){
    global $db;
    $category = $db->query("SELECT * FROM kategori WHERE kategori_id = '{$categoryId}' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    return $category;
}

function getCategoryID($categoryName,$categoryType){
    global $db;
    $query = $db->query("SELECT kategori_id FROM kategori WHERE kategori_adi = '{$categoryName}' AND kategori_tipi = '{$categoryType}'")->fetch(PDO::FETCH_ASSOC);
    $categoryID = $query['kategori_id'];
    return $categoryID;
}

function getSubCategoryID($categoryName,$mainCategory){
    global $db;
    $query = $db->query("SELECT kategori_id FROM kategori WHERE kategori_adi = '{$categoryName}' AND kategori_ust = '{$mainCategory}' AND kategori_tipi = '1'")->fetch(PDO::FETCH_ASSOC);
    $categoryID = $query['kategori_id'];
    return $categoryID;
}

function getSatis($urunId){

    $urun_alis = getUrunInfo($urunId)['urun_alis'];
    $urun_satis = getUrunInfo($urunId)['satis'];

    if($urun_satis == 0){

        $categoryId = getUrunInfo($urunId)['kategori_bir'];

        $ust_kategori_kar_yuzdesi = getCategoryInfo($categoryId)['karyuzdesi'];

        $urun_satis = $urun_alis * ($ust_kategori_kar_yuzdesi + 100) / 100;

        $urun_satis = round($urun_satis, 2);
    }

    return $urun_satis;
}

function getCategoryShortName($categoryId){
    global $db;
    $category = $db->query("SELECT kategori_adi FROM kategori WHERE kategori_id = '{$categoryId}' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    $categoryName = $category['kategori_adi'];
    $categoryNameArray = explode(" ",$categoryName);
    $categoryShortName = $categoryNameArray[0];
    return $categoryShortName;
}

function getCategoryName($categoryId){
    global $db;
    $category = $db->query("SELECT kategori_adi FROM kategori WHERE kategori_id = '{$categoryId}' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    if($category && isset($category['kategori_adi'])){
        return $category['kategori_adi'];
    }
    return null;
}

function getUrunInfo($urunId){
    global $db;
    $urunInfo = $db->query("SELECT * FROM urun WHERE urun_id = '{$urunId}' LIMIT 1");
    if($urunInfo) {
        $urunInfo = $urunInfo->fetch(PDO::FETCH_ASSOC);
        return $urunInfo;
    }else{
        return false;
    }
}

function getUrunID($urunAdi,$kategori_iki,$kategori_bir){
    global $db;
    $category1ID = getCategoryID($kategori_bir,'0');
    $category2ID = getSubCategoryID($kategori_iki,$category1ID);
    $query = $db->query("SELECT urun_id FROM urun WHERE urun_adi = '{$urunAdi}' AND kategori_iki = '{$category2ID}' AND kategori_bir = '{$category1ID}' ")->fetch(PDO::FETCH_ASSOC);
    $urunId = $query['urun_id'];
    return $urunId;
}

function getHis($saniye){
    return date('H:i:s', $saniye);
}

function getdmY($saniye){
    return date('d.m.Y', $saniye);
}

function getFirmaInfos($firmaId){
    global $db;
    $firmaInfos = $db->query("SELECT * FROM clients WHERE id = '{$firmaId}'")->fetch(PDO::FETCH_ASSOC);
    return $firmaInfos;
}
function getClientId($name){
    global $db;
    $query = $db->query("SELECT id FROM clients WHERE name = '{$name}'")->fetch(PDO::FETCH_ASSOC);
    $id = $query[ 'id'];
    return $id;
}
function getClientName($id){
    global $db;
    $query = $db->query("SELECT name FROM clients WHERE id = '{$id}'")->fetch(PDO::FETCH_ASSOC);
    if ($query && isset($query['name'])) {
        return $query['name']; 
    }
    return null;
}

function getTurkishMonthName($month){

    switch ($month) {
        case '01':
            return 'Ocak';
            break;

        case '02':
            return 'Şubat';
            break;

        case '03':
            return 'Mart';
            break;

        case '04':
            return 'Nisan';
            break;

        case '05':
            return 'Mayıs';
            break;

        case '06':
            return 'Haziran';
            break;

        case '07':
            return 'Temmuz';
            break;

        case '08':
            return 'Ağustos';
            break;

        case '09':
            return 'Eylül';
            break;

        case '10':
            return 'Ekim';
            break;

        case '11':
            return 'Kasım';
            break;

        case '12':
            return 'Aralık';
            break;

        default:
            return 'Ocak';
            break;
    }

}

function ayAdi($ay){

    switch ($ay) {
        case '01':
            return 'Ocak';
            break;

        case '02':
            return 'Şubat';
            break;

        case '03':
            return 'Mart';
            break;

        case '04':
            return 'Nisan';
            break;

        case '05':
            return 'Mayıs';
            break;

        case '06':
            return 'Haziran';
            break;

        case '07':
            return 'Temmuz';
            break;

        case '08':
            return 'Ağustos';
            break;

        case '09':
            return 'Eylül';
            break;

        case '10':
            return 'Ekim';
            break;

        case '11':
            return 'Kasım';
            break;

        case '12':
            return 'Aralık';
            break;

        default:
            return 'Ocak';
            break;
    }

}

function uppercase_tr($string){

    $string = str_replace("ç", "Ç", $string);

    $string = str_replace("ğ", "Ğ", $string);

    $string = str_replace("ı", "I", $string);

    $string = str_replace("i", "İ", $string);

    $string = str_replace("ö", "Ö", $string);

    $string = str_replace("ş", "Ş", $string);

    $string = str_replace("ü", "Ü", $string);

    $string = strtoupper($string);

    return $string;

}

function explodeEachChar($x) {
    $c = array();
    while (strlen($x) > 0) {
        $c[] = substr($x,0,1);
        $x = substr($x,1);
    }
    return $c;
}

function bosluk_sil($string)
{
    $string = preg_replace("/\s+/", " ", $string);
    $string = trim($string);
    return $string;
}

function guvenlik($veri){
    global $db;
    $veri = bosluk_sil($veri);
    $veri_patlat = explodeEachChar($veri);
    foreach($veri_patlat as $key => $value)
    {
        $alfabedizi = "abcçdefgğhıijklmnoöqprsştuüvwxyzwqABCÇDEFGĞHIİJKLMNOÖPRSŞTUÜVYZWQ0123456789.,_-:/\@<>*# ";
        if (!preg_match("/[" . preg_quote($value, '/') . "]/i", $alfabedizi)) {
            unset($veri_patlat[$key]);
        }
    }
    $veri_birlestir = implode("", $veri_patlat);
    $veri = $veri_birlestir;
    return $veri;
}

function giris($name, $sifre){
    global $db;
    $query = $db->query("SELECT * FROM users WHERE name = '{$name}'")->fetch(PDO::FETCH_ASSOC);
    if($query && isset($query['id'])) {
        $userId = $query['id'];
    }
    $sorgu = $db->prepare("SELECT COUNT(*) FROM users WHERE name = '{$name}' AND password = '{$sifre}'");
    $sorgu->execute();
    $say = $sorgu->fetchColumn();
    if ($say == '0') {
        return false;
    }else{
        return $userId;
    }
}

function isLoggedIn(){

    return (isset($_SESSION['user_id'])) ? true: false;
}

function pasifmi($name){
    global $db;
    $sorgu = $db->prepare("SELECT COUNT(*) FROM users WHERE name = '{$name}' AND is_passive = '1'");
    $sorgu->execute();
    $say = $sorgu->fetchColumn();
    return ($say == '0') ? '0' : '1';
}

function tarihvarmi($tarih){

    global $db;

    $sorgu = $db->prepare("SELECT COUNT(*) FROM gelengiden WHERE tarih = '{$tarih}'");
    $sorgu->execute();
    $say = $sorgu->fetchColumn();

    return ($say == '0') ? '0' : '1';
}

function movementDateExists(string $date): bool
{
    global $db;

    $query = $db->prepare("SELECT 1 FROM movements WHERE date = :date LIMIT 1");
    $query->execute(['date' => $date]);

    return (bool) $query->fetchColumn();
}

function checkUserById($userId){

    global $db;

    $sorgu = $db->prepare("SELECT COUNT(*) FROM users WHERE id = '{$userId}'");
    $sorgu->execute();
    $say = $sorgu->fetchColumn();

    return ($say == '0') ? '0' : '1';
}

function checkUserByName($name){

    global $db;

    $sorgu = $db->prepare("SELECT COUNT(*) FROM users WHERE name = '{$name}'");
    $sorgu->execute();
    $say = $sorgu->fetchColumn();

    return ($say == '0') ? '0' : '1';
}

function kategoridolumu($kategori_id){

    global $db;

    $sorgu = $db->prepare("SELECT COUNT(*) FROM urun WHERE (kategori_bir = '{$kategori_id}' OR kategori_iki = '{$kategori_id}') AND silik = '0'");
    $sorgu->execute();
    $say = $sorgu->fetchColumn();

    return ($say == '0') ? '0' : '1';
}

function siparisvarmi($factoryId){

    global $db;

    $query = $db->prepare("SELECT COUNT(*) FROM siparis WHERE urun_fabrika_id = '{$factoryId}'");
    $query->execute();
    $count = $query->fetchColumn();

    return ($count == '0') ? '0' : '1';
}

function teklifvarmi($firmaid){

    global $db;

    $sorgu = $db->prepare("SELECT COUNT(*) FROM teklif WHERE tverilenfirma = '{$firmaid}'");
    $sorgu->execute();
    $say = $sorgu->fetchColumn();

    return ($say == '0') ? '0' : '1';
}

function yollaf($veri){

    $veri = addslashes($veri);

    $veri = strip_tags($veri);

    return $veri;

}

function cekf($veri){

    $veri = stripslashes($veri);

    $veri = strip_tags($veri);

    return $veri;

}

function uyeadcek($userId){

    global $db;

    $uyeadcek = $db->query("SELECT * FROM users WHERE id = '{$userId}'")->fetch(PDO::FETCH_ASSOC);

    $name = $uyeadcek['name'];

    return $name;

}

function firmaadcek($firmaid){

    global $db;

    $firmaadcek = $db->query("SELECT * FROM clients WHERE id = '{$firmaid}'")->fetch(PDO::FETCH_ASSOC);

    $firmaadi = $firmaadcek['name']; 

    return $firmaadi;

}

function isFactoryInUse($factoryId){

    global $db;

    $sorgu = $db->prepare("SELECT COUNT(*) FROM urun WHERE urun_fabrika = '{$factoryId}'");
    $sorgu->execute();
    $usay = $sorgu->fetchColumn();

    $sorgu = $db->prepare("SELECT COUNT(*) FROM siparis WHERE urun_fabrika_id = '{$factoryId}'");
    $sorgu->execute();
    $ssay = $sorgu->fetchColumn();

    $sorgu = $db->prepare("SELECT COUNT(*) FROM siparisformlari WHERE fabrikaid = '{$factoryId}'");
    $sorgu->execute();
    $sfsay = $sorgu->fetchColumn();

    return ($usay == '0' && $ssay == '0' && $sfsay == '0') ? '0' : '1';

}

function isCompanyInUse($id){

    global $db;

    $sorgu = $db->prepare("SELECT COUNT(*) FROM teklif WHERE tverilenfirma = '{$id}'");
    $sorgu->execute();
    $ssay = $sorgu->fetchColumn();

    $sorgu = $db->prepare("SELECT COUNT(*) FROM teklifformlari WHERE firmaid = '{$id}'");
    $sorgu->execute();
    $sfsay = $sorgu->fetchColumn();

    return ($ssay == '0' && $sfsay == '0') ? '0' : '1';

}

function ara($bas, $son, $yazi)
{
    @preg_match_all('/' . preg_quote($bas, '/') .
        '(.*?)'. preg_quote($son, '/').'/i', $yazi, $m);
    return @$m[1];
}

function backupDatabaseSave($db, $dbInstance)
{
    $config = ['dbname' => $dbInstance->getDbConfig()['dbname']];
    $backupDir = __DIR__ . "/../backups";
    $backupFile = $backupDir . "/backup_" . date("Y-m-d_H-i-s") . ".sql";
    $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    $sqlDump = "-- Veritabanı Yedeği: {$config['dbname']}\n-- Tarih: " . date("Y-m-d H:i:s") . "\n\n";
    foreach ($tables as $table) {
        $createTableStmt = $db->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_ASSOC);
        $sqlDump .= "DROP TABLE IF EXISTS `$table`;\n" . $createTableStmt['Create Table'] . ";\n\n";
        $rows = $db->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
        if ($rows) {
            foreach ($rows as $row) {
                $values = array_map([$db, 'quote'], array_values($row));
                $sqlDump .= "INSERT INTO `$table` VALUES (" . implode(", ", $values) . ");\n";
            }
            $sqlDump .= "\n";
        }
    }
    file_put_contents($backupFile, $sqlDump);

    return true;
}

function backupDatabaseDownload($db, $dbInstance)
{
    $config = ['dbname' => $dbInstance->getDbConfig()['dbname']];

    // Yedekleme dosyasını kaydetme işlemini kaldırdık, sadece indirme işlemi yapılacak
    $zipFile = tempnam(sys_get_temp_dir(), 'backup_') . ".zip"; // Geçici bir ZIP dosyası oluşturuluyor
    $zip = new ZipArchive();

    // Veritabanı yedeğini al
    $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    $sqlDump = "-- Veritabanı Yedeği: {$config['dbname']}\n-- Tarih: " . date("Y-m-d H:i:s") . "\n\n";

    foreach ($tables as $table) {
        $createTableStmt = $db->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_ASSOC);
        $sqlDump .= "DROP TABLE IF EXISTS `$table`;\n" . $createTableStmt['Create Table'] . ";\n\n";

        $rows = $db->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
        if ($rows) {
            foreach ($rows as $row) {
                $values = array_map([$db, 'quote'], array_values($row));
                $sqlDump .= "INSERT INTO `$table` VALUES (" . implode(", ", $values) . ");\n";
            }
            $sqlDump .= "\n";
        }
    }

    // SQL yedeğini geçici bir dosyaya yaz
    $tempSqlFile = sys_get_temp_dir() . "/backup.sql";
    file_put_contents($tempSqlFile, $sqlDump);

    // ZIP dosyasını oluştur ve SQL dosyasını ekle
    if ($zip->open($zipFile, ZipArchive::CREATE) === TRUE) {
        $zip->addFile($tempSqlFile, basename($tempSqlFile));
        $zip->close();
        unlink($tempSqlFile); // SQL dosyasını geçici dosyadan sil
    } else {
        echo "ZIP dosyası oluşturulamadı!";
        return;
    }

    // ZIP dosyasını indir
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . basename($zipFile) . '"');
    header('Content-Length: ' . filesize($zipFile));
    readfile($zipFile);
    unlink($zipFile); // ZIP dosyasını da sil
    exit;
}


?>