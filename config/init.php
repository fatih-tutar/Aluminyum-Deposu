<?php
	define('ROOT_PATH', dirname(__DIR__));

	session_start();
	ob_start();
    setlocale(LC_TIME, 'tr_TR.UTF-8');
    date_default_timezone_set('Europe/Istanbul');

    $tarihf2 = date("d-m-Y",time());
    $date = date("Y-m-d",time());
    $bugununsaniyesi = strtotime($tarihf2);
	$error = "";
    $currentPage = basename($_SERVER['PHP_SELF']);
    $currentYear = date("Y");

    include __DIR__ . '/database.php';
	include __DIR__ . '/functions.php';

    $dbInstance = new Database();
    $db = $dbInstance->getConnection();

	$company = $db->query("SELECT * FROM companies WHERE id = '2'")->fetch(PDO::FETCH_ASSOC);
	$companyPriceList = guvenlik($company['price_list']);
    $companyDolar = guvenlik($company['dolar']);
    $companyLme = guvenlik($company['lme']);

    if(isLoggedIn() === true){
		$userSessionId = $_SESSION['user_id'];
        $user = $db->query("SELECT * FROM users WHERE id = '{$userSessionId}'")->fetch(PDO::FETCH_OBJ);
        $authUser = $user;

        // Gerçek kullanıcı tipini sakla; type=3 kullanıcıları salt-okunur "inceleme hesabı" olarak ele al
        $realUserType = (string)($user->type ?? '');
        $isReadOnlyUser = ($realUserType === '3');
        if ($isReadOnlyUser && in_array($_SERVER['REQUEST_METHOD'] ?? 'GET', ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            http_response_code(403);
            echo '<!doctype html><html lang="tr"><head><meta charset="utf-8"><title>Erişim Engellendi</title></head><body style="font-family:Arial,sans-serif;padding:24px;"><h3>Erişim engellendi</h3><p>Bu hesap inceleme amaçlı salt-okunur olarak tanımlanmıştır. Veri ekleme, güncelleme veya silme işlemi yapılamaz.</p><p><a href="/">Ana sayfaya dön</a></p></body></html>';
            exit();
        }

        // type=3 kullanıcı admine özel menü ve sayfaları görebilsin (yalnızca görüntüleme)
        if ($isReadOnlyUser) {
            $user->type = '2';
            $authUser->type = '2';
        }

        $userPermissionKeys = [
            'buying_price','factory','quote','order','editing','transaction','stock_flow','selling_price',
            'total_view','visit','shipment','piece','pallet','alkop','office','vehicle'
        ];
        $userPermissionValues = explode(",", $user->permissions);
        $user->permissions = (object) array_combine($userPermissionKeys, $userPermissionValues);
        $authUser->permissions = $user->permissions;

        $company = $db->query("SELECT * FROM companies WHERE id = '{$user->company_id}'")->fetch(PDO::FETCH_OBJ);

        $companyPriceList = guvenlik($company->price_list);

        if((time() - (60 * 60 * 24)) > $company->backup_time && $realUserType === '2'){
            $query = $db->prepare("UPDATE companies SET backup_time = ? WHERE id = ?");
            $guncelle = $query->execute(array(time(),$user->company_id));
            backupDatabaseSave($db, $dbInstance);
        }
	}else if (!isLoggedIn() && !in_array($currentPage, ['login.php', 'fiyatlistesi.php'])) {
        header("Location:/login");
        exit();
    }
?>
