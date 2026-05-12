<?php 

	require_once __DIR__.'/../config/init.php';

	if (!isLoggedIn()) {
		
		header("Location:/login");

		exit();

	}else {

		if(isset($_POST['plan_duzenle'])){

            $plan_id = guvenlik($_POST['plan_id']);

            $plan = guvenlik($_POST['plan']);

            $plan_tarihi = guvenlik($_POST['plan_tarihi']);

			$plan_tarihi = strtotime($plan_tarihi);

            $plan_tekrar = guvenlik($_POST['plan_tekrar']);
            
            $plan_durum = guvenlik($_POST['plan_durum']);

            $query = $db->prepare("UPDATE plan SET plan = ?, plan_tarihi = ?, plan_tekrar = ?, plan_durum = ? WHERE plan_id = ?"); 

            $guncelle = $query->execute(array($plan,$plan_tarihi,$plan_tekrar,$plan_durum,$plan_id));

            header("Location: /job");

            exit();

        }

        if(isset($_POST['plan_sil'])){

            $plan_id = guvenlik($_POST['plan_id']);

            $query = $db->prepare("UPDATE plan SET plan_silik = ? WHERE plan_id = ?"); 

            $guncelle = $query->execute(array('1',$plan_id));

            header("Location: /job");

            exit();

        }
	}
?>

<!DOCTYPE html>

<html>

	<head>

		<title>Alüminyum Deposu</title>

		<?php include ROOT_PATH.'/template/head.php'; ?>

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
                background-color: #90ee90;
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
    <body class="body-white">
		<?php include ROOT_PATH.'/template/banner.php' ?>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<?= $error; ?>
				</div>
			</div>
			<div class="row">
                <div id="sidebar" class="sidebar col-md-2 pe-0">
                    <a href="#" onclick="openModal('form-div')">
                        <button class="btn btn-primary w-100 mb-2 mt-2" style="background-color: #003566; border-color: #003566;">
                            <i class="fas fa-file me-2"></i>
                            Sipariş Formu
                        </button>
                    </a>
                    <button id="closeSidebar" class="close-btn">&times;</button>
                    <?php include ROOT_PATH.'/template/sidebar2.php'; ?>
                </div>
				<div class="col-md-10">
                    <div class="d-block d-md-none">
                        <a href="#" onclick="openModal('form-div')">
                            <button class="btn btn-primary w-100 mb-2 mt-2" style="background-color: #003566; border-color: #003566;">
                                <i class="fas fa-file me-2"></i>
                                Sipariş Formu
                            </button>
                        </a>
                        <?php include ROOT_PATH.'/template/sidebar2.php'; ?>
                    </div>
					<?php include 'jobplan.php'; ?>
					<?php include 'sevkiyattakibi.php'; ?>
				</div>
			</div>	
		</div>

		<br/><br/><br/><br/><br/><br/>

		<?php include ROOT_PATH.'/template/script.php'; ?>

		<?php if (isset($realUserType) && $realUserType === '2') { ?>
		<div class="modal fade" id="dailyBackupReminderModal" tabindex="-1" aria-labelledby="dailyBackupReminderLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="dailyBackupReminderLabel">Günlük veritabanı yedeği</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
					</div>
					<div class="modal-body">
						<p class="mb-0">Verilerinizin güvenliği için günde bir kez veritabanı yedeğini bilgisayarınıza indirmenizi öneriyoruz.</p>
					</div>
					<div class="modal-footer d-flex flex-row flex-nowrap gap-2 py-2">
						<button type="button" class="btn btn-sm btn-secondary flex-fill" id="dailyBackupDismissBtn">Bugün hatırlatma</button>
						<button type="button" class="btn btn-sm btn-info flex-fill" id="dailyBackupDownloadBtn">Yedeği indir</button>
					</div>
				</div>
			</div>
		</div>
		<form id="dailyBackupDownloadForm" action="/yonetim" method="POST" target="_blank" class="d-none" aria-hidden="true">
			<input type="hidden" name="yedekal" value="1">
		</form>
		<script>
		(function () {
			var storageKey = 'dailyBackupReminder_v1';
			var today = <?= json_encode($date, JSON_UNESCAPED_UNICODE); ?>;
			var modalEl = document.getElementById('dailyBackupReminderModal');
			if (!modalEl || typeof bootstrap === 'undefined' || !bootstrap.Modal) {
				return;
			}
			function getState() {
				try {
					var raw = localStorage.getItem(storageKey);
					if (!raw) {
						return null;
					}
					return JSON.parse(raw);
				} catch (e) {
					return null;
				}
			}
			function setState(status) {
				try {
					localStorage.setItem(storageKey, JSON.stringify({ date: today, status: status }));
				} catch (e) {}
			}
			var state = getState();
			if (state && state.date === today && (state.status === 'dismiss' || state.status === 'downloaded')) {
				return;
			}
			var modal = new bootstrap.Modal(modalEl);
			modal.show();
			document.getElementById('dailyBackupDismissBtn').addEventListener('click', function () {
				setState('dismiss');
				modal.hide();
			});
			document.getElementById('dailyBackupDownloadBtn').addEventListener('click', function () {
				setState('downloaded');
				modal.hide();
				var f = document.getElementById('dailyBackupDownloadForm');
				if (f) {
					f.submit();
				}
			});
		})();
		</script>
		<?php } ?>

	</body>

</html>