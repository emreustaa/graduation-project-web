<?php

require '../database/config.php';
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
if (isset($_GET['userId'])) {
    $loginUsers = $db->query('SELECT * FROM users WHERE idUser=' . $_GET['userId'] . '')->fetch(PDO::FETCH_ASSOC);
    $userRoles = $db->query("SELECT * FROM roles WHERE idRole=" . $loginUsers['Roles_idRole'])->fetch(PDO::FETCH_ASSOC);
    $allUsers = $db->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);
    $totalApply = $db->query("SELECT COUNT(idApply) toplamBasvuru from applies WHERE Users_idUser=" . $_GET['userId'])->fetch(PDO::FETCH_ASSOC);
    $totalConfirmProject = $db->query("SELECT COUNT(idApply) onaylananBasvuru FROM applies INNER JOIN statutransections ON applies.idApply = statutransections.Applies_idApply 
    WHERE Users_idUser=" . $_GET['userId'] . "&&statutransections.Status_idStatus=1")->fetch(PDO::FETCH_ASSOC);
    $totalRejectProject = $db->query("SELECT COUNT(idApply) reddedilenBasvuru FROM applies INNER JOIN statutransections ON applies.idApply = statutransections.Applies_idApply 
    WHERE Users_idUser=" . $_GET['userId'] . "&&statutransections.Status_idStatus=2")->fetch(PDO::FETCH_ASSOC);
    $totalConfirm = $db->query("SELECT COUNT(idConfirms) toplamOnay from confirms")->fetch(PDO::FETCH_ASSOC);
    $totalAmount  = $db->query("SELECT SUM(confirmedAmount) toplamTutar FROM applies  WHERE Users_idUser=" . $_GET['userId'])->fetch(PDO::FETCH_ASSOC);
    $userApplies = $db->query("SELECT name,projectCode,date,currentRole FROM applies INNER JOIN statutransections ON applies.idApply = statutransections.Applies_idApply WHERE Users_idUser=" . $_GET['userId'])->fetchAll(PDO::FETCH_ASSOC);
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

    <!-- Title Page-->
    <title>Ana Sayfa</title>

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
    <link href="vendor/vector-map/jqvmap.min.css" rel="stylesheet" media="all">
    <link rel="shortcut icon" href="/bitirme/assets/images/tto.png">
    <!-- Main CSS-->
    <link href="css/theme.css" rel="stylesheet" media="all">

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

</head>

<body class="animsition">
    <div class="page-wrapper">
        <!-- MENU SIDEBAR-->
        <aside class="menu-sidebar2">
            <div class="logo">
                <a href="#">
                    <img src="images/icon/logo-white.png" alt="Cool Admin" />
                </a>
            </div>
            <div class="menu-sidebar2__content js-scrollbar1">
                <div class="account2">
                    <div class="image img-cir img-120">
                        <div class="container-image-add">
                            <img src="images/icon/profile.png" alt="Fsmvü" />
                            <div class="centered"> <?php echo ($loginUsers['name']) ? strtoupper(substr($loginUsers['name'], 0, 1))  : '' ?></div>
                        </div>
                    </div>
                    <h4 class="name"><?php echo $loginUsers['name'] . ' ' . $loginUsers['surname'] ?></h4>
                    <h4><?php echo $userRoles['description'] ?></h4>
                    <br>
                    <a href="../index.php">Çıkış</a>
                </div>
                <nav class="navbar-sidebar2">
                    <ul class="list-unstyled navbar__list">
                        <li class="active has-sub">
                            <a class="js-arrow" href="index.php?userId=<?php echo $_GET['userId'] ?>">
                                <i class="fas fa-home"></i>Ana Sayfa
                            </a>

                        </li>

                        <li class="has-sub">
                            <a class="js-arrow" href="table.php?userId=<?php echo $_GET['userId'] ?>">
                                <i class="fas fa-trophy"></i>Proje
                                <span class="arrow">
                                    <i class="fas fa-arrow-right"></i>
                                </span>
                            </a>
                        </li>

                        <li class="has-sub">
                            <a class="js-arrow" href="form.php?userId=<?php echo $_GET['userId'] ?>">
                                <i class="far fa-paper-plane"></i>Proje Başvurusu

                            </a>
                        </li>


                        <li class="has-sub">
                            <a class="js-arrow" href="entre-patent-form.php?userId=<?php echo $_GET['userId'] ?>">
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
        <div class="page-container2">
            <!-- HEADER DESKTOP-->
            <header class="header-desktop2">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="header-wrap2">
                            <div class="logo d-block d-lg-none">
                                <a href="#">
                                    <img src="images/icon/logo-white.png" alt="FSMVU" />
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            </header>


            <!-- BREADCRUMB-->
            <section class="au-breadcrumb m-t-75">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="au-breadcrumb-content">
                                    <div class="au-breadcrumb-left">
                                        <span class="au-breadcrumb-span">Buradasınız:</span>
                                        <ul class="list-unstyled list-inline au-breadcrumb__list">
                                            <li class="list-inline-item">
                                                <?php
                                                $bolunen =  explode("/", $_SERVER['PHP_SELF'], 3);
                                                echo ucfirst($bolunen[0]) . '/' . ucfirst($bolunen[1]) . '/'  . ucfirst($bolunen[2]);
                                                ?></li>
                                        </ul>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- END BREADCRUMB-->

            <section class="statistic">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="row">

                            <div class="col-md-6 col-lg-3">
                                <div class="statistic__item">
                                    <h2 class="number"><?php echo $totalApply['toplamBasvuru'] ?></h2>
                                    <span class="desc">BAŞVURULAN PROJE SAYISI</span>
                                    <div class="icon">
                                        <i class="zmdi zmdi-file"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="statistic__item">
                                    <h2 class="number"><?php echo $totalConfirmProject['onaylananBasvuru'] ?></h2>
                                    <span class="desc">ONAYLANAN PROJE SAYISI</span>
                                    <div class="icon">
                                        <i class="zmdi zmdi-account-o"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-lg-3">
                                <div class="statistic__item">
                                    <h2 class="number"><?php echo $totalRejectProject['reddedilenBasvuru'] ?></h2>
                                    <span class="desc">REDDEDİLEN PROJE SAYISI</span>
                                    <div class="icon">
                                        <i class="zmdi zmdi-check"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="statistic__item">
                                    <h2 class="number"><?php echo '₺' . $totalAmount['toplamTutar'] ?></h2>
                                    <span class="desc">Toplam Verilen Destek</span>
                                    <div class="icon">
                                        <i class="fa fa-turkish-lira"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section>
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="row">


                            <div class="col-xl-6">
                                <!-- USER DATA-->
                                <div class="user-data m-b-40">
                                    <h3 class="title-3 m-b-30">
                                        <i class="zmdi zmdi-account-calendar"></i>Onay Bekleyen Projeler
                                    </h3>

                                    <div class="table-responsive table-data">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <td>PROJE ADI</td>
                                                    <td>İLGİLİ KİŞİ</td>
                                                    <td>BAŞVURU TARİHİ</td>
                                                    <td></td>
                                                </tr>
                                            </thead>
                                            <tbody class="tbody">

                                                <?php foreach ($userApplies as $user) : ?>
                                                    <tr>

                                                        <td>
                                                            <div class="table-data__info">
                                                                <h6><?php echo $user['name'] ?></h6>
                                                                <span>
                                                                    <a href="#"><?php echo $user['projectCode'] ?></a>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <?php

                                                            if ($user['currentRole'] == "Girişimci") {
                                                                $roleClass = "role user";
                                                                $roleName = $user['currentRole'];
                                                            } else if ($user['currentRole'] == "Yetkili Hakem") {
                                                                $roleClass = "role admin";
                                                                $roleName = $user['currentRole'];
                                                            } else if ($user['currentRole'] == "TTO Yetkilisi") {
                                                                $roleClass = "role member";
                                                                $roleName = $user['currentRole'];
                                                            }
                                                            ?>
                                                            <span class="<?php echo $roleClass ?>"><?php echo $roleName ?></span>
                                                        </td>
                                                        <td>
                                                            <div class="table-data__info">
                                                                <h6><?php echo strftime("%e %B %Y", strtotime($user['date'])) ?></h6>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>


                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                                <!-- END USER DATA-->
                            </div>
                        </div>
                    </div>
                </div>
            </section>


            <section>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="copyright">
                                <p>© 2021 - Developed by Emre USTA - <a href="https://www.fsm.edu.tr">FSMVÜ</a>.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- END PAGE CONTAINER-->
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
    <script src="vendor/vector-map/jquery.vmap.js"></script>
    <script src="vendor/vector-map/jquery.vmap.min.js"></script>
    <script src="vendor/vector-map/jquery.vmap.sampledata.js"></script>
    <script src="vendor/vector-map/jquery.vmap.world.js"></script>

    <!-- Main JS-->
    <script src="js/main.js"></script>

</body>

</html>
<!-- end document-->