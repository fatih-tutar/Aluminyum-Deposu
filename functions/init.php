<?php
	session_start();
	ob_start();
    setlocale(LC_TIME, "turkish");

    $tarihf2 = date("d-m-Y",time());
    $bugununsaniyesi = strtotime($tarihf2);
	$tarihv3 = date("Y-m-d",time());
	$error = "";
    $currentPage = basename($_SERVER['PHP_SELF']);

    include 'database.php';
	include 'functions.php';

    $dbInstance = new Database();
    $db = $dbInstance->getConnection();

	$getCompanyPriceList = $db->query("SELECT * FROM companies WHERE id = '2'")->fetch(PDO::FETCH_ASSOC);
	$companyPriceList = guvenlik($getCompanyPriceList['price_list']);

    if(isLoggedIn() === true){
		$userSessionId = $_SESSION['user_id'];
        $user = $db->query("SELECT * FROM users WHERE id = '{$userSessionId}'")->fetch(PDO::FETCH_OBJ);

        $userPermissionKeys = [
            'buying_price','factory','quote','order','editing','transaction','stock_flow','selling_price',
            'total_view','visit','shipment','piece','pallet','alkop','office','vehicle'
        ];
        $userPermissionValues = explode(",", $user->permissions);
        $user->permissions = (object) array_combine($userPermissionKeys, $userPermissionValues);

        $company = $db->query("SELECT * FROM companies WHERE id = '{$user->company_id}'")->fetch(PDO::FETCH_OBJ);

        $companyPriceList = guvenlik($company->price_list);
	}else if (!isLoggedIn() && !in_array($currentPage, ['login.php', 'fiyatlistesi.php'])) {
        header("Location: login.php");
        exit();
    }
?>