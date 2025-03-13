<?php
	session_start();
	ob_start();
    setlocale(LC_TIME, 'tr_TR.UTF-8');

    $tarihf2 = date("d-m-Y",time());
    $date = date("Y-m-d",time());
    $bugununsaniyesi = strtotime($tarihf2);
	$error = "";
    $currentPage = basename($_SERVER['PHP_SELF']);
    $currentYear = date("Y");

    include 'database.php';
	include 'functions.php';

    $dbInstance = new Database();
    $db = $dbInstance->getConnection();

	$getCompanyPriceList = $db->query("SELECT * FROM companies WHERE id = '2'")->fetch(PDO::FETCH_ASSOC);
	$companyPriceList = guvenlik($getCompanyPriceList['price_list']);

    if(isLoggedIn() === true){
		$userSessionId = $_SESSION['user_id'];
        $user = $db->query("SELECT * FROM users WHERE id = '{$userSessionId}'")->fetch(PDO::FETCH_OBJ);
        $authUser = $user;

        $userPermissionKeys = [
            'buying_price','factory','quote','order','editing','transaction','stock_flow','selling_price',
            'total_view','visit','shipment','piece','pallet','alkop','office','vehicle'
        ];
        $userPermissionValues = explode(",", $user->permissions);
        $user->permissions = (object) array_combine($userPermissionKeys, $userPermissionValues);
        $authUser->permissions = $user->permissions;

        $company = $db->query("SELECT * FROM companies WHERE id = '{$user->company_id}'")->fetch(PDO::FETCH_OBJ);

        $companyPriceList = guvenlik($company->price_list);

        if((time() - (60 * 60 * 24)) > $company->backup_time && $user->type == '2'){
            $query = $db->prepare("UPDATE companies SET backup_time = ? WHERE id = ?");
            $guncelle = $query->execute(array(time(),$user->company_id));
            backupDatabaseSave($db, $dbInstance);
        }
	}else if (!isLoggedIn() && !in_array($currentPage, ['login.php', 'fiyatlistesi.php'])) {
        header("Location: login.php");
        exit();
    }
?>