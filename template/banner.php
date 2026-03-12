<?php if(isLoggedIn()){ ?>

    <?php
    if(isset($_POST['save_rate'])) {
        $dolarPost = guvenlik($_POST['dolarkuru']);
        $lmePost = guvenlik($_POST['lme']);

        if($dolarPost != $companyDolar || $lmePost != $companyLme){
            $query = $db->prepare("UPDATE companies SET dolar = ?, lme = ? WHERE id = ?");
            $guncelle = $query->execute(array($dolarPost,$lmePost,$authUser->company_id));
        }
        header("Location:/");
        exit();
    }
    ?>

    <div class="container-fluid" style="position: fixed; z-index: 5; background-color: black;">

        <div class="row">

            <div class="col-md-2 col-7" style="text-align: left; padding-top: 10px; padding-bottom: 10px;">

                <a href="/"><img src="/files/company/<?= $company->photo; ?>" class="img-responsive" alt="Alüminyum Deposu" width="70%" height="auto"></a>

            </div>

            <div class="col-md-3 offset-md-5 d-none d-md-block">
                <form action="" method="POST">
                    <div class="row mt-3 align-items-center" style="border-right: 1px solid #cdcdcd">
                        <div class="col-4">
                            <div class="d-flex align-items-center">
                                <span class="mr-2" style="color: #cdcdcd;">Dolar:</span>
                                <input type="text" style="background-color: black; color: #cdcdcd; border:none;" class="form-control form-control-sm" name="dolarkuru" value="<?= $companyDolar ?>">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="d-flex align-items-center">
                                <span class="mr-2" style="color: #cdcdcd;">LME:</span>
                                <input type="text" style="background-color: black; color: #cdcdcd; border:none;" class="form-control form-control-sm" name="lme" value="<?= $companyLme ?>">
                            </div>
                        </div>
                        <div class="col-4">
                            <button type="submit" class="btn btn-sm" style="color:#cdcdcd;" name="save_rate"><i class="fa fa-paper-plane"></i></button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-md-2 col-5 d-flex align-items-center justify-content-end" style="text-align: right;">

                <div class="dropdown" style="margin: 10px;">

                    <button class="btn btn-primary dropdown-toggle" style="background-color:black; border-style: none;" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                        <?= $user->name ?>

                    </button>

                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">

                        <a class="dropdown-item" href="/"><b>ANA SAYFA</b></a>

                        <a class="dropdown-item" href="/profil/<?= $user->id; ?>"><b>PROFİL</b></a>

                        <?php if($user->type == '2' || $user->type == '1' || $user->type == '3'){?>

                            <?php if($user->type == '2'){?>

                                <hr class="m-1"/>

                                <a class="dropdown-item" href="/catalog"><b>FİYATLAR</b></a>

                                <a class="dropdown-item" href="/job"><b>PLAN</b></a>

                                <a class="dropdown-item" href="/yonetim"><b>YÖNETİM</b></a>

                                <hr class="m-1"/>

                            <?php } ?>

                            <?php if($user->permissions->visit == '1'){?>

                                <a class="dropdown-item" href="/ziyaretler"><b>ZİYARETLER</b></a>

                            <?php } ?>

                            <?php if($user->permissions->stock_flow == '1'){?>

                                <a class="dropdown-item" href="/movement"><b>GELEN/GİDEN</b></a>

                            <?php } ?>

                            <?php if($user->permissions->transaction == '1'){?>

                                <a class="dropdown-item" href="/stock-activity"><b>İŞLEMLER</b></a>

                            <?php } ?>

                            <?php if($user->permissions->vehicle == '1'){?>

                                <a class="dropdown-item" href="/vehicle"><b>ARAÇLAR</b></a>

                            <?php } ?>

                            <?php if($user->type == '2' || $user->type == '1' || $user->type == '3'){?>

                                <a class="dropdown-item" href="/categories"><b>KATEGORİLER</b></a>

                            <?php } ?>

                            <hr class="m-1"/>

                            <a class="dropdown-item" href="/guide"><b>YARDIM</b></a>

                        <?php } ?>

                        <a class="dropdown-item" href="/logout"><b>ÇIKIŞ</b></a>

                    </div>

                </div>

            </div>

        </div>

    </div>

<?php }elseif (!isLoggedIn()) { ?>

    <div class="container-fluid" style="position: fixed; z-index: 2; background-color: white;">

        <div class="row">

            <div class="col-xl-10 col-lg-8 col-md-6 col-sm-6 col-6" style="text-align: left; padding-top: 10px; padding-bottom: 10px;">

                <a href="/"><img src="/files/img/defaultlogo.png" class="img-responsive" alt="Alüminyum Deposu" width="236" height="85"></a>

            </div>

            <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 col-6" style="text-align: right; padding-top: 30px;">

                <!--<div class="btn-group" role="group" aria-label="Basic example">

                    <button type="button" class="btn btn-danger" onclick="location.href='uyeol.php'"><i class="fa fa-user-plus"></i>&nbsp;&nbsp;&nbsp;Üye Ol</button>

                </div>-->

            </div>

        </div>

    </div>

<?php } ?>

<br/><br/><br/>