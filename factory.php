<?php 

	include 'functions/init.php';

	if (!isLoggedIn()) {
		
		header("Location:login.php");

		exit();

	}elseif (isLoggedIn()) {

		if($user->type == '0'){

			header("Location:index.php");

			exit();

		}

	if($user->type != '3'){

		if (isset($_POST['update_factory'])) {
			
			$id = guvenlik($_POST['id']);

			$name = guvenlik($_POST['name']);

			$phone = guvenlik($_POST['phone']);

			$email = guvenlik($_POST['email']);

			$laborCost = guvenlik($_POST['labor_cost']);

			$address = guvenlik($_POST['address']);

			$query = $db->prepare("UPDATE factories SET name = ?, phone = ?, email = ?, labor_cost = ?, address = ? WHERE id = ?");

			$guncelle = $query->execute(array($name,$phone,$email,$laborCost,$address,$id));

			$orderId = guvenlik($_POST['order_id']);

			header("Location:factory.php#".($orderId - 2));

			exit();

		}

		if (isset($_POST['delete_factory'])) {

			$id = guvenlik($_POST['id']);

			if (isFactoryInUse($id) == '1') {
			
				$error = '<br/><div class="alert alert-danger" role="alert">Bu fabrikanın kayıtlı olduğu bir ürün, sipariş veya sipariş formu var o yüzden silemiyoruz.</div>';

			}else{
			
				$delete = $db->prepare("UPDATE factories SET is_deleted = ? WHERE id = ?");

				$delete = $delete->execute(array('1',$id));

				$orderId = guvenlik($_POST['order_id']);

				header("Location:factory.php#".($orderId - 2));

				exit();

			}

		}

		if (isset($_POST['delete_order_form'])) {
			
			$id = guvenlik($_POST['id']);

			$orders = guvenlik($_POST['orders']);

			$orderArray = explode(",", $orders);

			foreach ($orderArray as $key => $value) {

				$query = $db->prepare("UPDATE siparis SET silik = ? WHERE siparis_id = ?"); 

				$update = $query->execute(array('1',$value));

			}

            $query = $db->prepare("UPDATE siparisformlari SET silik = ? WHERE formid = ?"); 

			$delete = $query->execute(array('1',$id));

			$orderId = guvenlik($_POST['order_id']);

			header("Location:factory.php#".($orderId - 2));

			exit();

		}

		if (isset($_POST['add_factory'])) {
			
			$name = guvenlik($_POST['name']);

			$phone = guvenlik($_POST['phone']);

			$email = guvenlik($_POST['email']);

			$address = guvenlik($_POST['address']);

			$debt = 0;

			$dueDate = 0;

			$query = $db->prepare("INSERT INTO factories SET name = ?, phone = ?, email = ?, address = ?, debt = ?, due_date = ?, company_id = ?, is_deleted = ?");

			$insert = $query->execute(array($name, $phone, $email, $address, $debt, $dueDate, $authUser->company_id,'0'));

			header("Location:factory.php");

			exit();

		}

		if (isset($_POST['delete_order'])) {
			
			$id = guvenlik($_POST['id']);

			$query = $db->prepare("UPDATE siparis SET silik = ? WHERE siparis_id = ?"); 

			$update = $query->execute(array('1',$id));

			$orderId = guvenlik($_POST['order_id']);

			header("Location:factory.php#".($orderId - 2));

			exit();

		}

		if (isset($_POST['delete_order_with_form'])) {
			
			$id = guvenlik($_POST['id']);

			$orderFormId = guvenlik($_POST['order_form_id']);

			$orders = guvenlik($_POST['orders']);

			$orderKey = guvenlik($_POST['order_key']);

			$orderArray = explode(",", $orders);

			unset($orderArray[$orderKey]);

			$orders = implode(",", $orderArray);

			$query = $db->prepare("UPDATE siparisformlari SET siparisler = ? WHERE formid = ?"); 

			$update = $query->execute(array($orders,$orderFormId));

			$query = $db->prepare("UPDATE siparis SET silik = ? WHERE siparis_id = ?");

			$delete = $query->execute(array('1',$id));

			$orderId = guvenlik($_POST['order_id']);

			header("Location:factory.php#".($orderId - 2));

			exit();

		}

		if (isset($_POST['called'])) {

			$id = guvenlik($_POST['id']);
			
			$debt = guvenlik($_POST['debt']);

			$dueDate = guvenlik($_POST['due_date']);

            $dueDate = strtotime($dueDate);

			$query = $db->prepare("UPDATE factories SET debt = ?, due_date = ? WHERE id = ?"); 

			$update = $query->execute(array($debt, $dueDate, $id));

			$orderId = guvenlik($_POST['order_id']);

			header("Location:factory.php#".($orderId - 2));

			exit();

		}

		if (isset($_POST['save'])) {

			$id = guvenlik($_POST['id']);
			
			$debt = guvenlik($_POST['debt']);

			$dueDate = 0;

			$query = $db->prepare("UPDATE factories SET debt = ?, due_date = ? WHERE id = ?"); 

			$update = $query->execute(array($debt, $dueDate, $id));

			$orderId = guvenlik($_POST['order_id']);

			header("Location:factory.php#".($orderId - 2));

			exit();

		}

		if (isset($_POST['payment_completed'])) {

			$id = guvenlik($_POST['id']);
			
			$debt = 0;

			$dueDate = 0;

			$query = $db->prepare("UPDATE factories SET debt = ?, due_date = ? WHERE id = ?"); 

			$update = $query->execute(array($debt, $dueDate, $id));

			$orderId = guvenlik($_POST['order_id']);

			header("Location:factory.php#".($orderId - 2));

			exit();

		}

	}}

?>

<!DOCTYPE html>

<html>

	<head>

		<title>Fabrikalar</title>

		<?php include 'template/head.php'; ?>

	</head>

	<body>

		<?php include 'template/banner.php' ?>

		<div class="container-fluid">

			<div class="row">
				
				<div class="col-md-12">
					
					<?= $error; ?>

				</div>

			</div>

			<div class="row">
				
				<div class="col-md-3 col-12">
					
					<div class="div4" style="padding-top: 20px; text-align: center;">

						<a href="#" onclick="return false" onmousedown="javascript:ackapa('formdivi');">
                            <h5>
                                <i class="fas fa-angle-double-down"></i>&nbsp;&nbsp;&nbsp;&nbsp;
                                <b>Fabrika Ekleme Formu</b>&nbsp;&nbsp;&nbsp;&nbsp;
                                <i class="fas fa-angle-double-down"></i>
                            </h5>
                        </a>

						<div id="formdivi" style="display: none;">
						
							<form action="" method="POST">
									
								<div><input type="text" name="name" class="form-control" style="margin-bottom: 10px;" placeholder="Fabrika Adı"></div>

								<div><input type="text" name="phone" class="form-control" style="margin-bottom: 10px;" placeholder="Fabrika Telefonu"></div>

								<div><input type="text" name="email" class="form-control" style="margin-bottom: 10px;" placeholder="Fabrika E-posta Adresi"></div>

								<div><button type="submit" class="btn btn-primary btn-block" name="add_factory">Fabrika Ekle</button></div>

							</form>

						</div>

					</div>

				</div>

				<div class="col-md-9 col-12" style="text-align: right; padding-top: 15px;">
					
					<a href="factory.php"><button class="btn btn-sm btn-success">Tüm Liste</button></a>

					<a href="factory.php?odemeler"><button class="btn btn-sm btn-info">Tutarlılar</button></a>

					<a href="factory.php?arananlar"><button class="btn btn-sm btn-primary">Aranılanlar</button></a>
					
					<a href="factory.php?tahsilatigecenler"><button class="btn btn-sm btn-danger">Gecikenler</button></a>

				</div>

			</div>
					
			<div class="div4">

				<div class="d-none d-sm-block">

					<div class="row" style="margin-top: 10px;">
						
						<div class="col-md-3"><button class="btn btn-info"><h5><b style="color: white;">Fabrika Adı</b></h5></button></div>

						<div class="col-md-2">

							<button class="btn btn-info"><h5><b style="color: white;">Telefon</b></h5></button>

						</div>

						<div class="col-md-2"><button class="btn btn-info"><h5><b style="color: white;">Tutar</b></h5></button></div>

						<div class="col-md-2"><button class="btn btn-info"><h5><b style="color: white;">Ödeme Tarihi</b></h5></button></div>

					</div>

					<hr/>

				</div>
				
				<?php 

					$id = 0;

					$totalFactoryDebt = 0;

					if(isset($_GET['arananlar'])){

						$query = $db->query("SELECT * FROM factories WHERE company_id = '{$authUser->company_id}' AND debt != 0 AND due_date != '0' AND due_date > '{$bugununsaniyesi}' ORDER BY name ASC", PDO::FETCH_ASSOC);

					}elseif (isset($_GET['odemeler'])) {

						$query = $db->query("SELECT * FROM factories WHERE company_id = '{$authUser->company_id}' AND debt != 0 ORDER BY name ASC", PDO::FETCH_ASSOC);
						
					}elseif (isset($_GET['tahsilatigecenler'])) {

						$query = $db->query("SELECT * FROM factories WHERE company_id = '{$authUser->company_id}' AND debt != 0 AND due_date != '0' AND due_date <= '{$bugununsaniyesi}' ORDER BY name ASC", PDO::FETCH_ASSOC);
						
					}else{

						$query = $db->query("SELECT * FROM factories WHERE company_id = '{$user->company_id}' ORDER BY name ASC", PDO::FETCH_ASSOC);

					}	

					if ( $query->rowCount() ){

						foreach( $query as $row ){

							$id++;

							$id = guvenlik($row['id']);

							$name = guvenlik($row['name']);

							$phone = guvenlik($row['phone']);

							$email = guvenlik($row['email']);

							$laborCost = guvenlik($row['labor_cost']);

							$address = guvenlik($row['address']);

							$debt = guvenlik($row['debt']);

							$totalFactoryDebt = $totalFactoryDebt + $debt;

							$dueDate = guvenlik($row['due_date']);

							$dueDateV2 = date("d-m-Y",$dueDate);

				?>

							<div class="row">

								<div class="col-md-3 col-12" style="margin-top: 7px;">
									
									<a href="#" onclick="return false" onmousedown="javascript:ackapa('orders-div-<?= $id; ?>');">

										<b><?= $name;?></b>
											
									</a>

								</div>

								<div class="col-md-2" style="margin-top: 7px;">

									<b><?= $phone; ?></b>

								</div>

								<div class="col-md-3" style="margin-top: 7px;">

									<?php

									if($dueDate == 0){

										echo '<form action="" method="POST"><div class="row" style="margin:0px; padding:5px;">';							

									}elseif($dueDate > $bugununsaniyesi){

										echo '<form action="" method="POST"><div class="row btn-primary" style="margin:0px; padding:5px;">';

									}else{

										echo '<form action="" method="POST"><div class="row btn-danger" style="margin:0px; padding:5px;">';

									}

									?>
											
											<div class="col-md-4">
												
												<?php if($debt == 0){?>
                                                    <input type="text" name="debt" class="form-control" placeholder="Tutar giriniz." style="margin-bottom: 5px;">
                                                <?php }else{ ?>
                                                    <input type="text" name="debt" class="form-control" value="<?= $debt; ?>" style="margin-bottom: 5px;">
                                                <?php } ?>

											</div>

											<div class="col-md-2">
												
												<button class="btn btn-dark btn-sm" type="submit" name="save"><i class="fas fa-save"></i></button>

											</div>

											<div class="col-md-4">
												
												<?php if($debt == 0){?>
                                                    <input type="text" id="tarih<?= $id; ?>" name="due_date" value="<?= "Tarih seçiniz."; ?>" class="form-control form-control-sm">
                                                <?php }else{ ?>
                                                    <input type="text" id="tarih<?= $id; ?>" name="due_date" value="<?= $dueDateV2; ?>" class="form-control form-control-sm">
                                                <?php } ?>

											</div>

											<div class="col-md-2">

												<input type="hidden" id="tarih-db" name="due_date">

												<input type="hidden" name="id" value="<?= $id; ?>">

												<input type="hidden" name="order_id" value="<?= $id; ?>">
												
												<button class="btn btn-dark btn-sm" type="submit" name="called">
                                                    <i class="fas fa-phone"></i>
                                                </button>												

											</div>	

										</div>

									</form>	

								</div>	

								<div class="col-md-1 col-6" style="margin-top: 7px;">

									<button class="btn btn-success btn-sm btn-block" type="submit" name="payment_completed">Temizle</button>																		
									
								</div>

								<div class="col-md-1 col-6" style="margin-top: 7px;">

									<a href="fabrikasiparis.php?id=<?= $id; ?>"><button class="btn btn-info btn-sm btn-block">Siparişler</button></a>												
									
								</div>		

								<div class="col-md-1 col-6" style="margin-top: 7px;">

									<a href="#" onclick="return false" onmousedown="javascript:ackapa('edit-div-<?= $id; ?>');"><button class="btn btn-warning btn-sm btn-block">Düzenle</button></a>
									
								</div>

								<div class="col-md-1 col-6" style="margin-top: 7px;">

									<a href="#" onclick="return false" onmousedown="javascript:ackapa('delete-div-<?= $id; ?>');"><button class="btn btn-secondary btn-sm btn-block">Sil</button></a>
									
								</div>

							</div>

							<div id="delete-div-<?= $id; ?>" class="alert alert-danger" style="display: none; text-align: right; margin-top: 15px;">												

								<form action="" method="POST">

									<input type="hidden" name="id" value="<?= $id; ?>">

									<input type="hidden" name="order_id" value="<?= $id; ?>">

									Silmek istediğinize emin misiniz?&nbsp;&nbsp;&nbsp;

									<button class="btn btn-success btn-sm" name="delete_factory" type="submit">Evet</button>&nbsp;&nbsp;&nbsp;

									<a href="#" onclick="return false" onmousedown="javascript:ackapa('delete-div-<?= $id; ?>');"><button class="btn btn-danger btn-sm">Hayır</button></a>

								</form>

							</div>

							<div id="edit-div-<?= $id; ?>" style="display: none; position: fixed; top: 20%; left: 20%; z-index: 1; " class="div2">			

								<div class="row">
									
									<div class="col-md-8 col-8">
										
										<h5><b><?= $name; ?></b></h5>

									</div>

									<div class="col-md-4 col-4" style="text-align: right;">
										
										<a href="#" onclick="return false" onmousedown="javascript:ackapa('edit-div-<?= $id; ?>');"><span style="font-size: 24px;"><i class="fas fa-times"></i></span></a>

									</div>

								</div>			

								<div class="alert-primary" style="padding: 10px;">

									<h5><b>Bilgi Düzenleme Formu</b></h5>

									<form action="" method="POST">

										<div class="row">
											
											<div class="col-md-5 col-12" style="margin-top: 5px;">

												<b>Fabrika Adı</b>
												
												<input type="hidden" name="id" value="<?= $id; ?>">
											
												<input type="text" name="name" class="form-control" value="<?= $name; ?>">

											</div>

											<div class="col-md-3 col-12" style="margin-top: 5px;">

												<b>Telefon</b><br/>
												
												<input type="text" name="phone" class="form-control" value="<?= $phone; ?>">

											</div>

											<div class="col-md-4 col-12" style="margin-top: 5px;">

												<b>E-posta</b><br/>
												
												<input type="text" name="email" class="form-control" value="<?= $email; ?>">

											</div>

										</div>

										<div class="row">

											<div class="col-md-4 col-12" style="margin-top: 5px;">

												<b>İşçilik</b><br/>
												
												<input type="text" name="labor_cost" class="form-control" placeholder="Sadece sayı giriniz." value="<?= $laborCost; ?>">

											</div>

										</div>

										<div class="row">
											
											<div class="col-md-12 col-12" style="margin-top: 5px;">

												<b>Adres</b><br/>

												<textarea name="address" class="form-control" rows="1"><?= $address; ?></textarea>

											</div>

										</div>

										<div class="row">
											
											<div class="col-md-12 col-12"  style="margin-top: 5px;">

												<input type="hidden" name="order_id" value="<?= $id; ?>">
												
												<button class="btn btn-primary" type="submit" name="update_factory">Güncelle</button>

											</div>

										</div>

									</form>

								</div>

							</div>

							<div id="orders-div-<?= $id; ?>" class="div2" style="display: none; margin: 0px -20px 0px -20px;">

								<div class="alert alert-primary">

									<div class="row">
										
										<div class="col-6" style="text-align: left;"><h5><b style="line-height: 40px;">Siparişler</b></h5></div>

										<div class="col-6" style="text-align: right;"><a href="pdf.php?id=<?= $id; ?>" target="_blank"><button class="btn btn-primary btn-sm">Sipariş Formuna Git</button></a></div>

									</div>																

									<?php

										$sipariscek = $db->query("SELECT * FROM siparis WHERE urun_fabrika_id = '{$id}' AND formda = '0' AND sirketid = '{$authUser->company_id}' AND silik = '0'", PDO::FETCH_ASSOC);

										if ( $sipariscek->rowCount() ){

											foreach( $sipariscek as $row ){

												$siparis_id = $row['siparis_id'];

												$urun_id = $row['urun_id'];

												$urunbilgicek = $db->query("SELECT * FROM urun WHERE urun_id = '{$urun_id}' AND sirketid = '{$user->company_id}'")->fetch(PDO::FETCH_ASSOC);

												$urun_adi = $urunbilgicek['urun_adi'];

												$katbilcek = $db->query("SELECT * FROM urun WHERE urun_id = '{$urun_id}' AND sirketid = '{$user->company_id}'")->fetch(PDO::FETCH_ASSOC);

												$kategori_bir = $katbilcek['kategori_bir'];

												$katadcek = $db->query("SELECT * FROM kategori WHERE kategori_id = '{$kategori_bir}' AND sirketid = '{$user->company_id}'")->fetch(PDO::FETCH_ASSOC);

												$kategori_bir_adi = $katadcek['kategori_adi'];

												$kategori_iki = $katbilcek['kategori_iki'];

												$katadcek = $db->query("SELECT * FROM kategori WHERE kategori_id = '{$kategori_iki}' AND sirketid = '{$user->company_id}'")->fetch(PDO::FETCH_ASSOC);

												$kategori_iki_adi = $katadcek['kategori_adi'];

												$siparis_id = $row['siparis_id'];

												$hazirlayankisi = $row['hazirlayankisi'];

												$urun_siparis_aded = $row['urun_siparis_aded'];

												$urun_fabrika_id = $row['urun_fabrika_id'];

												$ilgilikisi = $row['ilgilikisi'];

												$urun_id = $row['urun_id'];

												$siparissaniye = $row['siparissaniye'];

												$siparistarih = date("d-m-Y", $siparissaniye);

												$fabrikaadcek = $db->query("SELECT * FROM factories WHERE id = '{$urun_fabrika_id}' AND company_id = '{$authUser->company_id}'")->fetch(PDO::FETCH_ASSOC);

												$urun_fabrika_adi = $fabrikaadcek['name'];

									?>

												<div class="row">

													<div class="col-4 d-block d-sm-none" style="margin-top: 7px;"><b style="color: red;">Hazırlayan</b></div>
													
													<div class="col-md-2 col-8" style="margin-top: 7px;"><?= $hazirlayankisi; ?></div>

													<div class="col-4 d-block d-sm-none" style="margin-top: 7px;"><b style="color: red;">Fabrika</b></div>

													<div class="col-md-2 col-12" style="margin-top: 7px;"><?= $urun_fabrika_adi; ?></div>

													<div class="col-4 d-block d-sm-none" style="margin-top: 7px;"><b style="color: red;">İlgili</b></div>

													<div class="col-md-2 col-8" style="margin-top: 7px;"><?= $ilgilikisi; ?></div>

													<div class="col-4 d-block d-sm-none" style="margin-top: 7px;"><b style="color: red;">Ürün</b></div>

													<div class="col-md-3 col-8" style="margin-top: 7px;"><?= $urun_adi." ".$kategori_iki_adi." ".$kategori_bir_adi; ?></div>

													<div class="col-4 d-block d-sm-none" style="margin-top: 7px;"><b style="color: red;">Adet</b></div>

													<div class="col-md-1 col-8" style="margin-top: 7px;"><?= $urun_siparis_aded; ?></div>

													<div class="col-4 d-block d-sm-none" style="margin-top: 7px;"><b style="color: red;">Tarih</b></div>

													<div class="col-md-1 col-8" style="margin-top: 7px;"><?= $siparistarih; ?></div>

													<div class="col-md-1 col-12" style="margin-top: 7px; text-align: right;">
														
														<form action="" method="POST">

															<input type="hidden" name="id" value="<?= $siparis_id; ?>">

															<input type="hidden" name="order_id" value="<?= $id; ?>">
															
															<button type="submit" class="btn btn-danger btn-sm btn-block" name="delete_order" style="margin-bottom: 5px;">Sil</button>

														</form>

													</div>

												</div>

									<?php

											}

										}

									?>

								</div>

								<hr/>

								<div class="alert alert-info">

									<h5><b style="line-height: 40px;">Sipariş Formları</b></h5>

									<?php

									$formcek = $db->query("SELECT * FROM siparisformlari WHERE fabrikaid = '{$id}' AND sirketid = '{$authUser->company_id}' AND silik = '0' ORDER BY saniye DESC LIMIT 10", PDO::FETCH_ASSOC);

									if ( $formcek->rowCount() ){

										foreach( $formcek as $frm ){

											$formid = $frm['formid'];

											$siparisler = $frm['siparisler'];

											$fabrikaid = $frm['fabrikaid'];

											$formsaniye = $frm['saniye'];

											$formtarih = date("d-m-Y H:i:s",$formsaniye);

									?>

											<div class="row" style="margin-bottom: 3px;">
												
												<div class="col-10"><a onclick="return false" onmousedown="javascript:ackapa('formdivi<?= $formid; ?>');"><?= $formtarih." Tarihli Sipariş Formundaki Ürünler<br/>"; ?></a></div>

												<div class="col-1" style="text-align: right;"><a href="siparisform.php?id=<?= $formid; ?>" target="_blank"><button class="btn btn-warning btn-sm btn-block">Göster</button></a></div>

												<div class="col-1" style="text-align: right;"><form action="" method="POST"><input type="hidden" name="formid" value="<?= $formid; ?>"><input type="hidden" name="siparisler" value="<?= $siparisler; ?>"><input type="hidden" name="siraid" value="<?= $id; ?>"><button type="submit" name="siparisformunusil" class="btn btn-danger btn-sm btn-block">Sil</button></form></div>

											</div>

											<div id="formdivi<?= $formid; ?>" style="display: none; padding: 10px;">

												<hr/>

								<?php if(!empty($siparisler)){ ?>

												<div class="d-none d-sm-block">
												
													<div class="row">
														
														<div class="col-2"><b>Hazırlayan Kişi</b></div>

														<div class="col-2"><b>Talep Edilen Fabrika</b></div>

														<div class="col-2"><b>İlgili Kişi</b></div>

														<div class="col-3"><b>Ürün Adı</b></div>

														<div class="col-1"><b>Miktar</b></div>

														<div class="col-2"><b>Tarih</b></div>

													</div>

												</div>
											
												<?php

													$siparisleripatlat = explode(",", $siparisler);

													foreach ($siparisleripatlat as $key => $value) {
														
														$siparisbilgisi = $db->query("SELECT * FROM siparis WHERE siparis_id = '{$value}' AND sirketid = '{$user->company_id}' AND silik = '0'")->fetch(PDO::FETCH_ASSOC);

														if($siparisbilgisi) {
													
															$siparis_id = $siparisbilgisi['siparis_id'];

															$urun_id = $siparisbilgisi['urun_id'];

															$urunbilgicek = $db->query("SELECT * FROM urun WHERE urun_id = '{$urun_id}' AND sirketid = '{$user->company_id}'")->fetch(PDO::FETCH_ASSOC);

															if($urunbilgicek) {

																$urun_adi = $urunbilgicek['urun_adi'];

																$katbilcek = $db->query("SELECT * FROM urun WHERE urun_id = '{$urun_id}' AND sirketid = '{$user->company_id}'")->fetch(PDO::FETCH_ASSOC);

																$kategori_bir = $katbilcek['kategori_bir'];

																$katadcek = $db->query("SELECT * FROM kategori WHERE kategori_id = '{$kategori_bir}' AND sirketid = '{$user->company_id}'")->fetch(PDO::FETCH_ASSOC);

																$kategori_bir_adi = $katadcek['kategori_adi'];

																$kategori_iki = $katbilcek['kategori_iki'];

																$katadcek = $db->query("SELECT * FROM kategori WHERE kategori_id = '{$kategori_iki}' AND sirketid = '{$user->company_id}'")->fetch(PDO::FETCH_ASSOC);

																$kategori_iki_adi = $katadcek['kategori_adi'];

																$siparis_id = $siparisbilgisi['siparis_id'];

																$hazirlayankisi = $siparisbilgisi['hazirlayankisi'];

																$urun_siparis_aded = $siparisbilgisi['urun_siparis_aded'];

																$urun_fabrika_id = $siparisbilgisi['urun_fabrika_id'];

																$ilgilikisi = $siparisbilgisi['ilgilikisi'];

																$urun_id = $siparisbilgisi['urun_id'];

																$siparissaniye = $siparisbilgisi['siparissaniye'];

																$siparistarih = date("d-m-Y", $siparissaniye);

																$fabrikaadcek = $db->query("SELECT * FROM factories WHERE id = '{$urun_fabrika_id}' AND company_id = '{$user->company_id}'")->fetch(PDO::FETCH_ASSOC);

																$urun_fabrika_adi = $fabrikaadcek['name'];

												?>

																<div class="row">

																	<div class="col-4 d-block d-sm-none" style="margin-top: 7px;"><b style="color: red;">Hazırlayan</b></div>
																	
																	<div class="col-md-2 col-8" style="margin-top: 7px;"><?= $hazirlayankisi; ?></div>

																	<div class="col-4 d-block d-sm-none" style="margin-top: 7px;"><b style="color: red;">Fabrika</b></div>

																	<div class="col-md-2 col-8" style="margin-top: 7px;"><?= $urun_fabrika_adi; ?></div>

																	<div class="col-4 d-block d-sm-none" style="margin-top: 7px;"><b style="color: red;">İlgili</b></div>

																	<div class="col-md-2 col-8" style="margin-top: 7px;"><?= $ilgilikisi; ?></div>

																	<div class="col-4 d-block d-sm-none" style="margin-top: 7px;"><b style="color: red;">Ürün</b></div>

																	<div class="col-md-3 col-8" style="margin-top: 7px;"><?= $urun_adi." ".$kategori_iki_adi." ".$kategori_bir_adi; ?></div>

																	<div class="col-4 d-block d-sm-none" style="margin-top: 7px;"><b style="color: red;">Adet</b></div>

																	<div class="col-md-1 col-8" style="margin-top: 7px;"><?= $urun_siparis_aded; ?></div>

																	<div class="col-4 d-block d-sm-none" style="margin-top: 7px;"><b style="color: red;">Tarih</b></div>

																	<div class="col-md-1 col-8" style="margin-top: 7px;"><?= $siparistarih; ?></div>

																	<div class="col-md-1 col-12" style="margin-top: 7px; text-align: right;">
															
																		<form action="" method="POST">

																			<input type="hidden" name="order_form_id" value="<?= $formid; ?>">

																			<input type="hidden" name="orders" value="<?= $siparisler; ?>">

																			<input type="hidden" name="order_key" value="<?= $key; ?>">

																			<input type="hidden" name="id" value="<?= $siparis_id; ?>">

																			<input type="hidden" name="order_id" value="<?= $id; ?>">
																			
																			<button type="submit" class="btn btn-danger btn-sm btn-block" name="delete_order_with_form" style="margin-bottom: 5px;">Sil</button>

																		</form>

																	</div>

																</div>

												<?php
															}
														}

													}

												?>												

											<?php }else{ echo "Bu sipariş formunda ürün bulunmamaktadır."; } ?>

												<hr/>

											</div>

									<?php

										}

									}

									?>

								</div>

							</div>

							<hr style="margin: 0px; border: 2px solid black;" />

				<?php

						}

					}

				?>

				<div class="row">

					<div class="col-md-12" style="padding-top: 20px; padding-bottom: 10px; text-align: center;"><b style="font-size: 20px;">Toplam Borç Tutarı : <?= $totalFactoryDebt; ?> TL</b></div>

				</div>

			</div>

		</div>

		<br/><br/><br/><br/><br/><br/>

		<?php include 'template/script.php'; ?>

	</body>

</html>