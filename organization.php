<?php
include 'functions/init.php';

if (!isLoggedIn()) {
    header("Location:login.php");
    exit();
}

$bannerHidden = 0;
if (isset($_GET['pdf'])) {
    $authUser->type = 1;
    $bannerHidden = 1;
}

/* ---------- USERS ---------- */
$users = [];
$people = $db->query("SELECT * FROM organizations", PDO::FETCH_ASSOC);
foreach ($people as $key => $person) {
    $users[$key] = [
        'name'    => guvenlik($person['name']),
        'title' => guvenlik($person['title']),
        'photo'  => guvenlik($person['photo'])
    ];
}

/* ---------- SAVE ---------- */
if (isset($_POST['save_organization'])) {
    $organizationData = $_POST['organization'];
    $fileData = $_FILES['organization'];

    foreach ($organizationData as $index => $data) {
        $name = guvenlik($data['name']);
        $title = guvenlik($data['title']);

        $photo = null;
        $hasFile = isset($fileData['name'][$index]['uploadfile']) && $fileData['error'][$index]['uploadfile'] === UPLOAD_ERR_OK;

        if ($hasFile) {
            $ext = pathinfo($fileData['name'][$index]['uploadfile'], PATHINFO_EXTENSION);
            $photo = uniqid('org_', true) . '.' . $ext;

            move_uploaded_file(
                $fileData['tmp_name'][$index]['uploadfile'],
                "img/organizasyon/" . $photo
            );
        }

        if ($photo) {
            $db->prepare("UPDATE organization SET name = ?, title = ?, photo = ? WHERE id = ?")
                ->execute([$name, $title, $photo, $index + 1]);
        } else {
            $db->prepare("UPDATE organization SET name = ?, title = ? WHERE id = ?")
                ->execute([$name, $title, $index + 1]);
        }
    }

    header("Location:organization.php");
    exit();
}

/* ---------- CARD RENDER ---------- */
function renderOrgCard(int $i, array $users, $user, string $extraClass = '', string $fileClass = 'upload-button')
{
    $photo = empty($users[$i]['photo']) ? 'pp.png' : 'organizasyon/' . $users[$i]['photo'];
    $title = $users[$i]['title'] ?? '';
    $name = $users[$i]['name'] ?? '';
    ?>
    <div class="org-card <?= $extraClass ?> relative">
        <div class="bg1 white label ortali relative">
            <img src="img/<?= $photo ?>" alt="profile picture" class="profile-pic">
            <?php if ($user->type == 2) { ?>
                <input type="text" name="organization[<?= $i ?>][title]" value="<?= $title ?>">
            <?php } else { ?>
                <?= $title ?>
            <?php } ?>
        </div>
        <div class="bg2 white label ortali">
            <?php if ($user->type == 2) { ?>
                <input type="text" name="organization[<?= $i ?>][name]" value="<?= $name ?>">
            <?php } else { ?>
                <b><?= $name ?></b>
            <?php } ?>
        </div>
        <?php if ($user->type == 2) { ?>
            <input type="file" name="organization[<?= $i ?>][uploadfile]" class="<?= $fileClass ?>">
        <?php } ?>
    </div>
<?php } ?>

<!DOCTYPE html>
<html>
<head>
    <title>Organizasyon Şeması</title>
    <?php include 'template/head.php'; ?>

    <!-- ✅ ORİJİNAL STİLLER GERİ GELDİ -->
    <style>
        .ortali { display:flex; justify-content:center; align-items:center; }
        .space-between { justify-content: space-between; }
        .bg1 { background-color:#277790; text-align:center; }
        .bg2 { background-color:#276274; text-align:center; }
        .bg3 { background-color:#e7ecf0; }
        .white { color:white; }
        .dik-cubuk { height:40px; border-left:3px solid #276274; }
        .border-full {
            border-top:3px solid #276274;
            border-right:3px solid #276274;
            border-left:3px solid #276274;
            padding-top:50px;
            z-index:0;
        }
        .widthy30 { width:30%; }
        .widthy50 { width:50%; }
        .org-card { width:190px; z-index:2; font-size:10px; }
        .expand-left { transform: translateX(-50%); }
        .expand-right { transform: translateX(50%); }
        .expand-right-40 { transform: translateX(40%); }
        .expand-right--40 { transform: translateX(-40%); }
        .expand-bottom-right { transform: translateX(80%); margin-top:130px; }
        .label { width:190px; height:40px; padding:10px; padding-left:60px; }
        .label-high { width:750px; height:54px; }
        .relative { position:relative; }
        .profile-pic {
            position:absolute;
            top:15px;
            left:5px;
            width:50px;
            height:50px;
            border-radius:50%;
        }
        .upload-button { position:absolute; bottom:-20px; width:120px; font-size:10px; }
        .zi-1 { z-index:1; }
        .right-side-frame {
            width:330px; height:380px; position:absolute; right:-330px; top:100px;
        }
    </style>
</head>

<body>
<?php if ($bannerHidden == 0) { include 'template/banner.php'; } else { echo "<div class='mt-5'></div>"; } ?>
<br/>

<form action="" method="POST" enctype="multipart/form-data" style="margin-left:-220px;">
    <div class="ortali mb-4">
        <h2><b>OSMANLI ALÜMİNYUM ORGANİZASYON ŞEMASI</b></h2>
    </div>

    <!-- 0 -->
    <div class="ortali"><?php renderOrgCard(0, $users, $user); ?></div>

    <div class="ortali"><div></div><div class="dik-cubuk"></div></div>

    <div class="ortali">
        <div class="border-full relative widthy30">

            <!-- 1 - 2 -->
            <div class="ortali relative space-between">
                <?php renderOrgCard(1, $users, $user, 'expand-left'); ?>
                <?php renderOrgCard(2, $users, $user, 'expand-right'); ?>
            </div>

            <div class="label"></div>

            <!-- 3 - 4 -->
            <div class="ortali relative space-between">
                <?php renderOrgCard(3, $users, $user, 'expand-left'); ?>
                <?php renderOrgCard(4, $users, $user, 'expand-right'); ?>
            </div>

            <div class="label"></div>

            <!-- ALT SOL BLOK (5-10) + ALT SAĞ BLOK (11-16) -->
            <div class="ortali relative space-between">
                <div class="border-full widthy50 expand-left bg3 zi-1">
                    <div class="ortali relative space-between">
                        <?php renderOrgCard(5, $users, $user, 'expand-left'); ?>
                        <?php renderOrgCard(6, $users, $user, 'expand-right--40'); ?>
                    </div>

                    <div class="label"></div>

                    <div class="ortali relative space-between">
                        <?php renderOrgCard(7, $users, $user, 'expand-left'); ?>
                        <?php renderOrgCard(8, $users, $user, 'expand-right--40'); ?>
                    </div>

                    <div class="label"></div>

                    <div class="ortali relative space-between">
                        <?php renderOrgCard(9, $users, $user, 'expand-left'); ?>
                        <?php renderOrgCard(10, $users, $user, 'expand-right--40'); ?>
                    </div>
                </div>

                <div class="border-full widthy50 expand-right bg3 zi-1">
                    <div class="ortali relative space-between">
                        <?php renderOrgCard(11, $users, $user, 'expand-left'); ?>
                        <?php renderOrgCard(12, $users, $user, 'expand-right--40'); ?>
                    </div>

                    <div class="label"></div>

                    <div class="ortali relative space-between">
                        <?php renderOrgCard(13, $users, $user, 'expand-left'); ?>
                        <?php renderOrgCard(14, $users, $user, 'expand-right--40'); ?>
                    </div>

                    <div class="label"></div>

                    <div class="ortali relative space-between">
                        <?php renderOrgCard(15, $users, $user, 'expand-left'); ?>
                        <?php renderOrgCard(16, $users, $user, 'expand-right--40'); ?>
                    </div>
                </div>
            </div>

            <?php
            $firstUser = 17;
            $secondUser = 18;
            ?>

            <!-- SAĞ FRAME (17-18) -->
            <div class="border-full right-side-frame">
                <div style="border-bottom:3px solid #276274; height:60px;"></div>
                <div class="expand-bottom-right relative" style="width:290px;">
                    <?php renderOrgCard($firstUser, $users, $user, '', ''); ?>
                    <div style="margin-top:40px;">
                        <?php renderOrgCard($secondUser, $users, $user); ?>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="ortali mt-5" style="justify-content: space-evenly;">
        <?php if ($user->type == 2) { ?>
            <div></div>
            <button type="submit" class="btn btn-info" name="save_organization" style="width:300px; font-size:25px;">Kaydet</button>
            <a href="organization.php?pdf" class="btn btn-warning">PDF</a>
        <?php } ?>
    </div>
</form>

<br/><br/><br/><br/><br/><br/><br/><br/>
<?php include 'template/script.php'; ?>
</body>
</html>
