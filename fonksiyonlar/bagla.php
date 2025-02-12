<?php
	session_start();
	ob_start();
    setlocale(LC_TIME, "turkish");

    $tarihf2 = date("d-m-Y",time());
    $bugununsaniyesi = strtotime($tarihf2);
	$tarihv3 = date("Y-m-d",time());
	$hata = "";

    include 'database.php';
	include 'fonksiyonlar.php';

    $dbInstance = new Database();
    $db = $dbInstance->getConnection();

	$getCompanyPriceList = $db->query("SELECT * FROM companies WHERE id = '2'")->fetch(PDO::FETCH_ASSOC);
	$companyPriceList = guvenlik($getCompanyPriceList['price_list']);

    if(isLoggedIn() === true){
		$userSessionId = $_SESSION['user_id'];
        $user = $db->query("SELECT * FROM users WHERE id = '{$userSessionId}'", PDO::FETCH_OBJ)->fetch();

        $userPermissionKeys = [
            'buying_price','factory','quote','order','editing','transaction','stock_flow','selling_price',
            'total_view','visit','shipment','piece','pallet','alkop','office','vehicle'
        ];
        $userPermissionValues = explode(",", $user->permissions);
        $user->permissions = (object) array_combine($userPermissionKeys, $userPermissionValues);

        $company = $db->query("SELECT * FROM companies WHERE id = '{$user->company_id}'", PDO::FETCH_OBJ)->fetch();

        $companyPriceList = guvenlik($company->price_list);
	}
?>