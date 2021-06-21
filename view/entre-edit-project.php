<?php

require_once '../database/config.php';

session_start();
ob_start();
setlocale(LC_ALL, 'tr_TR', 'Turkish');
date_default_timezone_set('Europe/Istanbul');
if (!isset($_SESSION['login'])) {
    header('Location:../empty.php');
} elseif (isset($_SESSION['time']) && time() > $_SESSION['time']) {
    session_destroy();
    header('Location:../session-ended.php');
}
if (isset($_GET['userId']) && isset($_GET['projectCode'])) {
    $userId = htmlspecialchars($_GET['userId']);
    $projectCode = htmlspecialchars($_GET['projectCode']);
    $loginUsers = $db->query('SELECT * FROM users WHERE idUser=' . $_GET['userId'] . '')->fetch(PDO::FETCH_ASSOC);
    $userRoles = $db->query("SELECT * FROM roles WHERE idRole=" . $loginUsers['Roles_idRole'])->fetch(PDO::FETCH_ASSOC);
    $userApplies = $db->query("SELECT * FROM applies WHERE projectCode='$projectCode'")->fetch(PDO::FETCH_ASSOC);
    $categories  = $db->query('SELECT * FROM categories WHERE idCategories=' . $userApplies['Categories_idCategories'])->fetch(PDO::FETCH_ASSOC);
    $allCategories  = $db->query('SELECT * FROM categories')->fetchAll(PDO::FETCH_ASSOC);
    $allSectors = $db->query('SELECT * FROM sectors')->fetchAll(PDO::FETCH_ASSOC);
    $sectors = $db->query('SELECT * FROM sectors WHERE idSectors=' . $userApplies['Sectors_idSectors'])->fetch(PDO::FETCH_ASSOC);
    $naceCode =  $db->query("SELECT * FROM nacecodes WHERE idNace=" . $userApplies['NaceCodes_idNace'])->fetch(PDO::FETCH_ASSOC);
    $allNaceCodes = $db->query('SELECT * FROM naceCodes')->fetchAll(PDO::FETCH_ASSOC);
    $transitionApply = $db->query("SELECT * FROM transitions WHERE Applies_idApply=" . $userApplies['idApply'])->fetch(PDO::FETCH_ASSOC);
    $projectReports = $db->query("SELECT fileName,location,size from files WHERE Apply_idApply = " . $userApplies['idApply'] . " && User_idUser=" . $loginUsers['idUser'])->fetchAll(PDO::FETCH_ASSOC);

    //$statuTransections = $db->query('SELECT * FROM statuTransecitons WHERE Applies_idApply='.$_GET[''])

    if (isset($_POST['save'])) {

        $projectName = isset($_POST['project-name-input']) ? $_POST['project-name-input'] : null;
        $projectCode = isset($_POST['project-code-input']) ? $_POST['project-code-input'] : null;
        $projectTutar = isset($_POST['talep-tutar-input']) ? $_POST['talep-tutar-input'] : null;
        $projectNaceCode = isset($_POST['selectSm']) ? $_POST['selectSm'] : null;
        $projectSectors = isset($_POST['sectors']) ? $_POST['sectors'] : null;
        $projectContent = isset($_POST['proje-icerik-input']) ? $_POST['proje-icerik-input'] : null;
        $startDate = isset($_POST['start-date-input']) ? $_POST['start-date-input'] : null;
        $projectCategories = isset($_POST['categories']) ? $_POST['categories'] : null;
        $deadlineInput = isset($_POST['deadline-input']) ? $_POST['deadline-input'] : null;
        $projectGoal = isset($_POST['project-goal']) ? $_POST['project-goal'] : null;

        if (!$projectName) {
            $result['sonuc'] = 'Proje ismi girilmedi';
            echo 'projectName';
            exit;
        } else if (!$projectCode) {
            $result['sonuc'] = 'Proje kodu girilmedi';
            echo 'projectCode';
            exit;
        } else if (!$projectTutar) {
            $result['sonuc'] = 'Talep edilen tutar girilmedi';
            echo 'projectTutar';
            exit;
        } else if (!$projectNaceCode) {
            $result['sonuc'] = 'Proje nace kodu seçilmedi';
            echo 'projectNaceCode';
            exit;
        } else if (!$projectContent) {
            $result['sonuc'] = 'Proje içeriği girilmedi';
            echo 'projectContent';
            exit;
        } else if (!$startDate) {
            $result['sonuc'] = 'Proje başlangıç tarihi girilmedi';
            echo 'startDate';
            exit;
        } else if (!$projectCategories) {
            $result['sonuc'] = 'Proje kategorisi girilmedi';
            echo 'categories';
            exit;
        } else if (!$deadlineInput) {
            $result['sonuc'] = 'Proje süresi girilmedi';
            echo 'deadlineInput';
            exit;
        } else if (!$projectGoal) {
            $result['sonuc'] = 'Proje hedefi girilmedi';
            echo 'projectGoal';
            exit;
        } else if (!$projectSectors) {
            $result['sonuc']  = 'Proje sektör bilgisi seçilmedi';
            echo 'projectSectors';
            exit;
        } else {
            $applyUpdateQuery = $db->prepare("UPDATE applies SET name=?,goal=?,content=?,projectCode=?,requestedAmount=?,NaceCodes_idNace=?, Sectors_idSectors=?, Categories_idCategories=? WHERE idApply=" .
                $userApplies['idApply']);
            $updateResult = $applyUpdateQuery->execute([
                $projectName,
                $projectGoal,
                $projectContent,
                $projectCode,
                $projectTutar,
                $projectNaceCode,
                $projectSectors,
                $projectCategories,
            ]);

            if ($updateResult) {
                $actionQuery = $db->prepare('INSERT INTO actions SET name=?,projectCode=?,User_idUser=?,Roles_idRole=?');
                $actionSonuc = $actionQuery->execute([
                    "Proje kaydı güncellendi",
                    $projectCode,
                    $userId,
                    0,
                ]);
                if ($actionSonuc) {
                    echo "<script>alert('Proje kaydınız başarı ile güncellenmiştir..');document.location='table.php?userId=" . $_GET['userId'] . "'</script>";
                } else {
                    $hata = $actionQuery->errorInfo();
                    echo $hata[2];
                    exit;
                }
            } else {
                $hata = $applyUpdateQuery->errorInfo();
                echo $hata[2];
                exit;
            }
        }
    } else if (isset($_POST['exit'])) {
        echo '<script>window.location.href = "table.php?userId=' . $userId . '";</script>';
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="au theme template">
    <meta name="author" content="Hau Nguyen">
    <meta name="keywords" content="au theme template">
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <!-- Title Page-->
    <title>Başvurulan Projeler</title>

    <!-- Fontfaces CSS-->
    <link href="css/font-face.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-5/css/fontawesome-all.min.css" rel="stylesheet" media="all">
    <link href="vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">

    <!-- Bootstrap CSS-->
    <link href="vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">

    <!-- Vendor CSS-->
    <link href="vendor/animsition/animsition.min.css" rel="stylesheet" media="all">
    <link href="vendor/bootstrap-progressbar/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet" media="all">
    <link href="vendor/wow/animate.css" rel="stylesheet" media="all">
    <link href="vendor/css-hamburgers/hamburgers.min.css" rel="stylesheet" media="all">
    <link href="vendor/slick/slick.css" rel="stylesheet" media="all">
    <link href="vendor/select2/select2.min.css" rel="stylesheet" media="all">
    <link href="vendor/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet" media="all">
    <link rel="shortcut icon" href="/bitirme/assets/images/tto.png">
    <!-- Main CSS-->
    <link href="css/info.css" rel="stylesheet" media="all">


    <style>
        .container-image-add {
            position: relative;
            text-align: center;
            color: white;
        }

        .centered {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 60px;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


</head>

<body class="animsition">
    <div class="page-wrapper">

        <!-- MENU SIDEBAR-->
        <aside class="menu-sidebar2">
            <div class="logo">
                <a href="../index.php">
                    <img src="images/icon/logo-white.png" alt="FSMVÜ" />
                </a>
            </div>
            <div class="menu-sidebar2__content js-scrollbar1">
                <div class="account2">
                    <div class="image img-cir img-120">
                        <div class="container-image-add">
                            <img src="images/icon/profile.png" alt="Profile" />
                            <div class="centered"> <?php echo ($loginUsers['name']) ? strtoupper(substr($loginUsers['name'], 0, 1))  : '' ?></div>
                        </div>
                    </div>

                    <h4 class="name"><?php echo $loginUsers['name']  . ' ' .  $loginUsers['surname'] ?></h4>
                    <h4><?php echo $userRoles['description'] ?></h4>
                    <br>
                    <a href="../index.php">Çıkış</a>
                </div>
                <nav class="navbar-sidebar2">
                    <ul class="list-unstyled navbar__list">
                        <li class="active has-sub">
                            <a href="tto-yetkili.php?userId=<?php echo $_GET['userId'] ?>">
                                <i class="fas fa-home"></i>Ana Sayfa
                            </a>

                        </li>

                        <li class="has-sub">
                            <a class="js-arrow" href="#">
                                <i class="fas fa-trophy"></i>Proje
                                <span class="arrow">
                                    <i class="fas fa-angle-down"></i>
                                </span>
                            </a>
                            <ul class="list-unstyled navbar__sub-list js-sub-list">
                                <li>
                                    <a href="tto-yetkili-table.php?userId=<?php echo $_GET['userId'] ?>">
                                        <i class="fas fa-clipboard-list"></i>Tüm Projeler</a>
                                </li>

                                <li>
                                    <a href="tto-system-projects.php?userId=<?php echo $_GET['userId'] ?>">
                                        <i class="fas fa-tasks"></i>Devam Eden Projeler</a>
                                </li>

                                <li>
                                    <a href="tto-all-confirms.php?userId=<?php echo $_GET['userId'] ?>">
                                        <i class="fas fa-check"></i>Onaylananlar</a>
                                </li>
                                <li>
                                    <a href="tto-new-project.php?userId=<?php echo $_GET['userId'] ?>">
                                        <i class="fas fa-bell"></i>Yeni Başvurular</a>
                                </li>

                                <li>
                                    <a href="tto-editable-project.php?userId=<?php echo $_GET['userId'] ?>">
                                        <i class="far fa-edit"></i>Düzenleme Sürecindekiler</a>
                                </li>

                            </ul>
                        </li>

                        <li class="has-sub">
                            <a class="js-arrow" href="#">
                                <i class="fas fa-thumbtack"></i>Patent
                                <span class="arrow">
                                    <i class="fas fa-arrow-right"></i>
                                </span>
                            </a>
                        </li>

                    </ul>
                </nav>
            </div>
        </aside>
        <!-- END MENU SIDEBAR-->

        <!-- PAGE CONTAINER-->
        <div class="page-container">
            <!-- HEADER DESKTOP-->
            <header class="header-desktop2">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="header-wrap2">
                            <div class="logo d-block d-lg-none">
                                <a href="#">
                                    <img src="images/icon/logo-white.png" alt="FSMVÜ" />
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            </header>
            <!-- END HEADER DESKTOP-->

            <!-- MAIN CONTENT-->
            <div class="main-content">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-header">
                                        <strong>Proje</strong> Bilgileri
                                    </div>
                                    <div class="card-body card-block">

                                        <form action="" id="data-form" method="post" enctype="multipart/form-data" class="form-horizontal" accept-charset="UTF-8">

                                            <div class="row form-group">
                                                <div class="col col-md-3">
                                                    <label class=" form-control-label">Başvuran</label>
                                                </div>
                                                <div class="col-12 col-md-9">
                                                    <p class="form-control-static"><?php echo $loginUsers['name'] . ' ' . $loginUsers['surname'] ?></p>
                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col col-md-3">
                                                    <label for="email-input" class="form-control-label">Proje Kodu</label>
                                                </div>
                                                <div class="col-12 col-md-9">
                                                    <input type="text" id="email-input" name="project-code-input" value="<?php echo $userApplies['projectCode'] ?> " placeholder="Proje Kodu" class="form-control">

                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col col-md-3">
                                                    <label for="text-input" class="form-control-label">Proje Adı</label>
                                                </div>
                                                <div class="col-12 col-md-9">
                                                    <input value="<?php echo $userApplies['name'] ?>" type="text" id="text-input" name="project-name-input" placeholder="Proje Adı" class="form-control">
                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col col-md-3">
                                                    <label for="text-input" class="form-control-label">Proje Hedefi</label>
                                                </div>
                                                <div class="col-12 col-md-9">
                                                    <input value="<?php echo $userApplies['goal'] ?>" type="text" id="text-input" name="project-goal" placeholder="Proje Hedefi" class="form-control">
                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col col-md-3">
                                                    <label for="password-input" class="form-control-label">Talep Edilen Destek Tutarı</label>
                                                </div>
                                                <div class="col-12 col-md-9">
                                                    <input type="text" id="password-input" name="talep-tutar-input" placeholder="Tutar" value="<?php echo $userApplies['requestedAmount'] ?> " class="form-control">

                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col col-md-3">
                                                    <label for="password-input" class=" orm-control-label">Verilen Destek Tutarı</label>
                                                </div>
                                                <div class="col-12 col-md-9">
                                                    <input type="text" id="password-input" name="destek-tutar-input" value="<?php echo $userApplies['confirmedAmount'] ?> " class="form-control">
                                                </div>
                                            </div>

                                            <div st class="row form-group">
                                                <div class="col col-md-3">
                                                    <label class="form-control-label">Nace Kodları</label>
                                                </div>
                                                <div class="col-12 col-md-9">
                                                    <select name="selectSm" id="SelectLm" class="form-control-sm form-control">
                                                        <option value="<?php echo $naceCode['idNace'] ?>"><?php echo substr_replace(substr_replace($naceCode['code'], '.', -2, 0), '.', -5, 0) . ' - ' . $naceCode['description'] ?></option>
                                                        <?php foreach ($allNaceCodes as $nace) : ?>
                                                            <option value="<?php echo $nace['idNace'] ?>"><?php echo substr_replace(substr_replace($nace['code'], '.', -2, 0), '.', -5, 0) . ' - ' . $nace['description'] ?></option>
                                                        <?php endforeach; ?>
                                                    </select>

                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col col-md-3">
                                                    <label for="select" class=" form-control-label">Kategoriler</label>
                                                </div>
                                                <div class="col-12 col-md-9">
                                                    <select name="categories" id="select" class="form-control">
                                                        <option value="<?php echo $categories['idCategories'] ?>"><?php echo $categories['title'] ?></option>
                                                        <?php foreach ($allCategories as $category) : ?>
                                                            <option value="<?php echo $category['idCategories'] ?>"><?php echo $category['title'] ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col col-md-3">
                                                    <label for="select" class=" form-control-label">Sektörler</label>
                                                </div>
                                                <div class="col-12 col-md-9">

                                                    <select name="sectors" id="select" class="form-control">
                                                        <option value="<?php echo $sectors['idSectors'] ?>"><?php echo $sectors['name'] ?></option>
                                                        <?php foreach ($allSectors as $sector) : ?>
                                                            <option value="<?php echo $sector['idSectors'] ?>"><?php echo $sector['name'] ?></option>
                                                        <?php endforeach; ?>
                                                    </select>

                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col col-md-3">
                                                    <label for="textarea-input" class="form-control-label">Proje İçeriği</label>
                                                </div>
                                                <div class="col-12 col-md-9">
                                                    <textarea name="proje-icerik-input" id="textarea-input" rows="9" class="form-control"> <?php echo $userApplies['content'] ?></textarea>
                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col col-md-3">
                                                    <label for="start-date-input" name="startDate" class="form-control-label">Başlangıç Tarihi</label>
                                                </div>
                                                <div class="col-12 col-md-9">
                                                    <input type="text" id="start-date-input" name="start-date-input" placeholder="Tarihi Yazınız" value="<?php echo $transitionApply['startDate'] ?>" class="form-control">
                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col col-md-3">
                                                    <label for="deadline-input" class="form-control-label">Proje Süresi(Ay)</label>
                                                </div>
                                                <div class="col-12 col-md-9">
                                                    <input type="text" id="deadline-input" name="deadline-input" placeholder="Tarihi Yazınız" value="<?php echo $transitionApply['deadline'] ?>" class="form-control">

                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col col-md-3">
                                                    <label for="password-input" class="form-control-label">Bitiş Tarihi</label>
                                                </div>
                                                <div class="col-12 col-md-9">
                                                    <input type="text" id="start-date-input" name="start-date-input" placeholder="Tarihi Yazınız" disabled=TRUE value="<?php echo ($transitionApply['endDate'] != null) ? $transitionApply['endDate'] : "Belirtilmemiş" ?>" class="form-control">
                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col col-md-3">
                                                    <label for="file-input" class="form-control-label">Yüklenen Belgeler</label>
                                                </div>
                                                <div class="col-12 col-md-9">
                                                    <?php foreach ($projectReports as $pr) : ?>

                                                        <ul>
                                                            <a href="http://localhost/bitirme/view/<?php echo $pr['location'] ?>">
                                                                <li style="display: inline;"> <?php echo $pr['fileName']  ?></li>
                                                            </a>
                                                        </ul>

                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                            <button type="submit" name="save" class="btn btn-primary btn-sm" style="float: right;">
                                                <i class="fa fa-save"></i> Değişiklikleri Kaydet

                                            </button>


                                            <button type="submit" name="exit" class="btn btn-primary btn-sm" style="float: right;  margin-right: 10px;">
                                                <i class="fa fa-times"></i> Vazgeç

                                            </button>

                                        </form>

                                    </div>

                                </div>

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="copyright">
                                    <p>© 2021 - Developed by Emre USTA - <a href="https://www.fsm.edu.tr">FSMVÜ</a>.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Jquery JS-->
    <script src="vendor/jquery-3.2.1.min.js"></script>
    <!-- Bootstrap JS-->
    <script src="vendor/bootstrap-4.1/popper.min.js"></script>
    <script src="vendor/bootstrap-4.1/bootstrap.min.js"></script>
    <!-- Vendor JS       -->
    <script src="vendor/slick/slick.min.js">
    </script>
    <script src="vendor/wow/wow.min.js"></script>
    <script src="vendor/animsition/animsition.min.js"></script>
    <script src="vendor/bootstrap-progressbar/bootstrap-progressbar.min.js">
    </script>
    <script src="vendor/counter-up/jquery.waypoints.min.js"></script>
    <script src="vendor/counter-up/jquery.counterup.min.js">
    </script>
    <script src="vendor/circle-progress/circle-progress.min.js"></script>
    <script src="vendor/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="vendor/chartjs/Chart.bundle.min.js"></script>
    <script src="vendor/select2/select2.min.js">
    </script>

    <!-- Main JS-->
    <script src="js/main.js"></script>

</body>

</html>
<!-- end document-->