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
if (isset($_GET['userId']) && isset($_GET['patentCode'])) {
    $userId = htmlspecialchars($_GET['userId']);
    $patentCode = htmlspecialchars($_GET['patentCode']);
    $loginUsers = $db->query('SELECT * FROM users WHERE idUser=' . $_GET['userId'] . '')->fetch(PDO::FETCH_ASSOC);
    $userRoles = $db->query("SELECT * FROM roles WHERE idRole=" . $loginUsers['Roles_idRole'])->fetch(PDO::FETCH_ASSOC);
    $userApplies = $db->query("SELECT * FROM patents WHERE patentCode='$patentCode'")->fetch(PDO::FETCH_ASSOC);
    $findUsers = $db->query("SELECT name,surname FROM users WHERE idUser=" . $userApplies['Users_idUser'])->fetch(PDO::FETCH_ASSOC);
    $categories  = $db->query('SELECT * FROM categories WHERE idCategories=' . $userApplies['Categories_idCategories'])->fetch(PDO::FETCH_ASSOC);
    $sectors = $db->query('SELECT * FROM sectors WHERE idSectors=' . $userApplies['Sectors_idSectors'])->fetch(PDO::FETCH_ASSOC);
    $naceCode =  $db->query("SELECT * FROM nacecodes WHERE idNace=" . $userApplies['NaceCodes_idNace'])->fetch(PDO::FETCH_ASSOC);
    $transitionApply = $db->query("SELECT * FROM patentstatus WHERE Patents_idPatents=" . $userApplies['idPatent'])->fetch(PDO::FETCH_ASSOC);
    $projectReports = $db->query("SELECT fileName,location,size from patentfiles WHERE Patents_idPatents = " . $userApplies['idPatent'])->fetchAll(PDO::FETCH_ASSOC);
    $projectActions = $db->query("SELECT name,date,User_idUser,Roles_idRole FROM actions WHERE projectCode='$patentCode' ORDER BY date DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    $actionRole = $db->query("SELECT description FROM roles WHERE idRole=" .  $projectActions['Roles_idRole'])->fetch(PDO::FETCH_ASSOC);
    //$statuTransections = $db->query('SELECT * FROM statuTransecitons WHERE Applies_idApply='.$_GET[''])

    if (isset($_POST['submit'])) {
        echo '<script>window.location.href = "tto-yetkili-patent-table.php?userId=' . $userId . '";</script>';
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
                            <a href="index.php?userId=<?php echo $_GET['userId'] ?>">
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
                            <a class="js-arrow" href="tto-yetkili-patent-table.php?userId=<?php echo $_GET['userId'] ?>">
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
                                                    <p class="form-control-static"><?php echo $findUsers['name'] . ' ' . $findUsers['surname'] ?></p>
                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col col-md-3">
                                                    <label for="email-input" class="form-control-label">Proje Kodu</label>
                                                </div>
                                                <div class="col-12 col-md-9">
                                                    <input type="text" id="email-input" name="project-code-input" value="<?php echo $userApplies['patentCode'] ?> " disabled=TRUE placeholder="Proje Kodu" class="form-control">

                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col col-md-3">
                                                    <label for="text-input" class="form-control-label">Proje Adı</label>
                                                </div>
                                                <div class="col-12 col-md-9">
                                                    <input value="<?php echo $userApplies['name'] ?>" type="text" id="text-input" name="project-name-input" disabled=TRUE placeholder="Proje Adı" class="form-control">
                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col col-md-3">
                                                    <label for="text-input" class="form-control-label">Proje Hedefi</label>
                                                </div>
                                                <div class="col-12 col-md-9">
                                                    <input value="<?php echo $userApplies['goal'] ?>" type="text" disabled="TRUE" id="text-input" name="project-goal" class="form-control">
                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col col-md-3">
                                                    <label for="password-input" class="form-control-label">Projenin Yenilikçi Yönü</label>
                                                </div>
                                                <div class="col-12 col-md-9">
                                                    <textarea type="text" disabled id="yeni-yon-input" name="yeni-yon-input" rows="9" class="form-control"><?php echo $userApplies['newInfo'] ?></textarea>
                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col col-md-3">
                                                    <label for="textarea-input" class="form-control-label">Proje İçeriği</label>
                                                </div>
                                                <div class="col-12 col-md-9">
                                                    <textarea name="proje-icerik-input" id="textarea-input" rows="9" class="form-control" disabled=TRUE> <?php echo $userApplies['content'] ?></textarea>
                                                </div>
                                            </div>

                                            <div st class="row form-group">
                                                <div class="col col-md-3">
                                                    <label class="form-control-label">Nace Kodları</label>
                                                </div>
                                                <div class="col-12 col-md-9">
                                                    <input type="text" id="password-input" name="nace-code-input" value="<?php echo $naceCode['code'] ?> - <?php echo $naceCode['description'] ?> " disabled=TRUE class="form-control">
                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col col-md-3">
                                                    <label for="select" class=" form-control-label">Kategoriler</label>
                                                </div>
                                                <div class="col-12 col-md-9">
                                                    <input type="text" id="password-input" name="category-input" value="<?php echo $categories['title'] ?> " disabled=TRUE class="form-control">
                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col col-md-3">
                                                    <label for="select" class=" form-control-label">Sektörler</label>
                                                </div>
                                                <div class="col-12 col-md-9">
                                                    <input type="text" id="password-input" name="sectors-input" value="<?php echo $sectors['name'] ?>" disabled=TRUE class="form-control">
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

                                            <div class="row form-group">
                                                <div class="col col-md-3">
                                                    <label for="file-input" class="form-control-label">Güncel Hareket: </label>
                                                </div>
                                                <div class="col-12 col-md-9">
                                                    <input type="text" id="project-action-input" name="project-action-input" disabled=TRUE value="<?php echo $actionRole['description'] . ' tarafından ' . strftime("%e %B %Y %H:%M:%S ", strtotime($projectActions['date'])) . ' tarihinde ' . $projectActions['name'] ?>" class="form-control">
                                                </div>
                                            </div>

                                            <input type="hidden" name="submit" id="">
                                            <button type="submit" class="btn btn-primary btn-sm" style="float: right;">
                                                <i class="fa fa-angle-left"></i> Geri Dön

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