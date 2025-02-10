<?php 

	include 'fonksiyonlar/bagla.php';

	if ($girdi != 1) {
		
		header("Location:giris.php");

		exit();

	}elseif ($girdi == 1) {

		if(isset($_POST['plan_duzenle'])){

            $plan_id = guvenlik($_POST['plan_id']);

            $plan = guvenlik($_POST['plan']);

            $plan_tarihi = guvenlik($_POST['plan_tarihi']);

			$plan_tarihi = strtotime($plan_tarihi);

            $plan_tekrar = guvenlik($_POST['plan_tekrar']);
            
            $plan_durum = guvenlik($_POST['plan_durum']);

            $query = $db->prepare("UPDATE plan SET plan = ?, plan_tarihi = ?, plan_tekrar = ?, plan_durum = ? WHERE plan_id = ?"); 

            $guncelle = $query->execute(array($plan,$plan_tarihi,$plan_tekrar,$plan_durum,$plan_id));

            header("Location: plan.php");

            exit();

        }

        if(isset($_POST['plan_sil'])){

            $plan_id = guvenlik($_POST['plan_id']);

            $query = $db->prepare("UPDATE plan SET plan_silik = ? WHERE plan_id = ?"); 

            $guncelle = $query->execute(array('1',$plan_id));

            header("Location: plan.php");

            exit();

        }
	}
?>

<!DOCTYPE html>

<html>

	<head>

		<title>Alüminyum Deposu</title>

		<?php include 'template/head.php'; ?>

		<style type="text/css">
			.gorsel-container {
			    width:100%;
			    overflow:hidden;
			    margin:0;
			    height:170px;
			}

			.gorsel-container img {
			    display:block;
			    width:100%;
			    margin:-20px 20;
			}
			.sevkCardBlue{
				background-color: #17a2b8;
				border-radius: 5px;
				color: black;
				margin-bottom: 5px;
			}
            .sevkCardDarkBlue{
                background-color: #146e9f;
                border-radius: 5px;
                color: black;
                margin-bottom: 5px;
            }
			.sevkCardYellow{
				background-color: #ffc107;
				border-radius: 5px;
				color: black;
				margin-bottom: 5px;
			}
			.sevkCardGreen{
				background-color: #28a745;
				border-radius: 5px;
				color: black;
				margin-bottom: 5px;
			}
			.text-fiyat {
				font-size: 17px; 
				font-weight: bold;
			}
			@media (max-width:576px) {
				.text-fiyat {
					font-size: 15px; 
					font-weight: normal;
				}
			}
		</style>
	</head>
	<body>
		<?php include 'template/banner.php' ?>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<?php echo $hata; ?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-2 col-12">
					<?php include 'template/sidebar.php'; ?>
				</div>
				<div class="col-md-10">
					<div class="row mx-1">
						<?php include 'tools/agirlikhesaplama.php'; ?>
					</div>
					<?php include 'tools/isplani.php'; ?>
					<?php include 'tools/sevkiyattakibi.php'; ?>
				</div>
			</div>	
		</div>

		<br/><br/><br/><br/><br/><br/>

		<?php include 'template/script.php'; ?>

	</body>

</html>